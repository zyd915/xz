<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 后台管理类.
 */
class OrderController extends IndexController
{
    // 初始化
    public function init()
    {
        // 父类加载
        parent::init();
        // 加载admin/idea.js
        $this->assign(array('jsInsertBundles' => array('admin/order')));
        // 调用百度editor, uploadify上传插件 时间选择插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'bdeditor,uploadify,datetimepicker',
                    'this' => $this
                )
            )
        );

    }
    
    // 订单信息
    public function orderInfoAction()
    {
        $oid = arGet('oid');

        // 获取用户数据
        $order = OrderModel::model()->getDb()
            ->order('oid desc')
            ->where(array('oid' => $oid))
            ->queryRow();

        $order = arModule('Order')->getOrdersDetailInfo($order);

        $this->assign(array('order' => $order));
        $this->setLayOutfile('');
        $this->display('@/Order/orderInfo');

    }
   
    // 订单信息
    public function orderShowAction()
    {
        $data = arRequest();
        $condition = array();
        if(!empty($data['keywords']))
        {
             $keywords = $data['keywords'];
              $condition =
                     implode('or',
                    array(
                        // //对外显示订单号
                        " outer_trade_no = '$keywords' ",
                        // //交易内部号
                        " trade_no = '$keywords' ",
                        // //订单名称
                        " subject like '%$keywords%' ",
                        // //订单创建时间
                        //" ctime like '%$keywords%' ",
                        // //易交价格
                         " price = '$keywords' ",
                        // //支付类型  支付宝  快钱等。。
                       // " type like '%$keywords%' ",
                        // //订单状态
                        //" status = '$keywords' ",
                    )
                 );

        }
            if (is_numeric(arRequest('type'))) {
                $type = arRequest('type');
                if($condition)
                {
                    $condition .= " and type = $type";
                }else{
                    $condition = "type = $type";
                }
            }
             if (is_numeric(arRequest('status'))) {
                $status = arRequest('status');
                if($condition)
                {
                    $condition .= " and state = $status";
                }else{
                    $condition = "state = $status";
                }
            }
             if (arRequest('searchTime')) {
                $searchTime = arRequest('searchTime');
                $sestime = strtotime(urldecode(arRequest('sestime')));
                $seetime = strtotime(urldecode(arRequest('seetime')));
                if($condition)
                {
                    $condition .= " and $searchTime >= $sestime and $searchTime< $seetime";
                }else{
                     $condition = "$searchTime >= $sestime and $searchTime < $seetime";
                }
                $this->assign(array('seetime' => $seetime,'sestime' => $sestime));
            }
      

        $countperpage = arRequest('countperpage',20);
        $count = OrderModel::Model()->getDb()
                ->where($condition)
                ->count();
        $page = new Page($count, $countperpage);
        $orderShow = OrderModel::Model()->getDb()
            ->where($condition)
            ->limit($page->limit())
            ->order('ctime desc')
            ->queryAll();
        $this->assign(array('order'=>$orderShow,'page'=>$page->show(),'tips'=>'主键id,对外显示订单号,对内显示订单号，订单名称，价格'));
        
        $this->display('@/Order/orderShow');

    }

    // 数据
    public function orderDataAction()
    {
        $condition = array();
        if (arRequest('keywords')) :
            $data['keywords'] = arRequest('keywords');
            if (!empty($data['keywords'])) :
                if (is_numeric($data['keywords'])) :
                    $condition['uid'] = $data['keywords'];
                endif;
            endif;
        endif;
        if(arRequest('subject')) :
            $subject = arRequest('subject');
            $condition = array(" subject like '%$subject%' ");
        endif;

         if(arRequest('uname')) :
            $uname = arRequest('uname');
            $cond = array(" uname like '%$uname%' ");
            $condition['uid'] = AddressModel::model()->getDb()->where($cond)->select('uid')->queryAll();
        endif;

        $stime = strtotime(arRequest('stime'));
        $etime = strtotime(arRequest('etime'));
        if (is_numeric(arRequest('pstatus', null))) :
            $condition['pstatus'] = arRequest('pstatus');
        endif;

        if ($searchTime = arRequest('searchTime')) :
            $condition[$searchTime . ' > '] = $stime;
            $condition[$searchTime . ' < '] = $etime;
        endif;
        if(arRequest('field'))
        {
            $field = 'total';
        }else{
             $field = 'toatlprice';
        }

        if(arRequest('selectfield'))
        {
            $group = arRequest('selectfield');
            $select = "*, count(pid) as total, sum(dprice) as toatlprice";
        }else{
            $group = 'uid';
            $select = "*, count(uid) as total, sum(dprice) as toatlprice";
        }
        $condition['isfinal'] = OrderModel::STATUS_FINAL_YES;
        if(arRequest('dprice')) :
           $dprice = explode('-', arRequest('dprice'));
           $having = 'sum(dprice) >'.$dprice[0].' and sum(dprice) <= '.$dprice[1];
           $total = OrderModel::model()->getDb()->select($select)->where($condition)->group('uid')->having($having)->count();
           echo $total;
           $page = new Page($total, arRequest('countperpage', 30));
           $orders = OrderModel::model()->getDb()
                ->limit($page->limit())
                ->select($select)
                ->order("$field desc")
                ->where($condition)
                ->having($having)
                ->group($group)
                ->queryAll();
        else :
            $total = OrderModel::model()->getDb()->where($condition)->group('uid')->count();
            $page = new Page($total, arRequest('countperpage', 30));
            $orders = OrderModel::model()->getDb()
                ->limit($page->limit())
                ->select($select)
                ->order("$field desc")
                ->where($condition)
                ->group($group)
                ->queryAll();
        endif;

        $orders = arModule('Order')->getOrdersDetailInfo($orders);

        // orders
        $this->assign(array('orders' => $orders,'group'=>$group,'page' => $page->show()));
        $this->display('@/Order/orderData');

    }

    // 获取用户订单情况
    public function userOrderCountAction()
    {

        if(arRequest('uid'))
        {
             $condition = array(
            'uid' => arRequest('uid'),
            );
        }
         if(arRequest('pid'))
        {
             $condition = array(
            'pid' => arRequest('pid'),
            );
        }
        if(arRequest('searchTime')) :
            $searchTime = arRequest('searchTime');
            $stime = strtotime(urldecode(arRequest('stime')));
            $etime = strtotime(urldecode(arRequest('etime')));
            $condition[$searchTime . ' > '] = $stime;
            $condition[$searchTime . ' < '] = $etime;

        endif;
        if (is_numeric(arRequest('pstatus'))) :
            $condition['pstatus'] = arRequest('pstatus');
        endif;
        $condition['isfinal'] = OrderModel::STATUS_FINAL_YES;
        $orders = OrderModel::model()->getDb()->where($condition)->queryAll();
        $orders = arModule('Order')->getOrdersDetailInfo($orders);
        $this->showJson($orders);

    }

    // 退货/款处理
    public function abortAction()
    {
        if ($rid = arGet('rid')) :
            OrderRefundModel::model()->getDb()->where(array('rid' => $rid))->update(array('status' => OrderRefundModel::STATUS_YES));
        endif;
        if ($rid = arPost('abortId')) :
            OrderRefundModel::model()->getDb()->where(array('rid' => $rid))->update(array('status' => OrderRefundModel::STATUS_CANCLE,'abort' => arPost('abort')));
            return $this->showJsonSuccess(array('操作成功'));
        endif;
        $condition['status'] = OrderRefundModel::STATUS_NO;
        if ($status = arGet('opt')) :
            $condition['status'] = $status;
        endif;
        if ($stime = arGet('stime')) :
            $stime = strtotime(urldecode($stime));
            $condition['ctime >'] = $stime;
        endif;
        if ($etime = arGet('etime')) :
            $etime = strtotime(urldecode($etime));
            $condition['ctime <'] = $etime;
        endif;
        if ($otradeno = arGet('otradeno')) :
            $condition['otradeno'] = $otradeno;
        endif;
        if ($uid = arGet('uid')) :
            $condition['uid'] = $uid;
        endif;
        $count = OrderRefundModel::model()->getDb()->where($condition)->count();
        $page = new Page($count,15);
        $abort = OrderRefundModel::model()->getDb()->where($condition)->limit($page->limit())->queryAll();
        $abort = arModule('User')->getUinfo($abort);
        // 获取商品类型
        $abort = arModule('Order')->getAbortInfo($abort);
        $this->assign(array('page' => $page->show(), 'abort' => $abort));
        $this->display('@/Order/abort');

    }
    // 删除订单
    public function deleteOrderAction()
    {
        $oid = arRequest('oid');
        $sql = "oid in ($oid)";
        $deleteOrder = OrderModel::model()->getDb()->where($sql)->delete();
        if($deleteOrder)
        {
            $this->redirectSuccess(array('Order/index'));
        }else{
            $this->redirectError(array('Order/index',array('oid' => 'delete')));
        }
    }

    // 删除订单支付详情
    public function deleteOrderPayAction()
    {
        $oid = arRequest('oid');
        $sql = "pid in ($oid)";
        $deleteOrder = OrderPayModel::model()->getDb()->where($sql)->delete();
        if($deleteOrder)
        {
            $this->redirectSuccess(array('Order/orderShow'));
        }else{
            $this->redirectError(array('Order/index',array('oid' => 'delete')));
        }
    }
    
}
