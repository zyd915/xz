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
class ProductModel extends ArModel
{
    // 状态正常(用于产品状态，勿删)
    const STATUS_APPROVED = 1;
    // 状态异常或禁止(用于产品状态，勿删)
    const STATUS_FORBIDDEN = 0;

    // 集成状态 集成
    const CSTATUS_YES = 1;
    // 状态异常或禁止
    const CSTATUS_NO = 0;

    // 集成状态map
    public static $CSTATUS_MAP = array(
        '0' => '未集成',
        '1' => '集成',
    );

    // 产品类型
    // 普通
    const TYPE_COMMENT = 0;
    // 积分商品
    const TYPE_JF = 1;
    // 限时抢购
    const TYPE_TIME = 2;
    // 团购
    const TYPE_GROUP = 3;
    const TYPE_DETAIL = 4;

    public static $STATUS_MAP = array(
        '0' => '下架',
        '1' => '上架',
    );

    // 表名
    public $tableName = 'u_product';

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
            'name' => array('required', '名称不能为空'),
        );

    }

    // 修改即将写入数据的数据
    public function formatData($data)
    {
        // 默认用户激活状态
        $data['status'] = ProductModel::STATUS_APPROVED;
        // 添加数据gallery清理，
        $data['gallery'] = trim($data['gallery'], ',');
        // 添加时间
        $data['atime'] = time();
        return $data;

    }

    // 检测库存是否充足
    public function checkOutOfStock($pid, $has = 1)
    {
        return ProductModel::model()
            ->getDb()->where(array('pid' => $pid))
            ->queryColumn('stock') >= $has;

    }

    // 获取店铺下边所有产品
    public function getProductBySid($sid)
    {
        $apps = ProductModel::model()
            ->getDb()
            ->where(array('sid' => $sid))
            ->queryAll();

        foreach ($apps as &$app) :
            $app['logo'] = GalleryModel::model()
                ->getDb()
                ->where(array('gid' => $app['gallery']))
                ->queryRow();
        endforeach;
        return $apps;

    }

    // 让赞或者关注增加一个数字
    public function autoadd($key, $value, $field, $num)
    {
        $pvalue =ProductModel::model()->getDb()
            ->where(array($key=> $value))
            ->queryColumn($field);

        $pvalue = (int)$pvalue +(int)$num;

        $res = ProductModel::model()
            ->getDb()
            ->where(array($key=> $value))
            ->update(array($field=>$pvalue));
    }

}
