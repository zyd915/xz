<?php
class WxpayModule
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
            'price' => (double)($price / 100),
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
        arLm('admin.Model');
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
                'trade_no' => $bundle['transaction_id'],
                'type' => OrderPayModel::TYPE_WX,
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
    public function wxpayApi($subject, $price, $sellerEmail = 'mzm@coiu.cn')
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $key = arCfg('WXPAY_CONFIG.key');

        // 商户订单号
        $out_trade_no = $this->createOrder($subject, $sellerEmail, '', $price);

        $postData = array(
            'appid' => arCfg('WXPAY_CONFIG.appid'),
            'mch_id' => arCfg('WXPAY_CONFIG.mch_id'),
            'nonce_str' => md5(time()),
            'body' => $subject,
            'out_trade_no' => $out_trade_no,
            'total_fee' => $price,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url' => arCfg('WXPAY_CONFIG.notify_url'),
            'trade_type' => arCfg('WXPAY_CONFIG.trade_type'),
        );
        ksort($postData);
        $postString = urldecode(http_build_query($postData));
        $sign = strtoupper(md5($postString . '&key=' . $key));
        $postData['sign'] = $sign;

        $res = arComp('rpc.api')->remoteCall($url, $this->toXml($postData), 'post');

        $resArray = $this->fromXml($res);

        if (!empty($resArray['err_code_des'])) :
            return $resArray['err_code_des'];
        else :
            return array('code_url' => $resArray['code_url'], 'out_trade_no' => $out_trade_no);
        endif;

    }

     /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function fromXml($xml)
    {
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

    }

     /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function toXml($valus)
    {
        if(!is_array($valus)
            || count($valus) <= 0)
        {
            throw new WxPayException("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($valus as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

}
