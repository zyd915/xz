<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ck 
 * 2016 4 5
 */

/**
 * user 数据库模型.
 */
class ChairModel extends ArModel
{
    // 表名
    public $tableName = 'u_chair';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }



}
