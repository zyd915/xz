<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 广告数据库模型.
 */
class VideoCatModel extends ArModel
{
    // 表名
    public $tableName = 'h_video_cat';

    // 是否最新
    const IN_NEWS_YES = 1;
    const IN_NEWS_NO = 0;
    public static $NEWS_MAPS = array('否', '是');

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
