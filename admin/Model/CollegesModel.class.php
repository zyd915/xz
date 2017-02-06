<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 大学数据库模型.
 */
class CollegesModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;

    // 状态map
    public static $STATUS_MAP = array(
        0 => '隐藏',
        1 => '显示',
    );

    // 表名
    public $tableName = 'data_colleges';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
