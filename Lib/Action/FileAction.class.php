<?php
/**
 * Created by PhpStorm.
 * User: 10281_000
 * Date: 2015/7/16
 * Time: 14:33
 */

class FileAction extends  Action{

    /**
     * @param $path
     * @return UploadFile
     * 上传图片
     */
    private function _upCommon($path){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->uploadReplace = true;
        $upload->maxSize  = 20480000 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $this->cmkdirs($path);
        $upload->savePath =  $path;// 设置附件上传目录
        return $upload;
    }

    private function _upCommon2($path,$booleanThumb,$width=0,$height=0,$prefix=''){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->uploadReplace = true;
        $upload->maxSize  = 20480000 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $this->cmkdirs($path);
        $upload->savePath =  $path;// 设置附件上传目录
        if($booleanThumb){
            $upload->thumb = true;
            $upload->thumbMaxWidth = $width;
            $upload->thumbMaxHeight = $height;
            $upload->thumbPrefix   = $prefix;
            $upload->thumbRemoveOrigin = true; //删除原图
        }
        return $upload;
    }

    public function uploadImg(){
        $path   = 'Uploads/projects/';
		$prefix = "m_";
        $upload = $this->_upCommon($path);
		$upload->thumb = true;
        $upload->thumbMaxWidth = '640';
        $upload->thumbMaxHeight = '480';
        $upload->thumbPrefix   = $prefix;
        $upload->thumbRemoveOrigin = true; //删除原图
        if(!$upload->upload()) {// 上传错误提示错误信息
            $msg = $upload->getErrorMsg();
            $this->ajaxReturn($msg);
        }else{// 上传成功
            $msg = $upload->getUploadFileInfo();
            $npath = C('DIR_ROOT').$path.$prefix.$msg[0]['savename'];
            $msg[0]['imgPath'] = $npath;
            $msg[0]['status'] = 'success';
            $this->ajaxReturn($msg[0]);
        }
    }

    public function uploadNotifyMain(){
        $path = 'Uploads/notify/main/';
        $nid = I("get.nid");
        $prefix = "main_";
        $upload = $this->_upCommon2($path,true,640,480,$prefix);
        if(!$upload->upload()) {// 上传错误提示错误信息
            $msg = $upload->getErrorMsg();
            $this->ajaxReturn($msg);
        }else{// 上传成功
            $msg = $upload->getUploadFileInfo();
            $npath = C('DIR_ROOT').$path.$prefix.$msg[0]['savename'];
            if($nid){
                $ninfo = M()->query(" SELECT * FROM tczc_notify WHERE `id`='".$nid."' LIMIT 1");
                $sql = "UPDATE tczc_notify SET `pic`='".$npath."' WHERE `id`='".$nid."'";
                M()->query($sql);
                $n = str_replace(C('DIR_ROOT'), '', $ninfo[0]['pic']);
                if(file_exists($n)){
                    @unlink($n);
                }
            }
            $msg[0]['imgPath'] = $npath;
            $msg[0]['status'] = 'success';
            $this->ajaxReturn($msg[0]);
        }
    }
    public function kindEditorUpload(){
        header('Content-type: text/html; charset=UTF-8');
        $dir     = I('get.dir');
        $path   = './Uploads/details/'.$dir.'/';
        $upload = $this->_upCommon($path);
        $upload->thumb = true;
        $upload->thumbMaxWidth = '640';
        $upload->thumbMaxHeight = '420';
        $upload->thumbPrefix   = 's_';
        $upload->thumbRemoveOrigin = true; //删除原图
        if(!$upload->upload()) {// 上传错误提示错误信息
            $msg = $upload->getErrorMsg();
            $this->alert($msg);
            exit();
        }
        // 上传成功
        $msg = $upload->getUploadFileInfo();
        $data=array(
            'url'=>$path.'s_'.$msg[0]['savename'],
            'error'=>0
        );
        exit(json_encode($data));
    }

    public function UploadArticle(){
        header('Content-type: text/html; charset=UTF-8');
        $path   = './Uploads/article/';
        $upload = $this->_upCommon($path);
        $upload->thumb = true;
        $upload->thumbMaxWidth = '640';
        $upload->thumbMaxHeight = '420';
        $upload->thumbPrefix   = 'article_';
        $upload->thumbRemoveOrigin = true; //删除原图
        if(!$upload->upload()) {// 上传错误提示错误信息
            $msg = $upload->getErrorMsg();
            $this->alert($msg);
            exit();
        }
        // 上传成功
        $msg = $upload->getUploadFileInfo();
        $data=array(
            'url'=>$path.'article_'.$msg[0]['savename'],
            'error'=>0
        );
        exit(json_encode($data));
    }
    /**
     * 信息发布图片位置
     */
    public function uploadNotify()
    {
        $path = './Uploads/notify/inf/';
        $prefix = 'inf_';
        $upload = $this->_upCommon2($path,true,640,420,$prefix);
        if(!$upload->upload()) {// 上传错误提示错误信息
            $msg = $upload->getErrorMsg();
            $this->alert($msg);
            exit();
        }
        // 上传成功
        $msg = $upload->getUploadFileInfo();
        $data=array(
            'url'=>$path.$prefix.$msg[0]['savename'],
            'error'=>0
        );
        exit(json_encode($data));
    }
    /**
     * 创建层级目录
     * 以./Uploads/为开始
     */
    private function cmkdirs($path){
        $dirs = explode("/", $path);
        $cdir = '';
        foreach($dirs as $key=>$dir){
            $cdir.=$dir.'/';
           // if($key == 0 || $key == 1) continue;
            if(!is_dir($cdir)){
                mkdir($cdir);
                chmod($cdir,0777);
            }
        }
    }

    private function alert($msg) {
		header('Content-type: text/html; charset=UTF-8');
		import("ORG.Net.Services_JSON");
		$json = new Services_JSON();
		echo $json->encode(array('error' => 1, 'message' => $msg));
		exit;
	}
}