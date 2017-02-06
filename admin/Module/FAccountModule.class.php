<?php
class FAccountModule{
	// 通过模拟账号得到用户信息
	public function getUidInfo($arr)
	{
		 foreach ($arr as $key => $array) {
            $uid = $array['uid'];
            $getUserInfo = UserModel::model()->getDb()->where(array('uid' => $uid))->queryRow();

            $arr[$key]['getImgInfo'] = $getUserInfo;
        }
       return $arr;
	}

	// 通过user获取模拟用户信息
	public function getFaccount($arr)
	{
		foreach ($arr as $key => $array) {
			$arr[$key]['getFac'] = FAccountModel::model()->getDb()->where(array('uid' => $array['uid']))->queryRow();
		}
		return $arr;
	}
}
?>