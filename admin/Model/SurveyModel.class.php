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
class SurveyModel extends ArModel
{
    const TYPE_SELECTION = 1;
    const TYPE_CHECKED = 2;
    public static $TYPE_MAP = array(
        '1' => '选择题',
        '2' => '判断题',
    );

    public static $ANWSER_TYPE = array(
        '0' => 'A',
        '1' => 'B',
        '2' => 'C',
        '3' => 'D'
        );

    // 表名
    public $tableName = 'u_survey';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }




}
