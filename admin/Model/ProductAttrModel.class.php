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
class ProductAttrModel extends ArModel
{
    // 其它没有选中的地址
    const DEFAULT_STATUS = 0;
    // 当前选用的地址
    const NORMAL_STATUS = 1;

    public $tableName = 'u_product_attr';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    public function formatData($attribute)
    {
        return $attribute;
    }

}
