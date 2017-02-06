<?php
class webSiteModule {

// 获取project中的图片
    public function getGallery($arr)
    {
        foreach ($arr as $key => $gallery) {
          $arr[$key]['gallery'] = GalleryModel::model()->getDb()->where(array('gid' => $gallery['gid']))->queryRow();
        }
        return $arr;
    }

    // 除去数组中值为空的数组


}
?>