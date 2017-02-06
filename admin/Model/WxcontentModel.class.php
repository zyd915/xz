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
class WxcontentModel extends ArModel
{
    // 图文
    const TYPE_MUTY = 0;
    // 文本
    const TYPE_TEXT = 1;

    public static $TYPE_MAP = array(
        0 => '商品(图文)',
        1 => '文章(文本)',
    );

    // 表名
    public $tableName = 'u_wxcontent';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
