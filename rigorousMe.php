<?php
// +----------------------------------------------------------------------
// | Created by http://www.daydaymiss.com
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 
// +----------------------------------------------------------------------
// | Date ： 2018/5/15 17:31
// +----------------------------------------------------------------------
// | Author: rigorousMe <1025396319@qq.com>
// +----------------------------------------------------------------------
// | version: v0.01
// +----------------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);//当前系统的目录分隔符
define('__EXT',"<br/>\n");//当前系统的目录分隔符
define('APP_PATH',__DIR__.'/apps/');//应用目录
define('ROOT_PATH',dirname(realpath(APP_PATH)).DS);
define('BACKUP_PATH',__DIR__.'/backup/');//应用目录
define('BASE_URL',"http://".$_SERVER['HTTP_HOST']);//项目线上地址
define('URL_HTML_SUFFIX','jsp');//URL伪静态后缀
define('VIEW_SUFFIX','.tpl');//模版文件后缀
define('VIEW_DERP','-');//模版文件后缀
define('VIEW_PATH',__DIR__.DS."public".DS."tpl".DS);//模版文件路径
define('__Me__', 'RigorousMe');//自定义字符串
define('COMMON_PATH',APP_PATH.'common'.DS);//公共文件目录
header("Content-type: text/html; charset=utf-8");//设定标准的UTF-8返回字符
header('Access-Control-Allow-Origin:*');//允许跨域请求
header('Access-Control-Allow-Methods:POST,GET');// 响应类型
header('Access-Control-Allow-Headers:x-requested-with,content-type');// 响应头设置