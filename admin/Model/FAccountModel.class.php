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
class FAccountModel extends ArModel
{
    // 已使用
    const STATUS_APPROVED = 1;
    // 未使用
    const STATUS_FORBIDDEN = 0;
    // 状态map
    public static $STATUS_MAP = array(
        '0' => '未绑定',
        '1' => '已绑定',
    );
    // 表名
    public $tableName = 'u_faccount';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }





}
