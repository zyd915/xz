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
class RlistModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;
    // 默认头像
    const DEFAULT_LOGO_ID = '111';
    // 状态map
    public static $STATUS_MAP = array(
        '0' => '禁用',
        '1' => '激活',
    );
    const TYPE_ARTICLE = 1;
    const TYPE_PHOTO = 0;
    public static $TYPE_ROLE = array(
            '1' => '文章',
            '0' => '图片',
        );
    // 表名
    public $tableName = 'h_rlist';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }
}
