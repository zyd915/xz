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
class DataSearchModel extends ArModel
{
    const LK = 0;
    const WK = 1;
    static $kmMap = array('理科','文科');

    static $typeMap = array('诊断模拟', '高考模拟');

    const PC_TQ = 0;
    const PC_YIBEN = 1;
    const PC_ERBEN = 2;
    const PC_YIZHUAN = 3;
    const PC_ERZHUAN = 4;
    // 批次map
    static $pcMap = array(
        0 => '大学',
        1 => '大学录取线',
        2 => '专业',
        3 => '高中学校',
        4 => '地区',
    );

    // 标记名
    static $markNameKmMap = array('lk', 'wk');
    // 标记名
    static $markNamePcMap = array('colleges', 'fsx', 'majors', 'mschool', 'region');

    static $dbPrefix = 'data_yx';
    static $dbZyPrefix = 'data_zy';
    static $dbSearch = 'data';
    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取表名
    static function getTableName($pc)
    {
        return self::$dbSearch. '_' . self::$markNamePcMap[$pc];

    }

}
