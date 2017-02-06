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
class TuanModel extends ArModel
{
    //团购未开始
    const TUAN_NOTSTART = 0;
    //团购开始
    const TUAN_STARTED = 1;
    //团购结束
    const TUAN_ENDED = 2;
    // 对应上边状态
    public static $TUAN_MAP = array(
        0 => '未开始',
        1 => '已开始',
        2 => '已结束',
    );
    // 表名
    public $tableName = 'u_product_tuan';

    // 国外地址id


    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }







}
