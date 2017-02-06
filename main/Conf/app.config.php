<?php
/**
 * Ar default app config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
	'DEBUG_SHOW_TRACE' => false,
    'collegeInsert' => array('ErLzhuan','ErWzhuan','ErLben','ErWben','TqpL','TqpW','YiWben','YiLben','YiWzhuan','YiLzhuan'),

    // 报告页面
    'lbreport' => array(
        // 理科
        'Major-choice-l' => 'http://www.apesk.com/major-choice/g3_science/zyxz_report_admin_FROM_STONE_bzy1l.asp?id=',
        // 文科
        'Major-choice-w' => 'http://www.apesk.com/major-choice/g3/zyxz_report_admin_FROM_STONE_bzy1.asp?id=',
        // 专业选择
        'Major-choice-wlfk' => 'http://www.apesk.com/major-choice/zyxz_report_admin_FROM_STONE_wlfk1.asp?id=',
    ),

    // 支付宝配置
    'ALIPAY_CONFIG' => array(
        'partner' => '2088021533364825',
        'key' => 'ru5vy7gc6kc5goq2vqtbq9hf7uth0pjv',
        'sign_type' => strtoupper('MD5'),
        'input_charset' => strtolower('utf-8'),
        'cacert' => arCfg('PATH.EXT') . 'alipay' . DS . 'cacert.pem',
        'transport' => 'http',
    ),

    // 微信支付配置
    'WXPAY_CONFIG' => array(
        'appid' => 'wx7408a376ed720537',
        'mch_id' => '1269672701',
        'key' => 'FFDDDDYUIOPKJGRFDSWQBBSZXCFGSYUI',
        // 回调
        'notify_url' => 'http://www.xzgk.net/Weixinpay/wxnotifypda',
        'trade_type' => 'NATIVE',
    ),

);