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
class ArticleController extends BaseController
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
        $this->setLayOutfile('system');
        $showInNewsCats = ArticleCatModel::model()
            ->getDb()
            ->where(array('innews' => ArticleCatModel::IN_NEWS_YES))
            ->order('sorder desc')
            ->queryAll();
        $this->assign(array('cats' => $showInNewsCats));

    }

    // 初始化当前分类
    public function initCurrentCat($cid)
    {
        $currentCat = ArticleCatModel::model()
            ->getDb()
            ->where(array('cid' => $cid))
            ->queryRow();

        if (!$currentCat) :
            $this->redirect('Index/index');
        endif;

        $this->assign(array('currentCat' => $currentCat));

    }

    // 文章列表
    public function listAction()
    {
        arLm('admin.Module');
        if ($cid = arRequest('cid', 4)) :
            $this->initCurrentCat($cid);
            $condition = array(
                'cid' => $cid,
            );

            $count = ArticleModel::model()->getDb()->where($condition)->count();
            $page = new Page($count, 20);
            $articles = ArticleModel::model()->getDb()
                ->where($condition)
                ->limit($page->limit())
                ->order('sorder desc, aid desc')
                ->queryAll();
            // 详情
            $articles = arModule('Article')->getArticlesDetailInfo($articles);
            $this->assign(array(
                    'articles' => $articles,
                    'page' => $page->show(),
                )
            );
            $this->display();
        endif;

    }

    //详细信息
    public function detailAction()
    {
        arLm('admin.Module');
        $aid = arRequest('aid');
        $article = ArticleModel::model()->getDb()->where(array('aid' => $aid))->queryRow();
        if (!$article) :
            $this->redirect('list');
        endif;
        $this->initCurrentCat($article['cid']);
        $article = arModule('Article')->getArticlesDetailInfo($article);
        $this->assign(array('art' => $article));
        $this->display();

    }

}
