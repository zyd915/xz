<?php
// 用户中间件
class ProductAttributeModule
{
    // 批量插入
    public function insert($attr, $pid)
    {
        $batchBundle = array();

        foreach ($attr as $key => $attribute) :
            if (!empty($attribute)) :
                $batchBundle[] = array('pid' => $pid, 'val' => $attribute, 'nid' => $key);
            endif;
        endforeach;
        if (!empty($batchBundle)) :
            // 批量插入数据库
            return ProductAttrModel::model()->getDb()->batchInsert($batchBundle);
        else :
            return false;
        endif;


    }

    // 批量修改
    public function update($attr, $pid)
    {
        foreach ($attr as $key => $attribute) :
            $condition = array('nid' => $key);
            $updateArr = array('val' => $attribute);
            ProductAttrModel::model()->getDb()->where($condition)->update($updateArr);
        endforeach;
        return true;

    }

}
