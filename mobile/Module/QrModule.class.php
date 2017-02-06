<?php
// ARPHP二维码生成
class QrModule
{
    // 初始化
    public function initModule()
    {
        // 引入二维码类库
        include AR_APP_PATH . 'Ext' . DS . 'phpqrcode' . DS . 'qrlib.php';

    }

    // 生成png图片
    public function png($data, $size = 2, $fileName = false)
    {
        // 生成图片
        return QRcode::png($data, $fileName, 'H', $size);

    }

}
