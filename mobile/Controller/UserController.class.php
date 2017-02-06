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
class UserController extends BaseController
{
    public $user;
    /**
     * just the example of get contents.
     *
     * @return void
     */
    // 初始化方法
    public function init()
    {
        parent::init();
        // arModule('Vip')->generateCard(30);
        if (!in_array(arCfg('requestRoute.a_a'), array('signUp', 'loginMang', 'login', 'logout')) && empty($_SESSION['uid']) && !arModule('User')->dUserCookieString()) :
            $this->redirect('User/login','你还没有登录');
        else :
            $this->user = UserModel::model()
                ->getDb()
                ->where(array('uid' => arComp('list.session')->get('uid')))
                ->queryRow();
            $this->user = arModule('User')->getUserDetailInfo($this->user);
            $this->assign(array('userinfo' => $this->user));
        endif;
        // 有卡却状态不正确修复数据
        if ($this->user['vip'] && $this->user['type'] == UserModel::TYPE_N) :
            UserModel::model()->getDb()
                ->where(array('uid' => $this->user['uid']))
                ->update(array('type' => UserModel::TYPE_V));
        endif;

        // $this->setLayOutfile('user');

    }

    // 调试
    public function textAction()
    {
        var_dump(arCfg('EXTENSION_DIR') . 'captcha' . DS . 'captcha.php');
    }

    // 测试
    public function testAction()
    {
        // $uid = $_SESSION['uid'];
        // echo $uid;
    	echo arCfg('DIR.SEG') . 'Sys/js.seg';
    }

    // 专业测试跳转
    public function testRedirectAction()
    {
        if (!$this->user['vip']) :
            $this->redirectError('Index/vipCard', '该功能仅限VIP用户使用,请先升级VIP');
        else :
            if ($this->user['serial']) :
                $this->redirectError('zyTestResult', '你已经参加过测试了');
            else :
                if (!$email = arRequest('email')) :
                    $this->redirectError('index', '邮箱未设置');
                endif;
                if (empty($this->user['kaosheng']['name'])) :
                    $this->redirectError('userSetting', '考生姓名必填,请先完善基本资料');
                endif;
                if (arRequest('kl') == 'lk') :
                    $liangbiao = SerialsModel::$LIANGBIAO_MAPS[SerialsModel::LB_LK];
                elseif (arRequest('kl') == 'wk') :
                    $liangbiao = SerialsModel::$LIANGBIAO_MAPS[SerialsModel::LB_WK];
                else :
                    $liangbiao = SerialsModel::$LIANGBIAO_MAPS[SerialsModel::LB_WLFK];
                endif;
                // 先找多用code
                $serials = SerialsModel::model()->getDb()
                    ->where(array('stype' => SerialsModel::TYPE_MUT, 'times > ' => 0, 'status' => SerialsModel::STATUS_USE_NO))
                    ->queryRow();
                if ($serials) :
                    $checkcode = $serials['num'];
                    $times = $serials['times'];
                    $updateData = array('times' => $times - 1);
                    if ($times == 1) :
                        $updateData['status'] = SerialsModel::STATUS_USE_YES;
                    endif;
                    SerialsModel::model()->getDb()
                        ->where(array('num' => $checkcode))
                        ->update($updateData);
                else :
                    // 先找多用code
                    $serials = SerialsModel::model()->getDb()
                        ->where(array('stype' => SerialsModel::TYPE_ONE, 'status' => SerialsModel::STATUS_USE_NO))
                        ->queryRow();
                    if (!$serials) :
                        $this->redirectError('Index/index', '序列号未初始化，请联系客服');
                    endif;
                    $checkcode = $serials['num'];
                    $updateData = array('status' => SerialsModel::STATUS_USE_YES);
                    SerialsModel::model()->getDb()
                        ->where(array('num' => $checkcode))
                        ->update($updateData);
                endif;
                $serial = array(
                    'uid' => $this->user['uid'],
                    'num' => $checkcode,
                    'usetime' => time(),
                    'testemail' => $email,
                    'testname' => $this->user['kaosheng']['name'],
                    'lb' => $liangbiao,
                );

                UserSerialsModel::model()->getDb()->insert($serial);
                $url = 'http://www.apesk.com/h/go_zy_dingzhi.asp?checkcode='.$checkcode.'&hruserid=13908178607&l=' . $liangbiao . '&test_name=' . urlencode(iconv('utf-8', 'gbk', $this->user['kaosheng']['name'])) . '&test_email=' . $email;
                $this->assign(array('url' => $url));

                $this->display();
            endif;
        endif;

    }
    // 登陆
    public function loginMangAction()
    {
        Ar::setConfig('DEBUG_LOG', true);
        // echo UserModel::model()->gPwd
        $user = arPost();
        if ($user) :
            // 是否卡号登陆
            if (arModule('Vip')->isVipNo($user['name'])) :
                $vipcondition = array(
                    'cno' => $user['name'],
                    'pwd' => $user['pwd'],
                );
                $vip = UserVcardModel::model()->getDb()->where($vipcondition)->queryRow();
                if ($vip) :
                    if (!$uid = $vip['uid']) :
                        // 初始化一个用户
                        $newuser = array();
                        // 未注册系统用户 初始化默认用户
                        $newuser['rtime'] = time();
                        $newuser['ltime'] = time();
                        // 插入数据库 insert
                        if ($uid = UserModel::model()->getDb()->insert($newuser)) :
                            // 写入考生信息
                            $kaoSheng = array(
                                // 用户id
                                'uid'           => $uid,
                                // 考生姓名
                                // 'name'          => '未知考生',
                                // 四川
                                'areapid'       => '22',
                                // 成都
                                'areacid'       => '364',
                            );
                            // 记录考生信息表
                            UserKaoshengModel::model()->getDb()->insert($kaoSheng);
                            // 激活VIP卡操作
                            if ($vip['vtype'] == UserVcardModel::VTYPE_VIP) :
                                arModule('Vip')->active($vip['cno'], $uid);
                            else :
                                UserVcardModel::model()
                                    ->getDb()
                                    ->where(array('cno' => $vip['cno']))
                                    ->update(array('uid' => $uid));
                            endif;
                        endif;
                    endif;
                    // 保存session
                    arComp('list.session')->set('uid', $uid);
                    // 保存cookie
                    if ($user['rem'] === 'true') :
                        arModule('User')->gUserCookieString($uid);
                    endif;
                    $this->showJsonSuccess('登录成功');
                else :
                    $this->showJsonError('卡号或密码错误');
                endif;
            else :
                // 手机验证登陆
                $con = array(
                    'phone' => $user['name'],
                );
                $getCount = UserModel::model()->getDb()->where($con)->count();
                if ($getCount) :
                    $con['pwd'] = UserModel::gPwd($user['pwd']);
                    $login = UserModel::model()->getDb()->where($con)->queryRow();
                    if ($login) :
                        // 保存session
                        arComp('list.session')->set('uid', $login['uid']);
                        // 保存cookie
                        if ($user['rem'] === 'true') :
                            arModule('User')->gUserCookieString($login['uid']);
                        endif;
                        $this->showJsonSuccess('登录成功');
                    else :
                        $this->showJsonError('密码不正确');
                    endif;
                else :
                    $this->showJsonError('没有该用户注册信息');
                endif;
            endif;
        else :
            $this->showJsonError();
        endif;

    }

    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->display();
    }

    /* 用户信息 */
    public function userdetailAction()
    {
        $uid = $_SESSION['uid'];
        $user = UserKaoshengModel::model()
            ->getDb()
            ->where(array('uid' => $uid))
            ->queryRow();
        // var_dump($user);
        // exit;
        // if ($xgname = arRequest('xgname')):
        // 	 UserKaoshengModel::model()
        //     ->getDb()
        //     ->where(array('uid' => $uid))
        //     ->update(array('name' => $xgname));
        // endif;
        $this->assign(array('user' => $user));
        if (arRequest('isajax') == 1) :
        	$xgname = arRequest('name');
        	$update = UserKaoshengModel::model()
            	->getDb()
            	->where(array('uid' => $uid))
            	->update(array('name' => $xgname));
        	$this->showJsonSuccess($xgname);
        elseif(arRequest('isajax') == 2):
        	$xgschool = arRequest('name');
        	$update = UserKaoshengModel::model()
            	->getDb()
            	->where(array('uid' => $uid))
            	->update(array('schoolname' => $xgschool));
        	$this->showJsonSuccess($xgschool);
        elseif(arRequest('isajax') == 3):
        	$xgscorez1 = arRequest('name');
        	$update = UserKaoshengModel::model()
            	->getDb()
            	->where(array('uid' => $uid))
            	->update(array('scorez1' => $xgscorez1));
        	$this->showJsonSuccess($xgscorez1);
        elseif(arRequest('isajax') == 4):
        	$xgscorez2 = arRequest('name');
        	$update = UserKaoshengModel::model()
            	->getDb()
            	->where(array('uid' => $uid))
            	->update(array('scorez2' => $xgscorez2));
        	$this->showJsonSuccess($xgscorez2);
        elseif(arRequest('isajax') == 5):
        	$xgscorez3 = arRequest('name');
        	$update = UserKaoshengModel::model()
            	->getDb()
            	->where(array('uid' => $uid))
            	->update(array('scorez3' => $xgscorez3));
        	$this->showJsonSuccess($xgscorez3);
        elseif(arRequest('isajax') == 6):
        	$xgscoregk = arRequest('name');
        	$update = UserKaoshengModel::model()
            	->getDb()
            	->where(array('uid' => $uid))
            	->update(array('scoregk' => $xgscoregk));
        	$this->showJsonSuccess($xgscoregk);
        elseif(arRequest('isajax') == 8):
            $xglx = arRequest('name');
            $update = UserKaoshengModel::model()
                ->getDb()
                ->where(array('uid' => $uid))
                ->update(array('lx' => $xglx));
            if ($xglx == 0):
                $lx = '理科';
            else:
                $lx = '文科';
            endif;
            $this->showJsonSuccess($lx);
        elseif(arRequest('isajax') == 9):
            $name = arRequest('name');
            $condition = array();
            $condition['proname'] = $name[0];
            $condition['cityname'] = $name[1];
            $condition['areaname'] = $name[2];

            $update = UserKaoshengModel::model()
                ->getDb()
                ->where(array('uid' => $uid))
                ->update($condition);
            $this->showJsonSuccess($condition);
        elseif(arRequest('isajax') == 7):
            $xggender = arRequest('name');
            $update = UserKaoshengModel::model()
                ->getDb()
                ->where(array('uid' => $uid))
                ->update(array('gender' => $xggender));
            if ($xggender == 0):
                $gender = '男';
            else:
                $gender = '女';
            endif;
            $this->showJsonSuccess($gender);
        else:
        	$this->display();
        endif;

    }

    public function loginAction()
    {
        $this->display();

    }

    // 注册
    public function signUpAction()
    {
    // 提交注册数据
    if ($data = arPost()) :
        $user = array();
        $user['phone'] = $data['phone'];

        if (UserModel::model()->getDb()->where(array('phone' => $user['phone']))->count() > 0) :
            $this->redirectError('', '手机号已注册');
        endif;

        // 验证码
        if (!$data['roe-btn2'] || $data['roe-btn2'] != $_SESSION['call_yzm']) :
            $this->redirectError('', '手机验证码错误');
        else :
            $_SESSION['call_yzm'] = null;
        endif;

        $user['truename'] = $data['truename'];
        $user['pwd'] = UserModel::gPwd($data['password']);
        $user['rtime'] = time();
        $user['ltime'] = time();
        // 插入数据库 insert
        if ($uid = UserModel::model()->getDb()->insert($user)) :
            // 写入考生信息
            $kaoSheng = array(
                // 用户id
                'uid'           => $uid,
                // 考生姓名
                'name'          => $data['truename'],
                // 省id
                'areapid'       => $data['province'],
                'areacid'       => $data['city'],
                'areaaid'       => $data['county'],
                'schoolid'      => $data['seniorhigh'],
                'proname'       => DataRegionModel::model()->getDb()
                    ->where(array('region_id' => $data['province']))
                    ->queryColumn('region_name'),
                'cityname' => DataRegionModel::model()->getDb()
                    ->where(array('region_id' => $data['city']))
                    ->queryColumn('region_name'),
                'areaname'      => DataRegionModel::model()->getDb()
                    ->where(array('region_id' => $data['county']))
                    ->queryColumn('region_name'),
                'schoolname'    => DataMidSchoolModel::model()->getDb()
                    ->where(array('school_id' => $data['seniorhigh']))
                    ->queryColumn('school_name'),
                'banji'         => $data['banji'],
                'grade'         => $data['junior'],
                'gender'        => $data['sex'],
                'lx'            => $data['subjecttype'],
                'gkyear'        => $data['examtime'],
            );
            // 记录考生信息表
            UserKaoshengModel::model()->getDb()->insert($kaoSheng);
            // 保存cookie 下次自动登陆
            arModule('User')->gUserCookieString($uid);
            // 成功跳转
            $this->redirectSuccess('User/index', '注册成功');
        else :
            // 获取model错误信息
            $this->redirectError('', arComp('list.log')->get('UserModel'));
        endif;
    endif;
    //$ckey = arComp('list.session')->get('ckey');
    //var_dump($ckey);
    //$this->assign(array('ckey' => $ckey));
    $this->display();

    }

    // 发送手机验证码
    public function sendPhoneCodeAction()
    {
        $this->setLayOutfile('');
        $code = arModule('Vip')->randpw('4', 'NUMBER');
        $sendto = arRequest('phone');
/*
        if ($ckcode = arRequest('ckey')) :
            if (strtoupper($ckcode) != arComp('list.session')->get('ckey')) :
                return $this->showJsonError('验证码错误');
            else :
                arComp('list.session')->set('ckey', null);
            endif;
        else :
            return $this->showJsonError('验证码不能为空');
        endif;
*/
        if ($sendto) :
            $sendSuccess = arModule('Sms')->juhecurl($sendto, $code, '5940');
            if ($sendSuccess) :
                //状态为0，说明短信发送成功
                $_SESSION["call_yzm"] = $code;
                $_SESSION["call_phone"] = $sendto;
                $this->showJson(array('status' => 1), array('data' => true));
            else :
                $this->showJson(array('status' => 0), array('data' => true));
            endif;
        else :
            $this->showJson(array('status' => 0), array('data' => true));
        endif;

    }

    /*志愿表单*/
    public function myVolunteerFormAction()
    {
        $condition = array();
        $condition['uid'] = arRequest('uid');
        $zys = UserZyModel::model()->getDb()
            ->where($condition)
            ->queryAll();
        $this->assign(array('zys' => $zys));
        $this->display();
    }

    // 删除志愿表单
    public function deleteZyAction()
    {
        if ($zid = arRequest('zid')) :
            $dResult = UserZyModel::model()
                ->getDb()
                ->where(array('zid' => $zid))
                ->delete();
            if ($dResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError();
        endif;
    }

    // 退出登录
    public function loginOutAction()
    {
        session_start();
        session_unset();
        session_destroy();
        setcookie('userlogin', '', time() - 3600*24*15, '/');
        header("location:login");
        //$this->display();
    }

    public function simulationSaveFormAction()
    {
        $this->display();

    }

    public function gaokaoSaveFormAction()
    {
        $this->display();

    }

    public function vipCardAction()
    {
        $this->display();

    }

    public function buyCardAction()
    {
        $this->display();

    }

}
