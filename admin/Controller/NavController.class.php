<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */
class NavController extends BaseController
{
    // 菜单列表
    public function indexAction()
    {
        $menus = NavModel::model()->getAllMenuByPid(0);
        $this->assign(array('menus' => $menus));
        $this->display();

    }

    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function contentMenuAction()
    {
        $this->display();

    }

    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function contentAction()
    {
        $this->display();

    }

    // 菜单列表
    public function addAction()
    {
        Ar::setConfig('DEBUG_SHOW_ERROR', false);
        if ($data = arPost()) :
            if (empty($data['id'])) :
                $data['level'] = NavModel::LV_1;
                $opResult = NavModel::model()->getDb()->insert($data, 1);
            else :
                if ($pid = arRequest('pid')) :
                    if ($pid == 1) :
                        $data['level'] = NavModel::LV_1;
                    else :
                        $level = NavModel::model()
                            ->getDb()
                            ->where(array('id' => $pid))
                            ->queryColumn('level');
                        $data['level'] = $level + 1;
                    endif;
                endif;
                $condition = array('id' => $data['id']);
                $opResult = NavModel::model()->getDb()->where($condition)->update($data, 1);
            endif;

            if ($opResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError('', arComp('list.log')->get('NavModel'));
            endif;
        endif;

        $menus = NavModel::model()->getAllMenuByPid(0);

        if ($id = arRequest('id')) :
            $currentMenu = NavModel::model()->getDb()->where(array('id' => $id))->queryRow();
            $this->assign(array('currentMenu' => $currentMenu));
        endif;

        $this->assign(array('menus' => $menus));

        $this->display();

    }

    // 删除菜单
    public function deleteAction()
    {
        if ($id = arRequest('id')) :
            if (NavModel::model()->getDb()->where(array('pid' => $id))->count() > 0) :
                return $this->showJsonError('sub menu exists');
            endif;
            $res = NavModel::model()->getDb()->where(array('id' => $id))->delete();
            if ($res) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError();
        endif;

    }

    // 菜单列表
    public function editAction()
    {
        if (!$id = arRequest('id')) :
            $this->redirectError(array('index'), '菜单id不能为空');
        endif;
        $this->display();

    }

}
