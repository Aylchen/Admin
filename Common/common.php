<?php
	//查找某一条数据或检验是否存在
	function mysql_query_one($where,$tb){
		return M($tb)->where($where)->find();
	}
	//获取某条件下的所有数据
	function mysql_query_all($where,$order,$tb){
		return M($tb)->where($where)->order($order)->select();
	}
	//带分页显示
	function mysql_query_page($pagesize,$where,$order,$tb){
		import('ORG.Util.Page');// 导入分页类
		$model = M($tb);
		$count = $model->where($where)->count();
		$Page  = new Page($count,$pagesize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $model->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		return array($list,$show);
	}
	//获取某条件下的数据总数
	function mysql_query_count($where,$tb){
		return M($tb)->where($where)->count();	
	}

	//保存数据
	function mysql_query_save($where,$data,$tb){
		return M($tb)->where($where)->save($data);
	}

	function mysql_query_del($where,$tb){
		return M($tb)->where($where)->delete();
	}
	
	function mysql_query_add($data,$tb){
		return M($tb)->add($data);
	}

	function str2arr($str, $glue = ',') {
		return array_filter(explode ( $glue, $str ));
	}
	function arr2str($arr, $glue = ',') {
		return implode ( $glue, $arr );
	}

	function httpPost($url,$param){
        header("Content-type: text/html; charset=utf-8");
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$res = curl_exec ( $ch );
		curl_close ( $ch );
		return json_decode ( $res, true );
	}

    function httpGet($url) {
        header("Content-type: text/html; charset=utf-8");
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return json_decode ( $res, true );
	}
	//删除目录及其下的所有文件
	function deldir($dir) {
		//先删除目录下的文件：
		$dh=opendir($dir);
		while ($file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					deldir($fullpath);
				}
			}
		}
		closedir($dh);
		//删除当前文件夹：
		if(rmdir($dir)) {
			return true;
		} else {
			return false;
		}
	}

    function cmkdirs($path){
        $dirs = explode("/", $path);
        $cdir = '';
        foreach($dirs as $key=>$dir){
            $cdir.=$dir.'/';
            if($key == 0 || $key == 1) continue;
            if(!is_dir($cdir)){
                mkdir($cdir);
                chmod($cdir,0777);
            }
        }
    }

    function random(){
        return md5(time().rand(0,1000));
    }

    function getComplicatedWhere($param_array){
        $param_array = array_values(array_filter($param_array));//删除空元素，并重置索引

        $where = "";
        $param_num   = count($param_array);//获取参数个数
        if($param_num == 1){
            return empty($param_array[0]) ? "" : $param_array[0];
        }else{
            for($i=0;$i<$param_num;$i++){
                $w = $param_array[$i];
                $where.= $w.' and ';
            }
            return substr($where,0,-5);
        }

    }