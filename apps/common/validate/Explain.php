<?php
namespace app\common\validate;

use think\Validate;

class Explain extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25|min:4|check_name',
        'status' => 'in:1,0',
    ];
    protected $message  =   [
        'name.require' => '登陆账号不能为空',
        'name.min'     => '登陆账号不能少于4个字符',
        'name.max'     => '登陆账号不能超过25个字符',

        'status' => "状态值错误",

    ];
    protected $scene = [
        'edit'=>['name','status'],
        'add'=>['name','status'],
    ];
    public function __construct()
    {
        parent::__construct();
        $this->M = model('Explain');
    }
    protected function check_name($v)
    {
        return true;
    }
}