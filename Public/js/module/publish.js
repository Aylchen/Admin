/**
 * Created by Aylchen on 2015/7/15.
 */
function addCycle(s){
    var obj = $(s).parent(".controls");
    var total = $("#cycle_number").val()*1;
    var html = obj.html(),appendHtml = "";
    var parentNode = $("#profit_type_ht");
    var ahtml = '<a class="sAdd btn btn-primary" href="javascript:void(0);" onclick="minCycle(this);"><i class="icon-minus"></i></a>';
    appendHtml = (obj.find('.sAdd').length == 1) ? html+ahtml : html;
    parentNode.append("<div class=\"controls\">"+appendHtml+"</div>");
    $("#cycle_number").val(total+1);
    refreshIndex(parentNode,total+1);
}
function minCycle(s){
    var obj   = $(s).parents(".control-group").find(".controls:last-child");
    var total = $("#cycle_number").val()*1;
    obj.fadeOut(300);
    setTimeout(function(){obj.remove();},300);
    $("#cycle_number").val(total-1);
}

function refreshIndex(parentNode,index){
    parentNode.find(".controls:last-child").find(".cycle-cycle").attr("name","cycle"+index);
    parentNode.find(".controls:last-child").find(".cycle-select").attr("name","cycle_type"+index);
    parentNode.find(".controls:last-child").find(".cycle-profit").attr("name","profit"+index);
}


/*function validateForm(){
    return validateFormType(["textarea","select","input"]);
}
function validateFormType(type){
    var flagArr = [];
    for(var i= 0,len=type.length;i<len;i++){

        $(type[i]+"[data-required='required']").each(function(){
            if($(this).val()==''){
                $(this).parents('.control-group').removeClass('success');
                $(this).parents('.control-group').addClass('error');
                flagArr.push(false);
                return true;
            }
            flagArr.push(true);
            return true;
        });
        $(type[i]).focus(function(){
            $(this).parents('.control-group').removeClass('error');
        });
    }
    var flag = _validate(flagArr);
    return flag;
}

function validateFormPattern(){
    var flagArr = [];
    var pattern = /^\d+(\.\d{2})?$/;
    $("input[type='number']").each(function(){

        if(!pattern.test($(this).val()) && $(this).data("required")==='required'){
            $(this).parents('.control-group').removeClass('success');
            $(this).parents('.control-group').addClass('error');
            flagArr.push(false);
            return true;
        }
        flagArr.push(true);
        return true;
    });
    var flag = _validate(flagArr);
    return flag;
}

function _validate(flagArr){
    var flag = true;
    for(var j= 0,len=flagArr.length;j<len;j++){
        if(flagArr[j]==false){
            flag = false;
            break;
        }else   continue;
    }
    return flag;
}*/

var _error = 0;
function pubProject(){
    _error = 0;
    _validateFormElement();
    if(_error == 0){
        $("#content_form").val(editor.html());
        $.post(site_root+encodeURIComponent("Index/doPublish"),$("#projectForm").serialize(),function(result){
            if(result){showMsgbox("操作成功","ok"); location = site_root+"Index/index";}
        })
    }
}


function _validateFormElement(){
var number_pattern = /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/;
var tel_pattern =     /^1[3|4|5|7|8]\d{9}$/;
    $('.form-horizontal').find(":input").each(function(){
        if($(this).data("required") == 'required' && $(this).val() == ""){
            _addErrorStyle($(this),'此处为必填项');
           // return false;
        }else if($(this).attr("type") == 'number' && !number_pattern.test($(this).val()) && $(this).val()!=""){
            _addErrorStyle($(this),'请输入正确的数字');
          //  return false;
        }else if($(this).attr("type") == 'tel' && !tel_pattern.test($(this).val())){
            _addErrorStyle($(this),'手机号码格式不正确');
        //    return false;
        } //else{
            return true;//continue true | break false
       // }
    })
}

function _addErrorStyle(o,msg){
    o.parents('.control-group').removeClass('success');
    o.parents('.control-group').addClass('error');
    o.parents('.control-group').find(".help-inline").remove();
    o.parents('.control-group').append('<span class="help-inline" for="number" style="margin-left:28%">'+msg+'.</span>');
    _error+=1;
}

function _removeErrorStyle(o){
    o.parents('.control-group').removeClass('error');
    o.parents('.control-group').find(".help-inline").remove();
    _error-=1;
}

function uploadPicture(file,data){
    var data = JSON.parse(data);
    if(data.error){
        showMsgbox("上传失败","no");
    }else if(data.status){
        $(".upload-pre-item2 img").attr("src",data.imgPath);
        $("#img").val(data.imgPath);
        $(".upload-img-box").show();
        $(".lightbox_trigger").attr('href',data.imgPath);
    }else{
        showMsgbox(data,"no");
    }
}
var editor;
$(function(){
    $('.datepicker').datepicker({format:"yyyy-mm-dd"});
    $("#scanbtn").click(function(){
        var html = editor.html();
        easyDialog.open({container :{header : '预览效果',content : html}})
    });
    setTimeout(function(){
    	$("#upload_picture").uploadify({
	        "swf"             : resource_path+"uploadify/uploadify.swf",
	        "fileObjName"     : "download",
	        "buttonText"      : "上传图片",
	        "uploader"        : site_root+"File/uploadImg/",
	        "width"           : 240,
	        "height"          : 180,
	        'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
	        "onUploadSuccess" : uploadPicture
	    });
	    
	    
    },1000);


    KindEditor.ready(function(K){
        var dir = ($("#project_id").val() != "") ? $("#project_id").val() : "tmp";
        var options = {
            items : ['bold','italic', 'underline', 'strikethrough', 'removeformat', '|',
                'selectall', 'fontname','fontsize', 'forecolor','|',
                'image','hr','source', '|',
                'justifyleft', 'justifycenter', 'justifyright','justifyfull', '|',
                'undo', 'redo', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|',],
            width : '100%',
            height: '590px',
            allowFileManager: true,
            uploadJson : site_root+'File/kindEditorUpload/dir/'+dir
        }
        editor = K.create('#editor', options);
    });

    /**
     * 收益类型变化：实物回报无投资门槛
     */
    $("#profit_type").change(function(){
        var profit_type = $(this).val();
        if($(this).val()==2){
            $(".invest-base").hide();
        }else{
            $(".invest-base").show();
        }
        $.post(site_root+encodeURIComponent("Index/getProfitType"),{"type":profit_type},function(html){
            $("#profit_type_ht").html(html);
        })
    })

    $(":input").focus(function(){_removeErrorStyle($(this));})
    $(":input").change(function(){_removeErrorStyle($(this));})


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
/**
 * 最后筹款日期改变，筹款天数相应改变
 */
function caculateNewDiff(){
    var s_date = $("#s_time").val();
    var o = $("input[name='collect_endtime']");
    _removeErrorStyle(o);
    var e_date = o.val();
    if(s_date>=e_date){
        alert("结束时间要晚于发布时间");
    }
    var diff   = getDiffDdays(s_date,e_date);
    $("input[name='need_days']").val(diff);
}
/*
计算两个日期之间的天数
 */
function getDiffDdays(strDateStart,strDateEnd){
    var strSeparator = "-"; //日期分隔符
    var oDate1;
    var oDate2;
    var iDays;
    oDate1= strDateStart.split(strSeparator);
    oDate2= strDateEnd.split(strSeparator);
    var strDateS = new Date(oDate1[0], oDate1[1]-1, oDate1[2]);
    var strDateE = new Date(oDate2[0], oDate2[1]-1, oDate2[2]);
    iDays = parseInt(Math.abs(strDateS - strDateE ) / 1000 / 60 / 60 /24)//把相差的毫秒数转换为天数
    return iDays ;
}