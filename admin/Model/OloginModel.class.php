<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 外部登陆数据库模型.
 */
class OloginModel extends ArModel
{
    // qq
    const TYPE_QQ = 0;
    // 微信
    const TYPE_WEIXIN = 1;

    // 数据表
    public $tableName = 'u_user_ologin';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 数据
    public function formatData($login)
    {
        return $login;

    }

}
