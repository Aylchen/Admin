<?php
/**
 * Created by Aylchen.
 * User: 10281_000
 * Date: 2015/6/9
 * Time: 9:06
 */

class QrCode {
    private $appid;
    private $appsecret;
    private $access_token;
    private $scene_param;
    private $action_name;
    private $action_type;
    public function __construct($appid,$appsecret,$scene_param,$action_type){
        $this->appid        = $appid;
        $this->appsecret    = $appsecret;
        $this->access_token = $this->_getAccessToken();
        $this->scene_param  = $scene_param;
        $this->action_type  = $action_type;
        $this->action_name  = $this->_action();
    }
    private function _action(){
        $action_name = '';//二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
        switch ($this->action_type) {
            case 1:
                $action_name = 'QR_SCENE';
                break;
            case 2:
                $action_name = 'QR_LIMIT_SCENE';
                break;
            case 3:
                $action_name = 'QR_LIMIT_STR_SCENE';
                break;
        }
        return $action_name;
    }

    private function _sceneKey(){
        //获取post-json字符串的scene-key
        $key = '';
        switch ($this->action_type) {
            case 1:
            case 2:
                $key = "scene_id";
                break;
            case 3:
                $key = "scene_str";
                break;
            default:
                $key = "scene_str";
                break;
        }
        return $key;
    }

    private function _getAccessToken(){
        //获取access_token;要进行缓存
        $access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->appsecret";
        $json_return = httpGet($access_token_url);
        return $json_return['access_token'];
    }
    private function _getTicket(){
        /**
         * ticket要进行缓存
         */
        //获取ticket,由action_name来决定是临时的还是永久的
        $ticket_url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$this->access_token";
        $scene      = array($this->_sceneKey()=>$this->scene_param);
        $param      = array(
            "action_name" => $this->action_name,
            "action_info" => array(
                "scene"=>$scene
            )
        );

        if($this->action_type == 1){
            $param['expire_seconds'] = 604800;
        }
        $ticket_return = httpPost($ticket_url,json_encode($param));
        return $ticket_return;
    }

    public function createQrCode(){//获取二维码的图片地址
        $ticket_arr = $this->_getTicket();
        $ticket     = $ticket_arr['ticket'];
        $qrcode_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);//二维码地址
        return $qrcode_url;
    }


}