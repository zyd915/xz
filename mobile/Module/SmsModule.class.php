<?php
class SmsModule
{
    /**
     * 发送短信
     */
    // public function send($phoneNumber, $verfy, $time = "2", $tempId = "20106")
    // {
    //     // 加载发送短信库
    //     include arCfg('EXTENSION_DIR') . 'CCP_REST_SMS_DEMO_PHP' . DS . 'SendTemplateSMS.php';
    //     sendTemplateSMS($phoneNumber, array($verfy, $time), $tempId);

    // }
    public function juhecurl($sendto, $code, $tpl_id, $ispost = 1) 
    {
        $url="http://v.juhe.cn/sms/send";
        $smsConf['key'] = 'af8daa1642f6c3a6475eabea7b1281bf';
        // 发送的电话号码
        $smsConf['mobile'] = $sendto;
        $smsConf['tpl_id'] = $tpl_id;
        $smsConf['tpl_value'] = '#code#=' . $code;
        $params = $smsConf;
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if ( $ispost ) {
                curl_setopt( $ch , CURLOPT_POST , true );
                curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
                curl_setopt( $ch , CURLOPT_URL , $url );
        }else{
                if($params){
                    curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
                }else{
                    curl_setopt( $ch , CURLOPT_URL , $url);
                }
        }

        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }

        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );

        $content = $response;

        $result = json_decode($content,true);
        $error_code = $result['error_code'];
        if ($error_code == 0) {
            return true;
        } else {
            return false;
        }

    }

}
