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
class QuestModel extends ArModel
{

    public $tableName = 'h_quest';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }



}
