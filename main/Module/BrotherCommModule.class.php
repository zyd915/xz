<?php
class BrotherCommModule
{
    // 获取当前商会的全部兄弟商会
    public function getBroComm($cuid)
    {
        // 我申请的
        $condComm = ("askid = $cuid and status =1");
        $getAllMyComm = AddBroModel::model()->getDb()->where($condComm)->queryAll();
        $myBroCuid = array();
        foreach ($getAllMyComm as $key => $getAllMyComms) {
           $myBroCuid[] = $getAllMyComms['forid'];
        }
        // 向我申请的
        $condCommFor = ("forid = $cuid and status =1");
        $getAllMyCommFor = AddBroModel::model()->getDb()->where($condCommFor)->queryAll();
        foreach ($getAllMyCommFor as $key => $getAllMyCommFors) {
           $myBroCuid[] = $getAllMyCommFors['askid'];
        }
        return $myBroCuid;
    }
    // 找到行兄弟商会的bid
    public function seachBro($cuid)
    {
        $condComm = "(askid = $cuid and forid = $_SESSION[cid]) or (forid = $cuid and askid = $_SESSION[cid]) and status =1 ";
        $delBro = AddBroModel::model()->getDb()->where($condComm)->delete();
        $delArt = "(cuid = $cuid and opid = $_SESSION[cid]) or (opid = $cuid and cuid = $_SESSION[cid])";
        $delArt = ArtUpModel::model()->getDb()->where($delArt)->delete();
        if($delBro) {
            return true;
        }else{
            return false;
        }
    }

    // 查看文章是否被置顶
    public function checkArtUp($aid,$status='up')
    {
        $upcond['aid'] = $aid;
        $upcond['opid'] = $_SESSION['cid'];
        $thisart = ArticleModel::model()->getDb()->where(array('aid' => $aid))->queryRow();
        $upcond['cuid'] = $thisart['cid'];
        $upcount = ArtUpModel::model()->getDb()->where($upcond)->count();
        if($upcount){
            if($status == 'cancel') {
                $insertUp = ArtUpModel::model()->getDb()->where(array($upcond))->delete();
            }else{
                $uptime = ArtUpModel::model()->getDb()->where($upcond)->queryRow();
                $insertUp = ArtUpModel::model()->getDb()->where($upcond)->update(array('ctime'=>time()),true);
            }
        }else{
            $upcond['ctime'] = time();
            $upcond['cuid'] = $thisart['cid'];
            $insertUp = ArtUpModel::model()->getDb()->insert($upcond,true);
        }
        if($insertUp){
            return true;
        }else{
            return false;
        }
    }

    // 置顶文章
    public function UpArtOp($arr)
    {

        $upcond['opid'] = $_SESSION['cid'];
        foreach ($arr as $key => $arrs) {
            $upcond['aid'] = $arrs['aid'];
            $upcount = ArtUpModel::model()->getDb()->where($upcond)->count();
            if($upcount){
                $arr[$key]['checkup'] = false;
                // ECHO $upcount;
            }else{
                $arr[$key]['checkup'] = true;
            }
        }
        return $arr;

    }
}
