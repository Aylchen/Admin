<?php
/**
 * Created by Aylchen.
 * Date: 2015/7/14
 * Time: 9:38
 */

class IndexAction extends ParentAction {

    protected $trade_type_array = array("全部","充值","收益","投资","提现");
    protected $channel_array = array("全部","微信支付","线下转账","余额支付","其他");
    protected $status_array = array("全部","进行中","已完成");
    protected $empty = '<tr><td class="dataTables_empty" valign="top" colspan="15">没有相关数据</td></tr>';

    public function _initialize(){
        parent::_initialize();
    }
    Public function index(){
        $nowTime = date("Y-m-d H:i:s",time());
    	import("ORG.Util.Page");
		$where = $where1 = $where2 = $where3 = "";
		
		$project_name = I("post.project_key");

        $user = I("post.require_user");

		$status   = I("post.project_status");
		if(!empty($project_name)){
			$where1 = "tczc_project.title like '%".$project_name."%'";
			$this->assign("project_name",$project_name);
		}
		if(!empty($user)){

            $this->assign("require_user",$user);
            $user_id = I("post.user_id");
            (!empty($user_id)) && $this->assign("user_id",$user_id);
            $where2  = empty($user_id) ? "" : "tczc_project.user_id ='".$user_id."'";
		}
		if(!empty($status)){
			$where3 = "tczc_project.status = ".$status;
			$this->assign("project_status",$status);
		}
		$where = getComplicatedWhere(array($where1,$where2,$where3));
		
        $join1 = "tczc_project_requirement on tczc_project.project_requirement_id = tczc_project_requirement.project_id";
        $join2 = "tczc_user on tczc_user.user_id = tczc_project.user_id";
        $field = "tczc_project.*,tczc_user.nickname";
		
		$count = M("project")->where($where)->count();
        $Page  = new Page($count,10);
        $show  = $Page->show();
        $projects =  M("project")->join($join1)->join($join2)->where($where)->
        			field($field)->order("tczc_project.ctime desc")->limit($Page->firstRow.",".$Page->listRows)->select();
        $this->assign("platform_id",md5(C("PLATFORM_ID")));
        $this->assign("projects",$projects);
		$this->assign("page",$show);
        $this->assign("nowTime",$nowTime);
        $this->assign("empty",$this->empty);
        $this->breadCrumb("众筹项目管理");
        $this->display();
    }

    public function check(){
        $this->breadCrumb("项目审核");
        $where = $where1 = $where2 = "";
    //    $projects = mysql_query_all("","ctime desc","project_requirement");
        /**
         * 用户条件搜索
         */
        $user = I("post.require_user");
        (!empty($user)) && $this->assign("require_user",$user);
        $user_id = I("post.user_id");
        (!empty($user_id)) && $this->assign("user_id",$user_id);
        $where1  = empty($user_id) ? "" : "`user_id`='".$user_id."'";

        /**
         * 项目状态搜索
         */
        $status = I("post.require_status");
        (!empty($status)) && $this->assign("require_status",$status);
        $where2 = empty($status) ? "" :  "`status` = ".$status;


        $where = getComplicatedWhere(array($where1,$where2));
        import("ORG.Util.Page");
        $count = M("project_requirement")->where($where)->count();
        $Page  = new Page($count,10);
        $show  = $Page->show();

        $projects = M("project_requirement")->where($where)->order("ctime desc")->limit($Page->firstRow.",".$Page->listRows)->select();

        $projects_new = array();
        foreach($projects as $each){
            $isPublished = "";$userInfo = array();
            $project_id = $each['project_id'];
            $isPublished = mysql_query_one("`project_requirement_id`='".$project_id."'","project");
			$userInfo    = mysql_query_one("`user_id`='".$each['user_id']."'", "user");
            $each['isPublished'] = ($isPublished == '' || $isPublished == null) ? 0 : 1;
			$each['userInfo'] = $userInfo;
            $projects_new[] = $each;
        }
        $this->assign("projects" ,$projects_new);
        $this->assign("page",$show);
        $this->assign("empty",$this->empty);
        $this->display();
    }

    public function check_detail(){
        $this->breadCrumb("项目审核");
        $project_id = I("get.project_id");
        $thisTb  = "tczc_project_requirement";
        $joinTb  = "tczc_user" ;
        $field   = "$thisTb.*,$joinTb.nickname,$joinTb.headimgurl";
        $join    = "$joinTb on $joinTb.user_id = $thisTb.user_id";
        $project_info = M("project_requirement")->join($join)->field($field)
            ->where("$thisTb.project_id='".$project_id."'")->order("$thisTb.ctime desc")->find();
        //项目是否已发布
        $isPublished = mysql_query_one("`project_requirement_id`='".$project_id."'","project");
        $this->assign("project_info",$project_info);
        $this->assign("isPublished", $isPublished);
        $this->assign('href', C("SITE_ROOT")."Index/check");
        $this->display();
    }

    public function detail(){
        $this->breadCrumb("众筹项目管理");
        $this->assign('href', C("SITE_ROOT")."Index/index");
        $project_id = I("get.project_id");
        $where      = "`project_id`='" .$project_id."'";
        $project_info = mysql_query_one($where,"project");//项目信息
        $diffDays = $this->getRemainDays($project_info['ctime']);
        $cycle_info   = mysql_query_all($where,"","project_cycle");//收益类型
        /**
         * 获取投资人信息：tczc_user / tczc_project_invest / tczc_project_cycle
         */
        $thisTb  = "tczc_project_invest";
        $joinTb1 = "tczc_user" ;
        $joinTb2 = "tczc_project_cycle";
        $field = "$thisTb.*,$joinTb1.nickname,$joinTb1.headimgurl,$joinTb2.cycle_type,$joinTb2.cycle,$joinTb2.profit";
        $join1 = "$joinTb1 on $joinTb1.user_id = $thisTb.user_id";
        $join2 = "$joinTb2 on $joinTb2.cycle_id = $thisTb.cycle_id";
        $invests = M("project_invest")->join($join1)->join($join2)->field($field)->
                    where("$thisTb.project_id='".$project_id."'")->order("$thisTb.ctime desc")->select();//投资人
        $this->assign('project_info', $project_info);
        $this->assign("remain",$project_info['need_days']-$diffDays);
        $this->assign("cycle_info", $cycle_info);
        $this->assign("invests", $invests);
        $this->display();
    }

    private function getRemainDays($s_day){


        $Date_1=date("Y-m-d");
        $Date_2= substr($s_day,0,10);
        $d1=strtotime($Date_1);
        $d2=strtotime($Date_2);
        $Days=round(($d1-$d2)/3600/24);
        return $Days;
    }

    public function publish(){
        $project_id = I("get.project_id");
        $type       = I("get.type");
        if($type==1){//已发布项目进行修改(type标识)
            $project_info = mysql_query_one("`project_id`='".$project_id."'","project");
            $cycles = mysql_query_all("`project_id`='".$project_info['project_id']."'","","project_cycle");
            $this->assign("s_time",substr($project_info['ctime'],0,10));
            $this->assign('cycles',$cycles);
            $this->assign("type",$type);
            $this->breadCrumb("修改项目");
        }else{//平台直接发起及通过前台用户审核成功后发起(user_id标识)
            $project_info = mysql_query_one("`project_id`='".$project_id."'","project_requirement");
            $this->breadCrumb("发起项目");
        }
        $this->assign("n_time",date("Y-m-d",time()));
        $this->assign("project_info", $project_info);

       /* $this->assign("nowTime",date("Y-m-d",time()));*/
        $this->display();
    }

    public function getProfitType(){
        $type = I("post.type");
        $this->display("Index:publish/profit_type".$type);
    }


    public function muser(){
      //  $users = mysql_query_all("", "ctime desc", "user");
        $key = I("get.user_key");
        $user_id = I("get.user_id");
        $where1 = empty($key) ? "" : "`union_id` like '%".$key."%' OR `nickname` like '%".$key."%' OR `tel` like '%".$key."%'";
        $where = empty($user_id) ? $where1 : "`user_id`='".$user_id."'";
        $this->assign("user_key",$key);
        $lists = mysql_query_page(10,$where,"ctime desc","user");
        $this->breadCrumb("用户管理");
        $this->assign("users",$lists[0]);
        $this->assign("page",$lists[1]);
        $this->assign("empty",$this->empty);
        $this->display();
    }

    public function bank_card(){
        $this->breadCrumb("用户管理");
        $user_id = I("get.user_id");
        $bankcards = mysql_query_all("`user_id`='" . $user_id . "'", "", "bankcard");
        $empty  = "<tr><td colspan='6'>暂无银行卡信息</td></tr>";
        $this->assign('href', C("SITE_ROOT")."Index/muser");
        $this->assign("bankcards",$bankcards);
        $this->assign("empty",$empty);
        $this->display();
    }

    /**
     * 未与它表进行关联
     */

    public function trade_log(){
        $this->breadCrumb("用户交易记录");
        $thisTb  = "tczc_account";
        $joinTb  = "tczc_user";
        $joinTb2 = "tczc_project";
        //search条件.
        $w = $where = "";
        if(I("get.search")){
            $search_params = explode('-',I("get.search"));
            $where1 = $where2 = $where3 = "";
            if(intval($search_params[0] > 0 ))
                $where1  = "tczc_account.trade_type = ".(intval($search_params[0]));
            if(intval($search_params[1] > 0 ))
                $where2  = "tczc_account.channel = ".(intval($search_params[1]));
            if(intval($search_params[2] > 0 ))
                $where3  = "tczc_account.status = ".(intval($search_params[2])-1);
            if(!empty($search_params[3])){
                $where4  = "tczc_account.user_id = '".$search_params[3]."'";
                $one = mysql_query_one("`user_id`='".$search_params[3]."'","user");
                $this->assign("one",$one);
            }

            $w = getComplicatedWhere(array($where1,$where2,$where3,$where4));
            $this->assign("type",$search_params[0]);
            $this->assign("channel",$search_params[1]);
            $this->assign("status",$search_params[2]);
            $this->assign("type_array",$this->trade_type_array);
            $this->assign("channel_array",$this->channel_array);
            $this->assign("status_array",$this->status_array);
        }

        $where   = empty($w) ? $where : (empty($where) ? $w : $where. " and ".$w);
        $field   = "$thisTb.*,$joinTb.nickname,$joinTb.headimgurl,$joinTb2.title ";
        $join1   = "$joinTb ON $joinTb.user_id = $thisTb.user_id";
        $join2   = "$joinTb2 ON $joinTb2.project_id = $thisTb.relation_id ";
       // $records = mysql_query_all("", "ctime desc", "account");
        /*$sql     = " SELECT $thisTb.*,$joinTb.nickname,$joinTb.headimgurl,$joinTb2.title FROM $thisTb
                     LEFT JOIN $joinTb ON $joinTb.user_id = $thisTb.user_id
                     LEFT JOIN $joinTb2 ON $joinTb2.project_id = $thisTb.relation_id ".$where."
                     ORDER BY $thisTb.ctime DESC ";
        $records = M()->query($sql);*/

        import('ORG.Util.Page');// 导入分页类
        $model = M("account");
        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $show  = $Page->show();
        $list = $model->field($field)->where($where)->join($join1)->join($join2)->
        order("$thisTb.ctime DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign("records",$list);
        $this->assign("page",$show);
        $this->assign("empty",$this->empty);
        $this->display();
    }

    //三大栏目
    protected $item_type = array(1=>'关于我们',2=>'使用帮助',3=>'安全保障');

    public function article(){

        $items = mysql_query_all("","","article");
        foreach($items as $item){
            $c = htmlspecialchars_decode($item['content']);
            $item['simply_c'] = mb_substr(trim(strip_tags(html_entity_decode($c))), 0, 100, 'utf-8');
            $arr[$item["article_type"]][] = $item;
        }
        $this->assign("items",$arr);
        $this->breadCrumb("栏目管理");
        $this->assign("item_type",$this->item_type);
        $this->display();
    }

    public function article_detail(){
        $aid = I("get.article_id","");
        if($aid==""){header("Location:" . C("SITE_ROOT") . "Index/article");exit();}
        $article = mysql_query_one("`id`='".$aid."'","article");
        if($article==""){header("Location:" . C("SITE_ROOT") . "Index/article");exit();}
        $article['new_content'] = htmlspecialchars_decode($article['content']);
        $this->assign('article',$article);
        $this->breadCrumb("栏目管理");
        $this->assign('href', C("SITE_ROOT")."Index/article");
        $this->display();
    }

    public function identity(){
        $status = array("全部","待审核","审核已通过","审核未通过");
        $where = "";


        $search = I("get.search",0);
        if($search>0){
            $where = "tczc_identity_verification.status=".$search;
        }



        import('ORG.Util.Page');
        $model = M("identity_verification");
        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $show  = $Page->show();
        $field = "tczc_user.headimgurl,tczc_user.nickname,tczc_identity_verification.*";
        $join  = "tczc_user on tczc_user.user_id = tczc_identity_verification.user_id";
        $list = $model->field($field)->where($where)->join($join)->
                    order("tczc_identity_verification.ctime DESC")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign("list",$list);
        $this->assign("page",$show);
        $this->assign("empty",$this->empty);
        $this->breadCrumb("实名认证");
        $this->assign("statusArr",$status);
        $this->assign("search",$search);
        $this->display();
    }

    public function identity_detail(){
        $iv_id = I("get.iv_id");
        $where = "tczc_identity_verification.user_id='".$iv_id."'";
        $field = "tczc_user.headimgurl,tczc_user.real_name,tczc_user.id_no as idno,tczc_user.tel,tczc_identity_verification.*";
        $join  = "tczc_user on tczc_user.user_id = tczc_identity_verification.user_id";
        $iv = M("identity_verification")->field($field)->join($join)->where($where)->find();
        $this->assign("iv",$iv);
        $this->assign('href', C("SITE_ROOT")."Index/identity");
        $this->breadCrumb("实名认证");
        $this->display();
    }





    /**
     * @param max_cycle_month 收益周期中最大的月份
     */
    public function doPublish(){

        $project_array = $_POST;
        $type = $_POST['project_id'];//$type为空表示添加操作，$type不为空表示修改
        $project_id    = ($type != '') ? $type : random();
        $content = str_replace('src="/tczc_admin/', 'src="'.C("DIR_ROOT"), $_POST['content']);
        unset($project_array['project_id']);
        unset($project_array['content']);
        //收益类型
        $cycle_total = intval($_POST['cycle_number']);
        unset($project_array['cycle_number']);
        if($type!= ''){
            //清空所有收益类型，再进行添加操作----最简便的方式
            mysql_query_del("`project_id`='".$project_id."'","project_cycle");
        }

        $cycle_month_array = array();
        for($i=1;$i<($cycle_total+1);$i++){
            $cycle_id = random();
            $cycle_array = array();
            $cycle_month_array[] = ($_POST['cycle_type'.$i] == 2) ? $_POST['cycle'.$i] : $_POST['cycle'.$i]/30;

            $cycle_array = array(
                "cycle_id"=>$cycle_id,"project_id"=>$project_id,"cycle_type"=>$_POST['cycle_type'.$i],
                "profit"=>$_POST['profit'.$i],'cycle'=>$_POST['cycle'.$i]
            );
            mysql_query_add($cycle_array,"project_cycle");
            unset($project_array['cycle_type'.$i]);
            unset($project_array['cycle'.$i]);
            unset($project_array['profit'.$i]);
        }
        $max_cycle_month = max($cycle_month_array);


        if(!$_POST['user_id']){//平台直接发起项目
            $project_array['user_id'] = md5(C("PLATFORM_ID"));
        }
        $project_array['content'] = htmlspecialchars($content);
        $project_array['status'] = 3;//发布
        $project_array['collect_endtime'].=" 23:59:59";
        $endtime_stamp = strtotime("+".$max_cycle_month." months",strtotime($project_array['collect_endtime']));
        $project_array['endtime'] = date("Y-m-d H:i:s",$endtime_stamp);
        if($project_array['profit_type']==2){$project_array['endtime'] = $project_array['collect_endtime'];}
        if($type != ""){
            $rst  = mysql_query_save("`project_id`='".$project_id."'",$project_array,"project");
        }else{
            $project_array['project_id'] = $project_id;
            $project_array['ctime'] = date("Y-m-d H:i:s",time());
            $rst  =  mysql_query_add($project_array, "project");
        }

        $this->ajaxReturn(true);
    }

    public function notify()
    {
        $lists = mysql_query_page(10,"","ctime desc","notify");
        foreach($lists[0] as $item){
            $c = htmlspecialchars_decode($item['content']);
            $item['simply_c'] = mb_substr(trim(strip_tags(html_entity_decode($c))), 0, 100, 'utf-8');
            $l[] = $item;
        }
        $this->breadCrumb("信息列表");
        $this->assign("lists",$l);
        $this->assign("page",$lists[1]);
        $this->assign("empty",$this->empty);
        $this->display();
    }

    public function notify_edit(){
        $areas  = array("市辖区","汉台区","南郑县","城固县","洋县","西乡县","勉县","宁强县","略阳县","镇巴县","留坝县","佛坪县");
        $types = array("招聘","租房","二手房","二手车","本地服务");


        $id = I("get.nid");
        if($id){
            $notify_info = mysql_query_one("`id`='".$id."'",'notify');
            $this->assign("info",$notify_info);
        }

        $this->assign("areas",$areas);
        $this->assign("types",$types);
        $this->breadCrumb("信息列表");
        $this->assign('href', C("SITE_ROOT")."Index/notify");
        $this->display();
    }

    public function delNotif(){
        $nid = I("post.nid");
        $ninfo = M()->query(" SELECT * FROM tczc_notify WHERE `id`='".$nid."' LIMIT 1");
        $n = str_replace(C('DIR_ROOT'), '', $ninfo[0]['pic']);
        if(file_exists($n)){ @unlink($n); }
        $this->_g($ninfo[0]['content']);
        M()->query("DELETE FROM tczc_notify WHERE `id`='".$nid."'");
        $this->ajaxReturn(true);
    }

    /**
     * @param $n：删除content中的图片
     */
    private function _g($n){
        preg_match_all('/<img src="(.*?)"\s/',htmlspecialchars_decode($n),$matches);
        foreach($matches[1] as $v){
            $dir = str_replace(C('DIR_ROOT'), '', $v);
            if(file_exists($dir)){ @unlink($dir); }
        }
    }
}