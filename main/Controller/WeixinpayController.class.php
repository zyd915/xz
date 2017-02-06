<?php
class WeixinpayController extends BaseController
{
    public function payAction()
    {
        if (!$uid = arComp('list.session')->get('uid')) :
            $this->redirectError(array('Index/register', array('ar_back' => true)), '清先登陆');
        endif;
        $subject = arRequest('s', '微信购卡');
        // 分
        $price = '36000';
        $price = str_replace(array('元', '$'), '', $price);
        $res = arModule('Wxpay')->wxpayApi($subject, $price, 'xzjy_cd@126.com');
        if (strpos($res, 'weixin://') !== false) :
            $url = arU('Api/qrcode', array('data' => urlencode($res)), 'FULL');
            $this->showJsonSuccess($url);
            // $this->redirect($url);
        else :
            $this->showJsonError($res);
            // exit($res);
        endif;

    }
   
    // 通知地址
    public function wxnotifypdaAction()
    {
        $raw = file_get_contents('php://input');
        arComp('list.log')->record($raw, 'wx.raw');
        $param = arModule('Wxpay')->fromXml($raw);
        arComp('list.log')->record($param, 'wx.notify');
        if ($param['return_code'] == 'SUCCESS') :
            arModule('Wxpay')->markOrderStateComplete($param['out_trade_no'], $param);
        else :

        endif;
        echo '';

    }

}
