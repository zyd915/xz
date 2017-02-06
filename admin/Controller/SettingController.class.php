<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 设置.
 */
class SettingController extends BaseController
{
  // 初始化
  public function init()
  {
    // 父类加载
    parent::init();
    $this->assign(array('jsInsertBundles' => array('admin/product', 'product/user')));


    // 动态加载的js
    $this->assign(array('jsInsertBundles' => array('admin/productManager')));
    $attributes = AttributeNameModel::model()->getDb()->queryAll('nid');

    // 所有属性
    $this->assign(array('attributes' => $attributes));
  }

  // 更改密码
  public function changePassAction()
  {
    if ($pass = arPost()) :
      if ($pass['newPass'] && $pass['newPass'] == $pass['confirmPass']) :
        $condition = array(
          'name' => $this->assign['admin']['name'],
          'pwd' => AdminModel::gPwd($pass['originPass']),
        );

        if (AdminModel::model()->getDb()->where($condition)->count() > 0) :
          // 更新密码
          AdminModel::model()->getDb()
            ->where($condition)
            ->update(array('pwd' => AdminModel::gPwd($pass['newPass'])));
          $this->redirectSuccess('Index/index', '更新密码');
        else :
          $this->redirectError('', '原密码错误');
        endif;
      else :
        $this->redirectError('', '两次密码不相等');
      endif;
    else :
      $this->display('@/Setting/changePass');
    endif;

  }

  // 地区设置
  public function areaAction()
  {
    $opt = arRequest('opt', '');
    $data = arPost();
    if ($data) :
      if ($opt == 'add') :
        if (empty($data['name'])) :
           $this->redirectError('', '地区名缺少，添加失败');
        endif;
        if (empty($data['pid'])) :
           $data['rank'] = 1;
        else :
          $data['rank'] = RegionModel::model()->getDb()
            ->where(array('rid' => $data['pid']))
            ->queryColumn('rank') + 1;
        endif;
        if (RegionModel::model()->getDb()->insert($data, 1)) :
           $this->redirectSuccess('', '添加成功');
        else :
           $this->redirectError('', '添加失败');
         endif;
      elseif ($opt == 'update') :
        $condition = array(
          'rid' => $data['rid'],
        );
        if (RegionModel::model()->getDb()->where($condition)->update($data, 1)) :
          $this->redirectSuccess('', '更新成功');
        else :
          $this->redirectError('', '更新失败');
        endif;
      elseif ($opt == 'delete') :
        $condition = array(
          'rid' => $data['rid'],
        );
        if (RegionModel::model()->getDb()->where($condition)->delete()) :
          $this->redirectSuccess('', '删除成功');
        else :
          $this->redirectError('', '删除失败');
        endif;
      endif;

    endif;

    // 调用百度地址选择插件
    arSeg(array(
          'loader' => array(
            'plugin' => 'select_area',
            'this' => $this
          )
      )
    );
    $this->assign(array('jsInsertBundles' => array('admin/area')));
    $this->display('@/Setting/area');
  }

  // 友情链接
  public function fLinkAction()
  {
    if(arGet('type'))
    {
      $cond['type'] = arGet('type');

    }else{
      $cond = array();
    }
    if ($data = arRequest())
    {

      if(!empty($data['keywords']))
      {
         $keywords = $data['keywords'];
         $cond =implode('or',
                  array(
                      " lid = '$keywords' ",
                      " name like '%$keywords%' ",
                  )
              );
      }
    }

    $count = LinkModel::model()->getDb()->where($cond)->count();
    $page = new Page($count,15);
    $link = LinkModel::model()->getDb()
      ->where($cond)
      ->order('lid desc')
      ->limit($page->limit())
      ->queryAll();
    $this->assign(array('link'=>$link,'page'=>$page->show(),'tips' => '链接id,名称'));
    $this->display('@/Setting/fLink');
  }

    // 添加链接
    public function addLinkAction()
    {
        $this->assign(array('jsInsertBundles' => array('admin/flink')));
        arSeg(array(
                    'loader' => array(
                        'plugin' => 'bdeditor,layer,ajaxfileupload',
                        'this' => $this
                    )
                )
        );
        if ($data = arPost()) :
            if ($lid = arRequest('lid')) :
                $linkData = LinkModel::model()->getDb()->where(array('lid'=>$data['lid']))->update($data, 1);
            else :
                $linkData = LinkModel::model()->getDb()->insert(arPost(), 1);
              
            endif;
            if ($linkData) :
                $this->redirectSuccess('fLink','数据添加成功');
            else :
                $this->redirectError('','数据添加失败');
            endif;
        else :
            $lid = arGet('lid');
            $link =  LinkModel::model()->getDb()->where(array('lid' => $lid))->queryRow();
            $this->assign(array('link'=>$link));
            $this->display('@/Setting/addLink');
        endif;

   }

  //友情链接处理
  public function linkManagerAction()
  {
    $opt = arRequest('opt');
    $data = arRequest();


   switch ($opt) {
    case 'delete':
      $lid = arRequest('lid');
      $linkData = LinkModel::model()->getDb()->where(array('lid'=>$lid))->delete();
      if($linkData)
     {
        return $this->showJsonSuccess('操作成功');
     }else{
        return $this->showJsonError('操作失败');
     }
     break;
   }
  }
  // 清除缓存
  public function clearCacheAction()
  {
    arComp('cache.file')->flushAll();
    $this->redirectSuccess(array('Index/index'), '清除缓存');

  }
  // 网站的基本信息列表
  public function sysSettingAction(){
    $count = SettingModel::model()->getDb()
      ->count();

    $page = new Page($count, 8);
    $set = SettingModel::model()->getDb()
      ->limit($page->limit())
      ->queryAll();
    // 动态加载的js
    $this->assign(array('jsInsertBundles' => array('admin/setManager')));
    $this->assign(array('page' => $page->show()));
    $this->assign(array('set' => $set));
    $this->display('@/Setting/sysSetting');

  }
  // 处理网站的一些内容如注册商标等等
  public function setManagerAction(){

    $opt = arRequest('opt');
    $sid = arRequest('sid');
    $this->assign(array('jsInsertBundles' => array('admin/productManager')));
    switch ($opt) {
      case 'delete':
         $delSet=SettingModel::model()->getDb()->where(array('s_sid'=>$sid))->delete();
         if($delSet){
            return $this->showJsonSuccess('操作成功');
          }else{
           return $this->showJsonError('操作失败');
          }
          break;
          case 'add':
          $this->display('@/Setting/sysSetAdd');
          break;

      case 'adding':
      $data['s_sname']=arRequest('s_sname');
      $data['s_content']=arRequest('s_content');
      $data['s_remark']=arRequest('s_remark');
      $addSet=SettingModel::model()->getDb()->insert($data,true);
      if($addSet){
        $this->redirectSuccess(array('sysSetting'));
      }else{
        $this->redirectError(array('sysSetting'));
      }
      break;
      case 'edit':
      $data['s_sid']=arGet('sid');
      $set = SettingModel::model()->getDb()->where($data)->queryRow();
      $this->assign(array('set'=>$set));
      $this->display('@/Setting/sysSetEdit');
      break;
      case 'editing':
      $s_sid=arRequest('s_sid');
      $data['s_sname']=arRequest('s_sname');
      $data['s_content']=arRequest('s_content');
      $data['s_remark']=arRequest('s_remark');
      $updateSet=SettingModel::model()->getDb()->where(array('s_sid'=>$s_sid))->update($data, true);
      if($updateSet){
        $this->redirectSuccess(array('sysSetting'));
      }else{
        $this->redirectError(array('sysSetting'));
      }
      break;
      default:
       $this->redirectError(array('sysSetting'), '非法操作');
      break;
        }
    }

  //客服列表
  public function cListServiceAction(){
    $count=ServiceModel::model()->getDb()->count();
    $page = new Page($count, 8);

    $cList = ServiceModel::model()->getDb()
      ->limit($page->limit())
      ->queryAll();

    $this->assign(array('cList'=>$cList));
    $this->assign(array('page'=>$page->show()));
    $this->assign(array('jsInsertBundles' => array('admin/cListManager')));
    $this->display('@/Setting/cListService');

  }

    // 客服的处理
  public function cListManagerAction()
  {
    $opt = arRequest('opt');
    switch($opt){
      case 'add':
      $this->display('@/Setting/cAddService');
      break;

      case 'adding':
      $data['c_sname']=arPost('c_sname');
      $data['c_snumber']=arPost('c_snumber');
      $data['c_stype']=arRequest('c_stype');
      $cListAdd=ServiceModel::model()->getDb()->insert($data,true);
      if($cListAdd){
         $this->redirectSuccess(array('cListService'));
      }else{
      $this->redirectError(array('cListService'));
      }
      break;
      case 'delete':
      $data=arRequest('cid');
      $cListDel=ServiceModel::model()->getDb()->where(array('c_sid'=>$data))->delete($data,true);
      if($cListDel){
        return $this->showJsonSuccess('操作成功');
      }else{
        return $this->showJsonError('操作失败');
      }
        break;
        case 'edit':
      $data=arRequest('cid');
      $cListEdit=ServiceModel::model()->getDb()->where(array('c_sid'=>$data))->queryRow();
      $this->assign(array('edit'=>$cListEdit));
      $this->display('@/Setting/cEditService');
        break;
        case 'editing':
      $c_sid=arPost('c_sid');
      $data['c_sname']=arPost('c_sname');
      $data['c_snumber']=arPost('c_snumber');
      $data['c_stype']=arPost('c_stype');

      $cListEdit=ServiceModel::model()->getDb()->where(array('c_sid'=>$c_sid))->update($data, true);
      if($cListEdit){
         $this->redirectSuccess(array('cListService'));
      }else{
        $this->redirectSuccess(array('cListService'));
      }
        break;
      default:
      $this->redirectError(array('cListService'), '非法操作');
      break;
    }

  }

    public function logSetAction()
    {
        $count=LogModel::model()->getDb()->count();
        $page = new Page($count, 8);
        $log = LogModel::model()->getDb()
            ->limit($page->limit())
            ->queryAll();

        $this->assign(array('log'=>$log));
        $this->assign(array('page'=>$page->show()));
        $this->display('@/Setting/logSet');

    }

    // 上传图片
    public function uploadPicAction()
    {
        $dstDir = arCfg('UPLOAD_DIR') . 'Link' . DS;
        // 上传图片名称
        $picName = arComp('ext.upload')->upload('uploadpic', $dstDir, 'img');
        if ($picName) :
            $file =  $dstDir . $picName;
            $gallery = array(
                'url' => arComp('url.route')->serverPath($file),
                'desc' => '',
            );
            $this->showJson($gallery);
        else :
            $this->showJsonError(arComp('ext.upload')->errorMsg);
        endif;

    }


}
