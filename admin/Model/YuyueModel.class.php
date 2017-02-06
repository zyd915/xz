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
class YuyueModel extends ArModel
{
    static public $STATUS_MAP = array(

    );

    public $tableName = 'u_user_yuyue';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
