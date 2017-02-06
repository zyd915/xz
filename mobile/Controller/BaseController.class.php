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
class BaseController extends ArController
{

 /**
     * just the example of get contents.
     *
     * @return void
     */

    // 初始化方法
    public function init()
    {
        // 加载admin model
        arLm('admin.Model');

        // 加载admin 扩展 主要是分页类 Page
        arLm('admin.Ext');

        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'layer,echarts',
                    'this' => $this
                )
            )
        );

        // 将存储的cookie转化为session
        arModule('User')->dUserCookieString();

        if (!empty($_SESSION['uid']) &&isset($_SESSION['uid'])) :
            $userSessInfo = UserModel::model()->getDb()->where(array('uid'=>$_SESSION['uid']))->queryRow();
            $userSessInfo = arModule('User')->getUserDetailInfo($userSessInfo);
            $this->assign(array('userSessInfo' => $userSessInfo));
        endif;

        // 获取分类
        $navs = NavModel::model()->getAllNavsByPid();
        $this->assign(array('navs' => $navs));

        // 获取友情链接
        $links = LinkModel::model()->getDb()->queryAll();
        $this->assign(array('links' => $links));

    }

}
