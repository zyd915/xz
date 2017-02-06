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
class CardModel extends ArModel
{

    public $tableName = 'u_card';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


}
