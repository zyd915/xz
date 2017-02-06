<?php
// 文章新闻管理类
class VcardController extends BaseController
{
    // 列表
    public function listAction()
    {
        $condition = array();

        if ($title = urldecode(arRequest('title'))) :
            $condition['cno like '] = '%' . $title . '%';
        endif;
        if (is_numeric(arRequest('status'))) :
            $condition['status'] = arRequest('status');
        endif;
        if (is_numeric(arRequest('vtype'))) :
            $condition['vtype'] = arRequest('vtype');
        endif;

        $total = UserVcardModel::model()->getDb()->where($condition)->count();
        $page = new Page($total, 30);

        $vcards = UserVcardModel::model()
            ->getDb()
            ->where($condition)
            ->limit($page->limit())
            ->order('cid desc')
            ->queryAll();

        // 详情
        $vcards = UserVcardModel::model()->getDetail($vcards);
        $this->assign(array('vcards' => $vcards, 'page' => $page->show()));
        $this->display();

    }

    // 添加专家
    public function vcardAddAction()
    {
        if ($data = arPost()) :
            if ($nums = $data['nums']) :
                arLm('main.Module');
                $vtype = $data['vtype'];
                // 生成卡
                arModule('Vip')->generateCard($nums, '后台添加', '', $vtype);
            endif;
            $this->redirectSuccess('list');
        endif;
        $this->display('add');

    }

    // 编辑专家
    public function vcardEditAction()
    {
        if ($data = arPost()) :
            if ($sid = $data['sid']) :
                $updateResult = UserVcardModel::model()
                    ->getDb()
                    ->where(array('sid' => $sid))
                    ->update($data, 1);
                $this->redirectSuccess(array('', array('sid' => $sid)));
            endif;
        endif;

        if ($sid = arRequest('sid')) :
            $vcard = UserVcardModel::model()
                ->getDb()
                ->where(array('sid' => $sid))
                ->queryRow();
            $this->assign(array('vcard' => $vcard));
            $this->display('add');
        endif;

    }

    // 删除专家
    public function vcardsDelAction()
    {
        if ($sid = arRequest('sid')) :
            $deleteResult = UserVcardModel::model()->getDb()
                ->where(array('sid' => $sid))
                ->delete();
        endif;
        $this->redirectSuccess('list');

    }

}
