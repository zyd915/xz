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
class ExpertController extends BaseController
{
    // 列表
    public function listAction()
    {
        $total = ExpertModel::model()->getDb()->count();
        $page = new Page($total, 8);
        $experts = ExpertModel::model()
            ->getDb()
            ->limit($page->limit())
            ->order('sorder desc')
            ->queryAll();
        $this->assign(array('experts' => $experts, 'page' => $page->show()));
        $this->display();

    }

    // 添加专家
    public function addAction()
    {
        if ($data = arPost()) :
            $data['des'] = $data['content'];
            $addResult = ExpertModel::model()->getDb()->insert($data, 1);
            if ($addResult) :
                $this->redirectSuccess('');
            endif;
        endif;
        arSeg(array(
                'loader' => array(
                    'plugin' => 'bdeditor,layer,ajaxfileupload',
                    'this' => $this
                )
            )
        );
        $this->display();

    }

    // 编辑专家
    public function editAction()
    {
        if ($data = arPost()) :
            if ($eid = $data['eid']) :
                $data['des'] = $data['content'];
                $updateResult = ExpertModel::model()
                    ->getDb()
                    ->where(array('eid' => $eid))
                    ->update($data, 1);
                $this->redirectSuccess(array('', array('eid' => $eid)));
            endif;
        endif;

        if ($eid = arRequest('eid')) :
            $expert = ExpertModel::model()
                ->getDb()
                ->where(array('eid' => $eid))
                ->queryRow();
            $this->assign(array('expert' => $expert));
            arSeg(array(
                    'loader' => array(
                        'plugin' => 'bdeditor,layer,ajaxfileupload',
                        'this' => $this
                    )
                )
            );
            $this->display('add');
        endif;

    }

    // 删除专家
    public function delAction()
    {
        if ($eid = arRequest('eid')) :
            $headPic = ExpertModel::model()->getDb()
                ->where(array('eid' => $eid))
                ->queryColumn('headpic');
            // 删除图片
            $picDir = arComp('url.route')->pathToDir($headPic);
            unlink($picDir);
            $deleteResult = ExpertModel::model()->getDb()
                ->where(array('eid' => $eid))
                ->delete();
        endif;
        $this->redirectSuccess('list');

    }

    // 上传图片
    public function uploadPicAction()
    {
        $dstDir = arCfg('UPLOAD_DIR') . 'Zhuangjia' . DS;
        // 上传图片名称
        $picName = arComp('ext.upload')->upload('uploadpic', $dstDir, 'img');
        if ($picName) :
            $file =  $dstDir . $picName;
            // $cropFile =  $dstDir . 's_' . $picName;
            // 裁剪图片
            // $cropTrue = GalleryModel::model()->crop($file, $cropFile, 600, 400);
            $gallery = array(
                'url' => arComp('url.route')->serverPath($file),
                // 'curl' => arComp('url.route')->serverPath($cropFile),
                'desc' => '',
            );
            $this->showJson($gallery);
        else :
            $this->showJsonError(arComp('ext.upload')->errorMsg);
        endif;

    }

}
