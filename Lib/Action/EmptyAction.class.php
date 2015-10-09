<?php

class EmptyAction extends Action{
    
    public function  _empty(){
        header("HTTP/1.0  404  Not Found"); 
        $this->assign('site_root',C('SITE_ROOT'));
		$this->assign('resource_path',C('RESOURCE_PATH'));
        $this->display('Public:404');
    }

}