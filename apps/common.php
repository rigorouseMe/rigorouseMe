<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Request;
// 应用公共文件
/**
 * 获取模板文件路径
 * 根据ThinkPHP 的Request对象拼接模板文件路径
 * @return string
 */
function _getTpl()
{
    $instance = Request::instance();
    return VIEW_PATH.$instance->module().DS.$instance->controller().VIEW_DERP.$instance->action().VIEW_SUFFIX;
}

/**
 * 中文字符转化为汉语拼音
 * @param $string
 * @return string
 */
function hz_To_ingyin($string)
{
    return \rigorous\Utf8Py::encode($string);
}

/**
 * 创建Zip文件
 * @param null $fileName
 * @param null $filePath
 */
function _zip_export($fileName=NULL,$filePath=NULL)
{
    $zip = new \ZipArchive;
    $path = ROOT_PATH.'backup'.DS.'zip'.DS;
    if (!is_dir($path)) 
    {
        _dir_mkdir($path);
    }
    $name = date('YmdHms',time())."---".date('H',time())."---".date('m',time()).".zip";
    $fileName = ($fileName)?$path.$fileName.'.zip':$path.$name;
    $filePath = $filePath?$filePath:ROOT_PATH;
    if ($zip->open($fileName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE)!==TRUE)
        return ['status'=>'-1','info'=>'无法打开文件，或者文件创建失败','path'=>$fileName];
    _zip_create(opendir($filePath),$zip,$filePath);
    $zip->close();//关闭
    if(!file_exists($fileName))
        return ['status'=>'-1','info'=>'压缩文件生成失败','path'=>$fileName];
    return ['status'=>'1','info'=>'压缩文件创建成功','path'=>$fileName];
}

/**
 * 递归处理文件夹
 * @param $openFile
 * @param $zipObj
 * @param $sourceAbso
 * @param string $newRelat
 */
function _zip_create($openFile,$zipObj,$sourceAbso,$newRelat = '')
{
    while(($file = readdir($openFile)) != false)
    {
        if(in_array($file,['.','..','log','temp','.git','zip']))
            continue;
        $sourceTemp = $sourceAbso.'/'.$file;
        $newTemp = $newRelat==''?$file:$newRelat.'/'.$file;
        if(is_dir($sourceTemp))
        {
            $zipObj->addEmptyDir($newTemp);
            _zip_create(opendir($sourceTemp),$zipObj,$sourceTemp,$newTemp);
        }
        if(is_file($sourceTemp))
        {
            $zipObj->addFile($sourceTemp,$newTemp);
        }
    }
}

/**
 * zip文件下载
 * @param $fileName
 */
function _zip_downlound($fileName)
{
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header('Content-disposition: attachment; filename='.basename($fileName)); //文件名
    header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: binary");
    header('Content-Length: '. filesize($fileName));
    @readfile($fileName);
}
/**
 * 发送邮件
 * @param string $toemail
 * @param null $config
 * @param null $data
 * @param string $emailType
 * @return array
 */
function _mail_Send($toemail='1025396319@qq.com',$config=NULL,$data=NULL,$emailType='stmp'){

    if(!$config){
        $config=[
            'host'=>'smtp.163.com',
            'username'=>'dream_donghao@163.com',
            'password'=>'meizumx22',
            'port'=>25,
        ];
    }
    if(!$data){
        $data=[
            'name'=>'测试人员',
            'title'=>'测试标题',
            'message'=>'测试内容',
        ];
    }
    //判断openssl是否开启
    $openssl_funcs = get_extension_funcs('openssl');
    if (!$openssl_funcs) {
        return ['status'=>-1,'info'=>'请先开启openssl扩展'];
    }
    import('PHPMailers'.DS.'PHPMailer', EXTEND_PATH);
    $mail = new PHPMailer(true);
    // 服务器设置
    if($emailType === 'stmp'){
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->SMTPAuth = true;                                    // 开启SMTP验证
        $mail->SMTPDebug = false;                                    // 开启Debug
    }else{
        return ['status'=>-1,'info'=>'未定义发送类型'];
    }
    $mail->CharSet='utf-8';
    $mail->Host = $config['host'];// 服务器地址
    $mail->Username = $config['username'];// SMTP 用户名（你要使用的邮件发送账号）
    $mail->Password = $config['password'];// SMTP 密码
    $mail->Port = $config['port'];// 端口
    $mail->setFrom($config['username'],$data['name']);// 来自
    $mail->addAddress($toemail);// 可以只传邮箱地址
    $mail->addReplyTo($config['username']);// 回复地址
    $mail->isHTML(true);// 设置邮件格式为HTML
    $mail->Subject = $data['title'];
    $mail->Body    = $data['message'];
    try {
        $mail->send();
        return ['status'=>1,'info'=>'邮件发送成功'];
    } catch (Exception $e) {
        return ['status'=>0,'info'=>'邮件发送失败'.$mail->ErrorInfo];
    }
}






/**
 * 随机获取数组中的值或者是键
 * @param $arr
 * @param int $t
 * @param int $n
 * @param string $v
 * @return array|mixed
 */
function _array_rond($arr,$t=1,$n=1,$v='value')
{
    if($t == 1)
        return ($v =='value')?$arr[array_rand($arr)]:array_rand($arr);
    else
        if($v=='value')
            return _arr_n_k_v($arr,array_rand($arr,$n));
        else
            return array_rand($arr,$n);
}

/**
 * 返回数组中需要的键值数据
 * @param $arr
 * @param $keys
 * @return array
 */
function _arr_n_k_v($arr,$keys){
    if(is_array($keys))
    {
        $rt = [];
        foreach ($keys as $k=>$v){
            $rt[$k] = $arr[$v];
        }
        return $rt;
    }else
    {
        return $arr[$keys];
    }
}
/**
 * 数组去除空值
 * @param $arr
 * @return mixed
 */
function _arr_unset($arr)
{
    foreach( $arr as $k=>$v){
        if(strlen(remove_spaces($v))===0)
            unset($arr[$k]);
    }
    return $arr;
}
/**
 * 从记录集中取出 对应需要的 列
 * @param $arr
 * @param $key
 * @return array
 */
function _arr_column($arr,$key)
{
    return array_column($arr,$key);
}
/**
 * 二维数组概率随机输出
 * @param $proArr
 * @return int|string
 */
function _arr_rand_two($proArr)
{
    $result = '';
    //概率数组的总概率精度
    $proSum = array_sum($proArr);
    //概率数组循环
    foreach ($proArr as $key => $proCur) {
        $randNum = mt_rand(1, $proSum);
        if ($randNum <= $proCur) {
            $result = $key;
            break;
        } else {
            $proSum -= $proCur;
        }
    }
    unset ($proArr);
    return $result;
}
/**
 * 创建文件夹
 * @param $path
 * @return mixed
 */
function _dir_mkdir($path)
{
    if (!file_exists($path)){
        mkdir("$path",0777,true);
    }
    return $path;
}
/**
 * 删除文件夹下的文件内容
 * @param  [type] $dir [description]
 * @return [type]      [description]
 */

function _dir_deldir($dirName)   
{   
    if(! is_dir($dirName))   
    {   
        return false;   
    }   
    $handle = @opendir($dirName);   
    while(($file = @readdir($handle)) !== false)   
    {   
        if($file != '.' && $file != '..')   
        {   
            $dir = $dirName . '/' . $file;   
            is_dir($dir) ? _dir_deldir($dir) : @unlink($dir);   
        }   
    }   
    closedir($handle);   
        
    return rmdir($dirName) ;   
}  


function _dir_list($path, $exts = '', $list= array()) {
    $path = _dir_path($path);
    $files = glob($path.'*');
    foreach($files as $v) {
        $fileext = fileext($v);
        if (!$exts || preg_match("/\.($exts)/i", $v)) {
            $list[] = $v;
            if (is_dir($v)) {
                $list = _dir_list($v, $exts, $list);
            }
        }
    }
    return $list;
}
function _dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if(substr($path, -1) != '/') $path = $path.'/';
    return $path;
}
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}
/**
 * 判断文件或文件夹是否可写
 * @param $file
 * @return bool
 */
function _is_really_writable($file)
{
    if (DIRECTORY_SEPARATOR === '/')
    {
        return is_writable($file);
    }
    if (is_dir($file))
    {
        $file = rtrim($file, '/') . '/' . md5(mt_rand());
        if (($fp = @fopen($file, 'ab')) === FALSE)
        {
            return FALSE;
        }
        fclose($fp);
        @chmod($file, 0777);
        @unlink($file);
        return TRUE;
    }
    elseif (!is_file($file) OR ( $fp = @fopen($file, 'ab')) === FALSE)
    {
        return FALSE;
    }
    fclose($fp);
    return TRUE;
}
/**
 * 将字节转换为可读文本
 * @param $size             字节大小
 * @param string $delimiter
 * @return string
 */
function _file_format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 获取输入的字符类型
 * @param $str          判断字符串
 * @return array|string
 */
function _input_type($str)
{
    $output = '';
    $zhCN = preg_match('/[' . chr(0xa1) . '-' . chr(0xff) . ']/', $str);
    $int = preg_match('/[0-9]/', $str);
    $english = preg_match('/[a-zA-Z]/', $str);
    if ($zhCN && $int && $english)
        $output = ['type' => 1, 'status' => '中文+数字+英文'];
    elseif ($zhCN && $int && !$english)
        $output = ['type' => 2, 'status' => '中文+数字'];
    elseif ($zhCN && !$int && $english)
        $output = ['type' => 3, 'status' => '中文+英文'];
    elseif (!$zhCN && $int && $english)
        $output = ['type' => 4, 'status' => '英文+数字'];
    elseif ($zhCN && !$int && !$english)
        $output = ['type' => 5, 'status' => '中文'];
    elseif (!$zhCN && $int && !$english)
        $output = ['type' => 6, 'status' => '数字'];
    elseif (!$zhCN && !$int && $english)
        $output = ['type' => 7, 'status' => '英文'];
    return $output;
}
/**
 * 删除字符串中的空格
 * @param $str
 * @return mixed
 */
function remove_spaces($str)
{
    $thisStart=array(" ","　","\t","\n","\r");
    $thisEnd=array("","","","","");
    return str_replace($thisStart,$thisEnd,$str);
}
/**
 *  Array数据转换为Json
 * @param $arr
 * @return string
 */
function _to_array_to_json($arr)
{
    return json_encode($arr,true);
}
/**
 * array转为XML
 * @param  array $arr array数据
 * @return xml
 */
function _to_array_to_xml($arr)
{
    if(!is_array($arr) || count($arr) <= 0)
        return "传入数据异常！";
    $xml = "<xml>";
    foreach ($arr as $key=>$val)
    {
        if (is_numeric($val))
            $xml.="<".$key.">".$val."</".$key.">";
        else
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    }
    $xml.="</xml>";
    return $xml;
}
/**
 * json转为xml
 * @param  json $json json数据
 * @return xml
 */
function _to_json_to_xml($json)
{
    return array_to_xml(json_to_array($json));
}
/**
 * Json数据转换为Array
 * @param $json
 * @return mixed
 */
function _to_json_to_array($json)
{
    return json_decode($json,true);
}
/**
 * 将XML转为array
 * @param  xml $xml XML数据
 * @return array
 */
function _to_xml_to_array($xml)
{
    return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
}
/**
 * 将XML转为json
 * @param  xml $xml XML数据
 * @return json
 */
function _to_xml_to_json($xml)
{
    return json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
}
/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 */
function _to_str_to_arr($str, $glue = ','){
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function _to_arr_to_str($arr, $glue = ','){
    return implode($glue, $arr);
}
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}
/**
 * 记录log
 */
function logs($content,$path=ROOT_PATH){
    $file = fopen($path,"a+");
    $string = date('Y-m-d H:m:s',time())."\t\t".$content."\n";
    fwrite($file,$string);
    fclose($file);
}
/**
 * 生成随机长度的字符串
 * @param int $len  长度
 * @param string $type  字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return bool|string
 */
function _rand_string($len = 6, $type = '', $addChars = '') {
    $str = '';
    switch ($type) {
        case 0 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1 :
            $chars = str_repeat ( '0123456789', 3 );
            break;
        case 2 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3 :
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) { //位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
    }
    if ($type != 4) {
        $chars = str_shuffle ( $chars );
        $str = substr ( $chars, 0, $len );
    } else {
        // 中文随机字
        for($i = 0; $i < $len; $i ++) {
            $str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
        }
    }
    return $str;
}
/**
 * 设置数据为保留2位小数的数据
 * @param int $int
 * @return string
 */
function save_sprint($int=0)
{
    return sprintf("%.2f", $int);
}
/**
 * 字符串中间设置加密
 * @param string $string 需要处理的字符串
 * @param string $str     加密片段
 * @param int $start    开始位置
 * @param int $length   处理长度
 * @return mixed
 */
function save_string_hide($string,$str='****',$start=3,$length=4)
{
    return substr_replace($string,$str,$start,$length);
}
/**
 * 将数组利用pid转换为无限分类的子集
 * @param array   $arr  处理前数组
 * @param string  $paringKey  条件判断键值
 * @return array
 */
function arr_find_pid($arr,$paringKey='pid',$pid=0,$nowK='findnemus')
{
    $tree = array();                                //每次都声明一个新数组用来放子元素
    foreach($arr as $v){
        if($v[$paringKey] == $pid){                      //匹配子记录
            $v[$nowK] = arr_find_pid($arr,$paringKey?$paringKey:'pid',$v['id']); //递归获取子记录
            if($v[$nowK] == null){
                unset($v[$nowK]);             //如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
            }
            $tree[] = $v;                           //将记录存入新数组
        }
    }
    return $tree;                                  //返回新数组
}

/**
 * 获取对应年对应月份的天数
 * @param  int $year 年
 * @param  int $month 月
 * @return int
 */
function _time_for_YM_to_days($year=NULL,$month=NULL)
{
    $year = empty($year)?(int)date("Y"):(int)$year;
    $month = empty($month)?(int)date("m"):(int)$month;
    return (in_array($month,[1,3,5,7,8,10,12]))?31:(($month == 2)?(($year%4 === 0)?29:28):30);
}
/**
 * 将时间戳转换为日期时间
 * @param $time
 * @param string $format
 * @return false|string
 */
function datetime($time, $format = 'Y-m-d H:i:s')
{
    return date($format,(is_numeric($time) ? $time : strtotime($time)));
}
/**
 * 获取服务器信息
 * @return array
 */
function get_system_info()
{
    $os = explode(" ", php_uname());
    $neihe = ('/'==DIRECTORY_SEPARATOR)?$os[2]:$os[1];
    return [
        ['type'=>'服务器域名','info'=>'<a href="'.request()->domain().'" target="_blank">'.request()->domain().'</a>/'],
        ['type'=>'服务器IP地址:端口','info'=>request()->ip().':'.$_SERVER['SERVER_PORT']],
        ['type'=>'服务器操作系统','info'=>$os[0].'&nbsp;内核版本：'.$neihe],
        ['type'=>'服务器解译引擎','info'=>$_SERVER['SERVER_SOFTWARE']],
        ['type'=>'服务器语言','info'=>getenv("HTTP_ACCEPT_LANGUAGE")],
        ['type'=>'绝对路径','info'=>$_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__))],
        ['type'=>'PHP相关信息','info'=>'版本 <a href="'.url(request()->module.DS."Index".DS."phpinfo").'" title="点击查看版本信息" target="_blank">'.PHP_VERSION.'</a>'],
        ['type'=>'脚本占用最大内存（memory_limit）','info'=>get_cfg_var("memory_limit")],
        ['type'=>'POST方法提交最大限制（post_max_size）','info'=>get_cfg_var("post_max_size")],
        ['type'=>'上传文件最大限制（upload_max_filesize）','info'=>get_cfg_var("upload_max_filesize")],
        ['type'=>'浮点型数据显示的有效位数（precision）','info'=>get_cfg_var("precision").'位'],
        ['type'=>'脚本超时时间（max_execution_time）','info'=>get_cfg_var("max_execution_time").'秒'],
        ['type'=>'socket超时时间（default_socket_timeout）','info'=>get_cfg_var("default_socket_timeout").'秒'],
    ];
}
/**
 * 格式化控制器名称
 * User=>User
 * UserGroup=>User_group
 * UserGroupList=>User_group_list
 * @param $string
 * @param string $connectorc
 * @return string
 */
function _format_controller($string,$connectorc="_"){
    $returnString = '';
    for ($i=0; $i < strlen($string); $i++) {
        $ascii_code = ord($string[$i]);
        if($ascii_code >= 65 && $ascii_code <= 90 && $i!== 0)
            $returnString .= $connectorc.chr($ascii_code + 32);
        else
            $returnString .= $string[$i];
    }
    return $returnString;
}
function pass($str)
{
    return sha1(md5($str));
}
/**
 * Curl Get请求
 * @param [type] $link [description]
 */
function _curl_Get($link)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
/**
 * Curl Post请求
 * @param [type] $link [description]
 * @param [type] $data [description]
 */
function _curl_POST($link,$data)
{
    $chpost = curl_init();
    curl_setopt($chpost, CURLOPT_TIMEOUT, 30);
    curl_setopt($chpost, CURLOPT_URL, $link);
    curl_setopt($chpost, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($chpost, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($chpost, CURLOPT_HEADER, FALSE);
    curl_setopt($chpost, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($chpost, CURLOPT_POST, TRUE);
    curl_setopt($chpost, CURLOPT_POSTFIELDS,$data);
    $outputpost = curl_exec($chpost);
    if($outputpost){
        curl_close($outputpost);
        return $outputpost;
    }else{
        $error = curl_errno($outputpost);
        curl_close($outputpost);
        return $error;
    }
}
/**
 * [is_weixin 检测是否是微信浏览器]
 * @return boolean [返回判断结果]
 */
function IS_WEIXIN(){
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }
    return false;
}
/**
 * [POST_ALL 获取所有POST提交的数据]
 * @return POST提交的数据数组
 */
function POST_ALL()
{
    return Request::instance()->post();
}
/**
 * [GET_ALL 获取所有GET提交的数据]
 * @return GET提交的数据数组
 */
function GET_ALL()
{
    return Request::instance()->get();
}
/**
 * [IS_POST description]
 * @return 1 = POST 请求
 */
function IS_POST()
{
    return Request::instance()->isPOST();
}
/**
 * [IS_GET description]
 * @return 1 =GET 请求
 */
function IS_GET()
{
    return Request::instance()->isGet();
}
/**
 * [IS_PUT description]
 * 是否为 PUT 请求
 * @return 1 =PUT 请求
 */
function IS_PUT()
{
    return Request::instance()->isPut();
}
/**
 * [IS_DELETE description]
 * 是否为 DELETE 请求
 * @return 1 =DELETE 请求
 */
function IS_DELETE()
{
    return Request::instance()->isDelete();
}
/**
 * [IS_AJAX description]
 * 是否为 Ajax 请求
 * @return 1 =AJAX 请求
 */
function IS_AJAX()
{
    return Request::instance()->isAjax();
}
/**
 * [IS_PJAX description]
 * 是否为 Pjax 请求
 * @return 1 =PJAX 请求
 */
function IS_PJAX()
{
    return Request::instance()->isPjax();
}
/**
 * [IS_MOBILE description]
 * 是否为手机访问
 * @return 1 =MOBILE 请求
 */
function IS_MOBILE()
{
    return Request::instance()->isMobile();
}
/**
 * [IS_HEAD description]
 * 是否为 HEAD 请求
 * @return 1 =HEAD 请求
 */
function IS_HEAD()
{
    return Request::instance()->isHead();
}
/**
 * [IS_PATCH description]
 * 是否为 Patch 请求
 * @return 1 =PATCH 请求
 */
function IS_PATCH()
{
    return Request::instance()->isPatch();
}
/**
 * [IS_OPTIONS description]
 * 是否为 OPTIONS 请求
 * @return 1 =OPTIONS 请求
 */
function IS_OPTIONS()
{
    return Request::instance()->isOptions();
}
/**
 * [IS_CIL description]
 * 是否为 cli
 * @return 1 =CIL 请求
 */
function IS_CIL()
{
    return Request::instance()->isCli();
}
/**
 * [IS_CGI description]
 * 是否为 cgi
 * @return 1 =CGI 请求
 */
function IS_CGI()
{
    return Request::instance()->isCgi();
}
/**
 * 判断是否SSL协议
 * @return boolean
 */
function IS_SSL() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = ip2long($ip);
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}
/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code) {
    static $_status = array(
        // Success 2xx
        200 => 'OK',
        // Redirection 3xx
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',  // 1.1
        // Client Error 4xx
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        // Server Error 5xx
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:'.$code.' '.$_status[$code]);
    }
}











