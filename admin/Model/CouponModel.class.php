<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * coupon 数据库模型.
 */
class CouponModel extends ArModel
{
    const STATUS_UNUSE = 0;
    const STATUS_USED = 1;
    const STATUS_OVERDUE = 2;

    // 状态map
    public static $STATUS_MAP = array(
        0 => '未使用',
        1 => '已使用',
        2 => '过期作废',
    );

    const TYPE_REDENVELOPE = 0;
    const TYPE_CASHCOUPON = 1;

    // 类型
    public static $TYPE_MAP = array(
        0 => '红包',
        1 => '现金劵',
    );



    // 表名
    public $tableName = 'u_coupon';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 修改即将写入数据的数据(用于注册)
    public function formatData($data)
    {
        $data['status'] = CouponModel::STATUS_UNUSE;
        $data['atime'] = time();
        return $data;

    }

    // 生成兑换码
    public function generateExchangeCode()
    {
        return chr(rand(65, 67)) . chr(rand(68, 90)) . date('YmdHis') . mt_rand(1000, 9999) . chr(rand(68, 90));

    }

}
