<?php
/**
 * Created by Aylchen
 * Date: 2015/7/16
 * Time: 14:33
 */

class MemberAction extends  Action{
	public function _initialize(){
		$this->assign("site_root",C("SITE_ROOT"));
        $this->assign("resource_path", C("RESOURCE_PATH"));
	}
	
	public function  _empty(){
        $this->display('Public:404');
    }
	public function index(){
		$r_url = C("SITE_ROOT")."Member/login";
		header("Location:$r_url");
	}
	
	public function login(){
		if(session("user_id")){
			$index = C("SITE_ROOT")."Index/index";
			header("Location:$index");
		}
		$this->display();
	}
	
	public function dologin(){
		$username = I("post.username");
		$pwd      = I("post.pwd");
		if($username==="tc" && $pwd === "tc"){
			session("tczc_user",md5(username));
			$this->ajaxReturn(true);
		}else{
			$this->ajaxReturn(false);
		}
	}
	
	public function logout(){
		session("tczc_user",null);
		$logout_url = C("SITE_ROOT")."Member/login";
		header("Location:$logout_url");
	}
	
   
}