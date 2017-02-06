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
class ArticleModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;

    // 状态map
    public static $STATUS_MAP = array(
        0 => '隐藏',
        1 => '显示',
    );

    //是热点新闻
    const HOT_STATUS = 1;
    // 不是热点新闻
    const NOTHOST_STATUS = 0;
    public static $STATUS_HOT = array(
        '1' => '是',
        '0' => '否',
    );

    // 是否最新
    const IS_NEW_YES = 1;
    const IS_NEW_NO = 0;
    public static $NEW_MAP = array(
        1 => '是',
        0 => '否',
    );

    // 表名
    public $tableName = 'h_article';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    static public function ArticleType()
    {
        if(!self::$ArticleType) {
           $ArtType = ArticleModel::model()->getDb()->queryAll();
           foreach ($ArtType as $ArtType) {
               self::$ArticleType[$ArtType['cid']] = $ArtType['cname'];
           }

        }
        return self::$ArticleType;
    }
}
