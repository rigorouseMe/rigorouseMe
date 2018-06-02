<?php
namespace rigorous;
/**
 * wiki From https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140183&token=&lang=zh_CN
 *  微信的一些实例化函数
 * 【基本函数】
 * 		GetCurl($link) 								@param 链接地址							模拟GET请求
 * 		PostCurl($link,$data) 						@param 链接地址,提交的数据				以post方式提交数据到对应的接口url
 * 		PostSSLCurl($xml,$url) 						@param 链接地址,提交的数据				使用证书,以post方式提交数据到对应的接口url
 * 		trimString($value)							@param 需要处理的值						设置参数时需要用到的字符处理函数
 * 		createNoncestr( $length = 32 )  			@param 长度 							产生随机字符串，不长于32位
 * 		formatBizQueryParaMap($paraMap, $urlencode) @param 数据,格式函数					格式化参数，签名过程需要用到
 * 		getSign($Obj)								@param 数组 							生成签名
 * 		JsonDencode($json) 							@param Json 							Json  转 array
 * 		JsonEncode($arr) 							@param Array 							array 转 Json
 * 		arrayToXml($arr) 							@param Array 							array 转 xml
 * 		xmlToArray($xml) 							@param Xml	 							xml   转 array
 * 		SetWebServiceForWxToken($Token)				@param Token 							设置的Token令牌
 * 		checkSignature($Token)						@param Token 							设置的Token令牌
 * 【类函数】		@return Array
 * 		WxGetAccessToken()	  														@param NULL	 							 					获取access_token
 * 		WxJsapiTicket($access_token)												@param access_token 					 					获取jsapi_ticket
 * 		wxGetUserList($access_token)												@param access_token 					 					获取关注用户列表	
 * 		wxGetUserShow($access_token,$openid) 										@param access_token,openid 				 					获取关注用户的信息
 * 		wxMsg($token,$data,$template_id,$openid,$url='',$topcolor='#FF0000')		@param access_token,格式化的数组,模板ID,openid,跳转链接,	给对应的用户发消息
 * 【 JsApi】
 * createPayXml() 						生成支付的Xml数据
 * getPayPrepayId()						利用支付接口得到的预支付id
 * setPayPrepayId($prepayId) 			设置预支付id
 * getPayParam()						返回支付需要的数据
 * 【 	 对账单接口 	】
 * createDownloadbillXml()
 * getDownloadbillResult()
 * 【 	订单查询接口 	】
 * createOrderXml()
 * getOrderResult() 
 * 【 	退款申请接口 	】
 * createRefundXml()
 * getRefundResult() 
 * 【 	退款查询接口 	】
 * createRefundqueryXml() 
 * getRefundqueryResult()
 * 【 	微信登陆 	】
 * wxGetCode($redirect_uri,$snsapi_base='snsapi_base') @param 回调地址,获取方式				用户同意授权，获取code
 * wxCodeExchangeForAccessToken($code) 				   @param 获取到的Code					通过code换取网页授权access_token
 * wxAccessTokenRefresh($refresh_token) 			   @param 需要刷新的access_token		刷新access_token（如果需要）
 * wxGetUserInfo($access_token,$openid) 			   @param access_token,openid 			拉取用户信息(需scope为 snsapi_userinfo)
 * wxCheckAccessToken($access_token,$openid) 		   @param access_token,openid			检验授权凭证（access_token）是否有效
 */
class WeixinFunctions
{
	// =======【基本信息设置】=====================================
	// 微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	private $appid;  		//公众号唯一标识
	//受理商ID，身份标识
	private $mchid; 		//商户号
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	private $key; 			//商户支付密钥
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	private $secret; 		//公众号的appsecret
	// =======【证书路径设置】=====================================
	// 证书路径,注意应该填写绝对路径
	// private $sslcertPath = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_cert.pem';  
	// private $sslkeyPath = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_key.pem'; 
	private $curlProxyHost = '0.0.0.0';
	private $curlProxyRort = '0';
	/**
	 * 支付相关参数
	 */
	private $Payparam; 		//支付信息数组
	private $PayPreId; 		//支付接口得到的预支付id
	private $PayUrl = 'https://api.mch.weixin.qq.com/pay/unifiedorder';//支付接口链接
	/**
	 * 订单
	 */
	private $OrderParam;    //订单信息数组
	private $OrderUrl = 'https://api.mch.weixin.qq.com/pay/orderquery';//订单接口链接

	/**
	 * 退款申请
	 */
	private $RefundParam;   //退款信息数组
	private $RefundUrl = "https://api.mch.weixin.qq.com/secapi/pay/refund"; //退款接口链接


	/**
	 * 退款查询
	 */
	private $RefundqueryParam;   //退款信息数组
	private $RefundqueryUrl = "https://api.mch.weixin.qq.com/pay/refundquery"; //退款接口链接

	/**
	 * 对账单接口
	 */
	private $DownloadbillParam;   //退款信息数组
	private $DownloadbillUrl = "https://api.mch.weixin.qq.com/pay/downloadbill"; //退款接口链接


	/**
	 * [__construct 初始化,定义相关配置]
	 * @param [type] $Config [配置数组]
	 */
	public function __construct($Config)
	{
		isset($Config['appid'])?$this->appid=$Config['appid']:$this->appid=0;
		isset($Config['secret'])?$this->secret=$Config['secret']:$this->secret=0;
		isset($Config['mchid'])?$this->mchid=$Config['mchid']:$this->mchid=0;
		isset($Config['key'])?$this->key=$Config['key']:$this->key=0;
		
	}
	/**
	 * [WxGetAccessToken description]
	 * @param  [string] $access_token [access_token]
	 * @return [Arr]               [查询结果]
	 */
	public function WxGetAccessToken()
	{
		// 获取access_token填写client_credential (必须)
		$grant_type = "client_credential";
		$link = "https://api.weixin.qq.com/cgi-bin/token?grant_type=".$grant_type."&appid=".$this->appid."&secret=".$this->secret;
		return $this->JsonDencode($this->GetCurl($link));
	}
	/**
	 * [WxJsapiTicket description]
	 * @param  [string] $access_token [access_token]
	 * @return [Arr]               [查询结果]
	 * 
	 */
	public function WxJsapiTicket($access_token)
	{
		// 获取access_token填写client_credential (必须)
		$link = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
		return $this->JsonDencode($this->GetCurl($link));
	}
	/**
	 * [wxGetUserList 获取关注用户列表]
	 * @param  [string] $access_token [access_token]
	 * @return [Arr]               [查询结果]
	 */
	public function wxGetUserList($access_token)
	{
		$link = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token;
		return $this->JsonDencode($this->GetCurl($link));
	}
	/**
	 * [wxGetUserShow description]
	 * @param  [string] $access_token [access_token]
	 * @param  [string] $openid       [用户Openid]
	 * @return [Arr]               [查询结果]
	 */
	public function wxGetUserShow($access_token,$openid)
	{
		$lang = "zh_CN";
		$link = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=".$lang;
		return $this->JsonDencode($this->GetCurl($link));
	}
	/**
	 * [weixinMst 微信推送消息]
	 * @param  [type] $token       [调用接口凭证]
	 * @param  [type] $data        [发送数据]
	 * @param  [type] $template_id [模板ID]
	 * @param  [type] $openid      [已关注公众号用户的openid]
	 * @return [type]              [返回发送结果]
	 */
    public function wxMsg($token,$data,$template_id,$openid,$url='',$topcolor='#FF0000')
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token;
        $template_msg=array('touser'=>$openid,'template_id'=>$template_id,'topcolor'=>$topcolor,'data'=>$data,'url'=>$url);    
        return $this->JsonDencode($this->PostCurl($link,$this->JsonEncode($template_msg)));
    }


    

    /**
     * 微信JSAPI支付 
     * 支付页面引用 	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
     * 支付页面支付按钮调用函数
	 * function weixinPay()
	 * {
	 * 	WeixinJSBridge.invoke(
	 * 		'getBrandWCPayRequest',
	 * 		{
	 * 			"appId":data.appId,
	 * 			"timeStamp":data.timeStamp,
	 * 			"nonceStr":data.nonceStr,
	 * 			"package":data.package,
	 * 			"signType":data.signType,
	 * 			"paySign":data.paySign,
	 * 		},
	 * 		function(res){
	 * 			WeixinJSBridge.log(res.err_msg);
	 * 			//alert(res.err_code+res.err_desc+res.err_msg);
	 * 			if(res.err_msg == 'get_brand_wcpay_request:cancel'){
	 * 				 * 用户取消支付事务操作
	 * 			}else if(res.err_msg == 'get_brand_wcpay_request:ok'){
	 * 				 * 用户支付成功事务操作
	 * 			}else{
	 * 				 * 其他状态事务操作
	 * 			}
	 * 		}
	 * 	);
	 * }
	 *
	 *	$WxFun = new /WeixinFunctions;
     *  $WxFun->setParam("openid",var string); 						//下单用户的openid
     *  $WxFun->setParam("body",var string);				 		//商品描述
     *  $WxFun->setParam("out_trade_no",var string); 				//订单号 
     *  $WxFun->setParam("total_fee",var int);	 					//总金额 单位：分
     *  $WxFun->setParam("notify_url",var string);					//异步处理地址 
     *  $WxFun->setParam("trade_type","JSAPI"); 					//交易类型
	 *  $WxFun->setPayPrepayId($WxFun->getPayPrepayId());				//支付接口得到的预支付id
	 *  $return = $WxFun->getPayParam();								//取得需要的Json数据
     */
	/**
	 * 生成支付接口参数xml
	 */
	public function createPayXml()
	{
		//检测必填参数
		if($this->Payparam["out_trade_no"] == null){
			die("缺少统一支付接口必填参数out_trade_no！"."<br>");
		}elseif($this->Payparam["body"] == null){
			die("缺少统一支付接口必填参数body！"."<br>");
		}elseif ($this->Payparam["total_fee"] == null ) {
			die("缺少统一支付接口必填参数total_fee！"."<br>");
		}elseif ($this->Payparam["notify_url"] == null) {
			die("缺少统一支付接口必填参数notify_url！"."<br>");
		}elseif ($this->Payparam["trade_type"] == null) {
			die("缺少统一支付接口必填参数trade_type！"."<br>");
		}elseif ($this->Payparam["trade_type"] == "JSAPI" && $this->parameters["openid"] == NULL){
			die("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！"."<br>");
		}else if($this->Payparam['trade_type'] == "NATIVE" && !$this->Payparam['product_id']){
			die("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
		}
	   	$this->Payparam["appid"] = $this->appid;							//公众账号ID
	   	$this->Payparam["mch_id"] = $this->mchid; 							//商户号
	   	$this->Payparam["spbill_create_ip"] = $_SERVER['REMOTE_ADDR'];		//终端ip	    
	    $this->Payparam["nonce_str"] = $this->createNoncestr();				//随机字符串
	    $this->Payparam["sign"] = $this->getSign($this->Payparam);			//签名
	    return  $this->arrayToXml($this->Payparam);
	}
	/**
	 * 获取prepay_id
	 */
	public function getPayPrepayId()
	{
		$return = $this->xmlToArray($this->PostCurl($this->PayUrl,$this->createPayXml()));
		$prepay_id = $return["prepay_id"];
		return $prepay_id;
	}
	/**
	 * 	作用：设置prepay_id
	 */
	public function setPayPrepayId($prepayId)
	{
		$this->PayPreId = $prepayId;
	}
	/**
	 * 	作用：设置jsapi的参数
	 */
	public function getPayParam()
	{
		$jsApiObj["appId"] = $this->appid;
	    $jsApiObj["timeStamp"] = time();
	    $jsApiObj["nonceStr"] = $this->createNoncestr();
		$jsApiObj["package"] = "prepay_id=".$this->PayPreId;
	    $jsApiObj["signType"] = "MD5";
	    $jsApiObj["paySign"] = $this->getSign($jsApiObj);
		return $this->JsonEncode($jsApiObj);
	}

	/**
	 * [GetPayUrl description]
     * $WxFun = new \WeixinFunctions($WxConfig);
     * $WxFun->setPayParam("body","test");
     * $WxFun->setPayParam("attach","test");
     * $WxFun->setPayParam("out_trade_no",time().date('YmdHis',time()).rand(10000,9999));
     * $WxFun->setPayParam("total_fee",1);
     * $WxFun->setPayParam('time_start',date("YmdHis",time()));
     * $WxFun->setPayParam('time_expire',date("YmdHis",time()+600));
     * $WxFun->setPayParam('notify_url','http://www.armbtb.com/notify.php');
     * $WxFun->setPayParam('trade_type','NATIVE');
     * $WxFun->setPayParam('product_id',time());
     * $ret =  $WxFun->GetPayUrl();
	 */
	public function GetPayUrl()
	{
		return $this->xmlToArray($this->PostCurl($this->PayUrl,$this->createPayXml()));
	}
	/**
	 * 	作用：设置请求参数
	 */
	public function setPayParam($PayparamK, $PayparamV)
	{
		$this->Payparam[$this->trimString($PayparamK)] = $this->trimString($PayparamV);
	}

	/**
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * 对账单接口
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 */
	/**
	 * 生成接口参数xml
	 */
	public function createDownloadbillXml()
	{
		if($this->DownloadbillParam["out_trade_no"] == null &&  $this->DownloadbillParam["transaction_id"] == null){
			die("订单查询接口中，out_trade_no、transaction_id至少填一个！"."<br>");
		}
	   	$this->DownloadbillParam["appid"] = $this->appid;//公众账号ID
	   	$this->DownloadbillParam["mch_id"] = $this->mchid;//商户号
	    $this->DownloadbillParam["nonce_str"] = $this->createNoncestr();//随机字符串
	    $this->DownloadbillParam["sign"] = $this->getSign($this->DownloadbillParam);//签名
	    return  $this->arrayToXml($this->DownloadbillParam);
	}
	/**
	 * 	作用：获取结果，使用证书通信
	 */
	public function getDownloadbillResult()
	{
		return $this->xmlToArray($this->PostSSLCurl($this->DownloadbillUrl,$this->createDownloadbillXml()));
	}
	/**
	 * 	作用：设置请求参数
	 */
	public function setDownloadbillParam($PayparamK, $PayparamV)
	{
		$this->Payparam[$this->trimString($PayparamK)] = $this->trimString($PayparamV);
	}
	/**
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * 退款查询
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 */
	/**
	 * 生成接口参数xml
	 */
	public function createRefundqueryXml()
	{		
		if($this->RefundqueryParam["out_refund_no"] == null && $this->RefundqueryParam["out_trade_no"] == null && $this->RefundqueryParam["transaction_id"] == null && $this->RefundqueryParam["refund_id "] == null){
			die("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！"."<br>");
		}
	   	$this->RefundqueryParam["appid"] = $this->appid;//公众账号ID
	   	$this->RefundqueryParam["mch_id"] = $this->mchid;//商户号
	    $this->RefundqueryParam["nonce_str"] = $this->createNoncestr();//随机字符串
	    $this->RefundqueryParam["sign"] = $this->getSign($this->RefundqueryParam);//签名
	    return  $this->arrayToXml($this->RefundqueryParam);
	}
	/**
	 * 	作用：获取结果，使用证书通信
	 */
	public function getRefundqueryResult()
	{
		return $this->xmlToArray($this->PostSSLCurl($this->RefundqueryUrl,$this->createRefundqueryXml()));
	}
	/**
	 * 	作用：设置请求参数
	 */
	public function setRefundqueryParam($PayparamK, $PayparamV)
	{
		$this->Payparam[$this->trimString($PayparamK)] = $this->trimString($PayparamV);
	}
	/**
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * 退款
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 */
	/**
	 * 生成接口参数xml
	 */
	public function createRefundXml()
	{
		if($this->RefundParam["out_trade_no"] == null && $this->RefundParam["transaction_id"] == null) {
			die("退款申请接口中，out_trade_no、transaction_id至少填一个！"."<br>");
		}elseif($this->RefundParam["out_refund_no"] == null){
			die("退款申请接口中，缺少必填参数out_refund_no！"."<br>");
		}elseif($this->RefundParam["total_fee"] == null){
			die("退款申请接口中，缺少必填参数total_fee！"."<br>");
		}elseif($this->RefundParam["refund_fee"] == null){
			die("退款申请接口中，缺少必填参数refund_fee！"."<br>");
		}elseif($this->RefundParam["op_user_id"] == null){
			die("退款申请接口中，缺少必填参数op_user_id！"."<br>");
		}
	   	$this->RefundParam["appid"] = $this->appid;//公众账号ID
	   	$this->RefundParam["mch_id"] = $this->mchid;//商户号
	    $this->RefundParam["nonce_str"] = $this->createNoncestr();//随机字符串
	    $this->RefundParam["sign"] = $this->getSign($this->RefundParam);//签名
	    return  $this->arrayToXml($this->RefundParam);
	}
	/**
	 * 	作用：获取结果，使用证书通信
	 */
	public function getRefundResult()
	{
		return $this->xmlToArray($this->PostSSLCurl($this->RefundUrl,$this->createRefundXml()));
	}
	/**
	 * 	作用：设置请求参数
	 */
	public function setRefundParam($PayparamK, $PayparamV)
	{
		$this->Payparam[$this->trimString($PayparamK)] = $this->trimString($PayparamV);
	}
	/**
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * 订单
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 */
	/**
	 * 生成接口参数xml
	 */
	public function createOrderXml()
	{
		//检测必填参数
		if($this->OrderParam["out_trade_no"] == null && $this->OrderParam["transaction_id"] == null) {
			die("订单查询接口中，out_trade_no、transaction_id至少填一个！"."<br>");
		}
	   	$this->OrderParam["appid"] = $this->appid;//公众账号ID
	   	$this->OrderParam["mch_id"] = $this->mchid;//商户号
	    $this->OrderParam["nonce_str"] = $this->createNoncestr();//随机字符串
	    $this->OrderParam["sign"] = $this->getSign($this->OrderParam);//签名
	    return  $this->arrayToXml($this->OrderParam);
	}
	/**
	 * 	作用：获取结果，使用证书通信
	 */
	public function getOrderResult()
	{
		return $this->xmlToArray($this->PostSSLCurl($this->OrderUrl,$this->createOrderXml()));
	}
	/**
	 * 	作用：设置请求参数
	 */
	public function setOrderParam($PayparamK, $PayparamV)
	{
		$this->OrderParam[$this->trimString($PayparamK)] = $this->trimString($PayparamV);
	}
    /**
     * 微信登陆相关函数
     */
	/**
	 * 第一步：用户同意授权，获取code
	 * 在确保微信公众账号拥有授权作用域（scope参数）的权限的前提下（服务号获得高级接口后，默认拥有scope参数中的snsapi_base和snsapi_userinfo）
	 * @param  [string redirect_uri 授权后重定向的回调链接地址，请使用urlEncode对链接进行处理]
	 * @param  [string snsapi_base 应用授权作用域， (必须) 
	 *                             	snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），
	 * 	                            snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息）
	 * ]
	 * @return [Null]
	 * 如果用户同意授权，页面将跳转至 $redirect_uri/?code=CODE&state=$state
	 */
	public function wxGetCode($redirect_uri,$snsapi_base='snsapi_base')
	{
		// 重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节 (非必需)
		$state 			 = time();
		// 无论直接打开还是做页面302重定向时候，必须带此参数 (必须)
		$wechat_redirect = "#wechat_redirect";
		// 拼接跳转链接
		$link = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=".$snsapi_base."&state=".$state.$wechat_redirect;
		// 跳转授权链接
		header("Location: $link");
	}
	/**
	 * 第二步：通过code换取网页授权access_token
	 * 这里通过code换取的是一个特殊的网页授权access_token,与基础支持中的access_token（该access_token用于调用其他接口）不同。
	 * 公众号可通过下述接口来获取网页授权access_token。
	 * 如果网页授权的作用域为snsapi_base，则本步骤中获取到网页授权access_token的同时，也获取到了openid，snsapi_base式的网页授权流程即到此为止。
	 * @param  [string $code 填写第一步获取的code参数]
	 * @return [Array $jsoninfo 请求返回的数组信息]
	 */
	public function wxCodeExchangeForAccessToken($code)
	{
		//填写为 authorization_code (必须)
		$grant_type 	= "authorization_code";
		$link = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->secret."&code=".$code."&grant_type=".$grant_type;
		return $this->JsonDencode($this->GetCurl($link));
	}

	/**
	 * 第三步：刷新access_token（如果需要）
	 * 由于access_token拥有较短的有效期，当access_token超时后，
	 * 可以使用refresh_token进行刷新，refresh_token有效期为30天，
	 * 当refresh_token失效之后，需要用户重新授权。
	 * @param  [string $refresh_token 填写通过access_token获取到的refresh_token参数 ]
	 * @return [Array $jsoninfo 请求返回的数组信息]
	 */
	public function wxAccessTokenRefresh($refresh_token)
	{
		//填写为 refresh_token (必须)
		$grant_type 	= "refresh_token";
		$link = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$this->appid."&grant_type=".$grant_type."&refresh_token=".$refresh_token;
		return $this->JsonDencode($this->GetCurl($link));
	}

	/**
	 * 第四步：拉取用户信息(需scope为 snsapi_userinfo)
	 * 如果网页授权作用域为snsapi_userinfo，则此时开发者可以通过access_token和openid拉取用户信息了。
	 * @param  [string access_token 网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同]
	 * @param  [string openid 用户的唯一标识]
	 * @return [Array $jsoninfo 请求返回的数组信息]
	 */
	public function wxGetUserInfo($access_token,$openid)
	{
		$lang = "zh_CN";
		$link = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=".$lang;
		return $this->JsonDencode($this->GetCurl($link));
	}

	/**
	 * 附：检验授权凭证（access_token）是否有效
	 * @param  [string access_token 网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同]
	 * @param  [string openid 用户的唯一标识]
	 * @return [Array $jsoninfo 请求返回的数组信息]
	 */
	public function wxCheckAccessToken($access_token,$openid)
	{
		$link = "https://api.weixin.qq.com/sns/auth?access_token=".$access_token."&openid=".$openid;
		return $this->JsonDencode($this->GetCurl($link));
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
	/**
	 * [PostCurl 模拟POST请求]
	 * @param [string] $link [请求地址]
	 * @param [array]  $data [提交的数据]
	 * @return [type] [请求结果]
	 */
	public function PostCurl($link,$data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $link);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$return = curl_exec($ch);
		if($return){
			curl_close($ch);
			return $return;
		} else { 
			$error = curl_errno($ch);
			curl_close($ch);
		}
	}
	public function PostSSLCurl($xml,$url)
	{
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT,$this->sslcertPath);
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY,$this->sslkeyPath);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		$data = curl_exec($ch);
		if($data){
			curl_close($ch);
			return $data;
		}
		else { 
			$error = curl_errno($ch);
			curl_close($ch);
			die;
		}
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
	 * 	作用：产生随机字符串，不长于32位
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
	 * 	作用：格式化参数，签名过程需要使用
	 */
	public function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}
	/**
	 * 	作用：生成签名
	 */
	public function getSign($Obj)
	{
		$Parameters = array();
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$this->key;
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
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
	 * 重复调用函数，利用php原生函数处理数据信息
	 * @param  [arr $arr 需要处理的数组数据]
	 * @return [json $json 返回的数组处理之后的json]
	 */
	public function JsonEncode($arr)
	{
		return json_encode($arr,true);
	}
	/**
	 * 	作用：array转xml
	 */
	public function arrayToXml($arr)
    {
		if(!is_array($arr) || count($arr) <= 0)
		{
    		die("数组数据异常！");
    	}
    	$xml = "<xml>";
    	foreach ($arr as $key=>$val)
    	{
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml; 
    }
	/**
	 * 	作用：将xml转为array
	 */
	public function xmlToArray($xml)
	{		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}
    /**
     * [SetWebServiceForWxToken 设置微信服务器配置的Token]
     * @param [string] $Token [设置的Token值]
     */
	public function SetWebServiceForWxToken($Token)
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature($Token)){
        	echo $echoStr;
        	exit;
        }
    }
    /**
     * [checkSignature 检查Token签名]
     * @param  [string] $Token [设置的Token值]
     * @return [boole]         [执行结果]
     */
	private function checkSignature($Token)
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
		$token = $Token;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
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