<?php
class AboutQtwController extends ArController{

    public function showMesAction()
    {

         arSeg(array(
                        'loader' => array(
                            'plugin' => 'bdeditor',
                            'this' => $this
                        )
                    )

          );

         if(arPost()) {
            $s_remark = arRequest('type');
            $s_scontent = arRequest('content');
            $count = SettingModel::model()->getDb()->where(array('cuid'=>0,'s_remark'=>$s_remark))->count();
            if($count){
               $update = SettingModel::model()->getDb()
                   ->where(array('cuid'=>0,'s_remark'=>$s_remark))
                   ->update(array('s_scontent'=>$s_scontent),true);
                $query = SettingModel::model()->getDb()->where(array('cuid'=>0,'s_remark'=>$s_remark))->queryRow();
                if($update){
                    $this->redirectSuccess(array('showMes',array('s_sid'=>$query['s_sid'])),'操作成功');
                }else{
                    $this->redirectError(array('showMes'),'操作失败');
                }
            }else{

                $data['s_remark'] = arRequest('type');
                $data['cuid'] = 0;
                $data['s_scontent'] = arRequest('content');

                $update = SettingModel::model()->getDb()->insert($data,true);
                if($update){
                    $this->redirectSuccess(array('showMes',array('s_sid'=>$update)),'操作成功');
                }else{
                    $this->redirectError(array('showMes'),'操作失败');
                }
            }
        }else{
             if(arRequest('type'))
            {
              $s_remark = arRequest('type');
              $getRow = SettingModel::model()->getDb()->where(array('cuid'=>0,'s_remark'=>$s_remark))->queryRow();
              $this->assign(array('getRow' => $getRow));

              $this->display();
            }else{
               $getRow = SettingModel::model()->getDb()->where(array('s_sid'=>arRequest('s_sid')))->queryRow();
               $this->assign(array('getRow' => $getRow));
               $this->display();
            }
        }

    }
}
?>