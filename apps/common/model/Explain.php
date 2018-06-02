<?php
namespace app\common\model;

use think\Model;

class Explain extends Model
{
    /**
     * 检测用户名是否存在
     * @param $u
     * @return bool
     */
    public function _check_user($u)
    {
        return $this::getByName(remove_spaces($u))?true:false;
    }
}
/**
 * 强化字符串
 * @param $str
 * @return string
 */
function pass($str)
{
    return sha1(md5($str).__Me__);
}
/**
 * 设置字符串
 * @param  string $value 设置的字符串
 * @return string
 */
function trim_string($value)
{
    $ret = null;
    if (null != $value && strlen($ret) != 0)
        $ret = $value;
    return $ret;
}