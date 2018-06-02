<?php
namespace app\admin\controller;


/**
 * 自动生成的文件管理Class
 */
class Fm extends Base
{
	/**
	 * 以项目目录为基础，生成项目压缩包
	 * @return [type] [description]
	 */
    public function createZip()
    {
        return json(_zip_export());
    }
    /**
     * 删除项目下自动生成的缓存文件、备份文件
     * @return [type] [description]
     */
    public function gitInitStart()
    {

    	$delDir = [
    		APP_PATH.'testing',
    		BACKUP_PATH,
    		LOG_PATH,
    		CACHE_PATH,
    		TEMP_PATH,
    	];
    	echo '开始执行删除多余目录',__EXT;
    	foreach ($delDir as $key => $v) {
    		echo '删除目录及目录下子文件',$v;
    		_dir_deldir($v);
    		echo '成功',__EXT;
    	}
    }
}