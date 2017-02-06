<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * user 数据库模型.
 */
class GalleryModel extends ArModel
{
    // 表名
    public $tableName = 'p_gallery';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 裁剪图片
    public function crop($originFile, $cropFile, $sizeWidth = 273, $sizeHeight = 273)
    {
        // 加载 wideimage 扩展
        require_once arCfg('EXTENSION_DIR') . 'wideimage'  . DS . 'WideImage.php';
        try {
            // 缩放
            WideImage::load($originFile)->resize($sizeWidth, $sizeHeight)->saveToFile($cropFile);
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

     // 裁剪图片
    public function cropByCoordinate($originFile, $cropFile, $x = 0, $y = 0, $sizeWidth = 273, $sizeHeight = 273)
    {
        // 加载 wideimage 扩展
        require_once arCfg('EXTENSION_DIR') . 'wideimage'  . DS . 'WideImage.php';
        try {
            // 缩放
            WideImage::load($originFile)->crop($x, $y, $sizeWidth, $sizeHeight)->saveToFile($cropFile);
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

}
