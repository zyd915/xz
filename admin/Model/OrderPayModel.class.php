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
class OrderPayModel extends ArModel
{
    // 支付方式 type
    // 支付宝
    const TYPE_ALIPAY = 0;
    // 支付宝
    const TYPE_ALIPAY_ESCOW = 1;
    // 快钱
    const TYPE_KUAIQIAN = 2;
    // 微信
    const TYPE_WX = 3;

    // 状态map
    public static $TYPE_MAP = array(
        0 => '支付宝及时到账',
        1 => '支付宝担保交易',
        2 => '快钱',
        3 => '微信',
    );

    // 支付状态 已支付 status
    const STATUS_OK = 1;
    // 支付状态 未支付 支付异常
    const STATUS_ERROR = 0;
    // 状态map
    public static $STATUS_MAP = array(
        0 => '等待支付',
        1 => '已支付',
    );

    // 表名
    public $tableName = 'u_order_pay';

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

}
