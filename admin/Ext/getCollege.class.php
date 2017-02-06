<?php
class getCollege {

// 获取url链接
   public function getUrl($i)
    {
        $allUrl = array();
        // for($i=1;$i<11;$i++) {
            $contents = arComp('rpc.api')->remoteCall("http://www.diyigaokao.com/college/list.aspx?&p=$i");
            $contents = preg_match('#<table(.*)</table>#sU', $contents, $matchContents);
            preg_match_all('#<a href=\"/college/(.*)</a>#sU', $matchContents[0], $match);
          foreach ($match[0] as $match) {
              $allUrl[] = $match;
          }
        // }
        return $allUrl;

    }
    // 获取内容
    public function getContents()
    {
       $j = arRequest('page');
       $getUrl = $this->getUrl($j);
       $collegeInfo = array(
        );
       $i = arRequest('list');
       // for($i=0;$i<10;$i++){
        $contents = preg_match('#"(.*)"#sU', $getUrl[$i], $matchUrl);
        $getCont = arComp('rpc.api')->remoteCall("http://www.diyigaokao.com".$matchUrl[1]);
        // 内容简介
        $matchCont = preg_match('#<div class=\"introduction\">(.*)</div>#sU',$getCont,$matchContColl);
        // 获取名称
        $getName = preg_match('#<div class=\"heading cf\">(.*)<h1>(.*)</h1>#sU',$getCont,$matchName);
        // $this->showJson($matchName[2]);
        // return json_encode(array('Name'=>$matchName[2],'content'=>$matchContColl[1]));
        return array('Name'=>$matchName[2],'content'=>$matchContColl[1]);
        sleep(3);
       // }
    }

    public function insertData()
    {
      $modelGet = arCfg('collegeInsert');
      $getCont = $this->getContents();
      foreach($getCont as $getCont){
        $cond['name like'] = '%'.$getCont['Name'].'%';
        $data['des'] = $getCont['content'];
        foreach ($modelGet as $key => $modelGet) {
          $modelGet = $modelGet.'model';
          $up = $modelGet::model()->getDb()->where($cond)->update($data,true);
          if($up) {
            echo true;
          }else{
            echo false;
          }

        }

      }
    }
    public function insertDataLike($getCont)
    {
        $modelGet = arCfg('collegeInsert');
        $cond['name like'] = '%'.$getCont['Name'].'%';
        $data['des'] = $getCont['content'];
        $true = '';
        $false = '';
        foreach ($modelGet as $key => $modelGet) {
          $modelGet = $modelGet.'model';
          $up = $modelGet::model()->getDb()->where($cond)->update($data,true);

          if($up) {
        //     // echo {'up':true,'name':$modelGet['Name']};
        //     echo $modelGet.'<br/>';
            $true .= $modelGet.'**';
          }else{
            $false .=$modelGet.'**';
          }

        }
        var_dump(array('0'=>$true,'1'=>$false));
        // return array('0'=>$true,'1'=>'false');

    }

}
?>