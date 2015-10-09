
function checkLogin(o){
	var username = $("input[name='username']").val();
	var pwd = $("input[name='pwd']").val();
	if(username==""){
		$("input[name='username']").attr("placeholder","请输入用户名").focus();
		return false;
	}
	if(pwd==""){
		$("input[name='pwd']").attr("placeholder","请输入密码").focus();
		return false;
	}
	var params = {"username":username,"pwd":pwd};
	$(o).attr("disabled",true);
	$.post(site_root+encodeURIComponent("Member/dologin"),params,function(r){
		if(r){location=site_root+"Index/index";}else{alert("用户名密码不匹配");$(o).attr("disabled",false);}
	})
	
}