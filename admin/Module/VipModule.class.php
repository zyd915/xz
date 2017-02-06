<?php
// 用户中间件
class VipModule
{
    // 传入有卡type
    public function getCardType($data)
    {
        if (!empty($data)) {
            foreach ($data as $key => $card) {
                $cardName = CardTypeModel::model()->getDb()
                    ->where(array('type' => $card['type']))
                    ->queryColumn('name');
                $data[$key]['cardname'] = $cardName;
            }
        }
        return $data;
    }

    // 修改状态
    public function statusToggle($cid)
    {
        $condtion = array(
            'cid' => $cid,
        );

        $status = CardModel::model()->getDb()
            ->where($condtion)
            ->queryColumn('status');

        // 状态调换
        if ($status == CardModel::DEFAULT_STATUS) :
            $status = CardModel::NORMAL_STATUS;
        else :
            $status = CardModel::DEFAULT_STATUS;
        endif;

        $updateStatus = array(
            'status' => $status,
        );

        CardModel::model()->getDb()->where($condtion)->update($updateStatus);

        return $status;

    }

    // 直接返回一个最大的卡type
    public function getType()
    {
        $type = CardTypeModel::model()->getDb()
                        ->select("max(type) as max")
                        ->queryRow();
                    if (empty($type)) {
                        $card['type'] = 1;
                    } else {
                        $card['type'] = $type['max']+1;
                    }
        return $type;
    }

}
