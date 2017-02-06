<?php
/**
* @author ck
*date 2016.4.21
*explain The lecture application module
*/

class ChairController extends BaseController{
	
	// 讲座报名列表
	public function indexAction()
	{
		$total = ChairModel::model()
			->getDb()
			->count();
		$page = new Page($total,10);
		$chair = ChairModel::model()
		  ->getDb()
		  ->limit($page->limit())
		  ->queryAll();
		$this->assign(array('chair' => $chair, 'page' => $page->show()));
		$this->display();
	}


	// 删除
	public function deleteAction()
	{
		$aid = arRequest('aid');
		$delete = ChairModel::model()
			->getDb()
			->where(array('aid' => $aid))
			->delete();
		if ($delete):
			$this->redirectSuccess(array('Chair/index','删除成功'));
		else:
			$this->redirectError(array('Chair/index','删除失败'));
		endif;
	}


}