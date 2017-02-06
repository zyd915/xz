<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * category 数据库模型.
 */
class CategoryModel extends ArModel
{

public $tableName = 'p_category';
    const YES_FOOTER = 1;
    CONST NO_FOOTER = 0;
    // 是否显示在footer
    public static $FOOTER_MAP = array(
        '1'=>'显示',
        '0'=>'不显示',
        );
    const YES_LEFT = 1;
    CONST NO_LEFT = 0;
    // 是否显示在left
    public static $LEFT_MAP = array(
        '1'=>'显示',
        '0'=>'不显示',
        );
    const YES_TOP = 1;
    CONST NO_TOP = 0;
    // 是否显示在TOP
    public static $TOP_MAP = array(
        '1'=>'显示',
        '0'=>'不显示',
        );

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取分类下面所有分类 在一个数据里面
    public function getAllCateIds($pid = 0, $containParent = true)
    {
        if ($containParent) :
            $cids = array($pid);
        else :
            $cids = array();
            // 第一次翻转回来
            $containParent = true;
        endif;

        $subArray = $this->getAllCategoriesByPid($pid);
        foreach ($subArray as $sub) :
            array_push($cids, $sub['cid']);
            if (is_array($sub['children'])) :
                foreach ($sub['children'] as $children) :
                    $cidNew = $this->getAllCateIds($children['cid'], $containParent);
                    $cids = array_merge($cids, $cidNew);
                endforeach;
            endif;
        endforeach;
        return array_unique($cids);

    }

    // 获取所有分类 默认获取所有分类
    public function getAllCategoriesByPid($pid = 0)
    {
        $con['pid'] = $pid;
        // $con['type'] = CategoryModel::IN_PC;
        $category = CategoryModel::model()
            ->getDb()
            ->where($con)
            ->queryAll();

        if ($category) :
            foreach ($category as & $cate) :
                $cate['children'] = $this->getAllCategoriesByPid($cate['cid']);
            endforeach;
        endif;
        return $category;

    }

      // 后台管理所有分类
    public function getManageCategoriesByPid($pid = 0)
    {
        $con['pid'] = $pid;
        $category = CategoryModel::model()
            ->getDb()
            ->where($con)
            ->queryAll();

        if ($category) :
            foreach ($category as & $cate) :
                $cate['children'] = $this->getAllCategoriesByPid($cate['cid']);
            endforeach;
        endif;
        return $category;

    }

    // 获取所有分类 默认获取所有分类
    public function getAllcateBySid($sid)
    {
        $cate = CategoryModel::model()
            ->getDb()
            ->where(array('cid' => $sid))
            ->queryRow();

        if ($cate['pid'] != 0) :
            $cate['parent'] = $this->getAllcateBySid($cate['pid']);
        endif;

        return $cate;

    }


        // 传入cid或pid,返回其根节点的cid
    public function getRootId($cid)
    {
        $pid = CategoryModel::model()->getDb()
            ->where(array('cid' => $cid))
            ->queryColumn('pid');
        if($pid != 0){
            $cid = $this->getRootId($pid);
        } else {
           return $cid;
        }
        return $cid;
    }

}
