<?php
class ApiModule
{
    // 处理用户日志
    public function doUserLog()
    {
        $action = arCfg('requestRoute.a_a');
        if (array_key_exists($action, UserLogModel::$LTYPE_MAP)) :
            $log = array(
                'time' => time(),
                'dowhat' => '查询' . UserLogModel::$LTYPE_MAP[$action] . '数据',
                'ltype' => $action,
            );
            $who = '';
            $where = '';
            // 是否登陆
            if ($uid = arComp('list.session')->get('uid')) :
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
                endif;
            else :
                $who = '游客';
            endif;
            $log['who'] = $who;
            $log['where'] = $where;
            UserLogModel::model()->getDb()->insert($log);
        endif;

    }

}
