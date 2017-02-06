<?php
// 微信jsapi支付(公众号支付)
class WxJsapiController extends BaseController
{
    public function init()
    {
        // Ar::setConfig('DEBUG_LOG', true);
        $this->setLayoutFile('');
        $this->apiDir = arCfg('DIR.EXT') . 'wxapi/';
        arComp('list.log')->record(arRequest(), arCfg('requestRoute.a_a'));

    }

    // 支付控制器
    public function payAction()
    {
        if (!$uid = arComp('list.session')->get('uid')) :
            $this->redirectError(array('Index/login', array('ar_back' => true)), '清先登陆');
        endif;
        $subject = arRequest('s', '微信公众号购卡');
        // 分
        $price = '36000';
        // $price = '1';
        arLm('main.Module');

        // 商户订单号
        $out_trade_no = arModule('Wxpay')->createOrder($subject, 'xzjy_cd@126.com', '', $price);
        $this->assign(array('out_trade_no' => $out_trade_no, 'subject' => $subject, 'price' => $price));

        require_once $this->apiDir . "lib/WxPay.Api.php";
        require_once $this->apiDir . "example/WxPay.JsApiPay.php";
        require_once $this->apiDir . 'example/log.php';
        $this->display();

    }

    // 回调通知
    public function notifyAction()
    {
        arLm('main.Module');
        require_once $this->apiDir . "lib/WxPay.Api.php";
        require_once $this->apiDir . "lib/WxPay.Notify.php";
        require_once $this->apiDir . 'example/log.php';
        $this->display();

    }

}
