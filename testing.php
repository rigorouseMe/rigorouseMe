<?php
// +----------------------------------------------------------------------
// | Created by http://www.daydaymiss.com
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 
// +----------------------------------------------------------------------
// | Date ： 2018/5/16 11:14
// +----------------------------------------------------------------------
// | Author: rigorousMe <1025396319@qq.com>
// +----------------------------------------------------------------------
// | version: v0.01
// +----------------------------------------------------------------------
if (version_compare("5.5", PHP_VERSION, ">")) {
    die("PHP 5.5 or greater is required!!!");
}
//额外配置
require __DIR__.DIRECTORY_SEPARATOR.'rigorousMe.php';
define('BIND_MODULE','testing');
//开始运行
require __DIR__.DS.'thinkphp'.DS.'start.php';
//配置自动生成
$build = include __DIR__.DS.'public'.DS.'build.php';
\think\Build::run($build);