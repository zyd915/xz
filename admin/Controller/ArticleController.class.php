<?php
// 文章新闻管理类
class ArticleController extends BaseController
{
    // 文章列表
    public function listAction()
    {
        $condition = array();
        if (arRequest('title')) :
            $title = arRequest('title');
            $condition['title like'] = '%' . $title . '%';
        endif;

        if ($cid = arRequest('cid')) :
            $condition['cid'] = $cid;
        endif;

        $count = ArticleModel::model()->getDb()->where($condition)->count();

        $count = ArticleModel::model()->getDb()->where($condition)->count();
        $page = new Page($count,20);
        $articles = ArticleModel::model()->getDb()
            ->where($condition)
            ->limit($page->limit())
            ->order('sorder desc, aid desc')
            ->queryAll();
        // 详情
        $articles = arModule('Article')->getArticlesDetailInfo($articles);
        // 文章分类
        $catMap = arModule('Article')->getCatMap();

        // 文章列表
        $this->assign(array('catMap' => $catMap, 'articles' => $articles, 'page' => $page->show()));

        $this->display('@/Article/index');

    }

    // 列表
    public function catAction()
    {
        $total = ArticleCatModel::model()->getDb()->count();
        $page = new Page($total, 20);
        $cats = ArticleCatModel::model()
            ->getDb()
            ->limit($page->limit())
            ->order('sorder desc')
            ->queryAll();
        $this->assign(array('cats' => $cats, 'page' => $page->show()));
        $this->display();

    }

    // 添加专家
    public function catAddAction()
    {
        if ($data = arPost()) :
            $addResult = ArticleCatModel::model()->getDb()->insert($data, 1);
            if ($addResult) :
                $this->redirectSuccess('cat');
            endif;
        endif;
        $this->display();

    }

    // 编辑专家
    public function catEditAction()
    {
        if ($data = arPost()) :
            if ($cid = $data['cid']) :
                $updateResult = ArticleCatModel::model()
                    ->getDb()
                    ->where(array('cid' => $cid))
                    ->update($data, 1);
                $this->redirectSuccess(array('', array('cid' => $cid)));
            endif;
        endif;

        if ($cid = arRequest('cid')) :
            $cat = ArticleCatModel::model()
                ->getDb()
                ->where(array('cid' => $cid))
                ->queryRow();
            $this->assign(array('cat' => $cat));
            $this->display('catAdd');
        endif;
        
    }

    // 测试
    public function testAction()
    {
        $dstDir = arCfg('UPLOAD_DIR') . 'Reg' . DS;
        var_dump($dstDir);
    }

    // 删除专家
    public function catDelAction()
    {
        if ($cid = arRequest('cid')) :
            $deleteResult = ArticleCatModel::model()->getDb()
                ->where(array('cid' => $cid))
                ->delete();
        endif;
        $this->redirectSuccess('cat');

    }

    // 上传图片
    public function uploadRegPicAction()
    {
        $dstDir = arCfg('UPLOAD_DIR') . 'Reg' . DS;
        // 上传图片名称
        $picName = arComp('ext.upload')->upload('uploadpic', $dstDir, 'img');
        if ($picName) :
            $file =  $dstDir . $picName;
            $cropFile =  $dstDir . 's_' . $picName;
            // 裁剪图片
            $cropTrue = GalleryModel::model()->crop($file, $cropFile, 600, 400);
            if ($cropTrue) :
                $gallery = array(
                    'url' => arComp('url.route')->serverPath($file),
                    'curl' => arComp('url.route')->serverPath($cropFile),
                    'desc' => '',
                );
                // 插入画廊
                $gid = GalleryModel::model()->getDb()->insert($gallery);
                if ($gid) :
                    $gallery['gid'] = $gid;
                    $this->showJson($gallery);
                endif;
            endif;
        else :
            $this->showJsonError(arComp('ext.upload')->errorMsg);

        endif;

    }

    // 操作产品
    public function articleManagerAction()
    {
        arSeg(array(
                'loader' => array(
                    'plugin' => 'bdeditor,layer,ajaxfileupload,datetimepicker',
                    'this' => $this
                )
            )
        );
        $opt = arRequest('opt');
        $aid = arRequest('aid', 0);
        $data = arRequest();

        $conditionition = array(
            'aid' => $aid,
        );

        // 文章分类
        $catMap = arModule('Article')->getCatMap();
        $this->assign(array('catMap' => $catMap));

        switch ($opt) {
            // 编辑
            case 'edit':
                if ($data = arPost()) :
                    if ($data['ctime']) :
                        $data['ctime'] = strtotime(arPost('ctime'));
                    else :
                        $data['ctime'] = time();
                    endif;
                    $updateResult = ArticleModel::model()->getDb()->where($conditionition)->update($data, true);
                    if ($updateResult) :
                        $this->redirectSuccess(array('list'));
                    else :
                        $this->redirectError(array('list'));
                    endif;
                endif;
                $conditionition['aid'] = $aid;
                $article = ArticleModel::model()->getDb()->where($conditionition)->queryRow();
                $article = arModule('Article')->getArtGallery($article);

                // var_dump($article);
                // die();
                if(get_magic_quotes_gpc()) :
                    $article['content'] = stripslashes($article['content']);
                    $article['content'] = str_replace('\"', '', $article['content']);
                endif;
                $this->assign(array('article' => $article));
                return $this->display('@/Article/manager');

                break;
            // 删除
            case 'delete':
                $deleteResult = ArticleModel::model()->getDb()->where($conditionition)->delete();
                if ($deleteResult) :
                    return $this->showJsonSuccess('操作成功');
                else :
                    return $this->showJsonError('操作失败');
                endif;

                break;
            // 添加
            case 'add':
                if ($data = arPost()) :
                    $data['ctime'] = time();
                    $insertResult = ArticleModel::model()->getDb()->insert($data, true);
                    if ($insertResult) :
                        $this->redirectSuccess(array('list'));
                    else :
                        $this->redirectError(array('list'));
                    endif;

                endif;

                Ar::setConfig('DEBUG_SHOW_ERROR', false);

                $this->display('@/Article/manager');

                break;
            default:
                $this->redirectError(array('list'), '非法操作');
                break;
        }

    }

}
