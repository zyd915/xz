<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ck <yushaohunzhu@sina.com>
 *
 *time:2016.5.23
 */

/**
 * user 数据库模型.
 */
class DataYhModel extends ArModel
{

    public $tableName = 'u_user_kaosheng';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


}
