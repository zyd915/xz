<?php
    class AdModule{
         // 获取图片的位置
        public function statusToggle($arr,$gid='gid')
        {
            if (arComp('validator.validator')->checkMutiArray($arr)) :
                foreach ($arr as $key => $gallery) {
                    $arr[$key]['galleryUrl'] = GalleryModel::model()->getDb()->where(array('gid'=>$gallery[$gid]))->queryRow();
                }
                else:
                  $arr['galleryUrl'] = GalleryModel::model()->getDb()->where(array('gid'=>$arr[$gid]))->queryRow();
            endif;
            return $arr;
        }
    }

    ?>