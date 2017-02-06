<?php
/**
*
*/
class AdController  extends ArController
{

    public function indexAction()
    {
        $count = AdModel::model()->getDb()->count();
        $page = new Page($count,15);
        $ad = AdModel::model()->getDb()->limit($page->limit())->queryAll();
        $this->assign(array('ads'=>$ad,'page'=>$page->show()));
        $this->display();
    }

    // 对图片的操作
    public function adManagerAction()
    {
         // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'layer,ajaxfileupload',
                    'this' => $this
                )
            )
        );
        if(arPost() && arRequest('opt') == 'add')
        {
            $data = arRequest();
            $data['ctime'] = time();
            $insert = AdModel::model()->getDb()->insert($data,true);
            if($insert){
                $this->redirectSuccess(array('index'),'操作成功');
            }else{
                $this->redirectError(array('manager'),'操作失败');
            }
        }elseif(!arPost() && arRequest('opt') == 'add'){
            $this->display('@/Ad/manager');
        }elseif(arPost() && arRequest('opt') == 'edit'){
            $aid = arRequest('aid');
            $data = arRequest();
            $update = AdModel::model()->getDb()->where(array('aid'=>$aid))->update($data,true);
            if($update){
                $this->redirectSuccess(array('index'),'操作成功');
            }else{
                $this->redirectError(array('manager'),'操作失败');
            }
        }elseif(!arPost() && arRequest('opt') == 'edit'){
            $aid = arRequest('aid');

            $adinfo = AdModel::model()->getDb()->where(array('aid'=>$aid))->queryRow();
            $adinfo = arModule('Ad')->statusToggle($adinfo,'galleryid');
            $this->assign(array('ad'=>$adinfo));
            $this->display('@/Ad/manager');
        }elseif(arRequest('opt') == 'del'){

        }else{
            $this->display();
        }
    }

    // 上传图片
    public function uploadRegPicAction()
    {
        $dstDir = arCfg('UPLOAD_DIR') . 'Reg' . DS;
        // echo $dstDir;
        // 上传图片名称
        $picName = arComp('ext.upload')->upload('uploadpic', $dstDir, 'img');
        if ($picName) :
            $file =  $dstDir . $picName;
            $cropFile =  $dstDir . 's_' . $picName;
            $mobile =  $dstDir . 'm_' . $picName;
            // 裁剪图片
            $cropTrue = GalleryModel::model()->crop($file, $cropFile, 600, 400);
            $mobileimg = GalleryModel::model()->crop($file, $mobile, 375, 156);
            if ($cropTrue && $mobileimg) :
                $gallery = array(
                    'url' => arComp('url.route')->serverPath($file),
                    'curl' => arComp('url.route')->serverPath($cropFile),
                    'murl' => arComp('url.route')->serverPath($mobile),
                    'desc' => '',
                );
                // 插入画廊
                $gid = GalleryModel::model()->getDb()->insert($gallery);
                if ($gid) :
                    $gallery['gid'] = $gid;
                    $this->showJson($gallery);
                endif;
            endif;
        else :
            $this->showJsonError(arComp('ext.upload')->errorMsg);

        endif;

    }
}
?>