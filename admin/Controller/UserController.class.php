<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 后台管理类.
 */
class UserController extends BaseController
{
    // 初始化
    public function init()
    {
        // 父类加载
        parent::init();
        // 加载admin/user.js
        $this->assign(array('jsInsertBundles' => array('admin/user')));
         arSeg(array(
                'loader' => array(
                    'plugin' => 'layer,datetimepicker',
                    'this' => $this
                )
            )
        );

    }

    // 向用户发送消息
    public function msgAction()
    {

        if ($data = arPost()) :
            if ($data['uid'] == 'all') :
                $uidBundle = UserModel::model()->getDb()->select('uid')->queryAll('uid');
                $uids = array_keys($uidBundle);
            else :
                $uids = explode(',', $data['uid']);
            endif;
            $sResult = arModule('Msg')->send($uids, $data);
            if ($sResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;
        $this->display('@/User/msg');

    }

    // 默认显示界面
    public function indexAction()
    {
        $headSet = HeadSetModel::model()->getDb()->order('grade asc')->queryAll();
        $select = arModule('Jf')->getJfSelect($headSet);
        $condition = array();
        if(arRequest('title'))
        {
            $keywords = urldecode(arRequest('title'));
            $condition = "(email like '%$keywords%' or truename like '%$keywords%' or phone like '%$keywords%')";
        }
        if(is_numeric(arRequest('status')))
        {
            $status = arRequest('status');
             if($condition) {
                $condition .= " and status = $status";
            }else{
                $condition = " status = $status";
            }
        }
       if(is_numeric(arRequest('active'))){
            // $headid = array_keys(arRequest('active'));
        // var_dump(arRequest('active'));
            $active = arRequest('active');
            $activeThis = HeadSetModel::model()->getDb()->where(array('active' => $active))->queryRow();
            $activeNext = HeadSetModel::model()->getDb()->where(array('grade' => ($activeThis['grade']+1)))->queryRow();
            if($condition)
            {
                if($activeNext)
                {
                   $condition .= " and jf >= $activeThis[active] and jf < $activeNext[active]";
               }else{
                    $condition .= " and jf >= $activeThis[active]";
               }

            }else{
                if($activeNext)
                {
                   $condition = "jf >= $activeThis[active] and jf < $activeNext[active]";
               }else{
                    $condition = "jf >= $activeThis[active]";
               }

            }

       }
       if(is_numeric(arRequest('indentifystatus'))) {
        $indentifystatus = arRequest('indentifystatus');
        if($condition)
        {
            $condition .=" and indentifystatus = $indentifystatus";
        }else{
            $condition = "indentifystatus = $indentifystatus";
        }
       }
        if(is_numeric(arRequest('virtualstatus'))) {
        $virtualstatus = arRequest('virtualstatus');
        if($condition)
        {
            $condition .=" and virtualstatus = $virtualstatus";
        }else{
            $condition = "virtualstatus = $virtualstatus";
        }
       }

       if(is_numeric(arRequest('type')))
       {
            $type = arRequest('type');
            if($condition)
            {
                $condition .= " and type = $type";
            }else{
                $condition = "type = $type";
            }

       }
        if(arRequest('sejf')){
            $jf = explode('-', arRequest('sejf'));
            if($condition) {
                $condition .= " and jf >= $jf[0] and jf < $jf[1]";
            }else{
                $condition = "jf >= $jf[0] and jf < $jf[1]";
            }

        }
        if(arRequest('sehjf')){
            $jf = explode('-', arRequest('sehjf'));
             if($condition) {
                $condition .= " and jf >= $jf[0] and jf < $jf[1]";
            }else{
                $condition = "jf >= $jf[0] and jf < $jf[1]";
            }

        }

        if(arRequest('searchTime'))
        {
            $etime = strtotime(urldecode(arRequest('etime')));
            $stime = strtotime(urldecode(arRequest('stime')));
            $searchTime = arRequest('searchTime');

            if($condition) {
                $condition .= " and $searchTime >= $etime and $searchTime <  $stime";
            }else{
                $condition = "$searchTime >= $etime and $searchTime <  $stime";
            }
        }

        $users = UserModel::model()->getDb()->queryAll();
        $uUid = array();
        foreach ($users as $users) {
            $uUid[] = $users['uid'];
        }
        $faccount = FAccountModel::model()->getDb()->select('uid')->queryAll();
        $fUid = array();
        foreach ($faccount as $faccount) {
            $fUid[] = $faccount['uid'];
        }
        if(arRequest('userCheck')) :
            $userCheck = arRequest('userCheck');
            if($userCheck == 1) :
                $getmeuid = array_intersect($uUid,$fUid);
                $getmeuid = implode(',', $getmeuid);
                if($condition) :
                    $condition .= " and uid in ($getmeuid)";
                    else :
                     $condition = "uid in ($getmeuid)";
                endif;
            elseif($userCheck == 2) :
                $getmeuid = array_diff($uUid,$fUid);
                $getmeuid = implode(',', $getmeuid);
                if($condition) :
                     $condition .= " and uid in ($getmeuid)";
                    else :
                     $condition = "uid in ($getmeuid)";
                endif;
             else :
            endif;
        endif;

        $count = UserModel::model()->getDb()->where($condition)->count();
        $page = new Page($count,20);
        $users = UserModel::model()->getDb()->where($condition)->limit($page->limit())->order('uid desc')->queryAll();
        $users = arModule('User')->getHeadSetInfo($users);
        $users = arModule('FAccount')->getFaccount($users);

        $users = UserKaoshengModel::model()->getKaoshengDetail($users);

        $this->assign(array('users' => $users,'select' => $select, 'page' => $page->show()));

        Ar::setConfig('DEBUG_SHOW_ERROR', false);

        // 覆盖父类模板显示
        $this->display('@/User/index');


    }

    // 删除用户信息
    public function deleteUserAction()
    {
        if($uid = arRequest('uid')) :
            $delete = UserModel::model()->getDb()
                ->where(array('uid' => $uid))
                ->delete();
            if ($delete):
                $this->showJsonSuccess();
            else:
                $this->showJsonError();
            endif;
        else:
            $this->showJsonError();
        endif;
    }

    // 删除用户测试数据
    public function deleteSerialAction()
    {
        if ($uid = arRequest('uid')) :
            $deleteResult = UserSerialsModel::model()->getDb()
                ->where(array('uid' => $uid))
                ->delete();
            if ($deleteResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError();
        endif;

    }

    // 激活禁止用户
    public function statusToggleAction()
    {
        // 获取参数id
        $id = arRequest('id');
        // 调用中间件
        $result = arModule('User')->statusToggle($id);

        return $this->showJson((string)$result);

    }

     //获取名字差不多的数据
    public function getLikeNameAction()
    {
        $condition = array();
        $uname = arRequest('uname');
        if($uname) {
            $condition['uname like'] = '%'.$uname.'%';
            $condition['status'] = UserModel::STATUS_APPROVED;
        }
        $getName = UserModel::model()->getDb()->where($condition)->queryAll();
        $this->showJson($getName);

    }

    // 给用户发送信息
    public function sendNewsAction()
    {
       // $uid = arRequest('uid');
       // $showUid = explode(',', $uid);
       // $this->display('@/User/msg');
    }

    // 查看留言的信息
    public function readMessAction()
    {
        $condition = array();
         if(arRequest('title'))
        {
            $keywords = arRequest('title');
            //  $condition = implode('or', array(
            // " ",
            // "  ",

            // ));
            $condition = "(mcontent like '%$keywords%' or mheader like '%$keywords%')";


        }
        if(is_numeric(arRequest('visitor')))
        {
            if(arRequest('visitor') == '2')
            {
                // $condition['uid'] = 0;
                if ($condition) :
                    $condition .= ' and uid = 0 ';
                else :
                    $condition = ' uid = 0 ';
                endif;
            }else{
                if ($condition) :
                    $condition .= ' and uid != 0';
                    else :
                    $condition = ' uid != 0';
                endif;
                // $condition['uid !='] = 0;
            }
        }
         if(arRequest('searchTime'))
        {
            $etime = strtotime(urldecode(arRequest('etime')));
            $stime = strtotime(urldecode(arRequest('stime')));
            $searchTime = arRequest('searchTime');
            if($condition) :
                $condition .= " and $searchTime < $etime and $searchTime >= $stime";
                else :
                $condition = "$searchTime < $etime and $searchTime >= $stime";
            endif;
        }
        if(is_numeric(arRequest('status')))
        {
            $status = arRequest('status');
            if($condition) :
                $condition .=" and status = $status";
            else :
                $condition = "status = $status";
            endif;
        }
       $getMess = MessageModel::model()->getDb()->where($condition)->count();
       $page = new Page($getMess,20);
        $getMess = MessageModel::model()->getDb()->where($condition)->limit($page->limit())->order('mid desc')->queryAll();
        $getMess = arModule('User')->getUserInfoLeaveMes($getMess);
        $this->assign(array('getOrder' => $getMess,'page'=>$page->show()));
        $this->display('@/User/readMess');
    }

    // 改变留言的状态
    public function changeNewsAction()
    {
        $mid = arRequest('mid');
        $getStatus = MessageModel::model()->getDb()->where(array('mid' => $mid))->queryColumn('status');
        if($getStatus == MessageModel::STATUS_APPROVED)
        {
            $show = MessageModel::model()->getDb()
            ->where(array('mid' => $mid))
            ->update(array('status' => MessageModel::STATUS_FORBIDDEN));


        }else{
              $show = MessageModel::model()->getDb()->where(array('mid' => $mid))->update(array('status' => MessageModel::STATUS_APPROVED));

        }
          $getStatus = MessageModel::model()->getDb()->where(array('mid' => $mid))->queryRow();
          return $this->showJson($getStatus);
    }

    // 获取用户的详细信息
    public function userinfoAction()
    {
        $uid =  arRequest('uid');
        $this->setLayOutfile('');
        $users = UserModel::model()->getDb()->where(array('uid' => $uid))->queryAll();
        $users = arModule('User')->getHeadSetInfo($users);
        $users = arModule('FAccount')->getFaccount($users);

        $users = arModule('User')->getImgInfo($users,'logogid');
        $zphotoz = arModule('User')->getCardPto($users[0],'zphotoz');
        $zselfb = arModule('User')->getCardPto($users[0],'zselfb');
        $zphotof = arModule('User')->getCardPto($users[0],'zphotof');
        $zbankz = arModule('User')->getCardPto($users[0],'zbankz');
        $zbankf = arModule('User')->getCardPto($users[0],'zbankf');
        $this->assign(array(

            'zphotoz' => $zphotoz,
            'zselfb' => $zselfb,
            'zphotof' => $zphotof,
            'zbankz' => $zbankz,
            'zbankf' => $zbankf,
            ''
            ));
       $this->assign(array('order' => $users[0]));

       $this->display('@/User/userinfo');

    }

    // 改变认证的状态
    public function indentifyStatusAction()
    {
        if(!arRequest('opt')) :
        $uid = arRequest('uid');
        // $indentifystatus = UserModel::model()->getDb()->where(array('uid' => $uid))->queryColumn('indentifystatus');
            $indentifystatus =  UserModel::model()->getDb()
                       ->where(array('uid' => $uid))
                       ->update(array('indentifystatus'=>UserModel::STATUS_INDENTIFY_YES,'type'=>UserModel::TYPE_REAL));
            if($indentifystatus) {
                return $this->showJsonSuccess();
            }else{
                return $this->showJsonError();
            }
      else :
           $uid = arRequest('uid');
            $uid = explode(',', $uid);
            $indentifystatus = UserModel::model()->getDb()->where(array('uid' => $uid))->queryAll();
            foreach ($indentifystatus as $key => $indentify) {
               $indentifystatus =  UserModel::model()->getDb()
                       ->where(array('uid' => $indentify['uid']))
                       ->update(array('indentifystatus'=>UserModel::STATUS_INDENTIFY_YES,'type'=>UserModel::TYPE_REAL));
                }
            $this->redirectSuccess(array('index'),'修改成功');
            // var_dump($uid);
       endif;
    }


    // 获取未读消息（用于读取管理员发送的消息）
    public function userMsgAction()
    {
        $msg = MsgModel::model()->getDb()->where(array('type !=' => MsgModel::TYPE_ADMIN, 'readed' => MsgModel::READED_NO))->order('stime desc')->limit(3)->queryAll();
        if ($msg) :
            foreach ($msg as $key => $msgDetail) :
                $msg[$key]['content'] = mb_substr($msgDetail['content'],'0','21','utf-8');
                $msg[$key]['stime'] = date("Y/m/d",$msgDetail['stime']);
            endforeach;
        endif;
        return $this->showJson($msg);

    }

    // 标记已读
    public function signReadedAction()
    {
        $uid = arComp('list.session')->get('uid');
        $mid = arPost('mid');
        MsgModel::model()->getDb()->where(array('mid' => $mid))->update(array('readed' => MsgModel::READED_YES));
        $count = MsgModel::model()->getDb()->where(array('type !=' => MsgModel::TYPE_ADMIN, 'readed' => MsgModel::READED_NO))->count();
        return $this->showJson($count);

    }

    // 账户金
    public function accountAction()
    {
        $cond = array();
        $user = AccountModel::model()->getDb()->where($cond)->queryAll();
        foreach ($user as $key => $users) {
            $user[$key]['userinfo'] = UserModel::model()->getDb()->where(array('uid' => $users['uid']))->queryRow();
        }
        $this->assign(array('users' => $user));
        $this->display('@/User/account');
    }

    // 更改用户金状态
    public function accountStatusAction()
    {
        $cond = arPost();
        if($cond['status'] == 1) {
            // 成功 更改状态 添加etime
               $conds['status'] = $cond['status'];
               $conds['etime'] = time();
               $up = AccountModel::model()->getDb()->where(array('a_uid' => $cond['auid']))->update($conds,true);
               if($up) {
                echo true;
               }else{
                echo false;
               }
        }else{
               $conds['status'] = $cond['status'];
               $conds['etime'] = time();
               $up = AccountModel::model()->getDb()->where(array('a_uid' => $cond['auid']))->update($conds,true);
               $nowJf = UserModel::model()->getDb()->where(array('uid'=>$cond['uid']))->queryColumn('jf');
               $chageJf['jf'] = $nowJf + $cond['money']*100;
               $jfs = UserModel::model()->getDb()->where(array(array('uid' => $cond['uid'])))->update($chageJf,true);
               if($up && $jfs) {
                echo true;
               }else{
                echo false;
               }
        }
    }

    //用户详细信息
    public function userMessageAction()
    {
        $this->setLayOutfile('');
        if ($uid = arRequest('uid')) :
            $user = UserModel::model()
                ->getDb()
                ->where(array('uid' => $uid))
                ->queryRow();
            $user = UserKaoshengModel::model()->getKaoshengDetail($user);
            $this->assign(array('user' => $user));
            $this->display();
        endif;

    }

    // 更改用户属性
    public function updatetypeAction() 
    {
        // if (arRequest('type') != UserModel::TYPE_V) :
        if (true) :
            // $res = UserModel::model()->getDb()->where(array('type != ' => UserModel::TYPE_V, 'uid' => arRequest('uid')))->update(array('type' => arRequest('type')));
            $res = UserModel::model()->getDb()->where(array('uid' => arRequest('uid')))->update(array('type' => arRequest('type')));
            if ($res) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError('变更失败');
            endif;
        else :
            $this->showJsonError('不能变更为VIP用户');
        endif;

    }

}
