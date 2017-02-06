<?php
// 中间件
class OrderModule
{
    // 获取店铺详细信息
    public function getOrdersDetailInfo(array $orders)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($orders)) :
            foreach ($orders as &$order) :
                $order = $this->getOrdersDetailInfo($order);
            endforeach;
        else :
            $order = $orders;
            $order['uname'] = UserModel::model()
                ->getDb()
                ->where(array('uid' => $order['uid']))
                ->queryColumn('uname');

            // 支付信息
            $order['payInfo'] = OrderPayModel::model()->getDb()->where(array('otradeno' => $order['otradeno']))->queryRow();

            $addressInfo = UserRaddressModel::model()->getDb()
                ->where(array('raid' => $order['recaddid']))
                ->queryRow();
            $order['addressInfo'] = $addressInfo;
            $order['raddress'] = $addressInfo['area'] . $addressInfo['address'];
            $order['tell'] = $addressInfo['tell'];
            $order['uname'] = $addressInfo['uname'];
            return $order;
        endif;

        return $orders;

    }
    // 获取商品的收货地址等等
    public function getOrderAddress($order)
    {
     foreach ($order as $key => $orders) :
        $order[$key]['addinfo'] = explode('.', $orders['desc']);
     endforeach;
     return $order;
    }

    //获取用户信息
    public function getUserInfoDetail($user)
    {
    foreach ($user as $key => $users) {
     $user[$key]['userInfo'] = UserModel::model()->getDb()->where(array('uid' => $users['uid']))->queryRow();
     $user[$key]['cuptime'] = date('Y-m-d H:i',$users['ctime']);
    }

        return $user;
    }

    // 获取产品的详细信息
     public function getProductInfo($product)
     {
        foreach ($product as $key => $products) {
          $product[$key]['productInfo'] = ProductModel::model()->getDb()->where(array('pid' => $products['pid']))->queryRow();
        }
        return $product;
     }

     // 将二维数组变成一位数组
     public function changeArray($arr,$value)
     {
        $thisarra = array();
         foreach ($arr as $key => $array) {
                 
                 array_push($thisarra, $array[$value]);
                }
        return $thisarra;
     }
}
