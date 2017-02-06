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
class CardTypeModel extends ArModel
{
    // 普通会员卡
    const C_CARD = 0;
    // 高级会员卡
    const H_CARD = 1;

    private static $TYPE_MAP;

    // 表名
    public $tableName = 'u_card_type';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取卡类型
    static public function typeMap()
    {
        if (!self::$TYPE_MAP) :
            $cardType = CardTypeModel::model()->getDb()->queryAll();

            foreach ($cardType as $type) :
                self::$TYPE_MAP[$type['type']] = $type['name'];
            endforeach;

        endif;
        return self::$TYPE_MAP;

    }


    public function test()
    {
        echo 'hello';
        exit();
    }

}
