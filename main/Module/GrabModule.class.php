<?php
class GrabModule{
    // 获取信息
    public function getNews()
    {
        $contents = arComp('rpc.api')->remoteCall('http://www.chinaqw.com/');
        // $contents = iconv('gb2312', 'utf-8', $contents);
        $contents = preg_match('#<div class="new_top_p ">(.*)<!--.*e-->
                       </div>#sU', $contents, $matchContents);
        // var_dump($matchContents);
        preg_match_all('#<a href="(.*)">(.*)</a>#sU', $matchContents[0], $match);
        // preg_match('#<div id="myTabhd_dd_Content0">.*<div class="new_top_p ">.*</div>#sU', $contents, $match);
        // var_dump($match);
        $news = array();
// var_dump($match[1]);
        foreach ($match[1] as $key => $url) :
            $news[] = array(
                'url' => arU('news', array('ukey' => arComp('hash.mcrypt')->encrypt($url))),//加密url地址
                // 'title' => iconv('gb2312', 'utf-8', $match[2][$key]),
                 'title' => $match[2][$key],
            );
        endforeach;
        return $news;

    }

    // 获取内容
    public function getContents($url)
    {
        $contents = arComp('rpc.api')->remoteCall($url);
        preg_match('#<title>(.*)</title>#', $contents, $title);
        $title = $title[0];
        $title = iconv('gb2312', 'utf-8', $title[1]);
        preg_match('#<div class="left_zw" style="position:relative">(.*)<div>#sU', $contents, $content);
        $content = $content[0];

        $content = iconv('gb2312', 'utf-8', $content);

         // var_dump(array($title, $content));
        return array('news'=>$title, 'content'=>$content);

    }

    // 国际新闻
    public function getInterNews()
    {
         // $this->display();
        $inetr = arComp('rpc.api')->remoteCall('http://www.chinanews.com/iframe/chinaqw/qwdd.shtml');
        // $contents = iconv('gb2312', 'utf-8', $contents);
        $contents = preg_match_all('#<div class="xwzxdd-dbt">(.*)</div>#sU', $inetr, $inter);
        preg_match_all('#<a href="(.*)">(.*)</a>#sU', $inter[0], $matchs);
        // var_dump($inter);
        // preg_match('#<div id="myTabhd_dd_Content0">.*<div class="new_top_p ">.*</div>#sU', $contents, $match);

        $new = array();

        foreach ($matchs[1] as $key => $url) :
            $new[] = array(
                'url' => arU('news', array('ukey' => arComp('hash.mcrypt')->encrypt($url))),//加密url地址
                'title' => iconv('gb2312', 'utf-8', $matchs[2][$key]),
                 // 'title' => $matchs[2][$key],
            );
        endforeach;

    return $new;
    }

}
