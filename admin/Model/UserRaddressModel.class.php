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
class UserRaddressModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;
    // 状态map
    public static $STATUS_MAP = array(
        0 => '未完成',
        1 => '完成',
    );

    // 支付状态 已支付
    const STATUS_PAY_YES = 1;
    // 支付状态 未支付
    const STATUS_PAY_NO = 0;
    // 状态map
    public static $STATUS_PAY_MAP = array(
        0 => '未支付',
        1 => '已支付',
    );

    // 发货状态 已发货
    const STATUS_SHIPPING_YES = 1;
    // 发货状态 未发货
    const STATUS_SHIPPING_NO = 0;
    // 状态map
    public static $STATUS_SHIPPING_MAP = array(
        0 => '未发货',
        1 => '已发货',
        2 => '已收货',
    );

    // 表名
    public $tableName = 'u_user_raddress';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 修改即将写入数据的数据
    public function formatData($data)
    {
        return $data;

    }

    // 生成订单号
    public function generateOrderTradeId()
    {
        return chr(rand(65, 67)) . chr(rand(68, 90)) . date('YmdHis');

    }

}
