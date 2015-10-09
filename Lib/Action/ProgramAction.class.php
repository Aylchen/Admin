<?php
class ProgramAction extends Action{
	public function doArticlePub(){
        $aid     = I("post.aid");
        $content = str_replace('src="/tc_admin/', 'src="'.C("DIR_ROOT"),  $_POST['content']);
        $arr['content']  = htmlspecialchars($content);
        mysql_query_save("`id`='".$aid."'",$arr,"article");
    }

    public function doAccessIv(){
        $uid = I("post.uid");
        $arr['status'] = 2;
        $r = mysql_query_save("`user_id`='".$uid."'",$arr,"identity_verification");
        $this->ajaxReturn($r);

    }

    public function doNoAccessIv(){
        $uid = I("post.uid");
        $arr['status'] = 3;
        $arr['iv_failed'] = I("post.reason");
        $r = mysql_query_save("`user_id`='".$uid."'",$arr,"identity_verification");
        $this->ajaxReturn($r);
    }

    public function doNotifyPub(){
        $content = htmlspecialchars(str_replace('src="/tc_admin/', 'src="'.C("DIR_ROOT"),  $_POST['content']));
        $arr  = array(
            'type' => stripslashes(I("post.type")),
            'area' => stripslashes(I("post.area")),
            'title'=> stripslashes(I("post.title")),
            'content'=>$content,
            'contact'=>stripslashes(I("post.contact")),
            'phone' => I("post.phone"),
            'pic' => I("post.pic"),
            'ctime' => date("Y-m-d H:i:s",time())
        );
        if(!I("post.nid")){
            $arr['id'] = substr(md5(time()),0,8);
            mysql_query_add($arr,'notify');
        }else{
            mysql_query_save("`id`='".I("post.nid")."'",$arr,"notify");
        }
        $this->ajaxReturn(true);
    }
}
