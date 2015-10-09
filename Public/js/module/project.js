/**
 * Created by 10281_000 on 2015/7/15.
 */
function checkAccess(i){
    var project_id = $("#project_id").val();
    var params = {
        "project_id" : project_id,
        "tag"   : i
    };
    $.post(site_root+encodeURIComponent("Project/checkAccessProject"),params,function(r){
        if(r){
            showMsgbox("操作成功","ok");
            location= site_root+"Index/publish/project_id/"+project_id;
        }else{
            showMsgbox("操作失败");
        }
    })
}
/**
 * 删除项目
 */
function delProject(project_id){
	if(confirm("确定要删除该项目?")){
			$.post(site_root+encodeURIComponent("Project/delProject"),{"project_id":project_id},
			function(r){
				if(r){showMsgbox("删除成功","ok");location=site_root+"Index/index";}
				else{showMsgbox("删除失败");}
			})
	}

}
/**
 * 完成线下交易
 * @param {Object} trade_id
 */
function completeTrade(trade_id,i){
	var params = {"trade_id":trade_id,"i":i};
	if(i==2){
		if(confirm("确定删除该记录？")){ajaxCall(params);}
	}else ajaxCall(params);
}

function ajaxCall(params){
	$.post(site_root+encodeURIComponent("Project/completeTrade"),params,
	function(){location = location.href;   })//
}
/**
 * 线下转账添加记录
 */
function addAccountRecord(self){
	if(!$("#user_id").val()){
		showMsgbox("请选择用户");return;
	}
	if(!$("#amount").val()){
		showMsgbox("请输入金额");return;
	}
	var params = {
		"user_id" : $("#user_id").val(),
		"amount"  : $("#amount").val(),
		"remark"  : $("#remark").val()
	};
	$(self).attr("disabled",true);
	$.post(site_root+encodeURIComponent("Project/addAccountRecord"),params,function(rst){
		if(rst){
			showMsgbox("充值记录添加成功","ok");
			setTimeout(function(){location = site_root+"Index/trade_log"},1000);
		}else{
			$(self).attr("disabled",false);
			showMsgbox("充值记录添加失败，请重试");
		}
	})
}

$(function(){
    $('.lightbox_trigger').click(function(e) {
        e.preventDefault();
        var image_href = $(this).attr("href");
        if ($('#lightbox').length > 0) {
            $('#imgbox').html('<img src="' + image_href + '" /><p><i class="icon-remove icon-white"></i></p>');
            $('#lightbox').slideDown(500);
        }
        else {
            var lightbox =
                '<div id="lightbox" style="display:none;">' +
                '<div id="imgbox"><img src="' + image_href +'" style="max-height:100%;margin-top:0;"/>' +
                '<p><i class="icon-remove icon-white"></i></p>' +
                '</div>' +
                '</div>';
            $('body').append(lightbox);
            $('#lightbox').slideDown(500);
        }
    });
    $('#lightbox').live('click', function() {
        $('#lightbox').hide(200);
    });
})