<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 广告数据库模型.
 */
class SerialsModel extends ArModel
{
    // 表名
    public $tableName = 'data_serials';

    // 序列号类型
    const TYPE_MUT = 1;
    const TYPE_ONE = 0;
    public static $STYPE_MAPS = array('一次性使用', '多次使用');

    // 序列号类型
    const STATUS_USE_YES = 1;
    const STATUS_USE_NO = 0;
    public static $STATUS_MAPS = array('未使用', '已使用');

    // 量表类型
    const LB_LK = 0;
    const LB_WK = 1;
    const LB_WLFK = 2;
    public static $LIANGBIAO_MAPS = array('Major-choice-l', 'Major-choice-w', 'Major-choice-wlfk');

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
