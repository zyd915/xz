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
class VideoModel extends ArModel
{
    //专家视频
    const EXPERT_VIDEO = 1;
    //教学视频
    const THEACH_VIDEO = 2;
    //公司活动
    const ACTIVE_VIDEO =3;

    public static $VTYPE_MAP =array(
        '1'=>'专家视频',
        '2'=>'教学视频',
        '3'=>'公司活动'
        );
    public $tableName = 'h_video';
    static public function model($class = __CLASS__)
    {
        return parent::model($class);
    }

}
