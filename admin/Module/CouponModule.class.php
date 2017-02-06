<?php
// 优惠券管理
class CouponModule
{
    // 生成现金券
    public function generate($num, $coupon)
    {
        $coupons = array();
        for ($i = 0; $i < $num; $i ++) :
            // 时间
            $coupon['atime'] = time();
            // 生成兑换码
            $coupon['excode'] = CouponModel::model()->generateExchangeCode();
            $coupons[] = $coupon;
        endfor;
        // 添加优惠券
        CouponModel::model()->getDb()->batchInsert($coupons);
        // 记录日志
        LogModel::model()->record(arCfg('admin.name'), '发放优惠劵*' . $num, $coupon['title']);
        return true;

    }

    // 获取优惠劵详细信息
    public function getCouponDetailInfo(array $coupons)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($coupons)) :
            foreach ($coupons as &$coupon) :
                $coupon = $this->getCouponDetailInfo($coupon);
            endforeach;
        else :
            $coupon = $coupons;

            $coupon['uname'] = UserModel::model()
                ->getDb()
                ->where(array('uid' => $coupon['uid']))
                ->queryColumn('uname');

            return $coupon;

        endif;

        return $coupons;

    }


}