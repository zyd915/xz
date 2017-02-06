<?php
// 相关数据库
class DataController extends BaseController
{
    public function init()
    {
        parent::init();
        error_reporting(0);
        set_time_limit(0);
        // exit('暂未开放');

    }

    // 一本院校数据库
    public function yuanxiaoAction()
    {
         // 调用上传插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'ajaxfileupload',
                    'this' => $this
                )
            )
        );
        $this->display('/Data/yuanxiao');

    }

    // 用户数据
    public function userDataAction()
    {
         // 调用上传插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'ajaxfileupload',
                    'this' => $this
                )
            )
        );
        $this->display('/Data/userData');

    }

    // 艺体数据
    public function ytsearchAction()
    {
         // 调用上传插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'ajaxfileupload',
                    'this' => $this
                )
            )
        );
        $this->display('/Data/ytsearch');

    }

    // 专业数据库
    public function zhuanyeAction()
    {
         // 调用上传插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'ajaxfileupload',
                    'this' => $this
                )
            )
        );
        $this->display('/Data/zhuanye');

    }

    // 数据查询
    public function searchAction()
    {
         // 调用上传插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'ajaxfileupload',
                    'this' => $this
                )
            )
        );
        $this->display('/Data/search');

    }

    // 数据查询导出
    public function exportsearchAction()
    {
        $km = '';
        $pc = arRequest('pc');
        $tableName = DataSearchModel::getTableName($pc);
        $columns = DataSearchModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataSearchModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $title = '数据查询' . DataSearchModel::$kmMap[$km] . DataSearchModel::$pcMap[$pc];
        $objPHPExcel = $this->loadExcel($title);

        // 设置头
        foreach ($columns as $key => $column) :
            if ($key >= 26) :
                $hkey = 'a' . chr(ord('a') + ($key - 26));
            else :
                $hkey = chr(ord('a') + $key);
            endif;
            $keyCell = strtoupper($hkey) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
        endforeach;

        // 设置数据
        foreach ($schools as $rowKey => $school) :
            $key = 0;
            foreach ($school as $column => $s) :
                if ($key >= 26) :
                    $hkey = 'a' . chr(ord('a') + ($key - 26));
                else :
                    $hkey = chr(ord('a') + $key);
                endif;
                if (strlen($s) > 50) :
                    // $s = '文字过长省略';
                endif;
                $keyCell = strtoupper($hkey) . ($rowKey + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $s);
                $key++;
            endforeach;
        endforeach;

        // Redirect output to a client鈥檚 web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    // 数据查询导入
    public function importsearchAction()
    {
        $km = '';
        $pc = arRequest('pc');
        $tableName = DataSearchModel::getTableName($pc);

        $columns = DataSearchModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataSearchModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $dstDir = arCfg('PATH.UPLOAD') . 'Temp' . DS;
        $fileName = arComp('ext.upload')->upload('inportFile', $dstDir, 'xlsx');
        $absoluteName = $dstDir . $fileName;
        $objPHPExcel = $this->loadExcelRead($absoluteName);
        $insertBundles = array();
        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) :
            $insertBundle = array();
            if ($objPHPExcel->getActiveSheet()->getRowDimension($row->getRowIndex())->getVisible()) {
                if ($row->getRowIndex() == 1) :
                    continue;
                endif;
                // 设置头
                foreach ($columns as $key => $column) :
                    if ($key >= 26) :
                        $hkey = 'a' . chr(ord('a') + ($key - 26));
                    else :
                        $hkey = chr(ord('a') + $key);
                    endif;
                    $keyCell = strtoupper($hkey);
                    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
                    $columnValue = $objPHPExcel->getActiveSheet()->getCell($keyCell.$row->getRowIndex())->getValue();
                    $columnValue = trim($columnValue);
                    $insertBundle[$column] = $columnValue;
                endforeach;
                $insertBundles[] = $insertBundle;
                // echo '    Row number - ' , $row->getRowIndex() , ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('E'.$row->getRowIndex())->getValue(), ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('D'.$row->getRowIndex())->getFormattedValue(), ' ';
            }

        endforeach;

        // 删除文件
        @unlink($absoluteName);
        if ($insertBundles) :
            // 先清空数据
            // DataSearchModel::model()
            //     ->getDb()
            //     ->where(array('des' => ''))
            //     ->table($tableName)
            //     ->delete();

            DataSearchModel::model()
                ->getDb()
                // ->where(array('des' => ''))
                // ->table($tableName)
                ->sqlExec('truncate table ' . $tableName);

            $insertResult = DataSearchModel::model()
                ->getDb()
                ->table($tableName)
                ->batchInsert($insertBundles);
            if ($insertResult) :
                $this->showJsonSuccess('成功执行' . count($insertBundles) . '条数据');
            else :
                $this->showJsonError('数据库执行错误');
            endif;
        else :
            $this->showJsonError('数据不能为空');
        endif;

    }

     // 分段数据查询
    public function sectionAction()
    {
         // 调用上传插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'ajaxfileupload',
                    'this' => $this
                )
            )
        );
        $this->display('/Data/section');

    }

    // 分段数据查询导出
    public function exportsectionAction()
    {
        $km = '';
        $pc = arRequest('pc');
        $tableName = DataSectionModel::getTableName($pc);
        $columns = DataSectionModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataSectionModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $title = '分段数据查询' . DataSectionModel::$kmMap[$km] . DataSectionModel::$pcMap[$pc];
        $objPHPExcel = $this->loadExcel($title);

        // 设置头
        foreach ($columns as $key => $column) :
            if ($key >= 26) :
                $hkey = 'a' . chr(ord('a') + ($key - 26));
            else :
                $hkey = chr(ord('a') + $key);
            endif;
            $keyCell = strtoupper($hkey) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
        endforeach;

        // 设置数据
        foreach ($schools as $rowKey => $school) :
            $key = 0;
            foreach ($school as $column => $s) :
                if ($key >= 26) :
                    $hkey = 'a' . chr(ord('a') + ($key - 26));
                else :
                    $hkey = chr(ord('a') + $key);
                endif;
                if (strlen($s) > 50) :
                    // $s = '文字过长省略';
                endif;
                $keyCell = strtoupper($hkey) . ($rowKey + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $s);
                $key++;
            endforeach;
        endforeach;

        // Redirect output to a client鈥檚 web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    // 分段数据查询导入
    public function importsectionAction()
    {
        $km = '';
        $pc = arRequest('pc');
        $tableName = DataSectionModel::getTableName($pc);

        $columns = DataSectionModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataSectionModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $dstDir = arCfg('PATH.UPLOAD') . 'Temp' . DS;
        $fileName = arComp('ext.upload')->upload('inportFile', $dstDir, 'xlsx');
        $absoluteName = $dstDir . $fileName;
        $objPHPExcel = $this->loadExcelRead($absoluteName);
        $insertBundles = array();
        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) :
            $insertBundle = array();
            if ($objPHPExcel->getActiveSheet()->getRowDimension($row->getRowIndex())->getVisible()) {
                if ($row->getRowIndex() == 1) :
                    continue;
                endif;
                // 设置头
                foreach ($columns as $key => $column) :
                    if ($key >= 26) :
                        $hkey = 'a' . chr(ord('a') + ($key - 26));
                    else :
                        $hkey = chr(ord('a') + $key);
                    endif;
                    $keyCell = strtoupper($hkey);
                    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
                    $columnValue = $objPHPExcel->getActiveSheet()->getCell($keyCell.$row->getRowIndex())->getValue();
                    $columnValue = trim($columnValue);
                    $insertBundle[$column] = $columnValue;
                endforeach;
                $insertBundles[] = $insertBundle;
                // echo '    Row number - ' , $row->getRowIndex() , ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('E'.$row->getRowIndex())->getValue(), ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('D'.$row->getRowIndex())->getFormattedValue(), ' ';
            }

        endforeach;

        // 删除文件
        @unlink($absoluteName);
        if ($insertBundles) :
            // 先清空数据
            // DataSearchModel::model()
            //     ->getDb()
            //     ->where(array('des' => ''))
            //     ->table($tableName)
            //     ->delete();

            DataSectionModel::model()
                ->getDb()
                // ->where(array('des' => ''))
                // ->table($tableName)
                ->sqlExec('truncate table ' . $tableName);

            $insertResult = DataSectionModel::model()
                ->getDb()
                ->table($tableName)
                ->batchInsert($insertBundles);
            if ($insertResult) :
                $this->showJsonSuccess('成功执行' . count($insertBundles) . '条数据');
            else :
                $this->showJsonError('数据库执行错误');
            endif;
        else :
            $this->showJsonError('数据不能为空');
        endif;

    }

    // 专业数据查询
    public function zysearchAction()
    {
         // 调用上传插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'ajaxfileupload',
                    'this' => $this
                )
            )
        );
        $this->display('/Data/zysearch');

    }

    // 专业数据查询导出
    public function exportzysearchAction()
    {
        $km = '';
        $pc = arRequest('pc');
        $tableName = DataZysearchModel::getTableName($pc);
        $columns = DataZysearchModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataZysearchModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $title = '专业数据查询' . DataZysearchModel::$kmMap[$km] . DataZysearchModel::$pcMap[$pc];
        $objPHPExcel = $this->loadExcel($title);

        // 设置头
        foreach ($columns as $key => $column) :
            if ($key >= 26) :
                $hkey = 'a' . chr(ord('a') + ($key - 26));
            else :
                $hkey = chr(ord('a') + $key);
            endif;
            $keyCell = strtoupper($hkey) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
        endforeach;

        // 设置数据
        foreach ($schools as $rowKey => $school) :
            $key = 0;
            foreach ($school as $column => $s) :
                if ($key >= 26) :
                    $hkey = 'a' . chr(ord('a') + ($key - 26));
                else :
                    $hkey = chr(ord('a') + $key);
                endif;
                if (strlen($s) > 50) :
                    // $s = '文字过长省略';
                endif;
                $keyCell = strtoupper($hkey) . ($rowKey + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $s);
                $key++;
            endforeach;
        endforeach;

        // Redirect output to a client鈥檚 web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    // 专业数据查询导入
    public function importzysearchAction()
    {
        $km = '';
        $pc = arRequest('pc');
        $tableName = DataZysearchModel::getTableName($pc);

        $columns = DataZysearchModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataZysearchModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $dstDir = arCfg('PATH.UPLOAD') . 'Temp' . DS;
        $fileName = arComp('ext.upload')->upload('inportFile', $dstDir, 'xlsx');
        $absoluteName = $dstDir . $fileName;
        $objPHPExcel = $this->loadExcelRead($absoluteName);
        $insertBundles = array();
        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) :
            $insertBundle = array();
            if ($objPHPExcel->getActiveSheet()->getRowDimension($row->getRowIndex())->getVisible()) {
                if ($row->getRowIndex() == 1) :
                    continue;
                endif;
                // 设置头
                foreach ($columns as $key => $column) :
                    if ($key >= 26) :
                        $hkey = 'a' . chr(ord('a') + ($key - 26));
                    else :
                        $hkey = chr(ord('a') + $key);
                    endif;
                    $keyCell = strtoupper($hkey);
                    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
                    $columnValue = $objPHPExcel->getActiveSheet()->getCell($keyCell.$row->getRowIndex())->getValue();
                    $columnValue = trim($columnValue);
                    $insertBundle[$column] = $columnValue;
                endforeach;
                $insertBundles[] = $insertBundle;
                // echo '    Row number - ' , $row->getRowIndex() , ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('E'.$row->getRowIndex())->getValue(), ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('D'.$row->getRowIndex())->getFormattedValue(), ' ';
            }

        endforeach;

        // 删除文件
        @unlink($absoluteName);
        if ($insertBundles) :
            // 先清空数据
            // DataSearchModel::model()
            //     ->getDb()
            //     ->where(array('des' => ''))
            //     ->table($tableName)
            //     ->delete();

            DataZysearchModel::model()
                ->getDb()
                // ->where(array('des' => ''))
                // ->table($tableName)
                ->sqlExec('truncate table ' . $tableName);

            $insertResult = DataZysearchModel::model()
                ->getDb()
                ->table($tableName)
                ->batchInsert($insertBundles);
            if ($insertResult) :
                $this->showJsonSuccess('成功执行' . count($insertBundles) . '条数据');
            else :
                $this->showJsonError('数据库执行错误');
            endif;
        else :
            $this->showJsonError('数据不能为空');
        endif;

    }

    // 艺体数据查询导入
    public function importytAction()
    {
        $tableName = 'data_zy_query_yt';

        $columns = DataZysearchModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataZysearchModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $dstDir = arCfg('PATH.UPLOAD') . 'Temp' . DS;
        $fileName = arComp('ext.upload')->upload('inportFile', $dstDir, 'xlsx');
        $absoluteName = $dstDir . $fileName;
        $objPHPExcel = $this->loadExcelRead($absoluteName);
        $insertBundles = array();
        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) :
            $insertBundle = array();
            if ($objPHPExcel->getActiveSheet()->getRowDimension($row->getRowIndex())->getVisible()) {
                if ($row->getRowIndex() == 1) :
                    continue;
                endif;
                // 设置头
                foreach ($columns as $key => $column) :
                    if ($key >= 26) :
                        $hkey = 'a' . chr(ord('a') + ($key - 26));
                    else :
                        $hkey = chr(ord('a') + $key);
                    endif;
                    $keyCell = strtoupper($hkey);
                    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
                    $columnValue = $objPHPExcel->getActiveSheet()->getCell($keyCell.$row->getRowIndex())->getValue();
                    $columnValue = trim($columnValue);
                    $insertBundle[$column] = $columnValue;
                endforeach;
                $insertBundles[] = $insertBundle;
                // echo '    Row number - ' , $row->getRowIndex() , ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('E'.$row->getRowIndex())->getValue(), ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('D'.$row->getRowIndex())->getFormattedValue(), ' ';
            }

        endforeach;

        // 删除文件
        @unlink($absoluteName);
        if ($insertBundles) :
            // 先清空数据
            // DataSearchModel::model()
            //     ->getDb()
            //     ->where(array('des' => ''))
            //     ->table($tableName)
            //     ->delete();

            DataZysearchModel::model()
                ->getDb()
                // ->where(array('des' => ''))
                // ->table($tableName)
                ->sqlExec('truncate table ' . $tableName);

            $insertResult = DataZysearchModel::model()
                ->getDb()
                ->table($tableName)
                ->batchInsert($insertBundles);
            if ($insertResult) :
                $this->showJsonSuccess('成功执行' . count($insertBundles) . '条数据');
            else :
                $this->showJsonError('数据库执行错误');
            endif;
        else :
            $this->showJsonError('数据不能为空');
        endif;

    }

    // 专业导出数据
    public function exportzyAction()
    {
        $km = arRequest('km');
        $pc = arRequest('pc');
        $tableName = DataZyModel::getTableName($km, $pc);

        $columns = DataZyModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataZyModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $title = '专业数据库' . DataZyModel::$kmMap[$km] . DataZyModel::$pcMap[$pc];
        $objPHPExcel = $this->loadExcel($title);

        // 设置头
        foreach ($columns as $key => $column) :
            if ($key >= 26) :
                $hkey = 'a' . chr(ord('a') + ($key - 26));
            else :
                $hkey = chr(ord('a') + $key);
            endif;
            $keyCell = strtoupper($hkey) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
        endforeach;

        // 设置数据
        foreach ($schools as $rowKey => $school) :
            $key = 0;
            foreach ($school as $column => $s) :
                if ($key >= 26) :
                    $hkey = 'a' . chr(ord('a') + ($key - 26));
                else :
                    $hkey = chr(ord('a') + $key);
                endif;
                if (strlen($s) > 50) :
                    // $s = '文字过长省略';
                endif;
                $keyCell = strtoupper($hkey) . ($rowKey + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $s);
                $key++;
            endforeach;
        endforeach;

        // Redirect output to a client鈥檚 web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    // 专业导入数据
    public function importzyAction()
    {
        $km = arRequest('km');
        $pc = arRequest('pc');
        $tableName = DataZyModel::getTableName($km, $pc);

        $columns = DataZyModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataZyModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $dstDir = arCfg('PATH.UPLOAD') . 'Temp' . DS;
        $fileName = arComp('ext.upload')->upload('inportFile', $dstDir, 'xlsx');
        $absoluteName = $dstDir . $fileName;
        $objPHPExcel = $this->loadExcelRead($absoluteName);
        $insertBundles = array();
        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) :
            $insertBundle = array();
            if ($objPHPExcel->getActiveSheet()->getRowDimension($row->getRowIndex())->getVisible()) {
                if ($row->getRowIndex() == 1) :
                    continue;
                endif;
                // 设置头
                foreach ($columns as $key => $column) :
                    if ($key >= 26) :
                        $hkey = 'a' . chr(ord('a') + ($key - 26));
                    else :
                        $hkey = chr(ord('a') + $key);
                    endif;
                    $keyCell = strtoupper($hkey);
                    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
                    $columnValue = $objPHPExcel->getActiveSheet()->getCell($keyCell.$row->getRowIndex())->getValue();
                    $columnValue = trim($columnValue);
                    $insertBundle[$column] = $columnValue;
                endforeach;
                $insertBundles[] = $insertBundle;
                // echo '    Row number - ' , $row->getRowIndex() , ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('E'.$row->getRowIndex())->getValue(), ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('D'.$row->getRowIndex())->getFormattedValue(), ' ';
            }

        endforeach;

        // 删除文件
        @unlink($absoluteName);
        if ($insertBundles) :
            // 先清空数据
            // DataZyModel::model()
            //     ->getDb()
            //     ->where(array('des' => ''))
            //     ->table($tableName)
            //     ->delete();
            DataZyModel::model()
                ->getDb()
                // ->where(array('des' => ''))
                // ->table($tableName)
                ->sqlExec('truncate table ' . $tableName);

            $insertResult = DataZyModel::model()
                ->getDb()
                ->table($tableName)
                ->batchInsert($insertBundles);
            if ($insertResult) :
                $this->showJsonSuccess('成功执行' . count($insertBundles) . '条数据');
            else :
                $this->showJsonError('数据库执行错误');
            endif;
        else :
            $this->showJsonError('数据不能为空');
        endif;

    }

    // 用户导出数据
    public function exportyhAction()
    {
        $tableName = 'u_user_kaosheng';
        $columns = DataYhModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataYhModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $title = '用户数据库';
        $objPHPExcel = $this->loadExcel($title);

        // 设置头
        foreach ($columns as $key => $column) :
            if ($key >= 26) :
                $hkey = 'a' . chr(ord('a') + ($key - 26));
            else :
                $hkey = chr(ord('a') + $key);
            endif;
            $keyCell = strtoupper($hkey) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
        endforeach;

        // 设置数据
        foreach ($schools as $rowKey => $school) :
            $key = 0;
            foreach ($school as $column => $s) :
                if ($key >= 26) :
                    $hkey = 'a' . chr(ord('a') + ($key - 26));
                else :
                    $hkey = chr(ord('a') + $key);
                endif;
                if (strlen($s) > 50) :
                    // $s = '文字过长省略';
                endif;
                $keyCell = strtoupper($hkey) . ($rowKey + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $s);
                $key++;
            endforeach;
        endforeach;

        // Redirect output to a client鈥檚 web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    // 用户导出数据
    public function exportyhlAction()
    {
        $tableName = 'u_user';
        $columns = DataYhModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataYhModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $title = '用户联系方式';
        $objPHPExcel = $this->loadExcel($title);

        // 设置头
        foreach ($columns as $key => $column) :
            if ($key >= 26) :
                $hkey = 'a' . chr(ord('a') + ($key - 26));
            else :
                $hkey = chr(ord('a') + $key);
            endif;
            $keyCell = strtoupper($hkey) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
        endforeach;

        // 设置数据
        foreach ($schools as $rowKey => $school) :
            $key = 0;
            foreach ($school as $column => $s) :
                if ($key >= 26) :
                    $hkey = 'a' . chr(ord('a') + ($key - 26));
                else :
                    $hkey = chr(ord('a') + $key);
                endif;
                if (strlen($s) > 50) :
                    // $s = '文字过长省略';
                endif;
                $keyCell = strtoupper($hkey) . ($rowKey + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $s);
                $key++;
            endforeach;
        endforeach;

        // Redirect output to a client鈥檚 web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    // 艺体导出数据
    public function exportytAction()
    {
        $tableName = 'data_zy_query_yt';

        $columns = DataYxModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataYxModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $title = '艺体数据库';
        $objPHPExcel = $this->loadExcel($title);

        // 设置头
        foreach ($columns as $key => $column) :
            if ($key >= 26) :
                $hkey = 'a' . chr(ord('a') + ($key - 26));
            else :
                $hkey = chr(ord('a') + $key);
            endif;
            $keyCell = strtoupper($hkey) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
        endforeach;

        // 设置数据
        foreach ($schools as $rowKey => $school) :
            $key = 0;
            foreach ($school as $column => $s) :
                if ($key >= 26) :
                    $hkey = 'a' . chr(ord('a') + ($key - 26));
                else :
                    $hkey = chr(ord('a') + $key);
                endif;
                if (strlen($s) > 50) :
                    // $s = '文字过长省略';
                endif;
                $keyCell = strtoupper($hkey) . ($rowKey + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $s);
                $key++;
            endforeach;
        endforeach;

        // Redirect output to a client鈥檚 web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    // 院校导出数据
    public function exportAction()
    {
        $km = arRequest('km');
        $pc = arRequest('pc');
        $tableName = DataYxModel::getTableName($km, $pc);

        $columns = DataYxModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataYxModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $title = '院校数据库' . DataYxModel::$kmMap[$km] . DataYxModel::$pcMap[$pc];
        $objPHPExcel = $this->loadExcel($title);

        // 设置头
        foreach ($columns as $key => $column) :
            if ($key >= 26) :
                $hkey = 'a' . chr(ord('a') + ($key - 26));
            else :
                $hkey = chr(ord('a') + $key);
            endif;
            $keyCell = strtoupper($hkey) . '1';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
        endforeach;

        // 设置数据
        foreach ($schools as $rowKey => $school) :
            $key = 0;
            foreach ($school as $column => $s) :
                if ($key >= 26) :
                    $hkey = 'a' . chr(ord('a') + ($key - 26));
                else :
                    $hkey = chr(ord('a') + $key);
                endif;
                if (strlen($s) > 50) :
                    // $s = '文字过长省略';
                endif;
                $keyCell = strtoupper($hkey) . ($rowKey + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $s);
                $key++;
            endforeach;
        endforeach;

        // Redirect output to a client鈥檚 web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    // excel
    public function importAction()
    {
        $km = arRequest('km');
        $pc = arRequest('pc');
        $tableName = DataYxModel::getTableName($km, $pc);

        $columns = DataYxModel::model()
            ->getDb()
            ->table($tableName)
            ->getColumns();
        $schools = DataYxModel::model()
            ->getDb()
            ->table($tableName)
            ->queryAll();

        $dstDir = arCfg('PATH.UPLOAD') . 'Temp' . DS;
        $fileName = arComp('ext.upload')->upload('inportFile', $dstDir, 'xlsx');
        $absoluteName = $dstDir . $fileName;
        $objPHPExcel = $this->loadExcelRead($absoluteName);
        $insertBundles = array();
        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) :
            $insertBundle = array();
            if ($objPHPExcel->getActiveSheet()->getRowDimension($row->getRowIndex())->getVisible()) {
                if ($row->getRowIndex() == 1) :
                    continue;
                endif;
                // 设置头
                foreach ($columns as $key => $column) :
                    if ($key >= 26) :
                        $hkey = 'a' . chr(ord('a') + ($key - 26));
                    else :
                        $hkey = chr(ord('a') + $key);
                    endif;
                    $keyCell = strtoupper($hkey);
                    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue($keyCell, $column);
                    $columnValue = $objPHPExcel->getActiveSheet()->getCell($keyCell.$row->getRowIndex())->getValue();
                    $columnValue = trim($columnValue);
                    $insertBundle[$column] = $columnValue;
                endforeach;
                $insertBundles[] = $insertBundle;
                // echo '    Row number - ' , $row->getRowIndex() , ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('E'.$row->getRowIndex())->getValue(), ' ';
                // echo $objPHPExcel->getActiveSheet()->getCell('D'.$row->getRowIndex())->getFormattedValue(), ' ';
            }

        endforeach;

        // 删除文件
        @unlink($absoluteName);
        if ($insertBundles) :
            // 先清空数据
            DataYxModel::model()
                ->getDb()
                ->where(array('pm >=' => '0'))
                ->table($tableName)
                ->delete();

            $insertResult = DataYxModel::model()
                ->getDb()
                ->table($tableName)
                ->batchInsert($insertBundles);
            if ($insertResult) :
                $this->showJsonSuccess('成功执行' . count($insertBundles) . '条数据');
            else :
                $this->showJsonError('数据库执行错误');
            endif;
        else :
            $this->showJsonError('数据不能为空');
        endif;

    }

    // 加载excel库
    public function loadExcel($title)
    {
        include arCfg('EXTENSION_DIR') . 'excel' . DS . 'Classes' . DS . 'PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords($title . "office 2007 openxml php")
            ->setCategory($title);
        return $objPHPExcel;

    }

    // 加载excel库
    public function loadExcelRead($file)
    {
        include arCfg('EXTENSION_DIR') . 'excel/Classes/PHPExcel/IOFactory.php';

        $objPHPExcel = PHPExcel_IOFactory::load($file);

        return $objPHPExcel;

    }


}
