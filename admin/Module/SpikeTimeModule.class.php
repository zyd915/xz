<?php
class SpikeTimeModule
{
    // 抢购详细信息
    public function getSpikeTimeInfo(array $products)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($products)) :
            foreach ($products as &$product) :
                $product = $this->getSpikeTimeInfo($product);
            endforeach;
        else :
            $product = $products;
            // 获取产品logo
            $product['spiketime'] = ProductSpikeTimeModel::model()
                ->getDb()
                ->where(array('pid' => $product['pid']))
                ->queryRow();
            return $product;
        endif;

        return $products;

    }

}