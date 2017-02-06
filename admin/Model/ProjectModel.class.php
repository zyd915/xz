<?php
class ProjectModel extends arModel{
    // 投资
    const TYPE_INVEST = 1;
    // 赞助
    const TYPE_SUPPORT = 2;
    // 机遇
    const TYPE_CHANCE = 3;

    // 集成状态map
    public static $TYPE_MAP = array(
        '1' => '投资',
        '2' => '赞助',
        '3' => '机遇',
    );
     // 表名
    public $tableName = 'u_project';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }
}
?>