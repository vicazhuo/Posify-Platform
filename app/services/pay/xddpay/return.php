<?php
/**
 * ---------------------支付成功，用户会跳转到这里-------------------------------
 * 
 * 此页就是接口后台的return_url页的网址
 * 支付成功，平台会把用户跳转回这里。
 * 
 * --------------------------------------------------------------
 */
	include 'define.php';

	//同步回调页面
	//接收参数
	$order_no = $_GET["order_no"];
	$subject = $_GET["subject"];
	$pay_type = $_GET["pay_type"];
	$money = $_GET["money"];
	$realmoney = $_GET["realmoney"];
	$result = $_GET["result"];
	$xddpay_order = $_GET["xddpay_order"];
	$app_id = $_GET["app_id"];
	$extra = $_GET["extra"];
	$sign = $_GET["sign"];
    
	//计算签名
	$mysign_forstr = "order_no=" . $order_no . "&subject=" . $subject . "&pay_type=" . $pay_type . "&money=" . $money . "&realmoney=" . $realmoney . "&result=" . $result . "&xddpay_order=" . $xddpay_order . "&app_id=" . $app_id . "&extra=" . $extra . "&" . $app_secret;
	$mysign = strtoupper(md5($mysign_forstr));
	
	if ($sign == $mysign) //验签
	{
		if ($result == "success"){
			//建议业务处理放在notify.asp页面，本页仅用于显示支付结果
			//此处在您数据库中查询：此笔订单号是否已经异步通知给您付款成功了。如成功了，就给他返回一个支付成功的展示。
			echo "恭喜，支付成功!，订单号：".$order_no;
		}
		else{
			echo "支付失败";
		}
	}
	else
	{
		echo "mysign_forstr=" . $mysign_forstr;	//调试时开启
		echo "<br>sign=" . $sign;
		echo "<br>mysign=" . $mysign;
		echo "<br><br>认证签名失败";
	}

?>