<?php
class YuyueModule
{
    // 获取店铺详细信息
    public function yyDetail(array $yuyues)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($yuyues)) :
            foreach ($yuyues as &$yuyue) :
                $yuyue = $this->yyDetail($yuyue);
            endforeach;
        else :
            $yuyue = $yuyues;
            $yuyue['expert'] = ExpertModel::model()->getDb()
                ->where(array('eid' => $yuyue['eid']))
                ->queryRow();
            return $yuyue;
        endif;
        return $yuyues;

    }

}
