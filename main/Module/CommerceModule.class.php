<?php
class CommerceModule
{
    // 获取商会的管理员信息
    public function getManagerInfo($arr)
    {
        foreach ($arr as $key => $cuid) {
           $arr[$key]['ManagerInfo'] = UserModel::model()->getDb()->where(array('uid' => $cuid['cuid']))->queryRow();
        }
        return $arr;
    }
    // 判断个人是否创建了商会
    public function createCommOrNot()
    {
        $userComm = CommerceModel::model()->getDb()->where(array('cuid' => $_SESSION['uid']))->count();
        if($userComm) {
            return true;
        }else{
            return false;
        }
    }
    // 获取兄弟社团的信息
    public function getBroCommInfo($arr,$opt = 'commerce')
    {
        if($opt == 'commerce') {
            foreach ($arr as $key => $userinfo) {

              $arr[$key]['ask'] = CommerceModel::model()->getDb()->where(array('cid' => $userinfo['askid']))->queryRow();
              $arr[$key]['for'] = CommerceModel::model()->getDb()->where(array('cid' => $userinfo['forid']))->queryRow();
            }
            return $arr;
        }else{
            foreach ($arr as $key => $userinfo) {
              $arr[$key]['ask'] = UserModel::model()->getDb()->where(array('uid' => $userinfo['uid']))->queryRow();
              $arr[$key]['for'] = CommerceModel::model()->getDb()->where(array('cid' => $userinfo['commid']))->queryRow();
            }
            return $arr;
        }
    }
    // 获取图片信息
       public function getGallery($arr,$gname="gid")
    {
        foreach ($arr as $key => $gallery) {
          $arr[$key]['gallery'] = GalleryModel::model()->getDb()->where(array('gid' => $gallery[$gname]))->queryRow();
        }
        return $arr;
    }

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

        // 查看文章的商会
        public function getCommToArt(array $arr)
        {
            if (arComp('validator.validator')->checkMutiArray($arr)) :
                foreach ($arr as $key => $arrs) {
                   // $arrs = $this->getCommToArt($arrs);
                   $arr[$key]['commerceInfo'] = CommerceModel::model()->getDb()
                                   ->where(array('cid' => $arrs['cuid']))
                                   ->queryRow();
                }
            else:
               $arr['commerceInfo'] = CommerceModel::model()->getDb()
                                   ->where(array('cid' => $arr['cuid']))
                                   ->queryRow();
                return $arr;
            endif;
            return $arr;
        }
}
