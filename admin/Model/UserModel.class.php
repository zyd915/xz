<?php

class UserModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;
    // 状态map
    public static $STATUS_MAP = array(
        0 => '禁用',
        1 => '激活',
    );

    const USER_NONRL = 0;

    // 类型
    const TYPE_N = 0;
    const TYPE_V = 1;
    const TYPE_A = 2;

    // 数据源
    public static $TYPE_MAP = array(
        0 => '普通会员',
        1 => 'VIP会员',
        2 => '专家',
    );

    const SUBJECT_LIKE = 0;
    const SUBJECT_WENKE = 1;
    public static $TYPE_SUBJECT = array(
        0 => '理科',
        1 => '文科',
    );

    const SEX_MALE = 0;
    const SEX_FEMALE = 1;

    public static $TYPE_SEX  = array(
        0 => '男',
        1 => '女',
    );

    // 表名
    public $tableName = 'u_user';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 验证入库规则
    public function rules()
    {
        return array(
            // 'phone' => array('required', '电话不能为空'),
            // 'pwd' => array('required', '密码不能为空'),
        );

    }

    // 生成User表密码规则
    public static function gPwd($pwd)
    {
        return md5($pwd . arCfg('APP_KEY'));

    }

    // 格式化入库数据
    public function formatData($user)
    {
        // 注册时间
        $user['rtime'] = time();
        // $user['pwd'] = self::gPwd($user['pwd']);
        return $user;

    }

}
