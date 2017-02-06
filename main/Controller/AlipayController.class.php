<?php
// 支付模块 支付宝
class AlipayController extends BaseController
{
    // 转账mzm支付宝接口
    public function payAction()
    {
        if (!$uid = arComp('list.session')->get('uid')) :
            $this->redirectError(array('Index/register', array('ar_back' => true)), '清先登陆');
        endif;
        $subject = arRequest('s', 'ALIPAY购卡');
        // $price = arRequest('p', '368');
        $price = '360';
        $price = str_replace(array('元', '$'), '', $price);
        return arModule('Alipay')->alipayApi($subject, $price, 'xzjy_cd@126.com');

    }

    // 通知页面
    public function notifyAction()
    {
        // 加载库
        require_once arCfg('PATH.EXT') . 'alipay' . DS . 'lib/alipay_notify.class.php';
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify(arCfg('ALIPAY_CONFIG'));
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) :
            // 商户订单号
            // $out_trade_no = $_POST['out_trade_no'];
            // 支付宝交易号
            // $trade_no = $_POST['trade_no'];
            // 交易状态
            // $trade_status = $_POST['trade_status'];
            if ($_POST['trade_status'] == 'TRADE_FINISHED') :
                //1、开通了普通即时到账，买家付款成功后。
                //2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
                // 日志
                arComp('list.log')->record(arRequest(''), 'alipay.nitify.finished');
                // 标记完成
                arModule('Alipay')->markOrderStateComplete(arRequest('out_trade_no'), arRequest());
            elseif ($_POST['trade_status'] == 'TRADE_SUCCESS') :
                //该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
                // 日志
                arComp('list.log')->record(arRequest(''), 'alipay.notify.success');
                // 标记完成
                arModule('Alipay')->markOrderStateComplete(arRequest('out_trade_no'), arRequest());
            endif;
            echo "success";     //请不要修改或删除
        else :
            // 日志
            arComp('list.log')->record(arRequest(''), 'alipay.fail');
            //验证失败
            echo "fail";
        endif;

    }

    // 跳转页面
    public function returnAction()
    {
        // 加载库
        require_once arCfg('PATH.EXT') . 'alipay' . DS . 'lib/alipay_notify.class.php';
        $alipayNotify = new AlipayNotify(arCfg('ALIPAY_CONFIG'));
        $verify_result = $alipayNotify->verifyReturn();

        if ($verify_result) :
            // 日志
            arComp('list.log')->record(arRequest(''), 'alipay.return.success');
            $this->redirect(array('Index/paySuccess'));
        else :
            arComp('list.log')->record(arRequest(''), 'alipay.return.fail');
            echo "验证失败";
        endif;

    }

}
