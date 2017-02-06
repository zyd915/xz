<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 抢购商品数据库模型.
 */
class ProductSpikeTimeModel extends ArModel
{
    const STATUS_NOTSTART = 0;
    const STATUS_STARTED = 1;
    const STATUS_ENDED = 2;
    // 对应上边状态
    public static $STATUS_MAP = array(
        0 => '未开始',
        1 => '已开始',
        2 => '已结束',
    );

    // 表名
    public $tableName = 'u_product_spiketime';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
