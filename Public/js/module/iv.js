function accessIv(uid){
    cancleModal();
    $.post(site_root+encodeURIComponent("Program/doAccessIv"),{"uid":uid},function(r){
        if(r){
            showMsgbox("操作成功","ok");
            window.location= location.href;
        }else{
            showMsgbox("操作失败");
        }
    })
}

function noAccess(){
    $("#reason_modal").removeClass("hide");
}

function noAccessIv(uid){
    if(!$("#reason").val()){
        $("#reason").attr("placeholder","请填写原因").focus();
        return;
    }
    $.post(site_root+encodeURIComponent("Program/doNoAccessIv"),{"uid":uid,"reason":$("#reason").val()},function(r){
        if(r){
            showMsgbox("操作成功","ok");
            window.location= location.href;
        }else{
            showMsgbox("操作失败");
        }
    })

}
function cancleModal(){
    $("#reason_modal").addClass("hide");
}
$(function(){
    $("#breadcrumb").append("<a href=\"javascript:void(0);\" class=\"current\">实名认证资料</a>");
    $("#sidebar > ul > li:eq(5)").addClass('active');

    $(".dropdown-menu li").click(function(){
        var obj = $(this);
        window.location = site_root+"Index/identity/search/"+obj.index();
    });




})
