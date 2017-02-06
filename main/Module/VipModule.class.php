<?php
// vip 相关控制
class VipModule
{
    // 生成card
    public function generateCard($nums = 1, $des = '系统自动生成', $uid = 0, $vtype = UserVcardModel::VTYPE_VIP)
    {
        if ($nums > 1) :
            $cards = array();
            for ($i = 0; $i < $nums; $i++) :
                $cardNo = arModule('Vip')->gCardNo();
                $pwd = arModule('Vip')->gPwd();
                $card = array(
                    'cno' => $cardNo,
                    'pwd' => $pwd,
                    'atime' => time(),
                    'des' => $des,
                    'vtype' => $vtype,
                    'status' => UserVcardModel::STATUS_NOTUSED,
                );
                $cards[] = $card;
            endfor;
            UserVcardModel::model()->getDb()->batchInsert($cards);
            return true;
        else :
            $cardNo = arModule('Vip')->gCardNo();
            $pwd = arModule('Vip')->gPwd();
            $card = array(
                'cno' => $cardNo,
                'pwd' => $pwd,
                'atime' => time(),
                'des' => $des,
                'vtype' => $vtype,
                'status' => UserVcardModel::STATUS_NOTUSED,
            );
            if ($uid) :
                // 自动激活
                $card['uid'] = $uid;
                $card['ptime'] = time();
            endif;
            UserVcardModel::model()->getDb()->insert($card);
            return $cardNo;
        endif;

    }

    // 成为vip
    public function tobeVip($uid)
    {
        if ($uid) :
            $card = UserVcardModel::model()
                ->getDb()
                ->where(array('uid' => $uid))
                ->queryRow();
            if (!$card) :
                // 先判断是否普通卡登录
                // 这里直接生成一张新卡
                // 添加新卡
                $cno = arModule('Vip')->generateCard(1, '购卡绑定新卡', $uid);

                $card = UserVcardModel::model()
                    ->getDb()
                    // ->where(array('uid' => 0, 'status' => UserVcardModel::STATUS_NOTUSED))
                    ->where(array('uid' => $uid))
                    ->queryRow();
            endif;
            // 有卡自动激活
            if ($card) :
                $cno = $card['cno'];
            else :
                // 添加新卡
                $cno = arModule('Vip')->generateCard(1, '购卡绑定新卡', $uid);
            endif;
            // 激活
            $res = arModule('Vip')->active($card['cno'], $uid);
            // 写入操作日志
            arComp('list.log')->record(array('cno' => $cno, 'uid' => $uid, 'lx' => '购买自动激活'), 'tobevip');
        endif;
        return true;

    }

    // 写入购卡激活日志
    public function writeActiveLog($uid)
    {
        $log = array(
            'time' => time(),
            'dowhat' => '成功购买圆梦卡',
            'ltype' => 'gouka',
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

    // 激活已有卡
    public function active($cno, $uid)
    {
        $condition = array(
            'cno' => $cno,
            'status' => UserVcardModel::STATUS_NOTUSED,
        );
        $ret = true;
        $card = UserVcardModel::model()->getDb()->where($condition)->queryRow();
        if ($card) :
            $card['des'] = '卡密激活';
            $card['uid'] = $uid;
            $card['activetime'] = time();
            $card['status'] = UserVcardModel::STATUS_USED;
            // 激活VIP
            $card['vtype'] = UserVcardModel::VTYPE_VIP;

            $updateCardStatus = UserVcardModel::model()
                ->getDb()
                ->where($condition)
                ->update($card);
            if ($updateCardStatus) :
                // 更改用户状态为VIP
                UserModel::model()->getDb()
                    ->where(array('uid' => $uid))
                    ->update(array('type' => UserModel::TYPE_V));
                // 写入日志
                arComp('list.log')->record(array('cno' => $cno, 'uid' => $uid, 'lx' => '卡密激活'), 'active');
                // 购买日志
                $this->writeActiveLog($uid);
            else :
                arComp('list.log')->record(array('cno' => $cno, 'uid' => $uid, 'lx' => '卡密激活'), 'active.fail');
                $ret = '激活失败';
            endif;
        else :
            $ret = '此卡不存在或已被激活，不能重复激活';
        endif;
        return $ret;

    }

    // 生成随机卡号
    public function gCardNo()
    {
        return arModule('Vip')->randpw(9, 'NUMBER');

    }

    // 生成vip密码
    public function gPwd()
    {
        return strtolower(arModule('Vip')->randpw(6, 'NUMBER'));

    }

    // 生成随机值
    public function randpw($len = 8, $format = 'ALL')
    {
        $is_abc = $is_numer = 0;
        $password = $tmp ='';
        switch ($format) {
            case 'ALL':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
            case 'CHAR':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
            case 'NUMBER':
            $chars='0123456789';
            break;
            default :
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        }
        while (strlen($password) < $len) {
            $tmp =substr($chars,(mt_rand()%strlen($chars)),1);
            if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
                $is_numer = 1;
            }
            if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
                $is_abc = 1;
            }
            $password .= $tmp;
        }
        if ($is_numer <> 1 || $is_abc <> 1 || empty($password)) {
            $password = $this->randpw($len, $format);
        }
        return $password;
    }

    // 是否 vip 号卡
    public function isVipNo($no)
    {
        return (is_numeric($no) && strlen($no) === 9);

    }

    // 是否VIP用户
    public function ifVip($uid)
    {
        // 专家也是vip
        if (UserModel::model()->getDb()->where(array('uid' => $uid, 'type' => UserModel::TYPE_A))->count() > 0) :
            return true;
        endif;

        if (UserVcardModel::model()->getDb()->where(array('uid' => $uid, 'vtype' => UserVcardModel::VTYPE_VIP))->count() > 0) :
            return true;
        else :
            return false;
        endif;

    }


}
