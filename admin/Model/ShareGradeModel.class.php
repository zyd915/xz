<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * share数据库模型.
 */
class ShareGradeModel extends ArModel
{
    // 第一级
    const RANK_ONE = 1;
    // 第二级
    const RANK_TWO = 2;
    // 第三级
    const RANK_THREE = 3;

    // 状态map
    public static $RANK_MAP = array(
        1 => '一级代理',
        2 => '二级代理',
        3 => '三级代理',
    );

    // 表名
    public $tableName = 'u_share_grade';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

}
