<?php
// 用户中间件
class JfModule
{
    // 用于生成头衔选择下拉框需要的数据
    public function getJfSelect($headSet)
    {
        $select = array();
        if ($headSet) :
            foreach ($headSet as $headSetDetail) :
                $select[$headSetDetail['active']] = $headSetDetail['name'];
            endforeach;
        endif;
        return $select;

    }

    // 用于获取头衔(传一维数组)
    public function getUserHead($user)
    {
        if ($user) :
            $headSet = HeadSetModel::model()->getDb()->order('grade asc')->queryAll();
            foreach ($headSet as $key => $userDetail) :
                if ((int)$user['active'] == 0) :
                    $user['headSet'] = $headSet[0]['name'];
                endif;
                if ((int)$userDetail['active'] <= $user['active']) :
                    $user['headSet'] = $userDetail['name'];
                endif;
            endforeach;
        endif;
        return $user;

    }

    // 用于获取头衔（传二维数组）
    public function getHead($user)
    {
        if ($user) :
            foreach ($user as $key => $userDetail) :
                $user[$key] = $this->getUserHead($userDetail);
            endforeach;
        endif;
        return $user;

    }

}
