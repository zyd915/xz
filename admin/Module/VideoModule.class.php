<?php
class VideoModule
{
	// 分类
    public function getCatMap()
    {
        // 视频分类
        $cats = VideoclassModel::model()->getDb()
            ->order('sorder desc')
            ->queryAll();

        $catMap = array();
        // cats分类
        foreach ($cats as $cat) :
            $catMap[$cat['cid']] = $cat['name'];
        endforeach;
        return $catMap;

    }
	

    // 获取文章的图片
    public function getArtGallery(array $arr)
    {
        if (arComp('validator.validator')->checkMutiArray($arr)) :
            foreach ($arr as $key => $gallery) {
                if($gallery['gid']){
                    $arr[$key]['galleryUrl'] = GalleryModel::model()->getDb()->where(array('gid'=>$gallery['gid']))->queryRow();
                }
            }
        else :
            if($arr['gid']){
               $arr['galleryUrl'] = GalleryModel::model()->getDb()->where(array('gid'=>$arr['gid']))->queryRow();
            }
        endif;
        return $arr;
    }

    // 获取店铺详细信息
    public function getArticlesDetailInfo(array $articles)
    {

        if (arComp('validator.validator')->checkMutiArray($articles)) :
            foreach ($articles as &$article) :
                $article = $this->getArticlesDetailInfo($article);
            endforeach;
        else :
            $article = $articles;
            $article['catName'] = VideolistModel::model()
                ->getDb()
                ->where(array('cid' => $article['cid']))
                ->queryColumn('name');
            // html 反转义
            $article['content'] = stripcslashes($article['content']);

            return $article;
        endif;

        return $articles;
    }





}















?>