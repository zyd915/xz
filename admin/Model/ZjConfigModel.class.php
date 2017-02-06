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
class ZjConfigModel extends ArModel
{
    // 表名
    public $tableName = 'u_zj_config';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
