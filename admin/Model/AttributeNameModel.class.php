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
class AttributeNameModel extends ArModel
{
    // 其它没有选中的地址
    const DEFAULT_STATUS = 0;
    // 当前选用的地址
    const NORMAL_STATUS = 1;
    // 国家的属性名,这个不能乱动，通过这个属性名查找相关产品

    const COUNTRY = 1;
    public $tableName = 'u_product_attr_name';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    public function formatData($attribute)
    {
        return $attribute;
    }

  
    public function attrString($attributes = array())
    {
        $attrString = '';
        foreach ($attributes as $key => $attr) :
            $nameInfo = AttributeNameModel::model()->getDb()
                ->where(array('nid' => $key))
                ->queryRow();
                $attrString .= " #" . $nameInfo['name'] . '  ' . $attr . $nameInfo['dw'];
        endforeach;

        return $attrString;

    }



}
