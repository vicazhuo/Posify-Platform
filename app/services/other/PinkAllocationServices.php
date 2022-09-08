<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app\services\other;

use app\services\BaseServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserServices;
use crmeb\services\CacheService;
use think\facade\Log;

/**
 *拼团分佣，退款
 * Class PinkAllocationServices
 * @package app\services\other
 */
class PinkAllocationServices extends BaseServices
{

    public function newRefund($oid,$uid, $refund_price)
    {

           $refundData = [
                'refund_reason' => "拼团返佣未拼中退款到余额",
                'refund_explain' => '拼团返佣未拼中退款到余额',
                'refund_img' => '',
            ];
            /** @var StoreOrderServices $storeOrderServices */
            $storeOrderServices =app()->make(StoreOrderServices::class);
            $order = $storeOrderServices->get($oid);
            /** @var StoreOrderRefundServices $services */
            $services =app()->make(StoreOrderRefundServices::class);
            $refindId = $services->applyRefundV2((int)$oid, $uid, [], [], 1, (float)$refund_price, $refundData,1);
            $RefdndData =$services->get(['order_id'=>$refindId]);
            $res =$services->payOrderRefund(1,$order,$RefdndData->toArray());
            $refund_data['pay_price'] = $order['pay_price'];
            $refund_data['refund_price'] = $refund_price;
            if ($order['refund_price'] > 0) {
                $refund_data['refund_id'] = $order['order_id'] . rand(100, 999);
            }
           $RefdndData =$services->get(['order_id'=>$refindId]);
           $RefdndData->refund_status = 2;
           if ($RefdndData->save()) {
               $services->storeProductOrderRefundY($refund_data, $order, $refund_price);
               Log::record("用户ID $uid 拼团失败退款 $refund_price 元", 'debug');
           }
    }

    /**
     *
     * 拼团不中分配奖金
     * @param $pinkAll
     * @param $pink
     * @param $product
     */
    public function payBonus($list, $lucklyToTal, $bonus)
    {
        if ($lucklyToTal < 1) {
            return false;
        }
        if ($lucklyToTal == 1) {
            $lkIds[]= mt_rand(0, count($list) - $lucklyToTal);
        } else {
            $lkIds= array_rand($list, $lucklyToTal);
        }
        $lucklysBonus = count($list);
        //计算平均奖金金额
        $egBonus = floatval($bonus) / intval($lucklysBonus-count($lkIds));
        $luckUserList= [];
        foreach ($list as $k => $v) {
            //退款，分配奖金
            if (!in_array($k, $lkIds)) {
                $bonus = round($egBonus,2);
                $this->payBrokerage($v['uid'],$bonus,$bonus,$v['order_id_key']);
                $this->newRefund($v['order_id_key'],$v['uid'],$v['total_price']);
            }
        }
    }
    private function payBrokerage($uid,$bonus,$linkId){

        /** @var UserBrokerageServices $BrokerageServices */
        $BrokerageServices = app()->make(UserBrokerageServices::class);
        $num = ['number'=>$bonus];
        $BrokerageServices->income('get_pink_brokerage',$uid,$num, $bonus,$linkId);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if($userServices->bcInc($uid, 'brokerage_price',$bonus, 'uid')){
            Log::record("用户ID $uid 分配奖金 $bonus 元", 'debug');
        }

    }

}
