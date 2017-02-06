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
class SettingModel extends ArModel
{

    //客服号的类型
    const SER_QQ = 0;
    const SER_WW = 1;

    public static $SER_MAP = array(
        0 => 'QQ',
        1 => '旺旺',

    );

    //网站上杂项的属性
    const SYS_PHONE=0;
    const SYS_EMAIL=1;
    const SYS_WORK=2;
    const SYS_OTHER=3;
    public static $SYS_MAP = array(
          0 => '电话',
          1 => '邮箱',
          2 => '商号',
          3 => '其他',

    );
    //设置过期时间
    const OVEREMAIL = 5;
    // 网站杂项的名称

    // 官方订购热线
    const SITE_HOTLINE = 4;
    // 顾客关怀中心
    const SIET_WARECENTER=1;
    // 认证
    const SITE_INDENTIFICATION=2;
    // 蜀ICP备
    const SITE_CORPORATE=3;
     // 网站名称
    const SITE_SITENAME = 0;
    // 网站发送邮件
    const SITE_EMAIL=5;
    const SITE_DESCRIPTION= 6;
    const SITE_KEYWORD= 7;
    const SITE_TITLE = 8;
    const SITE_OTHER = 9;

    PUBLIC static $SITE_MAP = array(
        0 => '网站名称',
        1 => '顾客关怀中心',
        2 => '认证机构',
        3 => 'ICP备',
        4 => '400电话',
        5 => '网站发送邮件',
        6 => 'meta描述',
        7 => 'meta关键字',
        8 => 'title',
        9 => '其他',
    );

    // 表名
    public $tableName = 's_set';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取设置
    static public function getSet($mark)
    {
        if ($mark) :
            if (is_numeric($mark)) :
                return self::model()->getDb()->where(array('s_remark' => $mark))->queryColumn('s_content');
            else :
                return self::model()->getDb()->where(array('s_sname' => $mark))->queryColumn('s_content');
            endif;
        endif;

    }

}
