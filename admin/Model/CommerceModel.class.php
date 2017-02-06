<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 商会数据库模型.
 */
class CommerceModel extends ArModel
{
    // 表名
    public $tableName = 'h_commerce';
    public static $COMM_STATUS = array(
        '1' => '通过',
        '0' => '不通过',
        );
    const YES_HOT = "标记";
    const NOT_HOT = "未标记";
    public static $HOT_STATUS = array(
        '0' =>'未标记',
        '1' => '标记',
        );

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }



}
