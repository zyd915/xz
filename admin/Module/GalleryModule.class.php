<?php
// 用户中间件
class GalleryModule
{
    // 删除产品图片
    public function delGallery($gid)
    {
        $galleryCondition = array('gid' => $gid);

        $galleryInfo = GalleryModel::model()->getDb()
            ->where($galleryCondition)
            ->queryRow();

        GalleryModel::model()->getDb()->where($galleryCondition)->delete();

        // 转换为绝对路径
        $dirUrl = arComp('url.route')->pathToDir($galleryInfo['url']);
        $dirUrlc = arComp('url.route')->pathToDir($galleryInfo['curl']);

        @unlink($dirUrl);
        @unlink($dirUrlc);

        return true;

    }
    // 获取图片信息
    public function getPht($arr)
    {
         if($gid = isset($arr['gallery'])?$arr['gallery']:$arr['gid'])
            {
            $galleryInfor = GalleryModel::model()->getDb()->where(array('gid' => $gid))->queryRow();
                if($galleryInfor)
                {
                  $arr['gInfor'] = $galleryInfor;
                }
            }
        return $arr;
    }

    // 获取图片(二维数组)
    public function getImg($data)
    {
        if ($data) :
            foreach ($data as $key => $dataDetail) :
                $data[$key] = $this->getPht($dataDetail);
            endforeach;
        endif;
        return $data;

    }

   
}
