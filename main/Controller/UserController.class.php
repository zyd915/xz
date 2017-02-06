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
        if (empty($_SESSION['uid']) && !arModule('User')->dUserCookieString()) :
            $this->redirect('Sign/login','你还没有登录');
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

        $this->setLayOutfile('user');

    }

    //首页
    public function indexAction()
    {
        if (!$this->user['kaosheng']['name'] || !$this->user['kaosheng']['schoolname']) :
            $this->redirect('userSetting');
        endif;
        $this->display();

    }

    // 升级VIP
    public function tobeVipAction()
    {
        if ($this->user['vip']) :
            $this->redirectError('index', '你已经是VIP用户');
        else :
            $this->redirect('alipay/pay');
        endif;

    }

    // 激活
    public function vipjihuoAction()
    {
        if ($this->user['vip']) :
            $this->showJsonError('你已经是VIP用户');
        else :
            if (($vipno = arPost('vipno')) && $pwd = arPost('vippwd')) :
                $condition = array(
                    'cno' => $vipno,
                    'pwd' => $pwd,
                    // 'vtype' => UserVcardModel::VTYPE_VIP,
                    // 'status' => UserVcardModel::NOTUSED,
                );
                $card = UserVcardModel::model()->getDb()->where($condition)->queryRow();
                if ($card) :
                    if ($card['vtype'] != UserVcardModel::VTYPE_VIP) :
                        return $this->showJsonError('只有vip卡可以激活');
                    endif;
                    // 激活卡
                    $retMsg = arModule('Vip')->active($card['cno'], $this->user['uid']);
                    if ($retMsg === true) :
                        return $this->showJsonSuccess('激活成功');
                    else :
                        return $this->showJsonError($retMsg);
                    endif;
                else :
                    return $this->showJsonError('卡号或密码错误');
                endif;
            endif;
        endif;

    }

    //用户设置
    public function userSettingAction()
    {
        if ($kaosheng = arPost()) :
            if(date('YmdHis',time()) > 20160622200000):
                UserModel::model()->getDb()->where(array('uid' => $_SESSION['uid']))->update(array('time' => 1),true);
            endif;
            $kaosheng['proname'] = DataRegionModel::model()->getDb()
                ->where(array('region_id' => $kaosheng['areapid']))
                ->queryColumn('region_name');
            $kaosheng['cityname'] = DataRegionModel::model()->getDb()
                ->where(array('region_id' => $kaosheng['areacid']))
                ->queryColumn('region_name');
            $kaosheng['areaname'] = DataRegionModel::model()->getDb()
                ->where(array('region_id' => $kaosheng['areaaid']))
                ->queryColumn('region_name');
            $kaosheng['schoolname'] = DataMidSchoolModel::model()->getDb()
                ->where(array('school_id' => $kaosheng['schoolid']))
                ->queryColumn('school_name');

            $condition = array(
                'uid' => $this->user['uid'],
            );

            if (UserKaoshengModel::model()->getDb()->where($condition)->count() > 0) :
                // 是否有添加高考成绩
                if ($kaosheng['scoregk']) :
                    arModule('Kaosheng')->updateGaokaoScore($this->user['uid'], $kaosheng['scoregk']);
                    // 只能修改一次
                    unset($kaosheng['scoregk']);
                endif;
                // 更新
                $updateuser = UserKaoshengModel::model()->getDb()
                    ->where($condition)
                    ->update($kaosheng, true);
            else :
                // 添加考生 考虑重新绑定的原因
                $kaosheng['uid'] = $this->user['uid'];
                UserKaoshengModel::model()->getDb()->insert($kaosheng, true);
            endif;
            // 记录考生信息表
            $this->redirectSuccess('');
        endif;

        $provinces = DataRegionModel::model()->getProvince();
        $citys = DataRegionModel::model()->getAllreginByPid($this->user['kaosheng']['areapid'], false);
        $countys = DataRegionModel::model()->getAllreginByPid($this->user['kaosheng']['areacid'], false);
        $schools = DataMidSchoolModel::model()
            ->getDb()
            ->where(array('area' => $this->user['kaosheng']['areaaid']))
            ->queryAll();
        $this->assign(array('schools' => $schools, 'provinces' => $provinces, 'citys' => $citys, 'countys' => $countys));
        $this->display();

    }

    //考生走向
    public function examineeAction()
    {

         $this->display();

    }

    //专业对比
    public function professionalAction()
    {
         $this->display();

    }

    //专家咨询
    public function expertAction()
    {
        $prodetail = arModule('Article')->getArtListHead('专家一对一服务',3,'false',true,array('10',70));
        $this->assign(array('expect'=>$prodetail));
        // var_dump($prodetail);
        $this->display();

    }

    //专家咨询-->专家详细信息
    public function expertDetailAction()
    {
        if (($yid = arRequest('yid')) || ($eid = arRequest('eid'))) :
            if ($yid) :
                $yuyue = YuyueModel::model()->getDb()
                    ->where(array('yid' => $yid, 'uid' => $this->user['uid']))
                    ->queryRow();
                $eid = $yuyue['eid'];
                $this->assign(array('yuyue' => $yuyue));
            endif;

            $expert = ExpertModel::model()->getDb()
                ->where(array('eid' => $eid))
                ->queryRow();

            $this->assign(array('expert' => $expert));

            $this->display();
        else :
            $this->redirectError(array('expert'), '必须先选择一位专家');
        endif;

    }

    //专家咨询-->订单付款
    public function expertDetailPayAction()
    {
        // module
        arLm('admin.Module');
        $yid = arRequest('yid');

        $yuyue = YuyueModel::model()->getDb()->where(array('yid' => $yid))->queryRow();
        $yuyue = arModule('Yuyue')->yyDetail($yuyue);

        $this->assign(array('yuyue' => $yuyue));

        $this->display();

    }

    //专家咨询-->专家电话解答
    public function expertDetailMessageAction()
    {
        $aid = arRequest('aid');
        $showOne = ArticleModel::model()->getDb()->where(array('aid'=>$aid))->queryRow();
        $showOne = arModule('Article')->getArtGallery($showOne);
        $this->assign(array('showOne'=>$showOne));
        $this->display();

    }

    //专家咨询-->我的咨询单
    public function expertDetailFormAction()
    {
        arLm('admin.Module');

        $yuyues = YuyueModel::model()->getDb()->where(array('uid' => $this->user['uid']))->queryAll();
        $yuyues = arModule('Yuyue')->yyDetail($yuyues);
        $this->assign(array('yuyues' => $yuyues));

        $this->display();

    }

    //专家咨询-->我的咨询单详细信息
    public function expertDetailFormMessageAction()
    {
        $aid = arRequest('aid');
        $showOne = ArticleModel::model()->getDb()->where(array('aid'=>$aid))->queryRow();
        $showOne = arModule('Article')->getArtGallery($showOne);
        $this->assign(array('showOne'=>$showOne));
         $this->display();

    }

    //测录取概率
    public function simulationAcceptanceProbabilityAction()
    {
         $this->display();

    }

    //院校数据查询
    public function universityDataAction()
    {
         $this->display();

    }

    //艺体数据库查询
    public function artDatabaseQueryAction()
    {
         $this->display();

    }

    //学校对比
    public function universityAction()
    {
         $this->display();

    }

    //专业数据查询
    public function professionalDataAction()
    {
         $this->display();

    }

    //志愿模拟系统1
    public function simulationSystemOneAction()
    {
         $this->display();

    }
    //志愿模拟系统1选择批次页面
    public function simulationSystemOneInfoAction()
    {
         $this->display();

    }
    //志愿模拟系统1选择院校页面
    public function simulationSystemOneSchoolAction()
    {
         $this->display();

    }
    //志愿模拟系统1保存志愿表单
    public function simulationSystemOneSaveFormAction()
    {
         $this->display();

    }
    //志愿模拟系统1志愿录取概率显示
    public function simulationSystemOneVolunteerVerificationAction()
    {
         $this->display();

    }
    //志愿模拟系统1选择院校页面--提前批
    public function simulationSystemOneSchoolTQPAction()
    {
         $this->display();

    }
    //志愿模拟系统1选择院校信息页面(调试用)
    public function simulationSystemOneSchoolInfoAction()
    {
         $this->display();

    }

    //志愿模拟系统2
    public function simulationSystemTwoAction()
    {
        $this->display();
    }

    //志愿模拟系统2选择批次页面
    public function simulationSystemTwoInfoAction()
    {
        if ($gk = arPost('score')) :
            if(date('YmdHis',time()) > 20160622200000):
                UserModel::model()->getDb()->where(array('uid' => $_SESSION['uid']))->update(array('time' => 1),true);
            endif;
            $kaosheng = UserKaoshengModel::model()->getDb()->where(array('uid' => $_SESSION['uid']))->update(array('scoregk' => $gk),true);
        endif;
        $this->display();
    }

    //志愿模拟系统2选择院校页面
    public function simulationSystemTwoSchoolAction()
    {
         $this->display();

    }
    //志愿模拟系统2保存志愿表单
    public function simulationSystemTwoSaveFormAction()
    {
         $this->display();

    }
    //志愿模拟系统2志愿录取概率显示
    public function simulationSystemTwoVolunteerVerificationAction()
    {
         $this->display();

    }
    //志愿模拟系统2选择院校页面--提前批
    public function simulationSystemTwoSchoolTQPAction()
    {
         $this->display();

    }

    //测试页面,其他页面显示控制器按照这个写
    public function testAction()
    {
        echo date('YmdHis',time());
        //str_replace('-',"Shanghai","Hello world!");
        $this->display();
    }

    // 修改密码
    public function changePwdAction()
    {
        $getPwd =UserModel::model()->getDb()->where(array('uid'=>$_SESSION['uid']))->queryRow();
        $pwd = arPost();
        if ($getPwd['pwd']) :
            if ($getPwd['pwd'] == UserModel::gPwd($pwd['oPwd'])) :
                $pwds['pwd'] = UserModel::gPwd($pwd['nPwd']);
                $change = UserModel::model()->getDb()->where(array('uid'=>$_SESSION['uid']))->update($pwds,true);
                if ($change) :
                    $this->showJsonSuccess('密码修改成功');
                else :
                    $this->showJsonError('服务器繁忙，请重试');
                endif;
            else :
                $this->showJsonError('密码输入错误');
            endif;
        else :
            // 第一次初始化密码
            $pwds['pwd'] = UserModel::gPwd($pwd['nPwd']);
            $change = UserModel::model()->getDb()->where(array('uid'=>$_SESSION['uid']))->update($pwds,true);
            if ($change) :
                $this->showJsonSuccess('密码修改成功');
            else :
                $this->showJsonError('服务器繁忙，请重试');
            endif;
        endif;

    }

    // 修改vip密码
    public function changevipPwdAction()
    {
        if ($this->user['vip'] && arPost('nPwd')) :
            if (strlen(arPost('nPwd')) < 6) :
                return $this->showJsonError('密码长度不能小于6');
            endif;
            $change = UserVcardModel::model()
                ->getDb()
                ->where(array('cno' => $this->user['vip']['cno']))
                ->update(array('pwd' => arPost('nPwd')));
            if ($change) :
                $this->showJsonSuccess('修改成功');
            else :
                $this->showJsonError('修改失败');
            endif;
        else :
            $this->showJsonError('你还不是vip会员');
        endif;

    }

    // 激活卡片
    public function activeCardAction()
    {
        $this->display();

    }

    // 专业倾向测试报告
    public function zyTestResultAction()
    {
        $this->display();

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

    // 删除预约
    public function deleteYuyueAction()
    {
        if ($yid = arRequest('yid')) :
            YuyueModel::model()->getDb()->where(array('yid' => $yid, 'uid' => $this->user['uid']))->delete();
            $this->redirectSuccess('expertDetailForm');
        endif;

    }

    // 预约专家
    public function yyzjAction()
    {
        if ($data = arPost()) :
            if ($yid = $data['yid']) :
                $updateResult = YuyueModel::model()
                    ->getDb()
                    ->where(array('yid' => $this->user['uid'], 'yid' => $yid))
                    ->update($data, 1);
                $this->redirectSuccess(array('User/expertDetailForm'), '修改成功');
            else :
                $data['uid'] = $this->user['uid'];
                $data['atime'] = time();
                $yid = YuyueModel::model()->getDb()->insert($data);
                if ($yid) :
                    UserLogModel::model()->log('yuyue');
                    $this->redirectSuccess(array('User/expertDetailForm', array('yid' => $yid)), '预约成功');
                else :
                    $this->redirect('index');
                endif;
            endif;
        endif;

    }

    //系统使用指南
    public function systemGuidanceAction()
    {
         $this->display();

    }

    //我的志愿表单
    public function myVolunteerFormAction()
    {
        $zys = UserZyModel::model()->getDb()
            ->where(array('uid' => $this->user['uid']))
            ->limit(20)
            ->queryAll();
        $this->assign(array('zys' => $zys));
        $this->display();

    }

    // 删除
    public function deleteZyAction()
    {
        if ($zid = arRequest('zid')) :
            $dResult = UserZyModel::model()
                ->getDb()
                ->where(array('uid' => $this->user['uid'], 'zid' => $zid))
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

    // 专家配置
    public function zjSettingAction()
    {
        $uid = $this->user['uid'];
        $config = ZjConfigModel::model()->getDb()->where(array('uid' => $uid))->queryRow();
        if ($data = arPost()) :
            $content = serialize($data);
            if ($config) :
                ZjConfigModel::model()->getDb()
                    ->where(array('uid' => $uid))
                    ->update(array('content' => $content));
            else :
                ZjConfigModel::model()->getDb()
                    ->insert(array('uid' => $uid, 'content' => $content));
            endif;
            $this->redirectSuccess('');
        else :
            if ($config) :
                $configBundles = unserialize($config['content']);
                foreach ($configBundles as $key => $bundle) :
                    $key = str_replace('||', '.', $key);
                    // 写入全局配置
                    Ar::setConfig($key, $bundle);
                endforeach;
            endif;
        endif;
        $this->display();

    }
    // 高考模拟提前批
    public function gkmntqpAction()
    {
         $this->display();

    }
    public function gkmntqpsaveAction()
    {
         $this->display();

    }

}
