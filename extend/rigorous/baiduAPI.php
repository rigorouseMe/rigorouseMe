<?php
/**
 * Created by PhpStorm.
 * User: 董浩
 * Date: 2017-07-03
 * Time: 12:37
 */
namespace rigorous;
class baiduAPI{
	private $ak;  		//百度地图APIKey
    private $lng;        //经纬度
    private $lat;        //经纬度
    private $location;   //经度,纬度
    private $locationstart;   //经度,纬度
    private $locationend;   //经度,纬度
    private $fashion;    //路径规划方式 [driving=>驾车,riding=>骑行,walking=>步行]   
    private $originslocation;//经度,纬度                开始位置
    private $destinationslocation;//经度,纬度           结束位置
    /**
     * [__construct 初始化,定义相关配置]
     * @param [type] $Config [配置数组]
     */
    public function __construct($ak)
    {
        isset($ak)?$this->ak=$ak:$this->appid=0;
    }
    /**
     * [getCoordinate 位置信息转换]
     * @return [type] [description]
     */
    public function getCoordinate()
    {
        if($this->lng === null && $this->lat === null){
            return '信息不能为空';
        }
        $link = 'http://api.map.baidu.com/geoconv/v1/?coords='.$this->lng.','.$this->lat.'&from=1&to=5&ak='.$this->ak;
        $response = $this->JsonDencode($this->GetCurl($link));
        if((int)$response['status'] === 0){
            $return['lng']= $response['result'][0]['x'];
            $return['lat']= $response['result'][0]['y'];
            $return['status'] = 1;
        }else{
            $return['status'] = $response['status'];
        }
        return $return;
    }
    public function getLocation()
    {
        // 39.983424,116.322987
        if($this->location === null){
            die("经纬度不能为空"."<br>");
        }
        $link = 'http://api.map.baidu.com/geocoder/v2/?callback=renderReverse&location='.$this->location.'&output=xml&ak='.$this->ak;
        $response = $this->xmlToArray($this->GetCurl($link));
        if((int)$response['status'] === 0){
            $result = $response['result'];
            // 结构化地址信息
            $return['formattedaddress']= $result['formatted_address'];
            // 当前位置结合POI的语义化结果描述。
            $return['sematicaddress']= $result['sematic_description'];
            // 所在商圈信息，如 "人民大学,中关村,苏州街"
            $return['business']= $result['business'];
            // 详细信息
            $country = !empty($result['addressComponent']['country'])?$result['addressComponent']['country']:'';
            $province = !empty($result['addressComponent']['province'])?$result['addressComponent']['province']:'';
            $city = !empty($result['addressComponent']['city'])?$result['addressComponent']['city']:'';
            $district = !empty($result['addressComponent']['district'])?$result['addressComponent']['district']:'';
            $street = !empty($result['addressComponent']['street'])?$result['addressComponent']['street']:'';
            $street_number = !empty($result['addressComponent']['street_number'])?$result['addressComponent']['street_number']:'';
            $direction = !empty($result['addressComponent']['direction'])?$result['addressComponent']['direction']:'';
            $return['addressinfo']= $country.$province.$city.$district.$street.$street_number.$direction;
            // 返回状态
            $return['status'] = 1;
        }else{

            $return['status'] = $response['status'];
        }
        return $return;
    }
    public function routeMatrix()
    {
        if($this->setOriginslocation === null && $this->setDestinationslocation === null){
            die("开始位置和结束位置经纬度不能为空"."<br>");
        }
        if($this->fashion == null){
            $this->fashion = 'walking';
        }
        $link = 'http://api.map.baidu.com/routematrix/v2/'.$this->fashion.'?output=json&origins='.$this->setOriginslocation.'&destinations='.$this->setDestinationslocation.'&ak='.$this->ak;
        $response = $this->JsonDencode($this->GetCurl($link));
        if((int)$response['status'] === 0){
            $return['response'] = $response['result'];
            // 返回状态
            $return['status'] = 1;
        }else{
            $return['status'] = $response['status'];
        }
        return $return;
    }
    public function getdistance() {
        // lat<纬度>,lng<经度>
        // 34.313092,108.958221
        if($this->locationstart === null && $this->locationend===null){
            die("开始位置和结束位置经纬度不能为空"."<br>");
        }
        $locationA = explode(',',$this->locationstart);
        $locationB = explode(',',$this->locationend);
        // 将角度转为狐度
        $radLat1 = deg2rad($locationA[0]);
        $radLat2 = deg2rad($locationB[1]);
        $radLng1 = deg2rad($locationA[1]);
        $radLng2 = deg2rad($locationB[0]);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    } 
    /**
     *  作用：设置请求参数
     */
    public function setlng($lng)
    {
        $this->lng = $this->trimString($lng);
    }
    public function setlat($lat)
    {
        $this->lat = $this->trimString($lat);
    }
    public function setLocation($location)
    {
        $this->location = $this->trimString($location);
    }
    public function setLocationstart($locationstart)
    {
        $this->locationstart = $this->trimString($locationstart);
    }
    public function setLocationend($locationend)
    {
        $this->locationend = $this->trimString($locationend);
    }
    public function setFashion($fashion)
    {
        $this->fashion = $this->trimString($fashion);
    }
    public function setOriginslocation($originslocation)
    {
        $this->originslocation = $this->trimString($originslocation);
    }
    public function setDestinationslocation($destinationslocation)
    {
        $this->destinationslocation = $this->trimString($destinationslocation);
    }



    /**
     * [trimString 设置字符串]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function trimString($value)
    {
        $ret = null;
        if (null != $value) 
        {
            $ret = $value;
            if (strlen($ret) == 0) 
            {
                $ret = null;
            }
        }
        return $ret;
    }
    /**
     * 重复调用函数，利用php原生函数处理数据信息
     * @param  [json $json 需要处理的json数据]
     * @return [Array $arr 返回的json处理之后的数组]
     */
    public function JsonDencode($json)
    {
        return json_decode($json,true);
    }   
    /**
     *  作用：将xml转为array
     */
    public function xmlToArray($xml)
    {       
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);      
        return $array_data;
    }
    /**
     * 重复调用函数，利用php原生函数处理数据信息
     * @param  [arr $arr 需要处理的数组数据]
     * @return [json $json 返回的数组处理之后的json]
     */
    public function JsonEncode($arr)
    {
        return json_encode($arr,true);
    }
    /**
     *  作用：产生随机字符串，不长于32位
     */
    public function createNoncestr( $length = 32 ) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        }  
        return $str;
    }
    /**
     * [GetCurl 模拟GET请求]
     * @param [string] $link [请求地址]
     * @return [type] [请求结果]
     */
    public function GetCurl($link)
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
    private function dump($var, $echo = true, $label = null, $strict = true) {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo ($output);
            return null;
        } else {
            return $output;
        }
    }


}
