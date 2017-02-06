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
class IndexController extends BaseController
{
    // 登陆
    public function loginAction()
    {

        if (arPost()) :
           $adminInfo = arPost();
            if(strtoupper(arRequest('ckey')) == strtoupper(arComp('list.session')->get('ckey'))) :

            	$adminCondition = array(
                        'name' => $adminInfo['username'],
                        'pwd' => AdminModel::gPwd($adminInfo['password']),
                    );

    			$admin = AdminModel::model()->getDb()->where($adminCondition)->queryRow();
                if($admin) :
        			 arComp('list.session')->set('admin', $admin);
                     $this->redirectSuccess(array('index'), '登陆成功!');

                    else :

                    $this->redirectError(array('login','登录失败'));
                endif;
            else :
                   $this->redirectError('', '验证码错误');

            endif;
        else :
        	$this->setLayoutFile('');
            $this->display();
        endif;

    }

    // 首页
    public function indexAction(){
        $usercount = UserModel::model()->getDb()->count();
        $articlecount = ArticleModel::model()->getDb()->count();
        $this->assign(array(
            'userCount' => $usercount,
            'articleCount' => $articlecount,
            ));
        $usercount = UserModel::model()->getDb()->count();
        $this->display();
    }

    // 验证码
    public function captchaAction()
    {
        require arCfg('EXTENSION_DIR') . 'captcha' . DS . 'captcha.php';
        new CaptchaSecurityImages(169, 56, 5, 'ckey');

    }
    // 注销
    public function loginOutAction()
    {
        // 清空 session
        arComp('list.session')->flush();
        // 跳转
        $this->redirectSuccess(array('Index/login'), '退出成功');

    }
}