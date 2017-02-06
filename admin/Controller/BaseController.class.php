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
   // 初始化
    public function init()
    {
        // uploadify bug
        if (arRequest('sess')) :
            session_write_close();
            session_id(arRequest('sess'));
            session_start();
        endif;
        arLm('admin.Ext');

        $action = arCfg('requestRoute.a_a');
        if (!in_array($action, array('login', 'loginOut','captcha','checkCode'))) :
            if (!arModule('Admin')->checkIfLogin()) :
                $this->redirect(array('Index/login'));
            endif;
        endif;
        // 分配管理员
        $this->assign(array('admin' => arComp('list.session')->get('admin')));
        // 保存session 到配置
        Ar::setConfig('admin', arComp('list.session')->get('admin'));

        // ajax-loading 加载的 css
        $this->assign(array('cssInsertBundles' => array('pages/ajax-loading')));
        // ajax-loading 加载loading 库
        $this->assign(array('jsInsertBundles' => array('ajax-loading')));

        // 调用layer插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'layer',
                    'this' => $this
                )
            )
        );

    }

}
