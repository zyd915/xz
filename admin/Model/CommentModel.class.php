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
class CommentModel extends ArModel
{
    // 表名
    public $tableName = 'u_comment';
    // 产品类型
    const TYPE_PRODUCT = 1;

    const NORMAL_STATUE = 1;
    const FORBIDDEN_STATUE = 0;
    // 处理要插入的数据
    public function formatData($comment)
    {
        $comment['status'] = CommentModel::NORMAL_STATUE;
        $comment['ctime'] = time();
        return $comment;
    }


    static public function model($class = __CLASS__)
    {
        return parent::model($class);
    }

}
