<?php
namespace app\common\controller;
use think\Controller;
use think\Request;
use think\Url;
class Base extends Controller
{

    /**
     * [__construct 继承ThinkPHP的Base]
     * Base constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        #定义入口文件
        Url::root($request->baseFile());
        #定义默认返回Empty
        $this->assign('empty','<span class="empty">没有数据</span>');
        #定义分页数量
        $this->pagint = 10;
        #定义默认的排序规则
        $this->order = 'sort asc,id asc';
        #定义默认的Map查询数组
        $this->map['id']=['neq','-'];
        #检测模型文件是否存在，存在则创建模型
        $this->M = is_file(COMMON_PATH.'model'.DS.$this->request->controller().'.php')?model($this->request->controller()):false;
        #检测验证器模型文件是否存在，存在则实例化验证器
        $this->V = is_file(COMMON_PATH.'validate'.DS.$this->request->controller().'.php')?validate($this->request->controller()):false;
        _dir_mkdir(BACKUP_PATH);
        
    }
}