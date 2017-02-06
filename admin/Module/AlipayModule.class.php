<?php
// 支付宝交易 及时到账 担保交易
class AlipayModule
{
    // 加载担保交易创建交易
    public function loadEscowCreateTrade()
    {
        // 目录地址
        $alipayPath = arCfg('EXTENSION_DIR') . 'alipay/alipay_escow/create_partner_trade_by_buyer-PHP-UTF-8/';
        Ar::setConfig('ALIPAY_CONFIG.cacert', $alipayPath . 'cacert.pem');

        // 加载阿里库
        // require_once($alipayPath . "alipay.config.php");
        require_once($alipayPath . "lib/alipay_submit.class.php");
        // 通知页面
        require_once($alipayPath . "lib/alipay_notify.class.php");

    }

    // 发货
    public function deliver($otradeno, $logisticName = '', $invoiceNo = '', $transportType = 'EXPRESS')
    {
        if (empty($logisticName) || empty($invoiceNo)) :
            throw new ArException("物流公司或运单号不能为空");
        endif;

        if (!$payInfo = OrderPayModel::model()->getDb()->where(array('otradeno' => $otradeno))->queryRow()) :
            throw new ArException("支付信息不存在");
        endif;

        $condition = array(
            'otradeno' => $otradeno,
            'sstatus' => OrderModel::STATUS_SHIPPING_NO
        );

        if (!$order = OrderModel::model()->getDb()->where($condition)->queryRow()) :
            throw new ArException("该订单已经发货了");
        endif;

         // 目录地址
        $alipayPath = arCfg('EXTENSION_DIR') . 'alipay/alipay_escow/send_goods_confirm_by_platform-PHP-UTF-8/';
        Ar::setConfig('ALIPAY_CONFIG.cacert', $alipayPath . 'cacert.pem');

        // 加载阿里库
        require_once($alipayPath . "lib/alipay_submit.class.php");

        try {
            // 开启事物
            arComp('db.mysql')->transBegin();
            //支付宝交易号
            $trade_no = $payInfo['tradeno'];
            //必填

            //物流公司名称
            $logistics_name = $logisticName;
            //必填
            //物流发货单号
            $invoice_no = $invoiceNo;
            //物流运输类型
            $transport_type = $transportType;
            //三个值可选：POST（平邮）、EXPRESS（快递）、EMS（EMS）
            /************************************************************/

            //构造要请求的参数数组，无需改动
            $parameter = array(
                "service" => "send_goods_confirm_by_platform",
                "partner" => arCfg('ALIPAY_CONFIG.partner'),
                "trade_no"  => $trade_no,
                "logistics_name"    => $logistics_name,
                "invoice_no"    => $invoice_no,
                "transport_type"    => $transport_type,
                "_input_charset"    => arCfg('ALIPAY_CONFIG.input_charset')
            );

            //建立请求
            $alipaySubmit = new AlipaySubmit(arCfg('ALIPAY_CONFIG'));
            $html_text = $alipaySubmit->buildRequestHttp($parameter);
            //解析XML
            //注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
            $doc = new DOMDocument();
            $doc->loadXML($html_text);

            arComp('list.log')->record(serialize($doc), 'deliver');

            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //解析XML
            if (!empty($doc->getElementsByTagName( "alipay" )->item(0)->nodeValue)) {
                // $alipay = $doc->getElementsByTagName( "alipay" )->item(0)->nodeValue;
                // echo $alipay;
                $order['sstatus'] = OrderModel::STATUS_SHIPPING_YES;
                $order['stime'] = time();
                OrderModel::model()->getDb()->where(array('otradeno' => $otradeno))->update($order);
                // 提交
                arComp('db.mysql')->transCommit();
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            // 回滚
            arComp('db.mysql')->transRollBack();
            return false;
        }
    }

}
