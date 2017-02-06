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
class CaicuController extends BaseController
{
    // 获取返回数据
    public function receiveAction()
    {
        arComp('list.log')->record(arRequest(), 'ccrec');
        if ($testemail = arRequest('test_email')) :
            $serial = array(
                'reportid' => arRequest('reportid'),
                'hrid' => arRequest('hr_id'),
            );
            $upStatus = UserSerialsModel::model()
                ->getDb()
                ->where(array('testemail' => $testemail))
                ->update($serial);
            if ($upStatus) :
                // if ($mobile) {

                // } else {
                    $this->redirectSuccess('User/zyTestResult', '测试完成');
            //     }
                
            // else :
                $this->redirectError('index/index', '测试出错');
            endif;
        endif;

    }
   
}
