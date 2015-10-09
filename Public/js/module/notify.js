var editor;
$(function(){
     $("#breadcrumb").append("<a href=\"javascript:void(0);\" class=\"current\">信息详情</a> ");
     $("#sidebar > ul > li:eq(7)").addClass('active');
    //  var styleArr = ['','label-success','label-warning','label-important','label-info','label-inverse'];

    $("input").blur(function(){ _error($(this));});
    $("select").change(function(){ _error($(this));});

    $("#scanbtn").click(function(){
        var html = editor.html();
        easyDialog.open({container :{header : '预览效果',content : html}})
    });

    $("#confirm").click(function(){
        if(parseInt(validateForm()) > 0){
            return;
        }
        var params = {
            "title"   : $.trim($("#title").val()),
            "type"    : $.trim($("#type").val()),
            "area"    : $.trim($("#area").val()),
            "contact" : $.trim($("#contact").val()),
            "phone"   : $("#phone").val(),
            "pic"     : $("#pic").val(),
            "content" : editor.html(),
            "nid" : $("#nid").val()
        }
        $.post(site_root+encodeURIComponent("Program/doNotifyPub"),params,function(){
              showMsgbox("操作成功",'ok');
              window.location = site_root+"Index/notify";
        })

    })
    KindEditor.ready(function(K){
        var options = {
            items : ['bold','italic', 'underline', 'strikethrough', 'removeformat', '|',
                'selectall', 'fontname','fontsize', 'forecolor','|',
                'image','hr','source', '|',
                'justifyleft', 'justifycenter', 'justifyright','justifyfull', '|',
                'undo', 'redo', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|',],
            width : '100%',
            height: '590px',
            allowFileManager: true,
            uploadJson : site_root+'File/uploadNotify'
        }
        editor = K.create('#editor', options);
    });
})

var _count;
function validateForm(){
    _count = 0;
    $(".controls").each(function(){
        var obj = null;
        if($(this).find("input").length > 0){
            obj = $(this).find("input");
        }else{
            obj = $(this).find("select");
        }
        _error(obj);
        obj = null;
    });
    formatValidate();
    return _count;
}

function _error(obj){
    if($.trim(obj.val()) !== "" || $.trim(obj.val())){
        obj.siblings('.help-inline').remove();
        obj.parents('.control-group').removeClass('error');
    }else{
        _addError(obj,"此项不能为空");
        _count++;
    }
}


function formatValidate(){
    //格式检验：标题30字以内，手机号码格式
    var title = $.trim($("#title").val());
    if(title.length > 30){
        _addError($("#title"),"标题长度在30字以内");
        _count++;
    };
    var tel_pattern =  /^1[3|4|5|7|8]\d{9}$/;
    var phone = $.trim($("#phone").val());
    if(!tel_pattern.test(phone)){
        _addError($("#phone"),"手机号码格式不正确");
        _count++;
    }
}

function _addError(obj,msg){
    if(obj.siblings(".help-inline").length > 0){
        obj.siblings(".help-inline").html(msg);
    }else{
        obj.after('<span class="help-inline">'+msg+'</span>');
    }
    obj.parents('.control-group').addClass('error');
}
$("#upload_picture").uploadify({
    "swf"             : resource_path+"uploadify/uploadify.swf",
    "fileObjName"     : "download",
    "buttonText"      : "上传图片",
    "uploader"        : site_root+"File/uploadNotifyMain/nid/"+$("#nid").val(),
    "width"           : 240,
    "height"          : 180,
    'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
    "onUploadSuccess" : uploadPicture
});
function uploadPicture(file,data){
    var data = JSON.parse(data);
    if(data.error){
        showMsgbox("上传失败","no");
    }else if(data.status){
        $(".upload-pre-item2 img").attr("src",data.imgPath);
        $("#pic").val(data.imgPath);
        $(".upload-img-box").show();
        $(".lightbox_trigger").attr('href',data.imgPath);
    }else{
        showMsgbox(data,"no");
    }
}