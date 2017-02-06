<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 购物车 数据库模型.
 */
class CartModel extends ArModel
{
    // 表名
    public $tableName = 'u_cart';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
