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
class RegionModel extends ArModel
{

    // 表名
    public $tableName = 'p_region';

    // 国外地址id
    const RID_FOREIGN_COUNTRY = 9999;

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取国外地址 客户需求 。。。。都不想写到model里
    public function getForeignRegion()
    {
        return $this->getDb()
            ->where(
                array('rid > ' => self::RID_FOREIGN_COUNTRY, 'rank' => 1)
            )
            ->queryAll();

    }

    // 默认获取两级
    public function getReginBySubId($subId)
    {
        $regin = RegionModel::model()->getDb()
            ->where(array('rid' => $subId))
            ->queryRow();

        $reginProvence = RegionModel::model()->getDb()
            ->where(array('rid' => $regin['pid']))
            ->queryRow();

        return array('region' => $regin, 'parent' => $reginProvence);

    }

    // 获取所有分类 默认获取所有分类
    public function getAllreginByPid($pid = 0, $sub = true)
    {
        $region = RegionModel::model()
            ->getDb()
            ->where(array('pid' => $pid))
            ->queryAll();

        if ($region && $sub) :
            foreach ($region as & $reg) :
                $reg['children'] = $this->getAllreginByPid($reg['rid']);
            endforeach;
        endif;
        return $region;

    }

    // 获取所有分类 默认获取所有分类
    public function getAllreginBySid($sid)
    {
        $region = RegionModel::model()
            ->getDb()
            ->where(array('rid' => $sid))
            ->queryRow();

        if ($region['pid'] != 0) :
            $region['parent'] = $this->getAllreginBySid($region['pid']);
        endif;

        return $region;

    }

}
