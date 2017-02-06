<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Order 数据库模型.
 */
class OrderModel extends ArModel
{
    // 状态正常
    const STATUS_APPROVED = 1;
    // 状态异常或禁止
    const STATUS_FORBIDDEN = 0;
    // 状态map
    public static $STATUS_MAP = array(
        0 => '未完成',
        1 => '完成',
    );

    // 支付状态 已支付
    const STATUS_PAY_YES = 1;
    // 支付状态 未支付
    const STATUS_PAY_NO = 0;
    // 状态map
    public static $STATUS_PAY_MAP = array(
        0 => '未支付',
        1 => '已支付',
    );

    // 发货状态 已发货
    const STATUS_SHIPPING_YES = 1;
    // 发货状态 未发货
    const STATUS_SHIPPING_NO = 0;
    // 状态map
    public static $STATUS_SHIPPING_MAP = array(
        0 => '未发货',
        1 => '已发货',
        2 => '已收货',
    );

    // 是否为最终订单，是为1，否为0
    const STATUS_FINAL_YES = 1;
    const STATUS_FINAL_NO = 0;

    // 订单是否是终极
    public static $STATUS_FINAL_MAP = array(
        0 => '子订单',
        1 => '最终订单',
    );

    // 订单类型
    // 普通购买订单（防止产品表type变动，以这里type决定）
    const TYPE_COMMON = 0;
    // 积分兑换
    const TYPE_JF = 1;

    // 表名
    public $tableName = 'u_order';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 修改即将写入数据的数据
    public function formatData($data)
    {
        $data['otradeno'] = OrderModel::model()->generateOrderTradeId();
        $data['ctime'] = time();
        $data['pstatus'] = OrderModel::STATUS_PAY_NO;
        $data['sstatus'] = OrderModel::STATUS_SHIPPING_NO;
        return $data;

    }

    // 生成订单号
    public function generateOrderTradeId()
    {
        return chr(rand(65, 67)) . chr(rand(68, 90)) . date('YmdHis');

    }

    // 生成多订单 购物车多商品等
    public function generateMutyOrder(array $productDataBundle)
    {
        // 父id
        $poid = $this->generateOrder($productDataBundle, OrderModel::STATUS_FINAL_NO, 0);
        foreach ($productDataBundle['products'] as $product) :
            $product['redenvlope'] = $productDataBundle['redenvlope'];
            $product['cashcoupon'] = $productDataBundle['cashcoupon'];
            $product['hjf'] = $productDataBundle['hjf'];
            $this->generateOrder($product, OrderModel::STATUS_FINAL_YES, $poid);
        endforeach;

        return $poid;

    }

    // 生成订单(单订单)
    public function generateOrder(array $productDataBundle, $final = OrderModel::STATUS_FINAL_YES, $poid = 0)
    {
        try {
            // 二位数组
            // if (arComp('validator.validator')->checkMutiArray($productDataBundle)) :
            //     $productDataBundle = array_merge($productDataBundle, $productDataBundle['product']);
            // endif;
            // 。。。
            if (!empty($productDataBundle['products'])) :
                $productDataBundle = array_merge($productDataBundle, $productDataBundle['products'][0]);
            endif;

            if (!empty($productDataBundle['product'])) :
                $productDataBundle = array_merge($productDataBundle, $productDataBundle['product']);
            endif;

            // 开启事物
            arComp('db.mysql')->transBegin();
            if ($final == OrderModel::STATUS_FINAL_NO) :
                $pid = 0;
                $subject = '复合订单';
                $quantity = $productDataBundle['aquantity'];
                $aprice = $productDataBundle['aprice'];
                $dprice = $productDataBundle['aprice'];
                $desc = '复合订单';
                $type = 0;
                $jf = 0;
            else :
                // 获取pid
                $pid = $productDataBundle['pid'];
                // 产品信息
                $product = ProductModel::model()->getDb()->where(array('pid' => $pid))->queryRow();
                // 数量
                $quantity = $productDataBundle['quantity'];
                // 单价
                $price = $product['price'];
                $dprice = $this->calculateDiscountPrice($productDataBundle);
                $desc = '下单 : ' . $productDataBundle['attrString'] . ' ' . $productDataBundle['desc'];
                $aprice = $quantity * $price;
                $subject = $product['name'] . ' * ' . $quantity;
                $type = $product['type'];
                $jf = $product['charge'];

                // 有父订单
                if ($poid && ($aprice > $dprice)) :
                    $pdprice = OrderModel::model()
                        ->getDb()
                        ->where(array('oid' => $poid))
                        ->queryColumn('dprice');
                    $cdprice = $aprice - $dprice;
                    $pdprice = $pdprice - $cdprice > 0 ? $pdprice - $cdprice : 0;
                    // 更新父订单折扣价
                    OrderModel::model()
                        ->getDb()
                        ->where(array('oid' => $poid))->update(array('dprice' => $pdprice));
                endif;

            endif;

            if (empty($productDataBundle['recaddid'])) :
                $recaddid = 0;
            else :
                $recaddid = $productDataBundle['recaddid'];
            endif;

            // price
            $order = array(
                // 用户id
                'uid' => $productDataBundle['uid'],
                // 产品id
                'pid' => $pid,
                // subject
                'subject' => $subject,
                // 类型
                'type' => $type,
                // 兑换积分,用于记录当时兑换积分
                'jf' => $jf,
                // 价格
                'price' => $aprice,
                // 折扣价
                'dprice' => $dprice,
                // 数量
                'quantity' => $quantity,
                // desc
                'desc' => $desc,
                // 收货地址id
                'recaddid' => $recaddid,
                // 父订单号
                'poid' => $poid,
                // 是否为终极订单
                'isfinal' => $final,
            );

            if (OrderModel::STATUS_FINAL_YES === $final) :
                $stock = $product['stock'] - $quantity;

                // 减库存
                ProductModel::model()->getDb()
                    ->where(array('pid' => $pid))
                    ->update(array('stock' => $stock));
                // 记录日志
                $who = UserModel::model()->getDb()->where(array('uid' => $order['uid']))->queryColumn('uname');
                $who = $who.' 用户id：'.$order['uid'];
                if ($order['type'] == ProductModel::TYPE_JF) :

                    $do = "使用积分".$order['jf']*$order['quantity'].'兑换了';
                    $what = $product['name']."X".$order['quantity'].' 产品id：'.$order['pid'];
                else :
                    $do = "使用人民币".$order['price']*$order['quantity'].'购买了';
                    $what = $product['name']."X".$order['quantity'].' 产品id：'.$order['pid'];
                endif;
                $res = LogModel::model()->record($who,$do,$what);
            endif;

            // 生成订单号
            $orderId = OrderModel::model()->getDb()->insert($order);
            // 提交
            arComp('db.mysql')->transCommit();
            return $orderId;

        } catch (Exception $e) {
            // 回滚
            arComp('db.mysql')->transRollBack();
            return false;

        }

    }

    // 计算折扣价
    public function calculateDiscountPrice($productDataBundle)
    {
        // 获取pid
        $pid = $productDataBundle['pid'];
        // 产品信息
        $product = ProductModel::model()->getDb()->where(array('pid' => $pid))->queryRow();
        // 数量
        $quantity = $productDataBundle['quantity'];
        // 单价
        $price = $product['price'];
        // 红包优惠
        $redenvlopeDiscount = 0;
        // 红包减免
        if (!empty($productDataBundle['redenvlope'])) :
            $condition = array(
                // 判断红包
                'cid' => $productDataBundle['redenvlope'],
                // 判断产品
                'pid' => $pid,
                // 类型
                'type' => CouponModel::TYPE_REDENVELOPE,
                // 判断是否为用户
                'uid' => $productDataBundle['uid'],
                // 判断状态为可用
                'status' => CouponModel::STATUS_UNUSE,
                // 判断过期
                'etime > ' => time(),
            );
            $redenvlopeDiscount = CouponModel::model()->getDb()->where($condition)->queryColumn('price');
        endif;

        // 红包优惠
        $cashcouponDiscount = 0;
        // 现金卷减免
        if (!empty($productDataBundle['cashcoupon'])) :
            $condition = array(
                // 判断红包
                'cid' => $productDataBundle['cashcoupon'],
                // 类型
                'type' => CouponModel::TYPE_CASHCOUPON,
                // 判断是否为用户
                'uid' => $productDataBundle['uid'],
                // 判断状态为可用
                'status' => CouponModel::STATUS_UNUSE,
                // 判断过期
                'etime > ' => time(),
            );
            $cashcouponDiscount = CouponModel::model()->getDb()->where($condition)->queryColumn('price');
        endif;

        // 积分优惠
        $hjfDiscount = 0;
        // 高级积分优惠
        if (!empty($productDataBundle['hjf'])) :
            $hjfDiscount = $productDataBundle['hjf'] / 100;
        endif;

        // 总价
        $basePrice = $quantity * $price;
        // 减去折扣额度
        $basePrice = $basePrice - $redenvlopeDiscount - $cashcouponDiscount - $hjfDiscount;

        return $basePrice > 0 ? $basePrice : 0;

    }

}
