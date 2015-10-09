<?php
return array(

    'URL_MODEL'		=>	3, //nginx不支持pathinfo，支持兼容模式?s=/module/func/
    'URL_CASE_INSENSITIVE'=>true,
    'URL_ROUTER_ON'   => true, //开启路由
    'DB_TYPE'		=>	'mysql',
    'DB_HOST'		=>	'localhost',
    'DB_NAME'		=>	'tczc',
    'DB_USER'		=>	'root',
    'DB_PWD'		=>	'',
    'DB_PORT'		=>	'3306',
    'DB_PREFIX'		=>	'tczc_',
    /* 数据库配置 */
    /*'DB_TYPE'   => 'mysql',
    'DB_HOST'   => 'plandodb.mysql.rds.aliyuncs.com:3306', 
    'DB_NAME'   => 'pldconsumer', 
    'DB_USER'   => 'weixin', 
    'DB_PWD'    => 'pulandu2015',  
    'DB_PORT'   => '3306', 
    'DB_PREFIX' => 'tczc_',*/

    'DIR_ROOT'      => "http://127.0.0.1/tc_admin/",
    'SITE_ROOT'     => "http://127.0.0.1/tc_admin/?s=",
    'RESOURCE_PATH' => "http://127.0.0.1/tc_admin/Public/",
    'NOPIC'         => "http://127.0.0.1/tc_admin/Public/images/404.png",
    'PLATFORM_ID'   => '11111111'
);