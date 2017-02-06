<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 广告数据库模型.
 */
class AdModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;

    // 状态map
    public static $STATUS_MAP = array(
        0 => '隐藏',
        1 => '显示',
    );

    // 首页轮播图
    const TYPE_INDEX = 0;
    // 网页底部
    const TYPE_BUTTOM = 1;
    // 公司活动上面
    const TYPE_BANNER = 2;
    // 顶部第一个
    const TYPE_HEAD_ONE = 3;
    // 顶部第二个
    const TYPE_HEAD_TWO = 4;
    // 即时资讯
    const TYPE_INTEFACE = 5;
    // 多媒体
    const TYPE_VIDEO = 6;
    //智赢学院
    const TYPE_COLLEGE = 7;
    // 专业评论
    const TYPE_COMMEN = 8;
    // 系统头像
    const  TYPE_HEADER = 9;
    // 位置
    public static $TYPE_MAP = array(
        0 => '首页轮播图',
        1 => '手机端轮播图'

    );

    // 表名
    public $tableName = 'h_ad';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 修改即将写入数据的数据
    public function formatData($data)
    {
        // 默认用户激活状态
        $data['ctime'] = time();
        return $data;

    }

}
