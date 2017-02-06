<?php
// 用户中间件
class ArticleModule
{
    // 首页文章
    public function indexNewArticles()
    {
        $articles = ArticleModel::model()
            ->getDb()
            ->limit(5)
            ->order('sorder desc, aid desc')
            ->where(array('isnew' => ArticleModel::IS_NEW_YES))
            ->queryAll();
        $articles = arModule('Article')->getArticlesDetailInfo($articles);
        return $articles;

    }

    // 首页文章
    public function indexArticles()
    {
        // 文章分类
        $articelsCats = ArticleCatModel::model()
            ->getDb()
            ->order('sorder desc')
            ->limit(3)
            ->queryAll();

        foreach ($articelsCats as &$acat) :
            $articles = ArticleModel::model()
                ->getDb()
                ->limit(10)
                ->where(array('cid' => $acat['cid']))
                ->queryAll();
            $articles = arModule('Article')->getArticlesDetailInfo($articles);
            $acat['articles'] = $articles;
        endforeach;
        return $articelsCats;

    }

   // 截取字符串
   public function substr_cut($str, $len, $charset="utf-8"){
        //如果截取长度小于等于0，则返回空
        if( !is_numeric($len) or $len <= 0 ){
        return "";
        }
        //如果截取长度大于总字符串长度，则直接返回当前字符串
        $sLen = strlen($str);
        if( $len >= $sLen ){
        return $str;
        }
        if ( strtolower($charset) == "utf-8" ){
        $len_step = 3;
        }else{
        $len_step = 2;
        }
        $len_i = 0;
        $substr_len = 0;
        for( $i=0; $i < $sLen; $i++ ){
        if ( $len_i >= $len ) break;
        if( ord(substr($str,$i,1)) > 0xa0 ){
        $i += $len_step - 1;
        $substr_len += $len_step;
        }else{
        $substr_len ++;
        }
        $len_i ++;
        }
        $result_str = substr($str,0,$substr_len );
        return $result_str;
        }
    // 获取成功案例的文章
    public function getCgal($limit,$cut = 'false',$arr = array()){
        $cgal['pid !='] =0;
        $cgal['name'] = "成功案例";
        $getcgal = CategoryModel::model()->getDb()->where($cgal)->queryColumn('cid');
        $cgal = ArticleModel::model()->getDb()->where(array('cid'=>$getcgal))->limit($limit)->order('aid desc')->queryAll();
        if($cut == 'true'){
            foreach ($cgal as $key => $cgals) {
                $cgal[$key]['title'] = $this->substr_cut($cgals['title'],$arr[0]);
                $cgal[$key]['content'] = $this->substr_cut($cgals['content'],$arr[1]);
            }
        }
        return $this->getArtGallery($cgal);
    }

    // 获取高考咨询
    public function getArtListHead($like,$limit,$pid='false',$cut="false",$arr=array())
    {
        $cgal['name'] = $like;
        if($pid !="false"){
            $cgal['pid !='] = 0;
        }
        $getcgal = CategoryModel::model()->getDb()->where($cgal)->queryColumn('cid');

        $cgal = ArticleModel::model()->getDb()->where(array('cid'=>$getcgal))->limit($limit)->order('aid desc')->queryAll();
        if($cut == 'true'){
            foreach ($cgal as $key => $cgals) {
                $cgal[$key]['title'] = $this->substr_cut($cgals['title'],$arr[0]);
                $cgal[$key]['content'] = $this->substr_cut($cgals['content'],$arr[1]);
            }
        }
        return $this->getArtGallery($cgal);

    }

    // 分类名称下的文章
    public function getCatArticles($catName, $count)
    {
        if (is_numeric($catName)) :
            $cid = $catName;
        else :
            $cid = ArticleCatModel::model()->getDb()
                ->where(array('name' => $catName))
                ->queryColumn('cid');
        endif;
        if ($cid) :
            $articles = ArticleModel::model()->getDb()->limit($count)->where(array('cid' => $cid))->queryAll();
            $articles = $this->getArticlesDetailInfo($articles);
            return $articles;
        else :
            return false;
        endif;

    }

    // 修改状态
    public function statusToggle($aid)
    {
        $condtion = array(
            'aid' => $aid,
        );

        $status = ArticleModel::model()->getDb()
            ->where($condtion)
            ->queryColumn('status');

        // 状态调换
        if ($status == ArticleModel::STATUS_APPROVED) :
            $status = ArticleModel::STATUS_FORBIDDEN;
        else :
            $status = ArticleModel::STATUS_APPROVED;
        endif;

        $updateStatus = array(
            'status' => $status,
        );

        ArticleModel::model()->getDb()->where($condtion)->update($updateStatus);

        return $status;

    }
    // 获取文章都子分类和所有父分类
    public function getAllCate($arrs)
    {
        if(arComp('validator.validator')->checkMutiArray($arrs)) {
            foreach ($arrs as $key => $getCate) {
               $this->getAllCate($getCate);
            }
        }else{
           $arr = CategoryModel::model()->getDb()->where(array('cid'=>$arrs['cid']))->queryRow();
            static $cates = array();
            if($arr['pid'] != 0){
               $cate = CategoryModel::model()->getDb()->where(array('cid'=>$arr['pid']))->queryRow();
               array_push($cates, $cate['name']);
               $this->getAllCate($cate);
            }
        }
         return $cates;
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
            $article['catName'] = ArticleCatModel::model()
                ->getDb()
                ->where(array('cid' => $article['cid']))
                ->queryColumn('name');
            // html 反转义
            $article['content'] = stripcslashes($article['content']);

            return $article;
        endif;

        return $articles;
    }

    // 分类
    public function getCatMap()
    {
        // 文章分类
        $cats = ArticleCatModel::model()->getDb()
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

    //得到所属分类

    public function getCid($arr)
    {
        if (arComp('validator.validator')->checkMutiArray($arr)) :
            foreach ($arr as $key => $value) {
              $cName = CategoryModel::model()->getDb()
              ->where(array('cid'=>$value['cid']))
              ->queryColumn('name');
              $arr[$key]['cName'] = $cName;
            }
        else :
           $arr['cName'] = CategoryModel::model()->getDb()->where(array('cid'=>$arr['cid']))->queryColumn('name');
        endif;
        return $arr;
    }

    public function getName($cid)
    {
       return CategoryModel::model()->getDb()->where(array('cid' => $cid))->queryColumn('name');
    }
    //得到一个pid得到所有子类cid
    public function getCids($cid)
    {
       $arr = array($cid);

       //  //输出二级分类cid
        $getCidkOne = CategoryModel::model()->getDb()->where(array('pid'=>$cid))->select('cid')->queryAll();

        foreach ($getCidkOne as $k => $pcidone) {
           array_push($arr, $pcidone['cid']);
        }

        $aee = array();
        foreach ($getCidkOne as $key => $getCidTwo) {
           $getCidkOne[$key]['lastCid'] = CategoryModel::model()->getDb()->select('cid')->where(array('pid'=>$getCidTwo['cid']))->queryAll();

           foreach ($getCidkOne[$key]['lastCid'] as $ks => $pcidont) {
            array_push($aee, $pcidont['cid']);
            }
        }

      return array_merge($arr, $aee);

    }

    public function selectCid($cidAll)
    {
       foreach ($cidAll as $key => $cids) {
        $arr = array($cids['cid']);
        $pid = CategoryModel::model()->getDb()->where(array('cid' => $cids['cid']))->queryColumn('pid');
        array_unshift($arr, $pid);
        foreach ($arr as $keys => $arrs) {
            if($pid !=0) {
            $pidt = CategoryModel::model()->getDb()->where(array('cid' => $arr[0]))->queryColumn('pid');
            array_unshift($arr, $pidt);
            }
        }
       array_shift($arr);
       $cidAll[$key]['sidlist'] = $arr;
        }
        return $cidAll;
    }
    //通过得到的类id 得到名称
    public function getNameAct($arr)
    {
        foreach ($arr as $key => $array) {
            $arr[$key]['getcNames'] = array();
            foreach ($array['sidlist'] as $keys => $getNmae) {
              $arr[$key]['getcNames'][] = CategoryModel::model()->getDb()->where(array('cid' => $getNmae))->queryColumn('name');

            }
          // echo 11111;
        }
        return $arr;
    }
}
