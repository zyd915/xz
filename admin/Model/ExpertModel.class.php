<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 专家数据库模型.
 */
class ExpertModel extends ArModel
{
    // 表名
    public $tableName = 'u_expert';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
