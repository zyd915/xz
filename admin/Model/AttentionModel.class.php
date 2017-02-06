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
class AttentionModel extends ArModel
{
    // 表名
    public $tableName = 'u_attention';
    // 初始化model
    // tp 想法
    const STATUS_TP_IDEA = 0;
    // tp 产品
    const STATUS_TP_PRODUCT = 1;
    // tp 店铺
    const STATUS_TP_SHOP = 2;

    // 处理要插入的数据
    public function formatData($attention)
    {
        // $attention['tp'] = STATUS_TP_IDEA;
        return $attention;
    }


    static public function model($class = __CLASS__)
    {
        return parent::model($class);
    }

}
