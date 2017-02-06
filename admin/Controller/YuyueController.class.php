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
class YuyueController extends BaseController
{
    // 列表
    public function listAction()
    {
        $total = YuyueModel::model()->getDb()->count();
        $page = new Page($total, 8);
        $yuyues = YuyueModel::model()
            ->getDb()
            ->limit($page->limit())
            ->order('yid desc')
            ->queryAll();
        $yuyues = arModule('Yuyue')->yyDetail($yuyues);

        $this->assign(array('yuyues' => $yuyues, 'page' => $page->show()));
        
        $this->display();

    }

    // 添加专家
    public function addAction()
    {
        if ($data = arPost()) :
            $data['des'] = $data['content'];
            $addResult = YuyueModel::model()->getDb()->insert($data, 1);
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
                $updateResult = YuyueModel::model()
                    ->getDb()
                    ->where(array('eid' => $eid))
                    ->update($data, 1);
                $this->redirectSuccess(array('', array('eid' => $eid)));
            endif;
        endif;

        if ($eid = arRequest('eid')) :
            $yuyue = YuyueModel::model()
                ->getDb()
                ->where(array('eid' => $eid))
                ->queryRow();
            $this->assign(array('yuyue' => $yuyue));
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
            $headPic = YuyueModel::model()->getDb()
                ->where(array('eid' => $eid))
                ->queryColumn('headpic');
            // 删除图片
            $picDir = arComp('url.route')->pathToDir($headPic);
            unlink($picDir);
            $deleteResult = YuyueModel::model()->getDb()
                ->where(array('eid' => $eid))
                ->delete();
        endif;
        $this->redirectSuccess('list');

    }


}
