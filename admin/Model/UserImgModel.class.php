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
class UserImgModel extends ArModel
{
    // 表名
    public $tableName = 'u_user_img';

    public static $TYEP_MAP = array(
        'zselfb' => '上半身照',
        'zphotoz' => '身份证正面照',
        'zphotof' => '身份证反面照',
        'zbankz' => '银行卡正面照',
        'zbankf' => '银行卡反面照'
    );

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


    public function formatData($data)
    {
        return $data;

    }


}
