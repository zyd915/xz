<?php
class CategroyController extends ArController {

    public function init()
    {
         arSeg(array(
                'loader' => array(
                    'plugin' => 'layer',
                    'this' => $this
                )
            )

            );
    }
   // 默认显示界面
    public function indexAction()
    {

        if ($cate = arPost()) :
            $opt = $cate['opt'];
            if ($opt == 'add') :
                if (!$pid = $cate['pid']) :
                    $cate['rank'] = 1;
                else :
                    // 父级rank+1
                    $cate['rank'] = CategoryModel::model()->getDb()->where(array('cid' => $pid))->queryColumn('rank') + 1;
                endif;
                $cateInsertId = CategoryModel::model()->getDb()->insert($cate, 1);
                if ($cateInsertId) :
                    return $this->showJsonSuccess('添加新分类成功');
                else :
                    return $this->showJsonError('添加新分类失败');
                endif;
            elseif ($opt == 'edit') :
                $updateTrue = CategoryModel::model()->getDb()->where(array('cid' => $cate['cid']))->update($cate, 1);
                if ($updateTrue) :
                    return $this->showJsonSuccess('更新成功');
                else :
                    return $this->showJsonError('更新失败');
                endif;
            elseif ($opt == 'delete') :
                $cid = $cate['cid'];
                if (CategoryModel::model()->getDb()->where(array('pid' => $cid))->count() > 0) :
                    return $this->showJsonError('删除失败, 父类下边还有子类');
                endif;
                $deleteTrue = CategoryModel::model()->getDb()->where(array('cid' => $cid))->delete();
                if ($deleteTrue) :
                    return $this->showJsonSuccess('删除成功');
                else :
                    return $this->showJsonError('删除失败');
                endif;
            endif;
        elseif ($cid = arGet('cid')) :
            $cateInfo = CategoryModel::model()->getDb()->where(array('cid' => $cid))->queryRow();

            return $this->showJson($cateInfo);
        endif;

        $cates = CategoryModel::model()->getManageCategoriesByPid(0);
        // 分配分类
        $this->assign(array('cates' => $cates));
        // 弹出框js
        $this->assign(array('jsInsertBundles' => array('admin/cate')));
        $this->display('@/Category/index');

    }
}
?>