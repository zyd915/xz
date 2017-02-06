<?php
// 用户中间件
class TuanModule
{
     // 抢购详细信息
    public function getTuanInfo(array $products)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($products)) :
            foreach ($products as &$product) :
                $product = $this->getTuanInfo($product);
            endforeach;
        else :
            $product = $products;
            // 获取产品logo
            $product['tuan'] = TuanModel::model()
                ->getDb()
                ->where(array('pid' => $product['pid']))
                ->queryRow();
            return $product;
        endif;

        return $products;

    }



}
