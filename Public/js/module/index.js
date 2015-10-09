$(function(){
		$("select").select2();
        $(".bar").each(function(){
            var width = $(this).parent(".progress").prev(".progress-label").html();
            var thisWidth = parseFloat(width);
            var parentNode = $(this).parent(".progress");
            if( thisWidth < 30){
                parentNode.addClass("progress-danger");
            }else if (thisWidth < 50){
                parentNode.addClass("progress-warning");
            }else if(thisWidth > 80){
                parentNode.addClass("progress-success");
            }
            $(this).animate({'width':width},3000);
        });
        
        $("#btn_search_one").bind("click",function(){
        	$("form").submit();
        });
        $("#pub_user").bind("click",function(){
        	$("form").submit();
        });
        $("#project_status").bind("change",function(){
        	$("form").submit();
        });
        
        
        $("#btn_search_user").click(function(){
            userLists = "";
            var obj = $(this);
            var val = $("#require_user").val();
            if(!val) {
                $("#require_user").val('');
                $("#user_id").val('');
                $("form").submit();return;
            }
            getSearchResult(val,obj);
        });

        $("#require_status").change(function(){
            $("form").submit();
        });
        
        
        
        
})
userLists = "";

function getSearchResult(val,obj){
    $(".sel-one").remove();
    $.post(site_root+encodeURIComponent("Project/getUserOne"),{"key":val},function(lists){

        userLists = lists;
        var liHtml = "";
        liHtml+='<div class="sel-one"><ul class="select2-results">';
        if(lists==""){
            liHtml+="<li class=\"select2-results-dept-0 select2-result select2-result-selectable\">搜索结果为空</li>";
        }else{
            for(var i= 0,len=lists.length;i<len;i++){
                liHtml+="<li class=\"select2-results-dept-0 select2-result select2-result-selectable\" onclick=\"selThisOne("+i+");\">";
                liHtml+='<div class="select2-result-label"><span class="select2-match"></span>';
                var tel = (lists[i]['tel'] == null || lists[i]['tel'] == "" || !lists[i]['tel']) ? "" : lists[i]['tel'];
                liHtml+= '<img src="'+lists[i]['headimgurl']+'" width="40" height="40"/>'+"&nbsp;&nbsp;"+lists[i]['nickname']+"&nbsp;&nbsp;"+tel;
                liHtml+='</div></li>';
            }
        }

        liHtml+='</ul></div>';
        obj.after(liHtml);
    })
}

function selThisOne(i){
    var innerHtl = userLists[i]['nickname'];
    $("#require_user").val(innerHtl);
    $(".sel-one").remove();
    $("#user_id").val(userLists[i]['user_id']);
    $("form").submit();
}