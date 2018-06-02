<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;
class Base extends \app\common\controller\Base
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
    public function index()
    {
        return json([
            'info'=>'生活不止眼前的苟且 还有诗和远方的田野'
        ]);
    }


}