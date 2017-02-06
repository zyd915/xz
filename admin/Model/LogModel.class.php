<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 系统日志记录
 */
class LogModel extends ArModel
{


    // 表名
    public $tableName = 's_log';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 修改即将写入数据的数据(用于注册)
    public function formatData($data)
    {
        // 默认用户激活状态
        $data['attime'] = time();
        return $data;

    }

    // 日志记录
    public function record($who, $do, $what)
    {
        $data = array(
            'who' => $who,
            'do' => $do,
            'what' => $what,
        );
        return $this->getDb()->insert($data);

    }

}
