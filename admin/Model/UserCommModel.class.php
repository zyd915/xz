<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 单个用户加入社团
 */
class UsercommModel extends ArModel
{
    // 表名
    public $tableName = 'h_userjoincomm';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }



}
