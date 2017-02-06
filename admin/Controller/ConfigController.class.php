<?php
class ConfigController extends BaseController
{
    public $configIniFile; 

    // 初始
    public function init()
    {
        parent::init();
        $this->configIniFile = AR_CONFIG_PATH . 'public.config.ini';

    }

    // display
    public function getAction() 
    {
        $data = file_get_contents($this->configIniFile);
        $this->assign(array('data' => $data));
        $this->display();

    }

    // 设置
    public function setAction()
    {
        if ($data = arPost('data')) :
            file_put_contents($this->configIniFile, $data);
            $this->redirectSuccess('get');
        endif;
        $this->redirect('get');

    }

}
