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
    /**
     * just the example of get contents.
     *
     * @return void
     */
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
        $successCases = arModule('Article')->getCatArticles('成功案例', 6);
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
        $chinaCityArray = json_decode($getPro, true);
        // 首页轮播图
        $headerListGallery = AdModel::model()->getDb()->where(array('type' => AdModel::TYPE_INDEX))->queryAll();
        $headerListGallery = arModule('Ad')->statusToggle($headerListGallery, 'galleryid');
        // 首页轮播图
        $img = AdModel::model()
            ->getDb()
            ->where(array('type' => 1))
            ->queryAll();
        // 循证讲堂
        $video = VideolistModel::model()
            ->getDb()
            ->limit(15)
            ->order('sorder desc , aid desc')
            ->queryAll();
        $this->assign(array(
            // 循证讲堂
            'video' => $video,
            // 轮播图
            'img' => $img,
            // 用户日志
            'userLogs' => $userLogs,
            // 统计数据
            'tjData' => $tjData,
            'successCases' => $successCases,
            'articelsIndexs' => $articelsIndexs,
            'indexNewArticels' => $indexNewArticels,
            'experts' => $experts,
            'headerListGallery' => $headerListGallery,
            'chinaCityArray' => $chinaCityArray));
        $this->display();

    }

    // 验证码
    public function captchaAction()
    {
        require arCfg('EXTENSION_DIR') . 'captcha' . DS . 'captcha.php';
        new CaptchaSecurityImages(135, 50, 4, 'ckey');
        //$ckey = arComp('list.session')->get('cey');
        //var_dump($ckey);

    }

    // 院校数据查询
    public function schooldata_queryAction()
    {
        $condition = array();
        // 学院
        if ($sname = arRequest('sname')):
            $condition['sname like'] = '%' . urldecode($sname) . '%';
        endif;
        // 省份
        if ($area = arRequest('area')):
            $condition['area like'] = '%' . urldecode($area) . '%';
        endif;
        // 隶属
        if ($ts = arRequest('ts')):
            if (is_numeric($ts)):
                $condition['ts like'] = '%' . $ts . '%';
            else:
                $condition['ls like'] = '%' . urldecode($ts) . '%';
            endif;
        endif;
        $total = CollegesModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);
        $school = CollegesModel::model()
            ->getDb()
            ->where($condition)
            ->limit($page->limit())
            ->queryAll();
        // 省份
        $provinces = DataRegionModel::model()
            ->getDb()
            ->where(array('region_type' => 1))
            ->queryAll();
        $this->assign(array('total' => $total,
            'school' => $school,
            'page' => $page->show(),
            'provinces' => $provinces,
        ));
        $this->display();

    }

    // 性格测试
    public function testAction()
    {
        //if (empty($_SESSION)):
        //  $this->redirectError('User/login','您还未登录');
       // else:
            $this->display();
        //endif;
    }

    public function schooldetailAction()
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
                ->limit(10)
                ->queryAll();

            $this->assign(array('sameAreaSchools' => $sameAreaSchools, 'college' => $college));
            $this->display();
        else :
            $this->redirectError('schooldata_query');
        endif;

    }

    // 专业列表
    public function professionalSelectAction()
    {
        $condition = array();
        //$condit = array();
        if ($mname = arRequest('mname')) :
            $condition['mname like '] = '%' . urldecode($mname) . '%';
        endif;
        $condition['lb'] = arRequest('lb', '本科');
        //$condit['lb'] = arRequest('lb', '专科');
        arLm('admin.Module');
        $majors = MajorsModel::model()->getDb()
            ->select('mid,xk,ml,mname,hot,dm,lb')
            ->where($condition)
            ->queryAll();
        $major = MajorsModel::model()->getDb()
            ->select('mid,xk,ml,mname,hot,dm,lb')
            ->where(array('lb' => '专科'))
            ->queryAll();
        $major = arModule('Data')->getMajorsFormatDatail($major);
        //var_dump($major);
        // exit;
        $majors = arModule('Data')->getMajorsFormatData($majors);
        // // 热门推荐
        // $hots = MajorsModel::model()->getDb()
        //     ->select('xk,ml,mname,hot,dm,lb')
        //     ->limit(10)
        //     ->order('hot desc')
        //     ->queryAll();
        // 热门
        $this->assign(array('majors' => $majors, 'major1' => $major));
        $this->display('chooseprofessional');
    }

    // 专业搜索
    public function verticalSearchAction()
    {

        $this->display();
    }

    // 专业详情
    public function professionaldetailAction()
    {
        $mid = arRequest('mid');
        $majors = MajorsModel::model()
            ->getDb()
            ->where(array('mid' => $mid))
            ->queryRow();
        $this->assign(array('majors' => $majors));
        $this->display();

    }

//    // 院校查询
//    public function schooldata_queryAction()
//    {
//        $this->display();
//
//    }

    // 用户信息
    public function userAction()
    {
        $this->display();

    }

    // 查找专家
    public function findExpertAction()
    {
        //if(isset($_SESSION['username'])):
            $experts = ExpertModel::model()->getDb()->queryAll();
            $this->assign(array('experts' => $experts));
            $this->display();
       // else:
            //header('User/loginOut');
      //  endif;
    }

    // 测试录取概率
    public function testProbabilityAction()
    {
        $this->display();

    }

    // 测试录取概率
    public function testProbabilityListAction()
    {
        $this->display();

    }

    // 调试
    public function textAction()
    {
        echo AR_SERVER_PATH;
        //$this->display();

    }

    //一 二 三诊模拟填报
    public function simulationSystemOneAction()
    {
        $this->display();

    }

    //一 二 三诊模拟填报 （选择批次）
    public function simulationSystemOneInfoAction()
    {
        $this->display();

    }

    //一 二 三诊模拟填报 (选择院校专业)
    public function simulationSystemOneSchoolAction()
    {
        $this->display();

    }

    //一 二 三诊模拟填报 (志愿单)
    public function simulationSystemOneSaveFormAction()
    {
        $uid = $_SESSION['uid'];
        $this->assign(array('uid' => $uid));
        $this->display();

    }

    /* 高考模拟填报 */
    public function gaokaosystemAction()
    {
        $this->display();

    }

    public function gaokaosysteminfoAction()
    {
        $this->display();

    }

    public function gaokaomoniAction()
    {
        $this->display();

    }

    public function gaokaomoniSaveFormAction()
    {
        $this->display();

    }

    public function zysearchAction()
    {
        $condition = array();
        if (arRequest('mname')):
            $condition['mname like'] = '%' . urldecode(arRequest('mname')) . '%';
        endif;
        $majors = MajorsModel::model()->getDb()
            ->select('mid,xk,ml,mname,hot,dm,lb')
            ->where($condition)
            ->queryAll();
        arLm('admin.Module');
        $majors = arModule('Data')->getMajorsFormatData($majors);
        // var_dump($majors);
        // exit;
        $this->assign(array('majors' => $majors));
        $this->display();

    }

    public function gaokaosaveAction()
    {
        $uid = $_SESSION['uid'];
        $this->assign(array('uid' => $uid));
        $this->display();

    }

    /*更多成功案例*/
    public function moreCaseAction()
    {
        $cid = ArticleCatModel::model()->getDb()
            ->where(array('name' => '成功案例'))
            ->queryColumn('cid');

        $count = ArticleModel::model()->getDb()
            ->where(array('cid' => $cid))
            ->count();
        $page = new Page($count, 10);
        $articles = ArticleModel::model()->getDb()
            ->limit($page->limit())
            ->where(array('cid' => $cid))
            ->queryAll();
        $articles = arModule('Article')->getArticlesDetailInfo($articles);
        $this->assign(array('cases' => $articles, 'page' => $page->show()));
        $this->display();
    }

    public function caseDetailAction()
    {
        $aid = arRequest('aid');
        $articles = ArticleModel::model()->getDb()
            ->where(array('aid' => $aid))
            ->queryRow();
        $this->assign(array('casedetail' => $articles));
        $this->display();

    }

    public function expertDetailAction()
    {
        $eid = arRequest('eid');
        $expert = ExpertModel::model()->getDb()->where(array('eid' => $eid))->queryRow();
        $this->assign(array('expert' => $expert));
        $this->display();

    }

    // 循证讲堂
    public function lectureRoomAction()
    {
        $class = VideoclassModel::model()
            ->getDb()
            ->queryAll();
        $this->assign(array('video' => $video, 'class' => $class, 'color' => $color));
        $this->display();

    }

    public function videoDetailAction()
    {
        $id = arRequest('aid');
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

    public function lectureMoreAction()
    {
        $class = VideoclassModel::model()
            ->getDb()
            ->queryAll();
        // $num = arRequest('num');
        // $number = 4;
        // if($num):
        //     $number+=$num;
        // endif;
        $condition = array();
        $cid = arRequest('cid');
        if ($cid):
            $condition['cid'] = $cid;
        endif;
        $mate = arRequest('mname');
        if ($mate):
            $condition['title like'] = '%'.Urldecode($mate).'%';
        endif;

        $total = VideolistModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 15);
        $video = VideolistModel::model()
            ->getDb()
            ->where($condition)
            ->limit($page->limit())
            //->limit(4)
            ->order('sorder desc , aid desc')
            ->queryAll();
        //$array['video'] = $video;
        //json_encode($array);
        //$video['max_page'] = $page->totalPages;
        if (arRequest('ajax') == '1') :
            return $this->showJson($video, array('max_page' => $page->totalPages));
        else :
            $this->assign(array('class' => $class, 'video' => $video, 'cid' => $cid));
            $this->display();
        endif;


    }

    public function zyRecommandPcAction()
    {
        if(empty($_SESSION['uid'])):
            $this->redirectError('User/login','您还未登录');
        else:
            $this->display();
        endif;
    }

    public function offlineLectureAction()
    {
            arLm('admin.Module');
            $condition['cid'] = 8;
            $count = ArticleModel::model()->getDb()->where($condition)->count();
            $page = new Page($count, 8);
            $articles = ArticleModel::model()->getDb()
                ->where($condition)
                ->limit($page->limit())
                ->order('sorder desc, aid desc')
                ->queryAll();
            // 详情
            $articles = arModule('Article')->getArticlesDetailInfo($articles);
            $this->assign(array(
                    'articles' => $articles,
                    'page' => $page->show(),
                )
            );
            $this->display();
    }

    public function offlineLectureDetailAction()
    {
        $uid = $_SESSION['uid'];
        $aid = arRequest('aid');
        $article = ArticleModel::model()->getDb()
            ->where(array('aid' => $aid))
            ->queryRow();
        $chair = ChairModel::model()
                ->getDb()
                ->where(array('uid' => $uid,'xid' => $aid))
                ->queryRow();
        if ($chair):
            $join = 1;
        endif;
        $this->assign(array('art' => $article,'join' => $join));
        $this->display();

    }

    // 讲座报名
    public function offlineSubmitInfoAction()
    {
        $data = arPost();
        $data['uid'] = $_SESSION['uid'];
        if ($data['name'] != '' && $data['seating'] != ''):
            ChairModel::model()->getDb()->insert($data, true);
        endif;
        $this->display();
    }

    // 省控线
    public function scoreLineAction()
    {
        $area = urldecode(arRequest('area'));
        if (empty($area)):
            $area = '四川';
        endif;
        $year = arRequest('year');
        if (empty($year)):
            $year = 15;
        endif;
        $condition['area'] = $area;
        $fsxs = FsxModel::model()->getDb()->where($condition)->queryAll();
        $this->assign(array('fsx' => $fsxs, 'area' => $area, 'year' => $year));
        $this->display();

    }

    // 提前批诊断模拟
    public function tqSimulationAction()
    {
        $this->display();

    }
    // 找回密码
    public function forgetAction()
    {
        $this->display();

    }

    // 专家预约
    public function expertZixunAction()
    {
        $uid = $_SESSION['uid'];
        $seid = arRequest('eid');
        $experts = ExpertModel::model()->getDb()->where(array('eid'=>$seid))->queryRow();
        if ($data = arPost()):
           if ($yid = arPost('yid')):
                $updateResult = YuyueModel::model()
                    ->getDb()
                    ->where(array('yid' => $yid))
                    ->update($data, 1);
                $this->redirectSuccess(array('Index/expertForm',array('yid' => $yid)), '修改成功');
            else :
                $data['uid'] = $uid;
                $data['atime'] = time();
                $yid = YuyueModel::model()->getDb()->insert($data);
                if ($yid) :
                    UserLogModel::model()->log('yuyue');
                    $this->redirectSuccess(array('Index/expertForm', array('yid' => $yid)), '预约成功');
                else :
                    $this->redirect('index');
                endif;
           endif;
        endif;
        $this->assign(array('experts'=>$experts));
        $this->display();

    }

    // 专家预约单
    public function expertFormAction()
    {
        $uid = $_SESSION['uid'];
         $yuyue = YuyueModel::model()
            ->getDb()
            ->where(array('uid' => $uid))
            ->queryAll();
        $this->assign(array('yuyue' => $yuyue));
        $this->display();

    }

    // 删除预约
    public function deleteYuyueAction()
    {
        if ($yid = arRequest('yid')) :
            YuyueModel::model()->getDb()->where(array('yid' => $yid))->delete();
            $this->redirectSuccess('expertForm');
        endif;

    }

    // 讲座报名存入数据库
    public function signUpInAction()
    {
        $uid = $_SESSION['uid'];
        if ($data = arPost()):
            $data['xid'] = arRequest('aid');
            $articles = ArticleModel::model()->getDb()
                ->where(array('aid' => $data['xid']))
                ->queryRow();
            $data['chair'] = $articles['title'];
            $data['uid'] = $uid;
            $chair = ChairModel::model()
                ->getDb()
                ->where(array('uid' => $data['uid'],'xid' => $data['xid']))
                ->queryRow();
            if ($chair):
                $this->redirectError(array('Index/signUpInfo'),'已报名');
            else:
                $insert = ChairModel::model()
                    ->getDb()
                    ->insert($data);
                if ($insert):
                    $this->redirectSuccess(array('Index/signUpInfo'),'报名成功');
                endif;
            endif;
        endif;
    }

    // 讲座报名列表
    public function signUpInfoAction()
    {
        $uid = $_SESSION['uid'];
        $user = UserKaoshengModel::model()
            ->getDb()
            ->where(array('uid' => $uid))
            ->queryRow();
        $chair = ChairModel::model()
                ->getDb()
                ->where(array('uid' => $uid))
                ->queryAll();
        $this->assign(array('user' => $user,'chair' => $chair));
        $this->display();
    }

    // 删除报名讲座
    public function offlineLectureDeleteAction()
    {
        $aid = arRequest('aid');
        if ($aid):
            $delete = ChairModel::model()
                ->getDb()
                ->where(array('aid' => $aid))
                ->delete();
            if ($delete):
                $this->redirectSuccess(array('Index/signUpInfo'),'删除成功');
            endif;
        endif;
    }


}
