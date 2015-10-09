/**
 * Created by 10281_000 on 2015/7/20.
 */
userLists = "";
$(function(){
    $("select").select2();
    /**
     * 获取用户列表，以方便搜索
     */
/*    setTimeout(function(){
        $.get(site_root+encodeURIComponent("Project/getUserLists"),null,function(lists){
            var optionsHtml = "";
            for(var i=0,len=lists.length;i<len;i++){
                optionsHtml+='<option value="'+lists[i]['user_id']+'">'+lists[i]['nickname']+'&nbsp;&nbsp;'+lists[i]['union_id']+'&nbsp;&nbsp;'+lists[i]['tel']+'</option>';
            }
            $("#user_id").append(optionsHtml);
        });
    },1000);*/

    /**
     * 复杂筛选
     */
    var arrow_down = '<span class="caret"></span>';
    $(".dropdown-menu li").click(function(){
        var pNode = $(this).parent("ul");
        pNode.prev("button").html($(this).html()+arrow_down);
        if(parseInt($(this).index()) == parseInt(pNode.next("input").val())) return;
        pNode.next("input").val($(this).index());
        var userHtl = (!$("#search_userid").val()) ? "" : '-'+$("#search_userid").val();

        location = site_root+"Index/trade_log/search/"+$("#search_type").val()+'-'+$("#search_channel").val()+'-'+$("#search_status").val()+userHtl;
    })
    /**
     * 筛选用户
     */
    $("#btn_search_one").click(function(){
        userLists = "";
        var obj = $(this);
        var one = $("#user_key").val();
        var userHtl = (!$("#search_userid").val()) ? "" : '-'+$("#search_userid").val();
        if(one==""|| one==null) location = site_root+"Index/trade_log/search/"+$("#search_type").val()+'-'+$("#search_channel").val()+'-'+$("#search_status").val()+userHtl;
        getSearchResult(one,obj,1);
    });
    $("#btn_search_two").click(function(){
        userLists = "";
        var obj = $(this);
        var val = $("#user_key2").val();
        getSearchResult(val,obj,2);
    })


})

function getSearchResult(val,obj,m){
    $(".sel-one").remove();
    $.post(site_root+encodeURIComponent("Project/getUserOne"),{"key":val},function(lists){

        userLists = lists;
        var liHtml = "";
        liHtml+='<div class="sel-one"><ul class="select2-results select-result'+m+'">';
        if(lists==""){
            liHtml+="<li class=\"select2-results-dept-0 select2-result select2-result-selectable\">搜索结果为空</li>";
        }else{
            for(var i= 0,len=lists.length;i<len;i++){
                liHtml+="<li class=\"select2-results-dept-0 select2-result select2-result-selectable\" onclick=\"selThisOne("+i+","+m+");\">";
                liHtml+='<div class="select2-result-label"><span class="select2-match"></span>';
                liHtml+= '<img src="'+lists[i]['headimgurl']+'" width="40" height="40"/>'+"&nbsp;&nbsp;"+returnNull(lists[i]['tel'])+"&nbsp;&nbsp;"+returnNull(lists[i]['union_id'])+"&nbsp;&nbsp;"+lists[i]['nickname'];
                liHtml+='</div></li>';
            }
        }

        liHtml+='</ul></div>';
        obj.after(liHtml);
    })
}

function returnNull(col){
    return (col == null || col == "" || !col) ? "" : col;
}

function selThisOne(i,j){
    var innerHtl = userLists[i]['nickname']+"  "+returnNull(userLists[i]['tel']);
    if(j==1){
        $("#user_key").val(innerHtl);
        $(".sel-one").remove();
        location = site_root+"Index/trade_log/search/"+$("#search_type").val()+'-'+$("#search_channel").val()+'-'+$("#search_status").val()+'-'+userLists[i]['user_id'];
    }
    if(j==2){
        $("#user_key2").val(innerHtl);
        $(".sel-one").remove();
        $("#user_id").val(userLists[i]['user_id']);
    }
}