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
class DataModel extends ArModel
{

    // 表名
    public $tableName = 'h_data';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }



}
