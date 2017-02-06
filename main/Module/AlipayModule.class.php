<?php
class AlipayModule
{
    // 创建订单并检查更新
    public function createOrder($subject, $sellerEmail, $buyereEmail, $price, $orderTradeParam = '')
    {
        $uid = arComp('list.session')->get('uid');

        if (!$uid) :
            exit('尚未登陆');
        endif;

        $orderTradeNo = $this->generateOrderTradeId();
        if (is_array($orderTradeParam)) :
            $orderTradeParam = var_export($orderTradeParam, true);
        endif;
        $orderInfo = array(
            'subject' => $subject,
            'seller_email' => $sellerEmail,
            'buyer_mail' => $buyereEmail,
            'price' => $price,
            'order_trade_no' => $orderTradeNo,
            'param' => $orderTradeParam,
            'ctime' => time(),
            // 用户id
            'uid' => $uid,
        );

        arComp('db.mysql')->table('u_order')->insert($orderInfo, 1);

        return $orderTradeNo;

    }

    // 标记订单成功
    public function markOrderStateComplete($orderTradeNo, $bundle = '')
    {
        $condition = array(
            'order_trade_no' => $orderTradeNo,
        );

        $order = arComp('db.mysql')->table('u_order')->where($condition)->queryRow();

        if ($order['state'] == 1) :
            arComp('list.log')->record('订单' . $orderTradeNo . '已经是完成状态', 'mark.fail');
            return false;
        else :
            $update = array(
                'state' => 1,
            );

            if (is_array($bundle)) :
                $update = array_merge($update, $bundle);
                $update['param'] = var_export($bundle, 1);
            endif;
            // 激活vip
            arModule('Vip')->tobeVip($order['uid']);
            arComp('list.log')->record('订单' . $orderTradeNo . '标记完成成功', 'mark.success');
            return arComp('db.mysql')->table('u_order')->where($condition)->update($update, 1);

        endif;

    }

    // 生成订单号
    public function generateOrderTradeId()
    {
        return chr(rand(65, 67)) . chr(rand(68, 90)) . date('YmdHis');

    }

    // 请求支付宝接口
    public function alipayApi($subject, $price, $sellerEmail = 'mzm@coiu.cn')
    {
        require_once arCfg('PATH.EXT') . 'alipay' . DS . 'lib/alipay_submit.class.php';
        //支付类型
        $payment_type = "1";
        //服务器异步通知页面路径
        $notify_url = arComp('url.route')->serverName() . arU('notify');
        //页面跳转同步通知页面路径
        $return_url = arComp('url.route')->serverName() . arU('return');

        $seller_email = $sellerEmail;

        $subject = $subject;
        $total_fee = $price;

        //订单描述
        $body = 'xz alipay order';

        //商品展示地址
        $show_url = 'http://www.xzgk.net';
        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1
        //商户订单号
        $out_trade_no = arModule('Alipay')->createOrder($subject, $seller_email, '', $price);

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => arCfg('ALIPAY_CONFIG.partner'),
            "payment_type"    => $payment_type,
            "notify_url"  => $notify_url,
            "return_url"  => $return_url,
            "seller_email"    => $seller_email,
            "out_trade_no"    => $out_trade_no,
            "subject" => $subject,
            "total_fee"   => $total_fee,
            "body"    => $body,
            "show_url"    => $show_url,
            "anti_phishing_key"   => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset"  => arCfg('ALIPAY_CONFIG.input_charset'),
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit(arCfg('ALIPAY_CONFIG'));

        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认支付 : ￥" . $total_fee);
        echo $html_text;

    }


}
