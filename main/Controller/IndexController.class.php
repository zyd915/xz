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

    // 首页
    public function indexAction()
    {
        // 加载admin 下的module
        arLm('admin.Module');
        // 专家简介 推荐三个
        $experts = ExpertModel::model()
            ->getDb()
            ->limit(6)
            ->order('sorder desc')
            ->queryAll();

        // 成功案例
        $successCases = arModule('Article')->getCatArticles('成功案例', 4);
        // 文章推荐
        $articelsIndexs = arModule('Article')->indexArticles();
        // 最新资讯
        $indexNewArticels = arModule('Article')->indexNewArticles();

        // 查询数据日志
        $userLogs = UserLogModel::model()->getDb()->order('lid desc')->limit(20)->queryAll();
        foreach ($userLogs as &$log) :
            $log['time'] = UserLogModel::model()->timeTranse($log['time']);
        endforeach;

        $tjData = UserLogModel::model()->tjData();

        // 读取中国的地方省份
        $chinaCity = arCfg('PATH.VIEW') . 'Index/chinaCity.html';
        $getPro = file_get_contents($chinaCity);
        $chinaCityArray =  json_decode($getPro,true);
        // 首页轮播图
        $headerListGallery = AdModel::model()->getDb()->where(array('type'=>AdModel::TYPE_INDEX))->queryAll();
        $headerListGallery = arModule('Ad')->statusToggle($headerListGallery,'galleryid');
        // 循证讲堂
        // 分类
        $class = VideoclassModel::model()
            ->getDb()
            ->queryAll();
        // 视频
        $video = VideolistModel::model()
            ->getDb()
            ->limit(12)
            ->order('sorder desc , aid desc')
            ->queryAll();
        // 访问量
        // for ($i = 0, $i < 1, $i ++ ){

        // }
        $this->assign(array(
            // 视频分类
            'class' => $class,
            // 循证讲堂
            'video' => $video,
            // 用户日志
            'userLogs' => $userLogs,
            // 统计数据
            'tjData' => $tjData,
            'successCases'=>$successCases,
            'articelsIndexs' => $articelsIndexs,
            'indexNewArticels' => $indexNewArticels,
            'experts' => $experts,
            'headerListGallery' => $headerListGallery,
            'chinaCityArray'=>$chinaCityArray));
        $this->display();
    }

    // 注册
    public function registerAction()
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
            $user['pwd'] = UserModel::gPwd($data['pwd']);
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

    // 判断验证码是否正确
    public function codeAction()
    {
        //$ckey = arComp('list.session')->get('ckey');
        //session_start();
        // $str_number = trim($_POST['ckey']);
        // if(strtolower($_SESSION['ckey'])==strtolower($str_number )){
        // echo "验证码正确";
        // }else{
        // echo "验证码不正确";
        // }
        //$cey = $_SESSION['ckey'];
        //var_dump($cey);
        //var_dump($ckey);

        //$this->dispaly('@index/register');
    }

    // 验证码
    public function captchaAction()
    {
        require arCfg('EXTENSION_DIR') . 'captcha' . DS . 'captcha.php';
        new CaptchaSecurityImages(135, 50, 4, 'ckey');
        //$ckey = arComp('list.session')->get('cey');
        //var_dump($ckey);

    }

    // 循征介绍
    public function summaryAction()
    {
        $this->getLeftInfoAction();
        $name='公司简介';
        $like['name like']='%'.$name.'%';
        $like['onleft'] = CategoryModel::YES_LEFT;
        $likeAid = CategoryModel::model()->getDb()->where($like)->queryColumn('cid');
        $summAid = ArticleModel::model()->getDb()->where(array('cid'=>$likeAid))->queryRow();
        $this->assign(array(
            'summAid' => $summAid,
        ));
        $jianId = '1962';
        $article = ArticleModel::model()->getDb()->where(array('aid' => $jianId))->queryRow();
        $this->assign(array('article' => $article));
        $this->display();

    }

    //查院校
    public function schoolSelectAction()
    {
        $condition = array();
        if ($sname = arRequest('sname')) :
            $condition['sname like '] = '%' . urldecode($sname) . '%';
        endif;
        if ($area = arRequest('area')) :
            $condition['area like '] = '%' . urldecode($area) . '%';
        endif;

        // 特色
        if ($ts = arRequest('ts')) :
            if (is_numeric($ts)) :
                // 211 985
                $condition['ts like '] = '%' . $ts . '%';
            else :
                // 隶属
                $condition['ls like '] = '%' . urldecode($ts) . '%';
            endif;
        endif;

        $total = CollegesModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取省份
        $provinces = DataRegionModel::model()->getProvince();

        $colleges = CollegesModel::model()->getDb()
            ->select('cid,sname,pm1,pm2,area,ts,ls,lx,hot,jb')
            ->where($condition)
            ->limit($page->limit())
            ->queryAll();

        // 四川院校推荐
        $sccondition['area like '] = '%' . '四川' . '%';
        $scColleges = CollegesModel::model()->getDb()
            ->select('cid,sname,pm1,pm2,area,ts,ls,lx,hot,jb')
            ->where($sccondition)
            // ->order('cid desc')
            ->limit(10)
            ->queryAll();
        $this->assign(array('scColleges' => $scColleges, 'provinces' => $provinces, 'schoolCount' => $total, 'colleges' => $colleges, 'page' => $page->show()));

        $this->display();

    }

    // 院校详情
    public function schoolSelectInfoAction()
    {
        $condition = array();
        if ($name = arRequest('name')) :
            $condition['sname'] = urldecode($name);
        elseif ($cid = arRequest('cid')) :
            $condition['cid'] = $cid;
        else :
            $this->redirect('index');
        endif;
        $college = CollegesModel::model()->getDb()
            ->where($condition)
            ->queryRow();
        if ($college) :
            // 同地区学校推荐
            $sameAreaSchools = $collegeAreaTj = CollegesModel::model()->getDb()
                ->where(array('area like ' => '%' . $college['area'] . '%', 'cid != ' => $college['cid']))
                ->order('hot desc')
                ->limit(15)
                ->queryAll();

            $this->assign(array('sameAreaSchools' => $sameAreaSchools, 'college' => $college));
            $this->display();
        else :
            $this->redirectError('schoolSelect');
        endif;

    }

    // 查专业
    public function professionalSelectAction()
    {
        $condition = array();
        if ($mname = arRequest('mname')) :
            $condition['mname like '] = '%' . urldecode($mname) . '%';
        endif;
        $condition['lb'] = arRequest('lb', '本科');
        arLm('admin.Module');
        $majors = MajorsModel::model()->getDb()
            ->select('xk,ml,mname,hot,dm,lb')
            ->where($condition)
            ->queryAll();
        $majors = arModule('Data')->getMajorsFormatData($majors);
        // 热门推荐
        $hots = MajorsModel::model()->getDb()
            ->select('xk,ml,mname,hot,dm,lb')
            ->limit(10)
            ->order('hot desc')
            ->queryAll();
        // 热门
        $this->assign(array('majors' => $majors, 'hots' => $hots));
        $this->display();

    }

    //专业详情
    public function professionalSelectInfoAction()
    {
        if ($dm = arRequest('dm')) :
            $condition = array('dm' => $dm);

        elseif ($name = arRequest('name')) :
            $name = urldecode($name);
            $condition = array('mname' => $name);
        endif;

        $major = MajorsModel::model()
            ->getDb()
            ->where($condition)
            ->queryRow();

        if (!$major) :
            $this->redirectError('professionalSelect', '无此专业');
        endif;

        // 热门推荐
        $hots = MajorsModel::model()->getDb()
            ->select('xk,ml,mname,hot,dm,lb')
            ->limit(10)
            ->order('hot desc')
            ->queryAll();
        $this->assign(array('major' => $major, 'hots' => $hots));
        $this->display();

    }

    //循征志愿卡
    public function vipCardAction()
    {
        arLm('admin.Module');
        $successCases = arModule('Article')->getCatArticles('成功案例', 4);

        $logs = UserLogModel::model()->getDb()
            ->where(array('ltype' => 'gouka'))
            ->order('lid desc')
            ->limit(20)
            ->queryAll();

        foreach ($logs as &$log) :
            $log['time'] = UserLogModel::model()->timeTranse($log['time']);
        endforeach;

        // 分配
        $this->assign(array('logs' => $logs, 'cases' => $successCases));

        $this->display();

    }

    //批次线
    public function scoreLineAction()
    {
        arLm('admin.Module');
        $condition = array();
        if ($area = arRequest('area')) :
            $area = urldecode($area);
            $condition['area'] = $area;
            $this->assign(array('area' => $area));
        endif;
        $fsxs = FsxModel::model()->getDb()->where($condition)->queryAll();
        $fsxs = arModule('Data')->fsxDetailInfo($fsxs);

        $province = arModule('Data')->getFsxArea();

        $this->assign(array('fsxs' => $fsxs, 'province' => $province));
        $this->display();

    }
    //分数详情页
    public function scoreLineInfoAction()
    {
        $this->display();

    }

    //专家咨询
    public function expertConsultationAction()
    {
        $experts = ExpertModel::model()->getDb()->queryAll();
        $this->assign(array('experts' => $experts));
        $this->display();

    }

    //专家咨询详细信息
    public function expertConsultationDetailAction()
    {
        $aid = arRequest('aid');
        $showOne = ArticleModel::model()->getDb()->where(array('aid'=>$aid))->queryRow();
        $showOne = arModule('Article')->getArtGallery($showOne);
        $this->assign(array('showOne'=>$showOne));
        $this->display();
    }

    //测试页面
    public function testAction()
    {
         $this->display('@/Index/Test');
    }

    //测试页面
    public function testsAction()
    {
         $this->display();
    }
    public function itestAction(){
        echo json_encode(array('a' => 'apple' , 'b' => 'boy' , 'msg' => 'true'));
    }
    public function indextestAction(){
        $this->display();
    }
     public function getContAction()
    {
        $getColl =  new getCollege();
        $echo = $getColl->getContents();
        $this->showJson($echo);
    }
    public function getContentAction()
    {
        $this->setLayOutfile('');
        $this->assign(array('play'=>'显示'));
        $this->display();
    }



    public function insertDataAction()
    {
      $getCont = arRequest();
      $getColl = new getCollege();
      $getColl->insertDataLike($getCont);
      // var_dump($getColl
    }

    // 发送手机验证码
    public function sendPhoneCodeAction()
    {
        $this->setLayOutfile('');
        $code = arModule('Vip')->randpw('4', 'NUMBER');
        $sendto = arRequest('phone');

        if (!arRequest('ismobile')) :

            if ($ckcode = arRequest('ckey')) :
                if (strtoupper($ckcode) != arComp('list.session')->get('ckey')) :
                    return $this->showJsonError('验证码错误');
                else :
                    arComp('list.session')->set('ckey', null);
                endif;
            else :
                return $this->showJsonError('验证码不能为空');
            endif;
        endif;

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

    //判断短信验证码的准确性
    public function checkPhoneCodeAction()
    {
        $code = arRequest('code');
        if(strtoupper($code) == strtoupper($_SESSION['call_yzm']))
        {
           $this->redirectSuccess();
        }else{
            $this->redirectError();
        }
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

    // 获取左边的分类
    public function getLeftInfoAction()
    {
      $name='公司介绍';
      $like['name like']='%'.$name.'%';
      $like['pid'] = 0;
      $cid = CategoryModel::model()->getDb()->where($like)->queryColumn('cid');
      $cateLeft = CategoryModel::model()->getDb()->where(array('pid'=>$cid,'onleft'=>CategoryModel::YES_LEFT))->queryAll();
      $this->assign(array('cateLeft'=>$cateLeft));
    }

    // 专家介绍
    public function expertsInfoAction()
    {
        $this->display();

    }

    //院校简介
    public function universityDetailAction()
    {
         $this->display();

    }

    //专业简介
    public function professionalDetailAction()
    {
         $this->display();

    }

    // 退出
    public function loginOutAction()
    {
        // 清除session
        arComp('list.session')->flush();
        // 删除cookie
        setcookie('userlogin', '', time() - 3600, '/');
        $this->redirect('index');

    }
    //支付成功
    public function paySuccessAction()
    {
        if ($uid = arComp('list.session')->get('uid')) :
            $cardInfo = UserVcardModel::model()->getDb()->where(array('uid' => $uid))->queryRow();
            if ($cardInfo) :
                $this->assign(array('card' => $cardInfo));
                $this->display();
            else :
                $this->redirectError('index', '购卡失败');
            endif;
        else :
            $this->redirect('index');
        endif;

    }

    //文科入口
    public function wenkerukouAction()
    {
        $this->display();

    }

    //理科入口
    public function likerukouAction()
    {
        $this->display();

    }

    //购卡提示
    public function tipAction()
    {
        $this->display();

    }

    //视频列表
    public function videolistAction()
    {
        $total = VideolistModel::model()
            ->getDb()
            ->count();
        $page = new Page($total, 12);
        // 视频分类
        $class = VideoclassModel::model()
            ->getDb()
            ->queryAll();
        // 分类
        $condition = array();
        $clas = arRequest('cid');
        if ($clas) :
            $condition['cid'] = $clas;
        endif;
        $video = VideolistModel::model()
            ->getDb()
            ->where($condition)
            ->order('sorder desc, aid desc')
            ->limit($page->limit())
            ->queryAll();
        // 搜索
   /*      $mate = Urldecode(arGet('mname'));
        $condition = array();
       if ($mate):
            $condition['title like'] = '$'.$mate.'$';
        endif;
        $video = VideolistModel::model()
            ->getDb()
            ->where($condition)
            ->limit($page->limit())
            ->queryAll();*/

        $this->assign(array('video'=>$video, 'class' => $class, 'clas' => $clas, 'page' => $page->show()));
        $this->display();
    }

    // 视频详情
    public function videodetailAction()
    {
        $id = arRequest('vid');
        $video = VideolistModel::model()
            ->getDb()
            ->where(array('aid' => $id))
            ->queryRow();
        // 访问量处理
        $fnums = $video['fnums'] + 1;
        // 更新数据
        VideolistModel::model()
            ->getDb()
            ->where(array('aid' => $id))
            ->update(array('fnums' => $fnums));
        $format = substr($video['video'],-3);
        $forma = substr($video['link'],-3);
        $this->assign(array('video'=>$video, 'format' => $format, 'forma' => $forma));
        $this->display();
    }

    // 视频搜索结果
    public function videoSearchAction()
    {
        // 分页
        $total = VideolistModel::model()
            ->getDb()
            ->count();
        $page = new page($total,12);
        // 搜索
        $mate = Urldecode(arGet('mname'));
        $condition = array();
        if ($mate):
            $condition['title like'] = '%'.$mate.'%';
        endif;
        $video = VideolistModel::model()
            ->getDb()
            ->where($condition)
            ->limit($page->limit())
            ->queryAll();
        $this->assign(array('video' => $video, 'page' => $page));
        $this->display();
    }

}
