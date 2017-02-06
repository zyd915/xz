<?php
// 文章新闻管理类
class SerialsController extends BaseController
{
    // 列表
    public function listAction()
    {
        $total = SerialsModel::model()->getDb()->count();
        $page = new Page($total, 20);
        $serials = SerialsModel::model()
            ->getDb()
            ->limit($page->limit())
            ->queryAll();
        $this->assign(array('serials' => $serials, 'page' => $page->show()));
        $this->display();

    }

    // 添加专家
    public function serialAddAction()
    {
        if ($data = arPost()) :
            $addResult = SerialsModel::model()->getDb()->insert($data, 1);
            if ($addResult) :
                $this->redirectSuccess('list');
            endif;
        endif;
        $this->display('add');

    }

    // 编辑专家
    public function serialEditAction()
    {
        if ($data = arPost()) :
            if ($sid = $data['sid']) :
                $updateResult = SerialsModel::model()
                    ->getDb()
                    ->where(array('sid' => $sid))
                    ->update($data, 1);
                $this->redirectSuccess(array('', array('sid' => $sid)));
            endif;
        endif;

        if ($sid = arRequest('sid')) :
            $serial = SerialsModel::model()
                ->getDb()
                ->where(array('sid' => $sid))
                ->queryRow();
            $this->assign(array('serial' => $serial));
            $this->display('add');
        endif;

    }

    // 删除专家
    public function serialsDelAction()
    {
        if ($sid = arRequest('sid')) :
            $deleteResult = SerialsModel::model()->getDb()
                ->where(array('sid' => $sid))
                ->delete();
        endif;
        $this->redirectSuccess('list');

    }

}
