<?php
// 视频管理
class VideoController extends BaseController{

	// 视频列表
	public function listAction()
	{
		$total = VideolistModel::model()->getDb()->count();
        //$clas = $video['cid'];
        //var_dump($video[1]['title']);
        $page = new Page($total, 20);
        $video = VideolistModel::model()
            ->getDb()
            ->limit($page->limit())
            ->order('sorder desc, aid desc')
            ->queryAll();

		$this->assign(array('video' => $video,'page' => $page, 'num' => $total));
		$this->display();
	}

    // 视频分类
    public function catAction()
    {
        $total = VideoclassModel::model()->getDb()->count();
        $page = new Page($total, 20);
        $cats = VideoclassModel::model()
            ->getDb()
            ->limit($page->limit())
            ->order('sorder desc')
            ->queryAll();
        $this->assign(array('cats' => $cats, 'page' => $page->show()));
        $this->display();

    }

    // 添加
    public function cataddAction()
    {
        if ($data = arPost()) :
            $addResult = VideoclassModel::model()->getDb()->insert($data, 1);
            if ($addResult) :
                $this->redirectSuccess('cat');
            endif;
        endif;
        $this->display();

    }

    // 编辑视频分类
    public function catEditAction()
    {
        if ($data = arPost()) :
            if ($cid = $data['cid']) :
                $updateResult = VideoclassModel::model()
                    ->getDb()
                    ->where(array('cid' => $cid))
                    ->update($data, 1);
                $this->redirectSuccess(array('cat', array('cid' => $cid)));
            endif;
        endif;

        if ($cid = arRequest('cid')) :
            $cat = VideoclassModel::model()
                ->getDb()
                ->where(array('cid' => $cid))
                ->queryRow();
            $this->assign(array('cat' => $cat));
            $this->display('catadd');
        endif;

    }

    // 删除视频分类
    public function catDelAction()
    {
        if ($cid = arRequest('cid')) :
            $deleteResult = VideoclassModel::model()->getDb()
                ->where(array('cid' => $cid))
                ->delete();
        endif;
        $this->redirectSuccess('cat');

    }

	// 视频管理
	 public function videoManagerAction()
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
        $catMap = arModule('Video')->getCatMap();
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
                    $updateResult = VideolistModel::model()->getDb()->where($conditionition)->update($data, true);
                    if ($updateResult) :
                        $this->redirectSuccess(array('list'));
                    else :
                        $this->redirectError(array('list'));
                    endif;
                endif;
                $conditionition['aid'] = $aid;
                $article = VideolistModel::model()->getDb()->where($conditionition)->queryRow();
                $article = arModule('Video')->getArtGallery($article);

                // var_dump($article);
                // die();
                if(get_magic_quotes_gpc()) :
                    $article['content'] = stripslashes($article['content']);
                    $article['content'] = str_replace('\"', '', $article['content']);
                endif;
                $this->assign(array('article' => $article));
                return $this->display('@/Video/manager');

                break;
            // 删除
            case 'delete':
                $deleteResult = VideolistModel::model()->getDb()->where($conditionition)->delete();
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
                    $insertResult = VideolistModel::model()->getDb()->insert($data, true);
                    if ($insertResult) :
                        $this->redirectSuccess(array('list'));
                    else :
                        $this->redirectError(array('list'));
                    endif;

                endif;

                Ar::setConfig('DEBUG_SHOW_ERROR', false);

                $this->display('@/Video/manager');

                break;
            default:
                $this->redirectError(array('list'), '非法操作');
                break;
        }

    }

    // 上传视频
    public function uploadRegPicAction()
    {
        $dstDir = arCfg('UPLOAD_DIR') . 'Video' . DS;
        // 上传视频名称
        $picName = arComp('ext.upload')->upload('uploadpic', $dstDir, array('avi','rmvb','rm','asf','divx','mpg','mpeg','mpe','wmv','mp4','mkv','vob'));
        //$picName = arComp('ext.upload')->upload('uploadpic', $dstDir, 'all');
        if ($picName) :
            $file =  $dstDir . $picName;
                $gallery = array(
                    'url' => arComp('url.route')->serverPath($file),
                );
                //$gid = VideolistModel::model()->getDb()->insert($gallery);
                $this->showJson($gallery);
        else :
            $this->showJsonError(arComp('ext.upload')->errorMsg);
        endif;
    }

    // 上传视频封面图片
    public function videopicAction()
    {
        $dstDir = arCfg('UPLOAD_DIR') . 'Video' . DS;
        // 上传图片名称
        $picName = arComp('ext.upload')->upload('uploadimgpic', $dstDir, 'img');
        if ($picName) :
            $file =  $dstDir . $picName;
            $gallery  = array(
                    'url' => arComp('url.route')->serverPath($file),
            );
            $this->showJson($gallery);
        else :
             $this->showJsonError(arComp('ext.upload')->errorMsg);
        endif;
    }



}