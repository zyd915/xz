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
class AddressModel extends ArModel
{
    // 其它没有选中的地址
    const DEFAULT_STATUS = 0;
    // 当前选用的地址
    const NORMAL_STATUS = 1;
    //默认地址
    const SET_ADD = 1;
    //非默认地址
    const NOSET_ADD = 0;
    public static $ADD_MAP=array(
        '0'=>'不是默认地址',
        '1'=>'默认地址',
        );
    public $tableName = 'u_user_raddress';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    public function formatData($address)
    {
        $address['sorder'] = AddressModel::DEFAULT_STATUS;
        return $address;
    }



}
