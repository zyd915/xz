<?php
// 获取考生信息
class UserModule
{
    // 获取考生信息
    public function getUserDetailInfo($user)
    {
        // 考生信息
        $kaosheng = UserKaoshengModel::model()
            ->getDb()
            ->where(array('uid' => $user['uid']))
            ->queryRow();
        $user['kaosheng'] = $kaosheng;
        // 购卡信息
        $user['vip'] = UserVcardModel::model()
            ->getDb()
            ->where(array('uid' => $user['uid'], 'vtype' => UserVcardModel::VTYPE_VIP))
            ->queryRow();

        if (!$user['vip']) :
            $user['normal'] = UserVcardModel::model()
                ->getDb()
                ->where(array('uid' => $user['uid'], 'vtype' => UserVcardModel::VTYPE_NORMAL))
                ->queryRow();
        endif;

        $user['serial'] = UserSerialsModel::model()->getDb()->where(array('uid' => $user['uid']))->queryRow();

        return $user;

    }

    // 生成ccokie
    public function gUserCookieString($uid)
    {
        $string = arComp('hash.mcrypt')->encrypt('user_' . $uid);
        setcookie('userlogin', $string, time() + 3600*24*15, '/');

    }

    // 解析cookie
    public function dUserCookieString()
    {
        if (!empty($_COOKIE['userlogin']) && !arComp('list.session')->get('uid')) :
            list($s, $uid) = explode('_', arComp('hash.mcrypt')->decrypt($_COOKIE['userlogin']));
            if (is_numeric($uid) && $uid > 0) :
                $user = UserModel::model()->getDb()->where(array('uid' => $uid))->queryRow();
                if (empty($user)) :
                    return false;
                endif;
                arComp('list.session')->set('uid', $uid);
                return true;
            else :
                return false;
            endif;
        endif;
        return false;

    }

    // 保存sesson
    public function saveLoginInfo($datas)
    {
        foreach ($datas as $key => $data) :
            arComp('list.session')->set($key, $data);
        endforeach;
        return true;

    }

    // 验证是否登陆
    public function checkAllowAction()
    {
        return in_array(arCfg('requestRoute.a_a'), array('login', 'logout', 'register', 'dkSq', 'xykSq', 'lcSq'));

    }

}
