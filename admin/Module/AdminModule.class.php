<?php
class AdminModule
{
    // 检测是否登陆
    public function checkIfLogin()
    {
        return !!arComp('list.session')->get('admin');

    }

}
