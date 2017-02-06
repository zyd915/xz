<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */
class SystemController extends BaseController
{
    /**
     * just the example of get contents.
     *
     * @return void
     */
    // 初始化方法
    public function init()
    {
        parent::init();
        // $this->setLayOutfile('system');

    }

    //系统介绍
    public function indexAction()
    {
        arLm('admin.module');
        $cid = ArticleCatModel::model()->getDb()
            ->where(array('name' => '成功案例'))
            ->queryColumn('cid');

        $count = ArticleModel::model()->getDb()
            ->where(array('cid' => $cid))
            ->count();
        $page = new Page($count, 10);
        $articles = ArticleModel::model()->getDb()
            ->limit($page->limit())
            ->where(array('cid' => $cid))
            ->queryAll();
        $articles = arModule('Article')->getArticlesDetailInfo($articles);
        $this->assign(array('cases' => $articles, 'page' => $page->show()));

        $this->display();

    }

    //详细信息
    public function DetailAction()
    {
        arLm('admin.Module');
        $aid = arRequest('aid');
        $art = ArticleModel::model()->getDb()->where(array('aid'=>$aid))->queryRow();
        $art['content'] = stripcslashes($art['content']);
        $art['title'] = stripcslashes($art['title']);
        $art = arModule('Article')->getArtGallery($art);
        $this->assign(array('art' => $art));
        $this->display();

    }
    
    //合作加盟
    public function agentAction()
    {
        $this->display();

    }
    //联系我们
    public function contactAction()
    {
        $this->display();

    }
    //免责声明
    public function disclaimerAction()
    {
        $this->display();

    }
    //专家团队
    public function expertTeamAction()
    {
        $this->display();

    }
    //成功案列
    public function successCaseAction()
    {
        $this->display();

    }
    //往期讲座回顾
    public function lectureReviewAction()
    {
        $this->display();

    }
    //使用指南
    public function useGuideAction()
    {
        $this->display();

    }

}
