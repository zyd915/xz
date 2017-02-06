<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * user 数据库模型.
 */
class UserLogModel extends ArModel
{
    public $tableName = 'u_user_log';

    static $LTYPE_MAP = array(
        'zySchoolLis' => '重点批推荐学校（诊断）',
        'zySchoolListRel' => '重点批推荐学校（高考）',
        'zySchoolListTqp ' => '提前批推荐学校',
        'zySchoolListTqp ' => '提前批推荐学校',
        'zySchoolListTqpRel' => '提前批推荐学校(高考)',
        'zySchoolListBx' => '备选批推荐学校',
        'zySchoolListBxRel' => '备选批推荐学校(高考)',
        'pcinfo' => '分段批次',
        'zhuanyeList' => '获取专业列表（志愿库）',
        'admissionOdds' => '录取概率',
        'yxzs' => '院校对比',
        'areaSchools' => '地区学校',
        'zyzs' => '专业招生',
        'getCollegesByZy' => '专业学校',
        'zhuanyeList' => '学校专业',
        'kaoshengZx' => '考生走向',
        'zyQuery' => '专业库查询',
        'majorqueryInfo' => '专业对比',
        'schoolZysQuery' => '学校专业',
        'gouka' => '购买圆梦卡',
        'yuyue' => '预约专家',
    );

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 客户端转换时间
    public function timeTranse($time)
    {
        $currentTime = time();
        $subTime = $currentTime - $time;
        // var_dump($currentTime, $subTime);
        // exit;

        if ($subTime < 60) :
            $time = $subTime . '秒前';
        elseif ($subTime < 60 * 60) :
            $time = (int)($subTime/60) . '分钟前';
        elseif ($subTime < 60 * 60 * 24) :
            $time = (int)($subTime/3600) . '小时前';
        elseif ($subTime < 60 * 60 * 48) :
            $time = '昨天 ' . date('H:i', $time);
        elseif ($subTime < 60 * 60 * 72) :
            $time = '前天 ' . date('H:i', $time);
        else :
            $time = date("m-d H:i", $time);
        endif;
        return $time;

    }

    // 统计数据
    public function tjData()
    {
        $zy = UserLogModel::model()
            ->getDb()
            ->where(array('ltype' => array('zyzs', 'zhuanyeList', 'zyQuery', 'majorqueryInfo', 'schoolZysQuery')))
            ->count() + 1268;

        $yx = UserLogModel::model()
            ->getDb()
            ->where(array('ltype' => array('areaSchools', 'yxzs', 'getCollegesByZy')))
            ->count() + 1782;

        $total = $zy + $yx;

        $zj = UserLogModel::model()
            ->getDb()
            ->where(array('ltype' => 'yuyue'))
            ->count() + 225;

        return array(
            'total' => $total,
            'zy' => $zy,
            'yx' => $yx,
            'zj' => $zj,
        );

    }

    // 记录日志
    public function log($ltype)
    {
        $uid = arComp('list.session')->get('uid');
        $log = array(
            'time' => time(),
            'dowhat' => self::$LTYPE_MAP[$ltype],
            'ltype' => $ltype,
        );
        $who = '';
        $where = '';
        // 是否登陆
        if ($uid) :
            $user = UserModel::model()->getDb()
                ->where(array('uid' => $uid))
                ->queryRow();
            $log['uid'] = $uid;
            arLm('main.Module');
            $user = arModule('User')->getUserDetailInfo($user);
            if ($user['kaosheng']['name']) :
                $who = arModule('Article')->substr_cut($user['kaosheng']['name'], 1). '同学';
                $where = $user['kaosheng']['proname'] . ':' . $user['kaosheng']['schoolname'];
            elseif ($user['vip']) :
                $who = 'VIP卡号:' . substr($user['vip']['cno'], 0, 5) . '******';
            elseif ($user['normal']) :
                $who = '普通卡号:' . substr($user['normal']['cno'], 0, 5) . '******';
            endif;
        else :
            $who = '游客';
        endif;
        
        $log['who'] = $who;
        $log['where'] = $where;
        // 写入日志
        UserLogModel::model()->getDb()->insert($log);

    }

}
