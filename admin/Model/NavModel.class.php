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
class NavModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;

    // 状态map
    public static $STATUS_MAP = array(
        0 => '禁用',
        1 => '激活',
    );

    const LV_1 = 0;
    const LV_2 = 1;
    const LV_3 = 2;

    // 级别map
    public static $LV_MAP = array(
        0 => '一级',
        1 => '二级',
        2 => '三级',
    );

    const NTYEP_SHOW = 0;
    const NTYPE_FUNC = 1;
    const NTYPE_URL = 2;

    // 级别map
    public static $NTYPE_MAP = array(
        0 => 'SHOW',
        1 => 'Javascript',
        2 => 'URL',
    );

    const DISPLAY_HEAD = 0;
    const DISPLAY_MID = 1;
    const DISPLAY_BOTTOM = 2;

    // 级别map
    public static $DISPLAY_MAP = array(
        0 => '头部',
        1 => '中间',
        2 => '底部',
    );

    // 表名
    public $tableName = 'p_nav';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 添加数据验证规则
    public function rules()
    {
        // 验证规则
        return array(
            'title' => array('required', '名称不能为空'),
            'name' => array('required', '名字不能为空'),
        );

    }

    // 修改即将写入数据的数据
    public function formatData($data)
    {
        $data['status'] = self::STATUS_APPROVED;
        return $data;

    }

    // 获取所有分类 默认获取所有分类
    public function getAllMenuByPid($pid = 0)
    {
        static $menuNotSort = array();

        $con['pid'] = $pid;
        $menu = NavModel::model()
            ->getDb()
            ->order('sort desc')
            ->where($con)
            ->queryAll();

        $padStringUnit = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $titlePadMap = array(
            '│', '├─', '└─',
        );

        if ($menu) :
            $length = count($menu);
            for ($i = 0; $i < $length; $i++) :
                $padString = $padStringUnit;
                if ($menu[$i]['level'] == 1) :
                    $padString = $padStringUnit .  $titlePadMap[0] .  $padStringUnit;
                elseif ($menu[$i]['level'] == 2) :
                    $padString = $padStringUnit . $titlePadMap[0] . $padStringUnit . $titlePadMap[0] . $padStringUnit;
                endif;
                // 最后一个分类
                if ($i == $length - 1) :
                    $padString .= $titlePadMap[2];
                else :
                    $padString .= $titlePadMap[1];
                endif;

                if ($menu[$i]['id'] != 1) :
                    $menu[$i]['title'] = $padString . $menu[$i]['title'];
                endif;

                array_push($menuNotSort, $menu[$i]);
                $this->getAllMenuByPid($menu[$i]['id']);
            endfor;
        endif;
        return $menuNotSort;

    }

    // 获取所有分类 默认获取所有分类
    public function getAllNavsByPid($pid = 1)
    {
        $con['pid'] = $pid;
        // $con['type'] = CategoryModel::IN_PC;
        $category = NavModel::model()
            ->getDb()
            ->order('sort desc')
            ->where($con)
            ->queryAll();

        if ($category) :
            foreach ($category as & $cate) :
                $cate['children'] = $this->getAllNavsByPid($cate['id']);
            endforeach;
        endif;
        return $category;

    }

}
