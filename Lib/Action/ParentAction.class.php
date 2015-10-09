<?php
/**
 * Created by PhpStorm.
 * User: 10281_000
 * Date: 2015/7/14
 * Time: 9:43
 */

class ParentAction extends Action{
    public function _initialize(){
    	if(session('tczc_user')==''){
			$r_url = C("SITE_ROOT")."Member/login";
			header("Location:$r_url");
		}
        /**
         *  'DIR_ROOT' => "http://192.168.2.105/pldConsumer/",
        'SITE_ROOT' => "http://192.168.2.105/pldConsumer/?s=",//"http://admin.plando.com.cn/?s="
        'RESOURCE_PATH' => "http://192.168.2.105/pldConsumer/Public/",//"http://admin.plando.com.cn/Public/",
         */

        $tczc_types = array(
            array("typeId"=>1,"typeName"=>'股权众筹'),
            array("typeId"=>2,"typeName"=>'基金产品'),
            array("typeId"=>3,"typeName"=>'公益产品'),
            array("typeId"=>4,"typeName"=>'回报众筹'),
        );
        $tczc_type_name = array(
            '1'=>'股权众筹',
            '2'=>'基金产品',
            '3'=>'公益产品',
            '4'=>'回报众筹',
        );
        $this->assign("tczc_type_name",$tczc_type_name);
        $this->assign("tczc_types",$tczc_types);
        $this->assign("dir_root",C("DIR_ROOT"));
        $this->assign("site_root",C("SITE_ROOT"));
        $this->assign("resource_path", C("RESOURCE_PATH"));
        $this->assign("href",C("SITE_ROOT")."Index/".ACTION_NAME);
        $this->assign("toCheck", $this->getToCheckNumber());
		$this->assign("trade_count",$this->getToCompleteTrade());
        $this->assign("iv_count",$this->getToHandleIv());
        $this->assign("nopic",C("NOPIC"));
    }

    private function getToCheckNumber(){
        //待审核项目数
        return mysql_query_count("`status`=1","project_requirement");
    }
	
	private function getToCompleteTrade(){
		//待处理交易
		return mysql_query_count("`status`=0","account");
	}

    private function getToHandleIv(){
        return mysql_query_count("`status`=1","identity_verification");
    }

    public function breadCrumb($title){
        $this->assign("title",$title);
    }
	
	public function  _empty(){
        $this->display('Public:404');
    }
}