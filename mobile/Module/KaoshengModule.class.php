<?php
// 获取考生信息
class KaoshengModule
{
    // 获取考生信息
    public function updateGaokaoScore($uid, $score)
    {
        if ($uid && is_numeric($score)) :
            $kaosheng = UserKaoshengModel::model()
                ->getDb()
                ->where(array('uid' => $uid))
                ->queryRow();
            // 先暂时不限制修改次数
            // if (!$kaosheng['scoregk']) :
            if (true) :
                $update = UserKaoshengModel::model()
                    ->getDb()
                    ->where(array('uid' => $uid))
                    ->update(array('scoregk' => $score));
                if ($update) :
                    // 写入日志
                    arComp('list.log')->record($score, 'upscoregk');
                    return true;
                else :
                    return false;
                endif;
            else :
                return false;
            endif;
        else :
            return false;
        endif;

    }

}
