<?php
// 用户中间件
class ProductModule
{
    // 修改状态
    public function statusToggle($pid)
    {
        $condtion = array(
            'pid' => $pid,
        );

        $status = ProductModel::model()->getDb()
            ->where($condtion)
            ->queryColumn('status');

        // 状态调换
        if ($status == ProductModel::STATUS_APPROVED) :
            $status = ProductModel::STATUS_FORBIDDEN;
        else :
            $status = ProductModel::STATUS_APPROVED;
        endif;

        $updateStatus = array(
            'status' => $status,
        );

        ProductModel::model()->getDb()->where($condtion)->update($updateStatus);

        return $status;

    }

    // 获取店铺详细信息
    public function getProductsDetailInfo(array $products)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($products)) :
            foreach ($products as &$product) :
                $product = $this->getProductsDetailInfo($product);
            endforeach;
        else :
            $product = $products;
            // 获取产品logo
            $product['galleryBundle'] = GalleryModel::model()
                ->getDb()
                ->where(array('gid' => explode(',', $product['gallery'])))
                ->queryAll('gid');

            // html 反转义
            $product['content'] = stripcslashes($product['content']);

            // 属性信息
            $product['attr'] = ProductAttrModel::model()
                ->getDb()
                ->where(array('pid' => $product['pid']))
                ->queryAll();

            return $product;

        endif;

        return $products;

    }

    // 删除产品图片
    public function delImg($gid, $oid = 0)
    {
        try {
            // 开启事物
            arComp('db.mysql')->transBegin();

            $galleryCondition = array('gid' => $gid);

            $galleryInfo = GalleryModel::model()->getDb()
                ->where($galleryCondition)
                ->queryRow();

            // 有关联数据
            if ($oid) :
                $condtion = array('pid' => $oid);
                $product = ProductModel::model()->getDb()->where($condtion)->queryRow();

                // 图片
                $galleryBundle = explode(',', $product['gallery']);

                if ($key = array_search($gid, $galleryBundle)) :
                    unset($galleryBundle[$key]);
                endif;

                if (!empty($galleryBundle)) :
                    $gallery = implode(',', $galleryBundle);
                else :
                    $gallery = '';
                endif;
                ProductModel::model()->getDb()->where($condtion)->update(array('gallery' => $gallery));
            endif;

            // 删除画廊数据
            arModule('Gallery')->delGallery($gid);

            // 提交
            arComp('db.mysql')->transCommit();
            return true;

        } catch (Exception $e) {
            // 回滚
            arComp('db.mysql')->transRollBack();
            return false;

        }

    }

    // 检查属性表是否可删
    public function checkNid($nid)
    {
        $res = ProductAttrModel::model()->getDb()
            ->where(array('nid' => $nid))
            ->queryRow();
        if (empty($res)) {
            return true;
        } else {
            return false;
        }
    }


    //获取第一张图片
     public function getFirstPho($gallery){

        $gallery = explode(',',$gallery);

        $products['img']=GalleryModel::model()->getDb()
            ->where(array('gid' =>$gallery[0]))
            ->queryColumn('url');
        return $products['img'];
     }
     
}
