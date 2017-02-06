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
class UserZyModel extends ArModel
{
    // 表名
    public $tableName = 'u_user_zhiyuan';
    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
