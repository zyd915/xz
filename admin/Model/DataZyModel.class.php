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
class DataZyModel extends ArModel
{
    const LK = 0;
    const WK = 1;
    static $kmMap = array('理科', '文科');

    static $typeMap = array('诊断模拟', '高考模拟');

    const PC_TQ = 0;
    const PC_YIBEN = 1;
    const PC_ERBEN = 2;
    const PC_YIZHUAN = 3;
    const PC_ERZHUAN = 4;
    // 批次map
    static $pcMap = array(
        0 => '提前批',
        1 => '本一',
        2 => '本二',
        3 => '专一',
        4 => '专二',
    );

    // 标记名
    static $markNameKmMap = array('lk', 'wk');
    // 标记名
    static $markNamePcMap = array('tqp', 'yiben', 'erben', 'yizhuan', 'erzhuan');

    static $dbPrefix = 'data_yx';
    static $dbZyPrefix = 'data_zy';
    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取表名
    static function getTableName($km, $pc)
    {
        return self::$dbZyPrefix . '_' . self::$markNameKmMap[$km] . '_' . self::$markNamePcMap[$pc];

    }

}
