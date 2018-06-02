<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\Config;
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
        Url::root($request->baseFile());
    }

    /**
     * 定义默认的Index
     * @return array
     */
    public function index()
    {
        return ['info'=>_array_rond(Config::get('default_return_info')),'status'=>-1,'data'=>NULL];
    }
}