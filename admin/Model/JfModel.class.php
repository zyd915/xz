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
class JfModel extends ArModel
{

    // 表名
    public $tableName = 'u_jf';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 修改即将写入数据的数据
    public function formatData($data)
    {
        $data['ctime'] = time();
        return $data;

    }


}
