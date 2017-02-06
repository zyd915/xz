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
class AdminModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;
    // 默认头像
    const DEFAULT_LOGO_ID = '111';
    // 状态map
    public static $STATUS_MAP = array(
        '0' => '禁用',
        '1' => '激活',
    );

    // 表名
    public $tableName = 's_admin';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 添加数据验证规则
    public function rules()
    {
        // 验证规则
        return array(
            // 'email' => array('required', '邮箱不能为空'),
        );

    }

    // 修改即将写入数据的数据
    public function formatData($data)
    {
        return $data;

    }

    // 生成User表密码规则
    public static function gPwd($pwd)
    {
        return md5($pwd . arCfg('APP_KEY') . '_admin');

    }

}
