<?php
/**
 * 抓取数据类数据来源 第一高考网
 *
 */
class GrabController extends BaseController
{
    // 抓取地址
    public $mainUrl = 'http://www.diyigaokao.com/college/list.aspx';
    // 翻页key
    public $pageKey = 'p';
    // 页
    public $page = '1';
    // 页
    public $maxPage = '277';

    // 获取中国大学学校
    public function schoolAction()
    {
        exit('close');
        header("Content-Type:text/html;charset=utf-8");
        $this->maxPage = 277;
        $this->mainUrl = 'http://www.diyigaokao.com/college/list.aspx';
        $this->pageKey = 'p';
        $this->page = arGet('page', '1');

        if ($this->page <= $this->maxPage) :
            echo '抓取第' . $this->page . '页<br>';
            echo 'url: ' . $this->mainUrl . '<br>';
            // $contents = arComp('rpc.api')->remoteCall($this->mainUrl, array($this->pageKey => $this->page), 'get');
            $contents = arComp('rpc.api')->remoteCall($this->mainUrl . '?' . $this->pageKey . '=' . $this->page);
            preg_match_all('#<tr>.*<td class="college">(.*)</tr>#sU', $contents, $matcharea);
            // $matcharea[0] = iconv('gb2312', 'utf-8', $matcharea[0]);
            if (!$matcharea) :
                echo '抓取出错';
                exit;
            else :
                $schools = array();
                foreach ($matcharea[0] as $dataArea) :
                    preg_match_all('#<td.*>(.*)</td>#sU', $dataArea, $match);
                    $school = array();
                    for ($i = 0; $i < count($match[0]); $i++) :
                        if ($i == 0) :
                            preg_match('#.*value="(\d+)".*#', $match[0][$i], $schoolId);
                            $schoolDetailInfo = arComp('rpc.api')->remoteCall('http://www.diyigaokao.com/college/' . $schoolId[1]);
                            preg_match('#院校地址：</strong>(.*)</li>#sU', $schoolDetailInfo, $address);
                            $address = $address[1];
                            preg_match('#招生网址：</strong><a href="(.*)" target="_blank">.*</a>#sU', $schoolDetailInfo, $schoolUrl);
                            $schoolUrl = isset($schoolUrl[1]) ? $schoolUrl[1] : '';
                            preg_match('#<div class="introduction">(.*)</div>#sU', $schoolDetailInfo, $description);
                            $description = trim(strip_tags($description[1], '<p><br>'));
                            $description = str_replace('●', '', $description);

                            preg_match('#<div class="summary cf">.*<a href="(.*)" target="_blank">招生章程</a>#sU', $schoolDetailInfo, $zsurl);
                            $zsurl = $zsurl[1];

                            preg_match('#<p>院校热度：<big><small style=".*"></small></big><em>(.*)</em></p>#sU', $schoolDetailInfo, $hot);
                            $hot = $hot[1];

                            $school[] = $address;
                            $school[] = $schoolUrl;
                            $school[] = $description;
                            $school[] = $zsurl;
                            $school[] = $hot;
                        endif;

                        $item = trim(preg_replace('#\s+#', ' ', strip_tags($match[0][$i])));
                        // 获取学校详细信息

                        // 排名特殊处理
                        if ($i == 1) :
                            $matchPm = array();
                            preg_match('#(\D+\d+)(\D+\d+){0,1}#', $item, $matchPm);
                            $school[] = $matchPm[1];
                            $school[] = $matchPm[2];
                        else :
                            $school[] = $item;
                        endif;
                    endfor;
                    $schoolTemp = array_combine(array('address', 'url', 'des', 'zsurl', 'hot', 'sname', 'pm1', 'pm2', 'area', 'ts', 'ls', 'jb', 'lx', 'lb'), $school);
                    $schools[] = $schoolTemp;
                    echo '学校:' . $school[5] . '<br>';
                endforeach;
                CollegesModel::model()->getDb()->batchInsert($schools);
                // 继续抓
                $this->redirect(array('', array('page' => $this->page + 1)));
            endif;
            // var_dump($contents);
        else :
            echo '抓取完成';
        endif;

    }

    // 抓取本科专业
    public function bkzyAction()
    {
        // exit('close');
        set_time_limit(0);
        header("Content-Type:text/html;charset=utf-8");
        $this->mainUrl = 'http://www.diyigaokao.com/major/bklist.aspx';

        $contents = arComp('rpc.api')->remoteCall($this->mainUrl);
        preg_match('#<!--找专业-专业列表 start-->(.*)<!--找专业-列表 end-->#sU', $contents, $matchArea);
        //<li><a href="/major/010101/" title="哲学" target="_blank">哲学</a></li>
        //本科专业地址
        preg_match_all('#<li><a href="(.*?)" title=".*?" target="_blank">.*?</a></li>#', $matchArea[1], $matchArray);
        $majors = array();
        foreach ($matchArray[1] as $majorNo) :
            $major = array();
            $majorInfo = arComp('rpc.api')->remoteCall('http://www.diyigaokao.com' . $majorNo);
            //<!-- 专业查询结果-介绍 start -->
            preg_match('#<!-- 专业查询结果-介绍 start -->(.*?)<!-- 专业查询结果-介绍 end -->
#sU', $majorInfo, $titleInfo);
            preg_match_all('#<li>(.*?)</li>#', $titleInfo[1], $li);
            //类别
            preg_match('#.*?\((.*?)\).*?#', $li[0][0], $lb);
            $major['lb'] =$lb[1];
            //学科 
            preg_match('#<li>.*?：(.*?)</li>#', $li[0][1], $xk); 
            $major['xk']=$xk[1];
            //门类 
            preg_match('#<li>.*?：(.*?)</li>#', $li[0][2], $ml); 
            $major['ml']=$ml[1];
            //代码
            preg_match('#<li>.*?：(.*?)</li>#', $li[0][3], $dm); 
            $major['dm']=$dm[1];           
            //专业
            preg_match("#<h2>(.*?)\(.*?\)</h2>#",  $titleInfo[1],$mname);
            $major['mname'] =$mname[1];
            // <!-- 专业查询结果-专业详情 start -->
            preg_match('#<!-- 专业查询结果-专业详情 start -->(.*?)<!-- 专业查询结果-专业详情 end -->#sU', $majorInfo, $zydes);
            preg_match('#<div class="inner">(.*?)</p>#sU', $zydes[1],$des);
            //详情
            $major['des']=$des[1]."</p>";
            preg_match('#<ol>(.*?)</ol>.*?<h4><i></i>相近专业</h4>#sU', $zydes[1],$employment);
            
            //preg_match('#<ol>(.*?)</ol>#sU', $employments[0],$employment);  
            $major['employment'] = $employment[1];
            // var_dump($major);
            // exit;
            MajorsModel::model()->getDb()->insert($major);

        endforeach;
        //MajorsModel::model()->getDb()->batchInsert($majors);
        echo '执行完成';

    }

    // 抓取专科专业
    public function zkzyAction()
    {
        // exit('close');
        set_time_limit(0);
        header("Content-Type:text/html;charset=utf-8");
        $this->mainUrl = 'http://www.diyigaokao.com/major/zklist.aspx';
        $contents = arComp('rpc.api')->remoteCall($this->mainUrl);
        preg_match('#<!--找专业-专业列表 start-->(.*)<!--找专业-列表 end-->#sU', $contents, $matchArea);
        //<li><a href="/major/010101/" title="哲学" target="_blank">哲学</a></li>
        //本科专业地址
        preg_match_all('#<li><a href="(.*?)" title=".*?" target="_blank">.*?</a></li>#', $matchArea[1], $matchArray);
        $majors = array();
        foreach ($matchArray[1] as $majorNo) :
            $major = array();
            $majorInfo = arComp('rpc.api')->remoteCall('http://www.diyigaokao.com' . $majorNo);
            //<!-- 专业查询结果-介绍 start -->
            preg_match('#<!-- 专业查询结果-介绍 start -->(.*?)<!-- 专业查询结果-介绍 end -->
#sU', $majorInfo, $titleInfo);
            preg_match_all('#<li>(.*?)</li>#', $titleInfo[1], $li);
            //类别
            preg_match('#.*?\((.*?)\).*?#', $li[0][0], $lb);
            $major['lb'] =$lb[1];
            //学科 
            preg_match('#<li>.*?：(.*?)</li>#', $li[0][1], $xk); 
            $major['xk']=$xk[1];
            //门类 
            preg_match('#<li>.*?：(.*?)</li>#', $li[0][2], $ml); 
            $major['ml']=$ml[1];
            //代码
            preg_match('#<li>.*?：(.*?)</li>#', $li[0][3], $dm); 
            $major['dm']=$dm[1];           
            //专业
            preg_match("#<h2>(.*?)\(.*?\)</h2>#",  $titleInfo[1],$mname);
            $major['mname'] =$mname[1];
            // <!-- 专业查询结果-专业详情 start -->
            preg_match('#<!-- 专业查询结果-专业详情 start -->(.*?)<!-- 专业查询结果-专业详情 end -->#sU', $majorInfo, $zydes);
            preg_match('#<div class="inner">(.*?)</p>#sU', $zydes[1],$des);
            //详情
            $major['des']=$des[1]."</p>"; 
            MajorsModel::model()->getDb()->insert($major);
        endforeach;
        //MajorsModel::model()->getDb()->batchInsert($majors);
        echo '执行完成';

    }

}
