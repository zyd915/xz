<?php
class DataModule
{
    // 获取学校
    public function getSchoolName($m, $k)
    {


    }

    /**
     * 获取基准分差
     *
     * $score 考生分数
     * $km    文科理科
     * $pc    批次
     * $type  志愿类型 ABCDEF
     */
    public function jzfc($score, $km, $pc, $type)
    {
        // 各个批次的位次参数
        $pcnum = arModule('Data')->getFdInfo($score, $km, false, true);
        $type = $type . '_' . $pcnum;

        $mark = self::getMark($km, $pc);
        return max($score + arCfg('XCM.' . $type) - self::getMinPcScore($km, $pc), 0);

    }

    /**
     * 获取基准分差
     *
     * $score 考生分数
     * $km    文科理科
     * $pc    批次
     * $type  志愿类型 ABCDEF
     */
    public function jzfcbx($score, $km, $pc, $type)
    {
        // 各个批次的位次参数
        $pcnum = arModule('Data')->getFdInfo($score, $km, false, true);
        $type = $type . '_' . $pcnum;

        $mark = self::getMark($km, $pc);
        $zdnum = arRequest('zdnum', 1);
        $fsmarkString = arCfg('FSX.' . $mark . '_' . strtoupper(arRequest('city', 'CD')));
        $fdNumArray = explode('#', $fsmarkString);
        // 1 2 3 诊断
        if (count($fdNumArray) == 3) :
            $fssepString = $fdNumArray[$zdnum - 1];
        else :
            $fssepString = $fdNumArray[0];
        endif;
        // 诊断模拟分地区
        list($min, $max) = explode(',', $fssepString);
        // 线差匹配配置
        return $max - $min + arCfg('XCM.BX_' . $type);

    }

    /**
     * 获取基准位次
     *
     * $score 考生分数
     * $km    文科理科
     * $pc    批次
     * $type  志愿类型 ABCDEF
     */
    public function jzwc($score, $km, $pc, $type)
    {
        // 分段表
        if ($km == 0) :
            $table = 'data_fsd_lk';
        else :
            $table = 'data_fsd_wk';
        endif;
        // 批次最低分
        $mpc = self::getMinPcScore($km, $pc);

        // 各个批次的位次参数
        $pcnum = arModule('Data')->getFdInfo($score, $km, true, true);
        $type = $type . '_' . $pcnum;

        // 修正分数
        $xscore = max($score + arCfg('WCM.' . $type), $mpc);

        $condition = array(
            'score' => $xscore,
        );
        // 分数表
        if ($rank = ArModel::model()->getDb()->table($table)->where($condition)->queryColumn('rank')) :
            return $rank;
        else :
            // 没有数据直接为1（第一名）
            return 1;
            // return false;
        endif;

    }

    /**
     * 获取基准位次(备选)
     *
     * $score 考生分数
     * $km    文科理科
     * $pc    批次
     * $type  志愿类型 ABCDEF
     */
    public function jzwcbx($score, $km, $pc, $type)
    {
        // 保持重点批次一致
        // return $this->jzwc($score, $km, $pc, $type);

        // 分段表
        if ($km == 0) :
            $table = 'data_fsd_lk';
        else :
            $table = 'data_fsd_wk';
        endif;
        // 批次最低分
        $mpc = self::getMinPcScore($km, $pc);

        // 各个批次的位次参数
        $pcnum = arModule('Data')->getFdInfo($score, $km, true, true);
        $type = $type . '_' . $pcnum;

        // 修正分数
        // $xscore = $mpc + arCfg('WCM.BX_' . $type);

        // 修正分数
        $xscore = max($score + arCfg('WCM.BX_' . $type), $mpc);

        $condition = array(
            'score' => $xscore,
        );
        // 分数表
        if ($rank = ArModel::model()->getDb()->table($table)->where($condition)->queryColumn('rank')) :
            return $rank;
        else :
            return false;
        endif;

    }

     /**
     * 获取基准位次(提前批)
     *
     * $score 考生分数
     * $km    文科理科
     * $pc    批次
     * $type  志愿类型 ABCDEF
     */
    public function jzwctqp($score, $km, $pc, $type)
    {
        // 分段表
        if ($km == 0) :
            $table = 'data_fsd_lk';
        else :
            $table = 'data_fsd_wk';
        endif;
        // 修正分数
        $xscore = $score + arCfg('WCM.TQP_' . $type);
        $condition = array(
            'score' => $xscore,
        );
        // 分数表
        if ($rank = ArModel::model()->getDb()->table($table)->where($condition)->queryColumn('rank')) :
            return $rank;
        else :
            return 1;
        endif;

    }

    /**
     * 获取基准分差
     *
     * $score 考生分数
     * $type  志愿类型 A B1 B2
     */
    public function jzfctqp($score, $type)
    {
        return $score + arCfg('XCM.TQP_' . $type);

    }



    // 获取批次下最低分数
    public function getMinPcScore($km, $pc)
    {
        $mark = self::getMark($km, $pc);
        if (arRequest('isgk') == 1 || arCfg('isgk') == 1) :
            list($min, $max) = explode(',', arCfg('FSX.' . $mark));
        else :
            $zdnum = arRequest('zdnum', 1);
            $fsmarkString = arCfg('FSX.' . $mark . '_' . strtoupper(arRequest('city', 'CD')));
            $fdNumArray = explode('#', $fsmarkString);
            // 1 2 3 诊断
            if (count($fdNumArray) == 3) :
                $fssepString = $fdNumArray[$zdnum - 1];
            else :
                $fssepString = $fdNumArray[0];
            endif;
            // 诊断模拟分地区
            list($min, $max) = explode(',', $fssepString);
        endif;
        return $min;

    }

     // 获取批次下最大分数
    public function getMaxPcScore($km, $pc)
    {
        $mark = self::getMark($km, $pc);
        if (arRequest('isgk') == 1 || arCfg('isgk') == 1) :
            list($min, $max) = explode(',', arCfg('FSX.' . $mark));
        else :
            $zdnum = arRequest('zdnum', 1);
            $fsmarkString = arCfg('FSX.' . $mark . '_' . strtoupper(arRequest('city', 'CD')));
            $fdNumArray = explode('#', $fsmarkString);
            // 1 2 3 诊断
            if (count($fdNumArray) == 3) :
                $fssepString = $fdNumArray[$zdnum - 1];
            else :
                $fssepString = $fdNumArray[0];
            endif;
            // 诊断模拟分地区
            list($min, $max) = explode(',', $fssepString);
        endif;
        return $max;

    }

    // 获得mark
    public function getMark($km, $pc, $upper = true)
    {
        $mark = DataYxModel::$markNameKmMap[$km] . '_' . DataYxModel::$markNamePcMap[$pc];
        if ($upper) :
            $mark = strtoupper($mark);
        endif;
        return $mark;

    }

    /**
     * 获取专业表名
     *
     * $km    文科理科
     * $pc    批次
     */
    public function getZyTableName($km, $pc)
    {
        return DataYxModel::$dbZyPrefix . '_' . DataYxModel::$markNameKmMap[$km] . '_' . DataYxModel::$markNamePcMap[$pc];

    }

    // 分数线
    public function fsxDetailInfo($fsxs)
    {
        $newTempArray = array();
        foreach ($fsxs as $fsx) :
            foreach ($fsx as &$item) :
                if (is_numeric($item) && $item == 0) :
                    $item = '--';
                endif;
            endforeach;
            if (isset($newTempArray[$fsx['area']])) :
                $newTempArray[$fsx['area']][] = $fsx;
            else :
                $newTempArray[$fsx['area']] = array();
                $newTempArray[$fsx['area']][] = $fsx;
            endif;
        endforeach;
        return $newTempArray;

    }

    // 获取有分数线的地区
    public function getFsxArea()
    {
        $fsxs = FsxModel::model()->getDb()->queryAll();
        $fsxs = $this->fsxDetailInfo($fsxs);
        return array_keys($fsxs);

    }

    // 获取所有省份
    public function getProvince()
    {
        return DataRegionModel::getProvince();

    }

    // 获取专业分类数据 初始有序三维数组
    public function getMajorsFormatData($majors)
    {
        $newTempArray = array();
        // 专业信息
        foreach ($majors as $major) :
            if (isset($newTempArray[$major['xk']])) :
                if (!isset($newTempArray[$major['xk']][$major['ml']])) :
                    $newTempArray[$major['xk']][$major['ml']] = array();
                endif;
                $newTempArray[$major['xk']][$major['ml']][] = $major;
            else :
                // 初始化
                $newTempArray[$major['xk']] = array();
                $newTempArray[$major['xk']][$major['ml']] = array();
                $newTempArray[$major['xk']][$major['ml']][] = $major;
            endif;
        endforeach;
        return $newTempArray;

    }

    // 获取专科分类数据 初始有序三维数组
    public function getMajorsFormatDatail($major)
    {
        $newTempArray = array();
        // 专业信息
        foreach ($major as $major) :
            if (isset($newTempArray[$major['xk']])) :
                if (!isset($newTempArray[$major['xk']][$major['ml']])) :
                    $newTempArray[$major['xk']][$major['ml']] = array();
                endif;
                $newTempArray[$major['xk']][$major['ml']][] = $major;
            else :
                // 初始化
                $newTempArray[$major['xk']] = array();
                $newTempArray[$major['xk']][$major['ml']] = array();
                $newTempArray[$major['xk']][$major['ml']][] = $major;
            endif;
        endforeach;
        return $newTempArray;

    }

    // 获取所属分段 重点备选批次
    public function getFdInfo($score, $km, $isgk = false, $getZdPcnum = false)
    {
        if ($score = arRequest('score')) :
            $pcInfo = array();
            $foundZd = false;
            foreach (DataYxModel::$pcMap as $pcnum => $pcname) :
                $currentPc = array(
                    'pcname' => $pcname,
                    'pcmark' => DataYxModel::$markNamePcMap[$pcnum],
                    'pcnumber' => $pcnum,
                    'is_zd' => 0,
                    'is_bx' => 0,
                    'is_disabled' => 1,
                );
                // 提前批跳过
                if ($pcnum == DataYxModel::PC_TQ) :
                    $pcInfo[] = $currentPc;
                    continue;
                else :
                    $fsxMark = strtoupper(DataYxModel::$markNameKmMap[$km] . '_' . DataYxModel::$markNamePcMap[$pcnum]);
                    if ($isgk) :
                        // 高考直接省控线
                        list($minScore, $maxScore) = explode(',', arCfg('FSX.' . $fsxMark));
                    else :
                        $zdnum = arRequest('zdnum', 1);
                        $fsmarkString = arCfg('FSX.' . $fsxMark . '_' . strtoupper(arRequest('city', 'CD')));
                        $fdNumArray = explode('#', $fsmarkString);
                        // 1 2 3 诊断
                        if (count($fdNumArray) == 3) :
                            $fssepString = $fdNumArray[$zdnum - 1];
                        else :
                            $fssepString = $fdNumArray[0];
                        endif;
                        // 诊断模拟分地区
                        list($minScore, $maxScore) = explode(',', $fssepString);
                    endif;
                    $currentPc['fsx'] = $minScore;
                    // 是否已经找到重点填报
                    if ($foundZd) :
                        // 备选推荐
                        $currentPc['is_bx'] = 1;
                        $currentPc['is_disabled'] = 0;
                    else :
                        if ($score >= $minScore) :
                            // 重点推荐
                            $currentPc['is_zd'] = 1;
                            $currentPc['is_disabled'] = 0;
                            // 返回重点批次番号
                            if ($getZdPcnum) :
                                return $pcnum;
                            endif;
                            $foundZd = true;
                        endif;
                    endif;
                endif;
                $pcInfo[] = $currentPc;
            endforeach;
            if ($foundZd) :
                return $pcInfo;
            else :
                return false;
            endif;
        else :
            return false;
        endif;

    }

}
