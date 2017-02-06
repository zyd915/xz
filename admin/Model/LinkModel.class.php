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
class LinkModel extends ArModel
{
    //合作机构
    const COOPER_COMPANY = 4;
    //合作媒体
    const COOPER_MEDIA = 1;
    //友情链接
    const COOPER_LINK =2;
    //footer链接
    const COOPER_FOOTER =3;
    public static $COOPER_MAP =array(
        '4'=>'合作机构',
        '1'=>'合作媒体',
        '2'=>'友情链接',
        '3'=>'footer链接'
        );
    public $tableName = 'h_link';
    static public function model($class = __CLASS__)
    {
        return parent::model($class);
    }

}
