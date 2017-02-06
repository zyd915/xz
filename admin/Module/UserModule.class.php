<?php
// 用户中间件
class UserModule
{
    // 修改状态
    public function statusToggle($uid)
    {
        $condtion = array(
            'uid' => $uid,
        );

        $status = UserModel::model()->getDb()
            ->where($condtion)
            ->queryColumn('status');

        // 状态调换
        if ($status == UserModel::STATUS_APPROVED) :
            $status = UserModel::STATUS_FORBIDDEN;
        else :
            $status = UserModel::STATUS_APPROVED;
        endif;

        $updateStatus = array(
            'status' => $status,
        );

        UserModel::model()->getDb()->where($condtion)->update($updateStatus);

        return $status;

    }
//获取图片字段为gallery
    public function getImgInfo($arr,$galleryid = 'galleryid')
    {
        foreach ($arr as $key => $getarr) {
            $getInfo = GalleryModel::model()->getDb()
                    ->where(array('gid' =>$getarr[$galleryid]))
                    ->queryAll();
            $arr[$key]['galleryInfo'] = $getInfo;
        }
        return $arr;
    }

    // 传入含有uid的二维数组
    public function getUinfo($data)
    {
        if (!empty($data)) {
            foreach ($data as $key=>$info) {
                $uid = $info['uid'];
                $uname = UserModel::model()->getDb()
                    ->where(array('uid' => $uid))
                    ->queryColumn('uname');
                $email = UserModel::model()->getDb()
                    ->where(array('uid' => $uid))
                    ->queryColumn('email');
                $data[$key]['uname'] = $uname;
                $data[$key]['email'] = $email;
            }

        }
            return $data;

    }

  // 获取头衔和马甲图片
    public function getHeadSetInfo($arr)
    {
        foreach ($arr as $key => $user) :
            $cond['active <='] = $user['jf'];
            $maxJf = HeadSetModel::model()->getDb()->where($cond)->queryAll();
            $max = $maxJf[0]['active'];
            $gradeactive = HeadSetModel::model()->getDb()->where(array('grade' => 1))->queryColumn('active');
                if($gradeactive > $user['jf']) {
                    $getHKey = HeadSetModel::model()->getDb()->where(array('grade' => 1))->queryColumn('id');
                }else{
                     for ($i=0; $i < count($maxJf); $i++) :

                      if($maxJf[$i]['active'] >= $max) :
                            $max = $maxJf[$i]['active'];
                      endif;

                    endfor;   
                     $getHKey = $this->getKey($maxJf,$max);
                }
           $getHeadSet = HeadSetModel::model()->getDb()->where(array('id' => $getHKey))->queryRow();
           $getHeadSet['galleryInfo'] = GalleryModel::model()->getDb()->where(array('gid' => $getHeadSet['gid']))->queryRow();
           $arr[$key]['headerset'] = $getHeadSet;
        endforeach;
        
       return $arr;
    }
    //接上获取名家
    public function getKey($maxJf,$max)
    {
        foreach ($maxJf as $key => $value) {
               if($value['active'] == $max) :
                return $maxJf[$key]['id'];
                endif;
           }
    }
    // 获取积分规则的名字，传入有rid的二维数组
    public function getRname($data)
    {
        if (!empty($data)) {
            foreach ($data as $key=>$value) {
                $rid = $value['rid'];
                $rname = Jf_rulesModel::model()->getDb()
                    ->where(array('rid' => $rid))
                    ->queryColumn('name');
                $data[$key]['rname'] = $rname;
            }
        }
        return $data;

    }
    //获取用户个人信息
    public function getUserInfo($arr)
    {   
        foreach ($arr as $key => $array) {
            $uid = $array['uid'];
            $getImgGid = UserImgModel::model()->getDb()->where(array('uid' => $uid))->queryAll();
            $getImgInfo = GalleryModel::model()->getDb()->where(array('gid' => $getImgGid['gid']))->queryAll();
            $arr[$key]['getImgInfo'] = $getImgInfo;
        }
       return $arr;

    }

    //用户留言和未登录留言获取信息
    public function getUserInfoLeaveMes($arr)
    {   
        foreach ($arr as $key => $array) {
            if($array['uid']) {
                $uid = $array['uid'];
              $getImgInfo = UserModel::model()->getDb()->where(array('uid' => $uid))->queryRow();
                $arr[$key]['uname'] =  $getImgInfo['uname'];
                $arr[$key]['phone'] =  $getImgInfo['phone'];
                $arr[$key]['email'] =  $getImgInfo['email'];  
            }else{
                $mdesc = explode('-', $array['mdesc']);
                $arr[$key]['uname'] =  $mdesc[0];
                $arr[$key]['phone'] =  $mdesc[1];
                $arr[$key]['email'] =  $mdesc[2];
            }
            
        }
       return $arr;

    }

    //获取对应图片
    public function getImg($arr,$type){
        foreach ($arr['imgifo'] as $key => $arrInfo) {
            if($arrInfo['type'] == $type){
            return $arrInfo['url']['url'];
            }
        }
    }

    // 通过用户表中logogid获取身份证图片等等
    public function getCardPto($arr,$type)
    {
      $getGid = UserImgModel::model()->getDb()->where(array('uid' => $arr['uid'],'type' => $type))->queryColumn('gid');
      $getUrl = GalleryModel::model()->getDb()->where(array('gid' => $getGid))->queryRow();
      return $getUrl;
      
    }


}
