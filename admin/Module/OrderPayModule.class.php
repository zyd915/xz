<?php
class OrderPayModule
{
    // 支付完成生成支付信息
    public function processPayComplete(array $data)
    {
        // $data =
        // array (
        //   'discount' => '0.00',
        //   'logistics_type' => 'EXPRESS',
        //   'payment_type' => '1',
        //   'subject' => '爱德堡晚秋清甜白葡萄酒（又名：爱德堡晚秋甜白葡萄酒） * 1',
        //   'logistics_fee' => '0.00',
        //   'trade_no' => '2015013018526135',
        //   'buyer_email' => '895466164@qq.com',
        //   'gmt_create' => '2015-01-30 14:40:54',
        //   'notify_type' => 'trade_status_sync',
        //   'quantity' => '1',
        //   'logistics_payment' => 'SELLER_PAY',
        //   'out_trade_no' => 'CG20150130143833',
        //   'seller_id' => '2088511633523261',
        //   'notify_time' => '2015-01-30 14:55:17',
        //   'trade_status' => 'WAIT_SELLER_SEND_GOODS',
        //   'is_total_fee_adjust' => 'N',
        //   'gmt_payment' => '2015-01-30 14:41:24',
        //   'total_fee' => '0.01',
        //   'seller_email' => 'mzm@coiu.cn',
        //   'price' => '0.01',
        //   'buyer_id' => '2088402383415350',
        //   'receive_mobile' => '15283525960',
        //   'gmt_logistics_modify' => '2015-01-30 14:40:54',
        //   'receive_phone' => '15283525960',
        //   'notify_id' => 'f60d55ba789d37d1a25531bb91609ddb3y',
        //   'receive_name' => 'imroot1',
        //   'use_coupon' => 'N',
        //   'sign_type' => 'MD5',
        //   'sign' => '52da0ebf8287fd6695ea6d7bea7d4b75',
        //   'receive_address' => '东兴区-内江-四川aaaa',
        // );

        try {
            // 开启事物
            arComp('db.mysql')->transBegin();

            $otradeno = $data['out_trade_no'];
            if (OrderPayModel::model()->getDb()->where(array('otradeno' => $otradeno))->count() > 0) :
                return false;
            else :
                $payInfo = array(
                    'otradeno' => $otradeno,
                    'tradeno' => $data['trade_no'],
                    'subject' => $data['subject'],
                    'ctime' => time(),
                    'price' => $data['total_fee'],
                    'selleremail' => $data['seller_email'],
                    'buyeremail' => $data['buyer_email'],
                    // 默认为支付宝担保交易
                    'type' => OrderPayModel::TYPE_ALIPAY_ESCOW,
                    // 默认为正常状态
                    'status' => OrderPayModel::STATUS_OK,
                    // 序列化参数
                    'param' => serialize($data),
                );

                OrderPayModel::model()->getDb()->insert($payInfo);

                $order = array(
                    // 支付了
                    'pstatus' => OrderModel::STATUS_PAY_YES,
                );

                // 更新订单状态
                OrderModel::model()->getDb()->where(array('otradeno' => $otradeno))->update($order);

                // 提交
                arComp('db.mysql')->transCommit();

                return true;
            endif;

        } catch (Exception $e) {
            // 回滚
            arComp('db.mysql')->transRollBack();
            return false;
        }
    }

    // 交易完成处理订单状态
    public function processPayFinished(array $data)
    {
        $otradeno = $data['out_trade_no'];
        try {
            // 开启事物
            arComp('db.mysql')->transBegin();
            // 修改订单状态
            $condition = array(
                'status' => OrderModel::STATUS_FORBIDDEN,
                'otradeno' => $otradeno,
            );

            $order = array(
                'status' => OrderModel::STATUS_APPROVED,
                'etime' => time(),
            );

            OrderModel::model()->getDb()->where($condition)->update($order);
            // 提交
            arComp('db.mysql')->transCommit();
            return true;

        } catch (Exception $e) {
            // 回滚
            arComp('db.mysql')->transRollBack();
            return false;
        }

    }

}
