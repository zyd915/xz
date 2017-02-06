<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 对外提供访问json数据。
 */
class ApiController extends ArController
{
    // 初始化方法
    public function init()
    {
        // 加载model
        arLm('admin.Model');
        // 加载Module
        arLm('admin.Module');

        arModule('Api')->doUserLog();

        Ar::setConfig('DEBUG_SHOW_LOG', false);

        if ($uid = arComp('list.session')->get('uid')) :
            $this->updateZjConfig($uid);
        endif;

    }

    // 更新专家配置
    public function updateZjConfig($uid)
    {
        $config = ZjConfigModel::model()->getDb()->where(array('uid' => $uid))->queryRow();
        if ($config) :
            $configBundles = unserialize($config['content']);
            foreach ($configBundles as $key => $bundle) :
                $key = str_replace('||', '.', $key);
                // 写入全局配置
                Ar::setConfig($key, $bundle);
            endforeach;
        endif;

    }

    // 验证邮箱是否已存在
    public function checkEmailAction()
    {
        $email = arRequest('email');
        if ($email) :
            if (UserModel::model()->getDb()->where(array('email' => $email))->count() > 0) :
                $this->showJsonError('邮箱已经被注册');
            else :
                $this->showJsonSuccess("恭喜你，此邮箱可以注册");
            endif;
        else :
            $this->showJsonError('参数无效');
        endif;

    }

    // 获取地区数据
    public function getAllregionByPidAction()
    {
        if ($data = arRequest()) :
            $pid = arRequest('pid', 0);
            // 是否查询所有子类
            $sub = arRequest('sub', false);
            // 数据太大 不查所有
            $sub = false;
            $region = RegionModel::model()->getAllreginByPid($pid, $sub);
            $this->showJson($region);
        else :
            $this->showJsonError();
        endif;

    }

    // 获取地区数据
    public function getAllregionBySidAction()
    {
        if ($data = arRequest()) :
            $sid = arRequest('sid', 0);
            $region = RegionModel::model()->getAllreginBySid($sid);
            $this->showJson($region);
        else :
            $this->showJsonError();
        endif;

    }

    // 获取商品数据
    public function getAllcateByPidAction()
    {
        if ($data = arRequest()) :
            $pid = arRequest('pid', 0);
            // 是否查询所有子类
            $cate = CategoryModel::model()->getAllCategoriesByPid($pid);
            $cate = arComp('format.format')->stripslashes($cate);
            $this->showJson($cate);
        else :
            $this->showJsonError();
        endif;

    }

    // 获取地区数据
    public function getAllcateBySidAction()
    {
        if ($data = arRequest()) :
            $sid = arRequest('sid', 0);
            $cate = CategoryModel::model()->getAllcateBySid($sid);
            $this->showJson($cate);
        else :
            $this->showJsonError();
        endif;

    }

    // 二维码生成
    public function qrcodeAction()
    {
        arLm('main.Module');
        if ($data = arRequest('data')) :
            $data = urldecode($data);
            $size = arRequest('size', 5);
            return arModule('Qr')->png($data, $size, false);
        else :
            $this->showJsonError();
        endif;

    }

    // 发送邮箱验证
    public function sendEmailRegAction()
    {
        $email = arRequest('email');
        if ($email != '') :
            $randCode = mt_rand(100000, 999999); // 验证码
            $subject = "欢迎注册世界之窗"; //邮件标题
            $body = "
欢迎您注册世界之窗，成为注册用户后即可享受更多的服务，
将下面的随机码复制到注册页面即可
随机码：$randCode
这是条来自世界之窗的验证信息，请勿回复！
更多内容请关注我们的微信和官网
            "; // 邮件内容

            $res = arModule('Mail')->send($email, '世界之窗注册验证码', $subject, $body);
            if ($res) :
                arComp('list.session')->set('randCodeReg', $randCode);
                return $this->showJsonSuccess('邮箱验证已发送');
            else :
                return $this->showJsonError('邮箱验证发送失败');
            endif;
        else :
            return $this->showJsonError();
        endif;

    }

    // 验证邮箱验证码
    public function checkEmailCodeAction()
    {
        $code = arRequest('code');
        if ($code) :
            if ($code == arComp('list.session')->get('randCodeReg')) :
                $this->showJsonSuccess("恭喜你，验证通过");
            else :
                arComp('list.session')->set('randCodeReg', null);
                $this->showJsonError('验证失败，验证码失效');
            endif;
        else :
            $this->showJsonError('参数无效');
        endif;

    }

    // 获取地区数据
    public function provinceListAction()
    {
        $provinces = DataRegionModel::model()->getProvince();
        $this->showJson($provinces);

    }

    // 获取下级城市
    public function subRegionListAction()
    {
        if ($pid = arRequest('pid')) :
            $region = DataRegionModel::model()->getAllreginByPid($pid, false);
            $this->showJson($region);
        else :
            $this->showJsonError('param invalid');
        endif;

    }

    // 获取地区县级下面的学校
    public function schoolsAction()
    {
        // 地区id
        if ($aid = arRequest('aid')) :
            $schools = DataMidSchoolModel::model()
                ->getDb()
                ->where(array('area' => $aid))
                ->queryAll();
            $this->showJson($schools);
        else :
            $this->showJsonError('param invalid');
        endif;

    }

    // 提前批推荐学校
    public function zySchoolListTqpAction()
    {
        // 志愿
        if (($score = arRequest('score')) && $type = arRequest('type')) :
            // 默认理科的一本
            $km = arRequest('km', DataYxModel::LK);
            $type = strtoupper($type);
            $pc = DataYxModel::PC_TQ;

            $tableName = DataYxModel::getTableName($km, $pc);
            // 志愿推荐方法
            switch ($type) {
                case 'A':
                case 'B1':
                case 'B2':
                    $minit = arCfg('FORMULA.m');
                    // 获取返回条数 没有则取配置
                    $limit = arCfg('FORMULA.k');
                    $condition = array();
                    // 是否有学校名字
                    if ($sname = arRequest('sname')) :
                        $condition['name like '] = '%' . $sname . '%';
                    endif;

                    // 地区匹配
                    if ($area = arRequest('area')) :
                        $area = str_replace('省', '', urldecode($area));
                        if (strpos($area, ',')) :
                            $condition['dy'] = explode(',', $area);
                        else :
                            $condition['dy'] = $area;
                        endif;
                    endif;

                    // 提前批算法
                    $jzfc = arModule('Data')->jzfctqp($score, $type);
                    // 算法文档逻辑
                    for ($m = $minit; $m <= arCfg('FORMULA.mmax'); $m++) :
                        // 轮训数据库 16预估分
                        $condition['plqf16 >='] = $jzfc - $m  < 0 ? 0 : $jzfc - $m;
                        $condition['plqf16 <='] = $jzfc + $m;

                        $columns = 'name,dy,pm,pc,lqf15,lqf14,lqf13,plqf16,zsjh15,zsjh14,zsjh13,fc15,fc14,fc13,pfc16';
                        if ($this->ifVip()) :
                            $columns .= ',lqwc14,lqwc13,lqwc15';
                        endif;
                        $schools = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->select($columns)
                            ->where($condition)
                            ->order('plqf16 asc')
                            ->limit($limit)
                            ->queryAll();

                        // 实际条数
                        $actualTotalNums = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->where($condition)
                            ->count();
                        // 判断配置k值
                        if (count($schools) >= $limit) :
                            // 满足k 退出执行
                            break;
                        elseif (!$schools) :
                            $schools = array();
                        endif;
                    endfor;
                    // $schools['actual_nums'] = $actualTotalNums;
                    // 限制条目
                    $limit = arCfg('LIMIT.NUMS');


                    if (!$this->ifVip() && count($schools) > $limit) :
                        // 限三次访问全部数据
                        $uid = arComp('list.session')->get('uid');
                        $searchTimekey = $uid . 'timekey';
                        $stimes = arComp('cache.file')->get($searchTimekey);
                        if ($stimes >= 3) :
                            $schoolsTemp = array();
                            for ($i = 0; $i < $limit; $i++) :
                                $schoolsTemp[$i] = $schools[$i];
                            endfor;
                            $schools = $schoolsTemp;
                        else :
                            $stimes += 1;
                            arComp('cache.file')->set($searchTimekey, $stimes);
                        endif;
                    endif;
                    // 计算概率
                    foreach ($schools as &$school) :
                        $percent = $this->admissionOdds($score, $school['pfc16']);
                        $school['odds'] = $percent['percent'];
                    endforeach;
                    $this->showJson($schools, array('actual_nums' => $actualTotalNums));
                    break;
                default:
                    return $this->showJsonError('志愿类型错误');
                    break;
            }
        else :
            $this->showJsonError('分数，科目，类型，批次均不能为空');
        endif;
    }

     // 提前批推荐学校
    public function zySchoolListTqpRelAction()
    {
       Ar::setConfig('isgk', 1);
        // 志愿
        if (($score = arRequest('score')) && $type = arRequest('type')) :
            // 默认理科的一本
            $km = arRequest('km', DataYxModel::LK);
            $type = strtoupper($type);
            $pc = DataYxModel::PC_TQ;

            $tableName = DataYxModel::getTableName($km, $pc);
            // 志愿推荐方法
            switch ($type) {
                case 'A':
                case 'B1':
                case 'B2':
                    // 获取返回条数 没有则取配置
                    $limitK = arCfg('FORMULA.k_r');
                    $condition = array();
                    // 是否有学校名字
                    if ($sname = arRequest('sname')) :
                        $condition['name like '] = '%' . $sname . '%';
                    endif;
                    // 地区匹配
                    if ($area = arRequest('area')) :
                        $area = str_replace('省', '', urldecode($area));
                        if (strpos($area, ',')) :
                            $condition['dy'] = explode(',', $area);
                        else :
                            $condition['dy'] = $area;
                        endif;
                    endif;
                    // 获取基准分差
                    $jzwc = arModule('Data')->jzwctqp($score, $km, $pc, $type);
                    $minit = arCfg('FORMULA.m_r');
                    $maxExitCount = 0;
                    // 算法文档逻辑
                    for ($m = $minit; ; ) :
                        $maxExitCount++;
                        if ($maxExitCount > 500) :
                            break;
                        endif;
                        // 轮训数据库 16预估位次
                        $condition['plqwc16 >='] = $jzwc - $m  < 0 ? 0 : $jzwc - $m;
                        $condition['plqwc16 <='] = $jzwc + $m;
                        $columns = 'plqwc16,name,dy,pm,pc,lqf15,lqf14,lqf13,plqf16,zsjh15,zsjh14,zsjh13,fc15,fc14,fc13,pfc16';
                        if ($this->ifVip()) :
                            $columns .= ',lqwc14,lqwc13,lqwc15';
                        endif;
                        // var_dump($condition);
                        $schools = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->select($columns)
                            ->where($condition)
                            // 这句要实现私有阶梯排序算法
                            // ->limit($limit)
                            ->order('pm asc,plqwc16 asc')
                            ->queryAll();

                        $lc = count($schools);
                        // var_dump($lc,$actualTotalNums);

                        // m扩大
                        $m = $m + $minit;

                        // 不加此条件 将卡死
                        if ($m > arCfg('LIMIT.MAX_M_Q')) :
                            break;
                        endif;

                        // 判断配置k值
                        if (count($schools) >= $limitK) :
                            // 满足k 退出执行 (条件1)
                            break;
                        elseif ($schools) :
                            // $lqfarr = array();
                            // for ($i = 0; $i < count($schools); $i++) :
                            //     $lqfarr[] = $schools[$i]['plqf16'];
                            // endfor;
                            // // 条件2：( max(k所学校的“16预估录取分”) - min(k所学校的“16预估录取分”) ) >= 200。
                            // if (max($lqfarr) - min($lqfarr) >= arCfg('FORMULA.mmax_r')) :
                            //     break;
                            // endif;
                        elseif (!$schools) :
                            $schools = array();
                        endif;
                    endfor;

                    // 限制条目
                    $limit = arCfg('LIMIT.NUMS');
                    if (!$this->ifVip() && count($schools) > $limit) :
                        $schoolsTemp = array();
                        for ($i = 0; $i < $limit; $i++) :
                            $schoolsTemp[$i] = $schools[$i];
                        endfor;
                        $schools = $schoolsTemp;
                    endif;
                     // 计算概率
                    foreach ($schools as &$school) :
                        $percent = $this->admissionOdds($score, $school['pfc16']);
                        $school['odds'] = $percent['percent'];
                    endforeach;

                    if (count($schools) >= $limitK) :
                        $actualTotalNums = $limitK;
                        $schools = $this->sortByhalf($schools, $limitK, 'plqwc16', $jzwc);
                    else :
                        $actualTotalNums = count($schools);
                    endif;

                    $this->showJson($schools, array('actual_nums' => $actualTotalNums));

                    break;
                default:
                    return $this->showJsonError('志愿类型错误');
                    break;
            }
        else :
            $this->showJsonError('分数，科目，类型，批次均不能为空');
        endif;

    }

    // 重点批推荐学校
    public function zySchoolListAction()
    {
        // 志愿
        if (($score = arRequest('score')) && $type = arRequest('type')) :
            // 默认理科的一本
            $km = arRequest('km', DataYxModel::LK);
            $type = strtoupper($type);
            if (arRequest('pc') === null) :
                $pc = DataYxModel::PC_YIBEN;
            else :
                $pc = arRequest('pc');
            endif;
            $tableName = DataYxModel::getTableName($km, $pc);
            // 志愿推荐方法
            switch ($type) {
                case 'A':
                case 'B':
                case 'C':
                case 'D':
                case 'E':
                case 'F':
                    $minit = arCfg('FORMULA.m');
                    // 获取返回条数 没有则取配置
                    $limit = arCfg('FORMULA.k');
                    $condition = array();
                    // 是否有学校名字
                    if ($sname = arRequest('sname')) :
                        $condition['name like '] = '%' . $sname . '%';
                    endif;

                    // 地区匹配
                    if ($area = arRequest('area')) :
                        $area = str_replace('省', '', urldecode($area));
                        if (strpos($area, ',')) :
                            $condition['dy'] = explode(',', $area);
                        else :
                            $condition['dy'] = $area;
                        endif;
                    endif;

                    // 获取基准分差
                    $jzfc = arModule('Data')->jzfc($score, $km, $pc, $type);
                    // 算法文档逻辑
                    for ($m = $minit; $m <= arCfg('FORMULA.mmax'); $m++) :
                        // 轮训数据库 16分差字段
                        $condition['pfc16 >='] = $jzfc - $m  < 0 ? 0 : $jzfc - $m;
                        $condition['pfc16 <='] = $jzfc + $m;

                        $columns = 'plqwc16,name,dy,pm,pc,lqf15,lqf14,lqf13,plqf16,zsjh15,zsjh14,zsjh13,fc15,fc14,fc13,pfc16';
                        if ($this->ifVip()) :
                            $columns .= ',lqwc14,lqwc13,lqwc15';
                        endif;
                        $schools = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->select($columns)
                            ->where($condition)
                            ->order('plqf16 asc')
                            ->limit($limit)
                            ->queryAll();

                        // 实际条数
                        $actualTotalNums = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->where($condition)
                            ->count();
                        // 判断配置k值
                        if (count($schools) >= $limit) :
                            // 满足k 退出执行
                            break;
                        elseif (!$schools) :
                            $schools = array();
                        endif;
                    endfor;
                    // $schools['actual_nums'] = $actualTotalNums;
                    // 限制条目
                    $limit = arCfg('LIMIT.NUMS');


                    if (!$this->ifVip() && count($schools) > $limit) :
                        // 限三次访问全部数据
                        $uid = arComp('list.session')->get('uid');
                        $searchTimekey = $uid . 'timekey';
                        $stimes = arComp('cache.file')->get($searchTimekey);
                        if ($stimes >= 3) :
                            $schoolsTemp = array();
                            for ($i = 0; $i < $limit; $i++) :
                                $schoolsTemp[$i] = $schools[$i];
                            endfor;
                            $schools = $schoolsTemp;
                        else :
                            $stimes += 1;
                            arComp('cache.file')->set($searchTimekey, $stimes);
                        endif;
                    endif;
                    // 计算概率
                    foreach ($schools as &$school) :
                        $percent = $this->admissionOdds($score, $school['pfc16']);
                        $school['odds'] = $percent['percent'];
                    endforeach;
                    $this->showJson($schools, array('actual_nums' => $actualTotalNums));
                    break;
                default:
                    return $this->showJsonError('志愿类型错误');
                    break;
            }
        else :
            $this->showJsonError('分数，科目，类型，批次均不能为空');
        endif;

    }

    // 冒泡算法
    public function bubbleSort($dataBundles, $field)
    {
        // 冒泡$field
        $bundleTotalCount = count($dataBundles);
        for ($i = 1; $i < $bundleTotalCount; $i++) :
            for ($k = 0; $k < $bundleTotalCount - $i; $k++) :
                if ($dataBundles[$k][$field] > $dataBundles[$k + 1][$field]) :
                    $tempK = $dataBundles[$k + 1];
                    $dataBundles[$k + 1] = $dataBundles[$k];
                    $dataBundles[$k] = $tempK;
                endif;
            endfor;
        endfor;
        return $dataBundles;

    }

    // 排序组合新的数组逻辑
    public function sortByhalf($dataBundles, $kvalue, $field, $baseValue)
    {

/*
        if ($bundleTotalCount > $kvalue) :
            $dataBundles = array_slice($dataBundles, 0, $kvalue);
            $bundleTotalCount = $kvalue;
        endif;
*/

        $dataBundles = $this->bubbleSort($dataBundles, $field);

        $tempDataBundles = array();
        // 两边排列
        foreach ($dataBundles as $dBundle) :
            if ($dBundle[$field] < $baseValue) :
                $tempDataBundles['left'][] = $dBundle;
            else :
                $tempDataBundles['right'][] = $dBundle;
            endif;
        endforeach;

        // 再进行排序
        $tempDataBundles['left'] = $this->bubbleSort($tempDataBundles['left'], $field);
        $tempDataBundles['right'] = $this->bubbleSort($tempDataBundles['right'], $field);

        arComp('list.log')->record($tempDataBundles['left'], 'left');
        arComp('list.log')->record($tempDataBundles['right'], 'right');


        $halfSepLeftValue = (int)($kvalue / 2);
        $halfSepRightValue = $kvalue - $halfSepLeftValue;

        // 快速排序类似算法
        $sortBundeles = array();
        // 实现左边序列
        if (count($tempDataBundles['left']) < $halfSepLeftValue) :
            $sortBundeles = $tempDataBundles['left'];
            $halfSepRightValue = $kvalue - count($tempDataBundles['left']);
        else :
            // $halfSepLeftValue = count($tempDataBundles['left']);
            $tempDataBundles['left'] = array_slice($tempDataBundles['left'], -$halfSepLeftValue);
            // $halfSepRightValue = $kvalue - count($tempDataBundles['left']);
            for ($counterLeft = 0; $counterLeft < $halfSepLeftValue; $counterLeft++) :
                if (isset($tempDataBundles['left'][$counterLeft])) :
                    $sortBundeles[$counterLeft] = $tempDataBundles['left'][$counterLeft];
                endif;
            endfor;
        endif;

        // 右边排序
        for ($counterRight = 0; $counterRight < $halfSepRightValue; $counterRight++) :
            if (isset($tempDataBundles['right'][$counterRight])) :
                $sortBundeles[] = $tempDataBundles['right'][$counterRight];
            endif;
        endfor;

        // 概率排序
        $sortBundeles = $this->sortByOdds($sortBundeles);

        return $sortBundeles;

    }

    // 概率排序
    public function sortByOdds($sortBundeles)
    {
        $countSortBundle = count($sortBundeles);
        // 录取概率排序倒叙
        for ($i = 1; $i < $countSortBundle; $i++) :
            for ($k = 0; $k < $countSortBundle - $i; $k++) :
                if ($sortBundeles[$k]['odds'] < $sortBundeles[$k + 1]['odds']) :
                    $tempK = $sortBundeles[$k + 1];
                    $sortBundeles[$k + 1] = $sortBundeles[$k];
                    $sortBundeles[$k] = $tempK;
                endif;
            endfor;
        endfor;
        return $sortBundeles;

    }

    // 重点批推荐学校(高考成绩)
    public function zySchoolListRelAction()
    {
        Ar::setConfig('isgk', 1);
        // 志愿
        if (($score = arRequest('score')) && $type = arRequest('type')) :
            // 默认理科的一本
            $km = arRequest('km', DataYxModel::LK);
            $type = strtoupper($type);
            if (arRequest('pc') === null) :
                $pc = DataYxModel::PC_YIBEN;
            else :
                $pc = arRequest('pc');
            endif;
            $tableName = DataYxModel::getTableName($km, $pc);
            // 志愿推荐方法
            switch ($type) {
                case 'A':
                case 'B':
                case 'C':
                case 'D':
                case 'E':
                case 'F':
                    // 获取返回条数 没有则取配置
                    $limitK = arCfg('FORMULA.k_r');
                    $condition = array();
                    // 是否有学校名字
                    if ($sname = arRequest('sname')) :
                        $condition['name like '] = '%' . $sname . '%';
                    endif;
                    // 地区匹配
                    if ($area = arRequest('area')) :
                        $area = str_replace('省', '', urldecode($area));
                        if (strpos($area, ',')) :
                            $condition['dy'] = explode(',', $area);
                        else :
                            $condition['dy'] = array($area);
                        endif;
                    endif;
                    // 获取基准分差
                    $jzwc = arModule('Data')->jzwc($score, $km, $pc, $type);
                    $minit = arCfg('FORMULA.m_r');
                    // 算法文档逻辑
                    for ($m = $minit; ; ) :
                        // 轮训数据库 16预估位次
                        // $condition['plqwc16 >='] = $jzwc - $m  < 0 ? 0 : $jzwc - $m;
                        // $condition['plqwc16 <='] = $jzwc + $m;
                        $columns = 'plqwc16,name,dy,pm,pc,lqf15,lqf14,lqf13,plqf16,zsjh15,zsjh14,zsjh13,fc15,fc14,fc13,pfc16';
                        if ($this->ifVip()) :
                            $columns .= ',lqwc14,lqwc13,lqwc15';
                        endif;
                        $schools = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->select($columns)
                            ->where($condition)
                            // 这句要实现私有阶梯排序算法
                            // ->limit($limit)
                            // ->order('pm asc,plqf16 asc')
                            // ->order('plqwc16 desc')
                            ->queryAll();
                        // 不依赖数据库算法
                        break;
                        /*

                        $lc = count($schools);
                        // var_dump($lc,$actualTotalNums);

                        // m扩大
                        $m = $m * 2;

                        // 不加此条件 将卡死
                        if ($m > arCfg('LIMIT.MAX_M_Q')) :
                            break;
                        endif;

                        // 判断配置k值
                        if (count($schools) >= $limitK) :
                            // 满足k 退出执行 (条件1)
                            break;
                        elseif ($schools) :
                            $lqfarr = array();
                            for ($i = 0; $i < count($schools); $i++) :
                                $lqfarr[] = $schools[$i]['plqf16'];
                            endfor;
                            // 条件2：( max(k所学校的“16预估录取分”) - min(k所学校的“16预估录取分”) ) >= 200。
                            if (max($lqfarr) - min($lqfarr) >= arCfg('FORMULA.mmax_r')) :
                                break;
                            endif;
                        elseif (!$schools) :
                            $schools = array();
                        endif;
                        */
                    endfor;


                     // 计算概率
                    foreach ($schools as &$school) :
                        $percent = $this->admissionOdds($score, $school['pfc16']);
                        $school['odds'] = $percent['percent'];
                    endforeach;

                    if (count($schools) >= $limitK) :
                        $actualTotalNums = $limitK;
// arComp('list.log')->record($schools,'zysc');
// arComp('list.log')->record(array($jzwc, $limitK),'jzwc');
                        $schools = $this->sortByhalf($schools, $limitK, 'plqwc16', $jzwc);
                    else :
                        // 默认从上到下
                        $schools = $this->sortByOdds($schools);
                        $actualTotalNums = count($schools);
                    endif;

                    // 限制条目
                    $limit = arCfg('LIMIT.NUMS');
                    if (!$this->ifVip() && count($schools) > $limit) :
                        $schoolsTemp = array();
                        for ($i = 0; $i < $limit; $i++) :
                            $schoolsTemp[$i] = $schools[$i];
                        endfor;
                        $schools = $schoolsTemp;
                    endif;

                    $this->showJson($schools, array('actual_nums' => $actualTotalNums));

                    break;
                default:
                    return $this->showJsonError('志愿类型错误');
                    break;
            }
        else :
            $this->showJsonError('分数，科目，类型，批次均不能为空');
        endif;

    }

    // 备选学校推荐
    public function zySchoolListBxAction()
    {
        // 志愿
        if (($score = arRequest('score')) && $type = arRequest('type')) :
            // 默认理科的一本
            $km = arRequest('km', DataYxModel::LK);
            $type = strtoupper($type);
            if (arRequest('pc') === null) :
                $pc = DataYxModel::PC_YIBEN;
            else :
                $pc = arRequest('pc');
            endif;
            $tableName = DataYxModel::getTableName($km, $pc);
            // 志愿推荐方法
            switch ($type) {
                case 'A':
                case 'B':
                case 'C':
                case 'D':
                case 'E':
                case 'F':
                    $minit = arCfg('FORMULA.m');
                    // 获取返回条数 没有则取配置
                    $limit = arCfg('FORMULA.k');
                    $condition = array();
                    // 是否有学校名字
                    if ($sname = arRequest('sname')) :
                        $condition['name like '] = '%' . $sname . '%';
                    endif;

                    // 地区匹配
                    if ($area = arRequest('area')) :
                        $area = str_replace('省', '', urldecode($area));
                        if (strpos($area, ',')) :
                            $condition['dy'] = explode(',', $area);
                        else :
                            $condition['dy'] = $area;
                        endif;
                    endif;

                    // 获取基准分差
                    $jzfc = arModule('Data')->jzfcbx($score, $km, $pc, $type);
                    // 算法文档逻辑
                    for ($m = $minit; $m <= arCfg('FORMULA.mmax'); $m++) :
                        // 轮训数据库 16分差字段
                        $condition['pfc16 >='] = $jzfc - $m  < 0 ? 0 : $jzfc - $m;
                        $condition['pfc16 <='] = $jzfc + $m;
                        $columns = 'plqwc16,name,dy,pm,pc,lqf15,lqf14,lqf13,plqf16,zsjh15,zsjh14,zsjh13,fc15,fc14,fc13,pfc16';
                        if ($this->ifVip()) :
                            $columns .= ',lqwc14,lqwc13,lqwc15';
                        endif;
                        $schools = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->select($columns)
                            ->where($condition)
                            ->limit($limit)
                            ->queryAll();
                        // 实际条数
                        $actualTotalNums = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->where($condition)
                            ->count();
                            // echo DataYxModel::model()->getDb()->lastSql;
                        // 判断配置k值
                        if (count($schools) >= $limit) :
                            // 满足k 退出执行
                            break;
                        elseif (!$schools) :
                            $schools = array();
                        endif;
                    endfor;
                    // 限制条目
                    $limit = arCfg('LIMIT.NUMS');
                    if (!$this->ifVip() && count($schools) > $limit) :
                        $schoolsTemp = array();
                        for ($i = 0; $i < $limit; $i++) :
                            $schoolsTemp[$i] = $schools[$i];
                        endfor;
                        $schools = $schoolsTemp;
                    endif;
                    // 计算概率
                    foreach ($schools as &$school) :
                        $percent = $this->admissionOdds($score, $school['pfc16']);
                        $school['odds'] = $percent['percent'];
                    endforeach;
                    $this->showJson($schools, array('actual_nums' => $actualTotalNums));
                    break;
                default:
                    return $this->showJsonError('志愿类型错误');
                    break;
            }
        else :
            $this->showJsonError('分数，科目，类型，批次均不能为空');
        endif;

    }

    // 备选推荐学校(高考成绩)
    public function zySchoolListBxRelAction()
    {
        Ar::setConfig('isgk', 1);
        // 志愿
        if (($score = arRequest('score')) && $type = arRequest('type')) :
            // 默认理科的一本
            $km = arRequest('km', DataYxModel::LK);
            $type = strtoupper($type);
            if (arRequest('pc') === null) :
                $pc = DataYxModel::PC_YIBEN;
            else :
                $pc = arRequest('pc');
            endif;
            $tableName = DataYxModel::getTableName($km, $pc);
            // 志愿推荐方法
            switch ($type) {
                case 'A':
                case 'B':
                case 'C':
                case 'D':
                case 'E':
                case 'F':
                    // 获取返回条数 没有则取配置
                    $limitK = arCfg('FORMULA.k_r');
                    $condition = array();
                    // 是否有学校名字
                    if ($sname = arRequest('sname')) :
                        $condition['name like '] = '%' . $sname . '%';
                    endif;
                    // 地区匹配
                    if ($area = arRequest('area')) :
                        $area = str_replace('省', '', urldecode($area));
                        if (strpos($area, ',')) :
                            $condition['dy'] = explode(',', $area);
                        else :
                            $condition['dy'] = $area;
                        endif;
                    endif;
                    // 获取基准分差
                    $jzwc = arModule('Data')->jzwcbx($score, $km, $pc, $type);
                    $minit = arCfg('FORMULA.m_r');
                    // 算法文档逻辑
                    for ($m = $minit; ; ) :
                        // 轮训数据库 16预估位次
                        // $condition['plqwc16 >='] = $jzwc - $m  < 0 ? 0 : $jzwc - $m;
                        // $condition['plqwc16 <='] = $jzwc + $m;
                        $columns = 'plqwc16,name,dy,pm,pc,lqf15,lqf14,lqf13,plqf16,zsjh15,zsjh14,zsjh13,fc15,fc14,fc13,pfc16';
                        if ($this->ifVip()) :
                            $columns .= ',lqwc14,lqwc13,lqwc15';
                        endif;
                        $schools = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            ->select($columns)
                            ->where($condition)
                            //->limit($limitK)
                            // ->order('plqf16 asc')
                            //->order('plqwc16 desc')
                            ->queryAll();
                        // 实际条数
                        $actualTotalNums = DataYxModel::model()
                            ->getDb()
                            ->table($tableName)
                            //->where($condition)
                            ->count();
                        // 不依赖数据库算法
                        break;

                        /*
                        // m扩大
                        $m = $m * 2;

                        // 不加此条件 将卡死
                        if ($m > arCfg('LIMIT.MAX_M_Q')) :
                            break;
                        endif;

                        // 判断配置k值
                        if (count($schools) >= $limitK) :
                            // 满足k 退出执行 (条件1)
                            break;
                        elseif ($schools) :
                            $lqfarr = array();
                            for ($i = 0; $i < count($schools); $i++) :
                                $lqfarr[] = $schools[$i]['plqf16'];
                            endfor;
                            // 条件2：( max(k所学校的“16预估录取分”) - min(k所学校的“16预估录取分”) ) >= 10。
                            if (max($lqfarr) - min($lqfarr) >= arCfg('FORMULA.mmax_r')) :
                                break;
                            endif;
                        elseif (!$schools) :
                            $schools = array();
                        endif;
                        */
                    endfor;


                    if ($actualTotalNums > $limitK) :
                        $actualTotalNums = $limitK;
                    endif;

                    // 计算概率
                    foreach ($schools as &$school) :
                        $percent = $this->admissionOdds($score, $school['pfc16']);
                        $school['odds'] = $percent['percent'];
                    endforeach;

                    if (count($schools) >= $limitK) :
                        $actualTotalNums = $limitK;
// arComp('list.log')->record($schools, 'zybx');
// arComp('list.log')->record(array($limitK, $jzwc), 'zybxjzwc');
                        $schools = $this->sortByhalf($schools, $limitK, 'plqwc16', $jzwc);
                    else :
                        // 默认从上到下
                        $schools = $this->sortByOdds($schools);
                        $actualTotalNums = count($schools);
                    endif;

                    // 限制条目
                    $limit = arCfg('LIMIT.NUMS');
                    if (!$this->ifVip() && count($schools) > $limit) :
                        $schoolsTemp = array();
                        for ($i = 0; $i < $limit; $i++) :
                            $schoolsTemp[$i] = $schools[$i];
                        endfor;
                        $schools = $schoolsTemp;
                    endif;

                    $this->showJson($schools, array('actual_nums' => $actualTotalNums));
                    break;
                default:
                    return $this->showJsonError('志愿类型错误');
                    break;
            }
        else :
            $this->showJsonError('分数，科目，类型，批次均不能为空');
        endif;

    }

    // 批次信息
    public function pcinfoAction()
    {
        if ($score = arRequest('score')) :
            if (arRequest('isgk') == 1) :
                $isgk = true;
            else :
                $isgk = false;
            endif;
            // 默认理科
            $km = arRequest('km', DataYxModel::LK);
            $pcInfo = arModule('Data')->getFdInfo($score, $km, $isgk);

            if ($pcInfo) :
                $this->showJson($pcInfo);
            else :
                $this->showJsonError('你所填的分数不符合最低要求');
            endif;
        else :
            $this->showJsonError('分数，科目均不能为空');
        endif;

    }


    // 获得专业
    public function zhuanyeListAction()
    {
        if (is_numeric(arRequest('pc'))) :
             $pc = arRequest('pc');
        endif;

        if (isset($pc)) :
            $sname = arRequest('sname');
            $sname = urldecode($sname);
            // 默认理科
            $km = arRequest('km', DataYxModel::LK);
            $condition = array();
            // 是否有学校名字
            if ($sname) :
                $condition['yxname'] = $sname;
            endif;

            // 专业匹配
            if ($zyname = arRequest('zyname')) :
                $condition['zyname'] = urldecode($zyname);
            endif;

            // 年份匹配
            if ($year = arRequest('year', '2015')) :
                $condition['year'] = $year;
            endif;
            // 获取专业表名
            $tableName = arModule('Data')->getZyTableName($km, $pc);
            $columns = 'yxname,zyname,pc,lb,lqf14 as slqf,lqf14,jf14 as sjf,jf14,year,lqxc,lqrs';
            if ($this->ifVip()) :
                $columns .= ',lqwc14 as slqwc,lqwc14';
            endif;
            $zyLists = DataYxModel::model()
                ->getDb()
                ->select($columns)
                ->table($tableName)
                ->where($condition)
                ->queryAll();
            if ($zyLists) :
                $this->showJson($zyLists);
            else :
                $this->showJsonError('记录为空');
            endif;
        else :
            $this->showJsonError('学校和批次不能为空');
        endif;

    }

    // 获取院校录取情况
    public function yxzsAction()
    {
        if (!$sname = trim(urldecode(arRequest('sname')))) :
            return $this->showJsonError('学校不能为空');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
        endif;

        if (arRequest('pc') === null) :
            return $this->showJsonError('请选择批次');
        else :
            $pc = arRequest('pc');
        endif;
        $tableName = DataYxModel::getTableName($km, $pc);
        $condition = array();
        $condition['name'] = $sname;
        $columns = 'fc15,fc14,fc13,jfxc15,jfxc14,jfxc13,jf15,jf14,jf13,lqf15,lqf14,lqf13,zsjh15,zsjh14,zsjh13';
        if ($this->ifVip()) :
            $columns .= ',lqwc15,lqwc14,lqwc13';
        endif;
        $yxInfo = DataYxModel::model()
            ->getDb()
            ->select($columns)
            ->table($tableName)
            ->where($condition)
            ->queryRow();

        if ($yxInfo) :
            // 查询多少个招生专业
            $tableName = arModule('Data')->getZyTableName($km, $pc);
            $zyCondition = array(
                'yxname' => $sname,
            );
            $yxInfo['zycounts'] = ArModel::model()->getDb()
                ->table($tableName)
                ->where($zyCondition)
                ->count();
            $this->showJson($yxInfo);
        else :
            return $this->showJsonError('记录为空');
        endif;

    }

    // 获取院校录取情况
    public function zyzsAction()
    {
        if (!$sname = trim(urldecode(arRequest('sname')))) :
            return $this->showJsonError('学校不能为空');
        endif;

        if (!$zyname = trim(urldecode(arRequest('zyname')))) :
            return $this->showJsonError('专业不能为空');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
        endif;

        if (arRequest('pc') === null) :
            return $this->showJsonError('请选择批次');
        else :
            $pc = arRequest('pc');
        endif;
        $tableName = arModule('Data')->getZyTableName($km, $pc);
        $condition = array();
        $condition['zyname'] = $zyname;
        $condition['yxname'] = $sname;

        $zyInfo = DataYxModel::model()
            ->getDb()
            ->select('pc,lqf14,jf14,lb')
            ->table($tableName)
            ->where($condition)
            ->queryRow();
        if ($zyInfo) :
            $this->showJson($zyInfo);
        else :
            $this->showJsonError('记录为空');
        endif;

    }

    // 包含专业的学校
    public function getCollegesByZyAction()
    {
        $condition = array();
        if (!$zyname = trim(urldecode(arRequest('zyname')))) :
            return $this->showJsonError('专业不能为空');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
        endif;

        if (arRequest('pc') === null) :
            return $this->showJsonError('请选择批次');
        else :
            $pc = arRequest('pc');
        endif;

        // 地区
        if ($area = arRequest('area')) :
            // $condition['']
        endif;

        $tableName = arModule('Data')->getZyTableName($km, $pc);
        $condition['zyname'] = $zyname;

        $zyInfo = DataYxModel::model()
            ->getDb()
            ->select('yxname,pc,lqf14,jf14,lb')
            ->table($tableName)
            ->where($condition)
            ->queryAll();
        if ($zyInfo) :
            $this->showJson($zyInfo);
        else :
            $this->showJsonError('记录为空');
        endif;

    }

    // 是否登陆
    public function ifloginAction()
    {
        if (arComp('list.session')->get('uid')) :
            $this->showJsonSuccess();
        else :
            $this->showJsonError('未登陆');
        endif;

    }

    // 根据地区查学校
    public function areaSchoolsAction()
    {
        if (!$area = trim(urldecode(arRequest('area')))) :
            return $this->showJsonError('地区不能为空');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
        endif;


        if (arRequest('pc') === null) :
            return $this->showJsonError('请选择批次');
        else :
            $pc = arRequest('pc');
        endif;
        $tableName = DataYxModel::getTableName($km, $pc);
        $condition = array();

        if ($sname = arRequest('sname')) :
            $condition['name like '] = '%' . urldecode($sname) . '%';
        endif;

        $condition['dy like '] = '%' . str_replace('省', '', $area) . '%';

        $yxInfo = DataYxModel::model()
            ->getDb()
            ->select('name,jf15,jf14,jf13,lqf15,lqf14,lqf13,zsjh15,zsjh14,zsjh13')
            ->table($tableName)
            ->where($condition)
            ->queryAll();
        // 获取学校的cid
        foreach ($yxInfo as &$yx) :
            $yx['cid'] = CollegesModel::model()
                ->getDb()
                ->where(array('sname' =>  preg_replace('#\(.*\)#', '', $yx['name'])))
                ->queryColumn('cid');
        endforeach;
        if ($yxInfo) :
            $this->showJson($yxInfo);
        else :
            return $this->showJsonError('记录为空');
        endif;

    }

    // 查学校里的所有专业
    public function schoolMajorsAction()
    {
        $condition = array();
        if (!$sname = trim(urldecode(arRequest('sname')))) :
            return $this->showJsonError('学校不能为空');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
        endif;

        if (arRequest('pc') === null) :
            return $this->showJsonError('请选择批次');
        else :
            $pc = arRequest('pc');
        endif;

        $tableName = arModule('Data')->getZyTableName($km, $pc);
        $condition['yxname'] = $sname;

        $zyInfo = DataYxModel::model()
            ->getDb()
            ->select('yxname,zyname,pc,lqf14,jf14,lb')
            ->table($tableName)
            ->where($condition)
            ->queryAll();
        if ($zyInfo) :
            $this->showJson($zyInfo);
        else :
            $this->showJsonError('记录为空');
        endif;

    }

    // 判断手机号是否可以用
    public function ifMobieCanUseAction()
    {
        if (arRequest('checkcode') == 1) :
            if ($ckcode = arRequest('ckey')) :

                if (strtoupper($ckcode) != arComp('list.session')->get('ckey')) :
                    return $this->showJsonError('验证码错误');
                endif;
            else :
                return $this->showJsonError('验证码不能为空');
            endif;
        endif;

        if ($mobile = arRequest('mobile')) :
            $records = UserModel::model()->getDb()->where(array('phone' => $mobile))->queryRow();
            if ($records) :
                if (arRequest('type') == 'reset') :
                    if ($records['type'] == 1) :
                        return $this->showJsonError('此电话号码已与其他卡号绑定了VIP');
                    else :
                        return $this->showJsonSuccess('电话可以注册');
                    endif;
                endif;
                $this->showJsonError('电话已注册');
            else :
                $this->showJsonSuccess('电话可以注册');
            endif;
        else :
            $this->showJsonError();
        endif;

    }

    // 发送手机绑定验证码
    public function sendPhoneCpCodeAction()
    {
        arLm('main.Module');
        $code = arModule('Vip')->randpw('4', 'NUMBER');
        $sendto = arRequest('phone');
        if (UserModel::model()->getDb()->where(array('phone' => $sendto))->count() > 0) :
            if ($sendto) :
                $sendSuccess = arModule('Sms')->juhecurl($sendto, $code, '6311');
                if ($sendSuccess) :
                    arComp('list.session')->set('phone_cp_code', $code);
                    $this->showJsonSuccess();
                else :
                    $this->showJsonError();
                endif;
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError('电话号码不存在');
        endif;

    }

    // 改变密码
    public function changePwdAction()
    {
        $sendto = arRequest('phone');
        $newPwd = arRequest('new_pwd');
        $code = arRequest('code');
        if (!$sendto || !$newPwd || !$code) :
            return $this->showJsonError('电话号码，新密码，验证码不能为空');
        endif;
        $sCode = arComp('list.session')->get('phone_cp_code');
        if (UserModel::model()->getDb()->where(array('phone' => $sendto))->count() > 0) :
            if ($sCode && $code == $sCode) :
                $pwd = UserModel::gPwd($newPwd);
                UserModel::model()->getDb()->where(array('phone' => $sendto))->update(array('pwd' => $pwd));
                arComp('list.session')->set('phone_cp_code', null);
                $this->showJsonSuccess('密码修改成功，请重新登陆');
            else :
                $this->showJsonError('验证码错误');
            endif;
        else :
            $this->showJsonError('电话号码不存在');
        endif;

    }

    // 发送手机绑定验证码
    public function sendPhoneBdCodeAction()
    {
        arLm('main.Module');
        $code = arModule('Vip')->randpw('4', 'NUMBER');
        $sendto = arRequest('phone');
        if ($sendto) :
            $sendSuccess = arModule('Sms')->juhecurl($sendto, $code, '6947');
            if ($sendSuccess) :
                arComp('list.session')->set('phone_bd_code', $code);
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError();
        endif;

    }

    // 绑定手机
    public function bdphoneAction()
    {
        if ($uid = arComp('list.session')->get('uid')) :
            if (($mobile = arRequest('mobile')) && (arRequest('code') == arComp('list.session')->get('phone_bd_code'))) :
                UserModel::model()->getDb()->where(array('uid' => $uid))->update(array('phone' => $mobile));
                arComp('list.session')->set('phone_bd_code', null);
                return $this->showJsonSuccess('绑定成功');
            else :
                return $this->showJsonError('验证码错误');
            endif;
        else :
            return $this->showJsonError('尚未登陆');
        endif;

    }

    // 专业查询 查询库
    public function zyQueryAction()
    {
        $condition = array();
        if (!$zyname = trim(urldecode(arRequest('zyname')))) :
            return $this->showJsonError('专业不能为空');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
            if ($km == 0) :
                $tableName = 'data_zy_query_lk';
            else :
                $tableName = 'data_zy_query_wk';
            endif;
        endif;

        if (arRequest('year') === null) :
            return $this->showJsonError('年份必填');
        else :
            $condition['year'] = arRequest('year');
        endif;

        $condition['zyname'] = $zyname;

        // 批次信息
        if (arRequest('pc')) :
            $condition['pc'] = urldecode(arRequest('pc'));
        else :
            return $this->showJsonError('批次必填');
        endif;

        $zys = ArModel::model()
            ->getDb()
            ->table($tableName)
            ->where($condition)
            ->order('fsmin desc')
            ->group('yxname')
            ->queryAll();

        if ($zys) :
            $this->showJson($zys);
        else :
            $this->showJsonError('记录为空');
        endif;

    }

    // 专业查询
    public function schoolsQueryAction()
    {
        $condition = array();

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
            if ($km == 0) :
                $tableName = 'data_zy_query_lk';
            else :
                $tableName = 'data_zy_query_wk';
            endif;
        endif;

        if (arRequest('year') === null) :
            // return $this->showJsonError('年份必填');
        else :
            $condition['year'] = arRequest('year');
        endif;

        if (arRequest('yxname')) :
            $condition['yxname like '] = '%' . urldecode(arRequest('yxname')) . '%';
        endif;

        if (arRequest('area')) :
            // 暂时不支持地区
            // $condition['area like '] = '%' . urldecode(arRequest('area')) . '%';
        endif;

        // 批次信息
        if (arRequest('pc')) :
            $condition['pc'] = urldecode(arRequest('pc'));
        endif;

        $zys = ArModel::model()
            ->getDb()
            ->select('yxname,pc,kl,year')
            ->table($tableName)
            ->group('yxname')
            ->where($condition)
            ->queryAll();

        if ($zys) :
            $this->showJson($zys);
        else :
            $this->showJsonError('记录为空');
        endif;

    }

    // 学校专业 查询库
    public function schoolZysQueryAction()
    {
        $condition = array();
        if (!arRequest('yxname')) :
            return $this->showJsonError('请选择学校');
        else :
            $condition['yxname'] = urldecode(arRequest('yxname'));
        endif;

        if (arRequest('area')) :
            // $condition['area like '] = '%' . urldecode(arRequest('area')) . '%';
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
            if ($km == 0) :
                $tableName = 'data_zy_query_lk';
            else :
                $tableName = 'data_zy_query_wk';
            endif;
        endif;

        if (arRequest('year') === null) :
            // return $this->showJsonError('年份必填');
        else :
            $condition['year'] = arRequest('year');
        endif;


        // 批次信息
        if (arRequest('pc')) :
            $condition['pc'] = urldecode(arRequest('pc'));
        endif;

        $zys = ArModel::model()
            ->getDb()
            ->select('yxname,zyname,fc,fsmin,pc,kl,year')
            ->table($tableName)
            ->where($condition)
            ->queryAll();

        if ($zys) :
            $this->showJson($zys);
        else :
            $this->showJsonError('记录为空');
        endif;

    }


    // 查询专业学科
    public function majorXksAction()
    {
        $condition = array();
        if (arRequest('lb') == 0) :
            $condition['lb'] = '本科';
        else :
            $condition['lb'] = '专科';
        endif;
        $xks = MajorsModel::model()->getDb()
            ->where($condition)
            ->select('xk')
            ->group('xk')
            ->queryAll();
        $this->showJson($xks);

    }

    // 查询专业学科
    public function majorMlsAction()
    {
        $condition = array();
        if ($xk = arRequest('xk')) :
            $condition['xk'] = urldecode($xk);
        else :
            return $this->showJsonError('学科不能为空');
        endif;
        $mls = MajorsModel::model()->getDb()
            ->where($condition)
            ->select('ml')
            ->group('ml')
            ->queryAll();
        $this->showJson($mls);

    }

    // 获取所有专业
    public function majorsByMlAction()
    {
        $condition = array();
        if ($ml = arRequest('ml')) :
            $condition['ml'] = urldecode($ml);
        else :
            return $this->showJsonError('门类不能为空');
        endif;
        $zys = MajorsModel::model()->getDb()
            ->where($condition)
            ->select('mid,ml,xk,mname,dm')
            ->queryAll();
        $this->showJson($zys);

    }

    // 考生走向查询
    public function kaoshengZxAction()
    {
        $condition = array();
        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
        endif;

        if (arRequest('pc') === null) :
            return $this->showJsonError('请选择批次');
        else :
            $pc = arRequest('pc');
        endif;
        $tableName = DataYxModel::getTableName($km, $pc);
        if (!$stype = arRequest('stype')) :
            $stype = 'lqf';
        endif;
        // 年份
        if (!$year = arRequest('year')) :
            return $this->showJsonError('请选择年份');
        endif;
        // 范围
        if ($range = arRequest('range')) :
            list($min, $max) = explode('-', $range);
            if (!is_numeric($min) || $min >= $max) :
                return $this->showJsonError('范围不合法');
            else :
                $column = $stype . $year;
                $zsjh = 'zsjh' . $year;
                $lqwc = 'lqwc' . $year;
                $lqf = 'lqf' . $year;
                $fc = 'fc' . $year;
                $jfxc = 'jfxc' . $year;
                $jf = 'jf' . $year;

                $columns = 'name,pc,lb,dy,pm,' . $zsjh . ' as zsjh, ' . $lqf . ' as lqf,' . $fc . ' as fc, ' . $jfxc . ' as jfxc ,' . $jf . ' as jf ';
                if ($this->ifVip()) :
                    $columns .= ' , '  . $lqwc . ' as lqwc';
                endif;

                $condition[$column . ' >= '] = $min;
                $condition[$column . ' < '] = $max;
                $schoolInfo = DataYxModel::model()
                    ->getDb()
                    ->select($columns)
                    ->table($tableName)
                    ->where($condition)
                    ->limit(80)
                    ->queryAll();
                if ($schoolInfo) :
                    $this->showJson($schoolInfo);
                else :
                    $this->showJsonError('记录为空');
                endif;
            endif;
        else :
            return $this->showJsonError('请填写范围');
        endif;

    }

    // 录取概率
    public function admissionOddsAction()
    {
        if ($sname = arRequest('sname')) :
            $sname = urldecode($sname);
        else :
            return $this->showJsonError('学校不能为空');
        endif;

        if (!$score = arRequest('score')) :
            return $this->showJsonError('考生分数不能为空');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
        endif;

        if (arRequest('pc') === null) :
            return $this->showJsonError('请选择批次');
        else :
            $pc = arRequest('pc');
        endif;

        // 表名
        $tableName = DataYxModel::getTableName($km, $pc);

        $condition['name'] = $sname;

        $columns = 'name,dy,pm,pc,lqf15,lqf14,lqf13,plqf16,zsjh15,zsjh14,zsjh13,fc15,fc14,fc13,pfc16';
        if ($this->ifVip()) :
            $columns .= ',lqwc14,lqwc13,lqwc15';
        endif;

        // 录取分数
        $lqf = DataYxModel::model()
            ->getDb()
            ->select($columns)
            ->table($tableName)
            ->where($condition)
            ->queryRow();

        if (!$lqf) :
            return $this->showJsonError('暂无记录');
        else :
            $lqf['percent'] = $this->admissionOdds($score, $lqf['pfc16']);
        endif;

        $this->showJson($lqf);

    }

    // 计算概率
    public function admissionOdds($score, $lqf)
    {
        $A1 = arCfg('PERCENT.A1');
        $A2 = arCfg('PERCENT.A2');
        $B1 = arCfg('PERCENT.B1');
        $B2 = arCfg('PERCENT.B2');

        // 专二不显示概率
        if (arRequest('pc') == 4) :
            $percent = array(
                'percent' => '-',
                'star' => '-',
            );
            return $percent;
        endif;
        // 用分差计算 新的需求，以前直接是预估分
        $score = $score - arModule('Data')->getMinPcScore(arRequest('km'), arRequest('pc'));

        // 算法逻辑
        $fc = $score - $lqf;
        if ($fc > 0) :
            if ($fc >= ((100 - $A1) / $A2)) :
                $p = 99;
                $star = 25;
            else :
                $p = (int)($A1 + $A2 * ($fc));
                $star = (int)($p / 4);
            endif;
        else :
            if (abs($fc) >= ($B1 / $B2)) :
                $p = 1;
                $star = 0;
            else :
                $p = (int)($B1 - ($B2 * abs($fc)));
                $star = (int)($p / 4);
            endif;
        endif;
        $percent = array(
            'percent' => $p,
            'star' => $star,
        );
        return $percent;

    }

    // 专业查询库
    public function majorqueryInfoAction()
    {
        $condition = array();
        if (!$yxname = trim(urldecode(arRequest('yxname')))) :
            return $this->showJsonError('学校不能为空');
        endif;

        if (!$zyname = trim(urldecode(arRequest('zyname')))) :
            return $this->showJsonError('专业不能为空');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('请选择科目');
        else :
            $km = arRequest('km');
            if ($km == 0) :
                $tableName = 'data_zy_query_lk';
            else :
                $tableName = 'data_zy_query_wk';
            endif;
        endif;

        // 三年分数
        $condition['year'] = array(2010,2011,2012,2013,2014,2015);
        $condition['yxname'] = $yxname;
        $condition['zyname'] = $zyname;

        $zys = ArModel::model()
            ->getDb()
            ->table($tableName)
            ->where($condition)
            ->queryAll('year');
        Ar::setConfig('DEBUG_LOG', true);
        if ($zys) :
            $tempZys = array();
            $tempZys[2013] = $zys[2013] ? $zys[2013] : array();
            $tempZys[2014] = $zys[2014] ? $zys[2014] : array();
            $tempZys[2015] = $zys[2015] ? $zys[2015] : array();
            $tempZys[2012] = $zys[2012] ? $zys[2012] : array();
            $tempZys[2011] = $zys[2011] ? $zys[2011] : array();
            $tempZys[2010] = $zys[2010] ? $zys[2010] : array();

            $this->showJson($tempZys);
        else :
            $this->showJsonError('记录为空');
        endif;

    }

    // 是否VIP
    public function ifVipAction()
    {
        arLm('main.Module');
        if ($uid = arComp('list.session')->get('uid')) :
            if (arModule('Vip')->ifVip($uid)) :
                $this->showJsonSuccess('你是VIP用户');
            else :
                $this->showJsonError('请先升级VIP');
            endif;
        else :
            $this->showJsonError('请先登录');
        endif;

    }

    // 是否vip内部用
    public function ifVip()
    {
        arLm('main.Module');
        if ($uid = arComp('list.session')->get('uid')) :
            if (arModule('Vip')->ifVip($uid)) :
                return true;
            else :
                return false;
            endif;
        else :
            return false;
        endif;

    }

    // 获取考生成绩
    public function userScoreAction()
    {
        arLm('main.Module');
        if ($uid = arComp('list.session')->get('uid')) :
            $user = UserModel::model()->getDb()
                ->select()
                ->where(array('uid' => $uid))
                ->queryRow();
            $user = arModule('User')->getUserDetailInfo($user);
            $score = array(
                'scorez1' => $user['kaosheng']['scorez1'],
                'scorez2' => $user['kaosheng']['scorez2'],
                'scorez3' => $user['kaosheng']['scorez3'],
                'scoregk' => $user['kaosheng']['scoregk'],
            );
            if ($user) :
                $this->showJson($score);
            else :
                $this->showJsonError('考生资料未填');
            endif;
        else :
            $this->showJsonError('暂未登录');
        endif;

    }

    // 获取位次信息
    public function wcAction()
    {
        if (!$score = arRequest('score')) :
            return $this->showJsonError('分数必填');
        endif;

        if (arRequest('km') === null) :
            return $this->showJsonError('科目必填');
        else :
            $km = arRequest('km');
        endif;

        if ($km == 0) :
            $table = 'data_fsd_lk';
        else :
            $table = 'data_fsd_wk';
        endif;

        $condition = array(
            'score' => $score,
        );
        $rank = ArModel::model()->getDb()->table($table)->where($condition)->queryColumn('rank');
        if ($rank) :
            $this->showJson(array('wc' => $rank));
        else :
            $this->showJson(array('wc' => 1));
            // $this->showJsonError('分数超出范围');
        endif;

    }

    // 分数
    public function apizyFixAreaAction()
    {
        $tableName = 'data_zy_query_lk';
        // $tableName = 'data_zy_query_wk';
        $datas = ArModel::model()->getDb()->table($tableName)->group('yxname')->where(array('area' => ''))->queryAll();
        $i = 0;
        foreach ($datas as $data) :
            $sname = $data['yxname'];
            $area = CollegesModel::model()->getDb()->where(array('sname' => $sname))->queryColumn('area');
            if ($area) :
                $i++;
                ArModel::model()->getDb()->table($tableName)->where(array('yxname' => $sname))->update($data);
            endif;
        endforeach;
        echo 'completed' . $i;


    }

    // 分数
    public function yitiScoreAction()
    {
        $condition = array();
        if ($sname = arRequest('sname')) :
            $sname = urldecode($sname);
            $condition['sname like '] = '%' . $sname . '%';
        endif;

        // 地区
        if ($area = arRequest('area')) :
            $condition['area'] = urldecode($area);
        endif;

        if ($year = arRequest('year')) :
            $condition['year'] = $year;
        endif;

        if ($lb = arRequest('lb')) :
            $condition['lb'] = urldecode($lb);
        endif;

        if (arRequest('pc')) :
            $condition['pc'] = urldecode(arRequest('pc'));
        else :
            $condition['pc'] = '本科';
        endif;

        // 录取分数
        $schools = DataYxModel::model()
            ->getDb()
            ->table('data_zy_query_yt')
            ->where($condition)
            ->limit(60)
            ->queryAll();

        if (!$schools) :
            return $this->showJsonError('没有记录');
        else :
            $this->showJson($schools);
        endif;

    }

    // 保存志愿
    public function saveUserZyAction()
    {
        if ($uid = arComp('list.session')->get('uid')) :
            arLm('main.Module');
            $user = UserModel::model()->getDb()->where(array('uid' => $uid))->queryRow();
            $user = arModule('User')->getUserDetailInfo($user);
            $zy = array(
                'name' => $user['kaosheng']['name'],
                'km' => arRequest('km'),
                'pc' => arRequest('pc'),
                'score' => arRequest('score'),
                'uid' => $uid,
                'ctime' => time(),
                'ztype' => arRequest('ztype'),
                'param' => arRequest('param'),
            );
            if ($zid = arRequest('zid')) :
                $insertId = UserZyModel::model()->getDb()->where(array('zid' => $zid))->update($zy);
            else :
                $insertId = UserZyModel::model()->getDb()->insert($zy);
            endif;
            if ($insertId) :
                return $this->showJsonSuccess();
            else :
                return $this->showJsonError();
            endif;
        else :
            return $this->showJsonError();
        endif;

    }

    // 获取志愿
    public function getUserZyAction()
    {
        if ($zid = arRequest('zid')) :
            $zy = UserZyModel::model()->getDb()->where(array('zid' => $zid))->queryRow();
            if ($zy) :
                $zy['param'] = stripslashes(stripslashes(stripslashes($zy['param'])));
                $this->showJson($zy);
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError();
        endif;

    }

    // 是否支付完成
    public function checkPayOkAction()
    {
        if ($uid = arComp('list.session')->get('uid')) :
            $payInfo = OrderModel::model()
                ->getDb()
                ->where(array('uid' => $uid, 'state' => OrderPayModel::STATUS_OK))
                ->count();
            if ($payInfo) :
                return $this->showJsonSuccess();
            else :
                return $this->showJsonError();
            endif;
        endif;
        return $this->showJsonError();

    }

}
