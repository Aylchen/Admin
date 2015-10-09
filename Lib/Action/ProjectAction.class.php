<?php
class ProjectAction extends Action{
	public function checkAccessProject(){
        $project_id = I("post.project_id");
        $tag = I("post.tag");
        $arr['status']  = ($tag==1) ? 2 : -1;
        $r = mysql_query_save("`project_id`='" . $project_id . "'", $arr, "project_requirement");
        $this->ajaxReturn($r);
    }

	public function completeTrade(){
		$trade_id = I("post.trade_id");
		$i        = I("post.i");
		$where    = "`id`='".$trade_id."'";
		switch($i){
			case 1:
				$arr['status'] = 1;
				mysql_query_save($where,$arr,"account");
				break;
			case 2:
				mysql_query_del($where, "account");
				break;
		}
		
	}
	
	public function delProject(){
		$project_id = I("post.project_id");
		$where = "`project_id`='".$project_id."'";
		$r = mysql_query_del($where,"project");
		mysql_query_del($where,"project_cycle");
		$this->ajaxReturn($r);
	}
	
	public function getUserLists(){
		$tb  = "tczc_user";
		$sql = "SELECT $tb.user_id,$tb.nickname, $tb.headimgurl,union_id,tel FROM $tb";
		$lists = M()->query($sql);
		$this->ajaxReturn($lists);
	}

    public function getUserOne(){
        $tb  = "tczc_user";
        $key  = trim(I("post.key"));
        $where = "WHERE `union_id` like '%".$key."%' OR `nickname` like '%".$key."%' or `tel` like '%".$key."%'";
        $sql = "SELECT $tb.user_id,$tb.nickname, $tb.headimgurl,union_id,tel FROM $tb ".$where;
        $lists = M()->query($sql);
        $this->ajaxReturn($lists);
    }
	
	public function addAccountRecord(){
		$user_id = I("post.user_id");
		$amount  = I("post.amount");
		$id = random();
		//添加记录
		$recodeArr = array(
			"user_id"=>$user_id,"amount"=>$amount,"remark"=>I("post.remark"),
			"trade_type"=>1,"channel"=>2,"status"=>1,"id"=>$id
		);
		$r1 = mysql_query_add($recodeArr, "account");
		//更改余额
		if($r1){
			$sql = " UPDATE tczc_user SET balance = balance+".$amount." WHERE `user_id`='".$user_id."' ";
			$r2 = M()->query($sql);
			$this->ajaxReturn(true);
		}else $this->ajaxReturn (false);
	}
}
