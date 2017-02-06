<?php
/**
 * user vip ka 数据库模型.
 */
class UserVcardModel extends ArModel
{
     // 状态正常
    const STATUS_USED = 0;
    // 状态异常或禁止
    const STATUS_NOTUSED = 1;
    // 状态map
    public static $STATUS_MAP = array(
        0 => '已使用',
        1 => '未使用',
    );

    // 普通卡
    const VTYPE_NORMAL = 0;
    // VIP卡
    const VTYPE_VIP = 1;

    // 状态map
    public static $VTYPE_MAP = array(
        0 => '普通',
        1 => 'VIP',
    );

    // 表名
    public $tableName = 'u_user_vcard';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取详情
    public function getDetail($vcards)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($vcards)) :
            foreach ($vcards as &$vcard) :
                $vcard = $this->getDetail($vcard);
            endforeach;
        else :
            $vcard = $vcards;
            if ($vcard['uid']) :
                $vcard['user'] = UserModel::model()->getDb()
                    ->select('truename,uid')
                    ->where(array('uid' => $vcard['uid']))
                    ->queryRow();
            endif;
            return $vcard;
        endif;
        return $vcards;

    }

}
