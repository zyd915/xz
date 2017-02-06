<?php
// 资源库
class DataRegionModel extends ArModel
{
    // 表名
    public $tableName = 'data_region';

    // 常量表示
    const REGION_COUNTRY = 0;
    const REGION_PROVINCE = 1;
    const REGION_CITY = 2;
    const REGION_COUNTY = 3;

    // 地区分类
    public static $TYPE_REGION = array(
        0 => '国家',
        1 => '省',
        2 => '市',
        3 => '地区',
    );

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取地区
    public function getAllreginByPid($pid = 0, $sub = true)
    {
        $region = DataRegionModel::model()
            ->getDb()
            ->where(array('parent_id' => $pid))
            ->queryAll();
        if ($region && $sub) :
            foreach ($region as & $reg) :
                $reg['children'] = $this->getAllreginByPid($reg['region_id']);
            endforeach;
        endif;
        return $region;

    }

    // 获取所有省
    public function getProvince()
    {
        $region = DataRegionModel::model()
            ->getDb()
            ->where(array('region_type' => DataRegionModel::REGION_PROVINCE))
            ->queryAll();
        return $region;

    }

}

