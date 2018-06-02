<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Url;
use think\Db;
class Base extends \app\common\controller\Base
{
    /**
     * [__construct 继承Common的Base]
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
        return $this->fetch(_getTpl());
    }
}