<?php
/**
 * Notes:
 * User: azhuo
 * Company:APM monaco
 * Auther:Awin zhuo
 * DateTime: 2022/5/6 11:39
 * @return
 */

namespace app\services\pay;


use app\services\BaseServices;

class XddpayServices extends BaseServices
{
    /**
     * @param string $subject
     * @param float $money
     * @param string $extra
     * @return array
     */
    public static function sign(string $subject,float $money,string $extra){

        $app_id =config('pay.xddpay.app_id');
        $app_secret =config('pay.xddpay.app_secret');
        $order_no = self::createrOrderNo();
        $pay_type = 44; //43 alipay  44 wechatpay
        $sign = md5("order_no=" . $order_no . "&subject=" . $subject . "&pay_type=" . $pay_type . "&money=" . $money . "&app_id=" . $app_id . "&extra=" . $extra . "&" . $app_secret);
        //经常遇到有研发问为啥sign值返回错误，大多数原因：1.参数的排列顺序不对；2.上面的参数少传了，但是这里的sign值又带进去计算了，导致服务端sign算出来和你的不一样。

        $returndata['order_no'] = $order_no;
        $returndata['subject'] = $subject;
        $returndata['pay_type'] = $pay_type;
        $returndata['money'] = $money;
        $returndata['app_id'] = $app_id;
        $returndata['extra'] =$extra;
        $returndata['sign'] =$sign;

        return $returndata;
    }

    private static function createrOrderNo(){

        return '1352334968593'.mt_rand(10000,99999);

    }

}
