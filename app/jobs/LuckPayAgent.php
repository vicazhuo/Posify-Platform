<?php

namespace app\jobs;

use app\services\activity\combination\StoreCombinationLucklyServices;
use app\services\activity\combination\StorePinkLucklyServices;
use app\services\user\UserServices;
use crmeb\basic\BaseJobs;
use crmeb\services\CacheService;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class LuckPayAgent extends BaseJobs
{
    use QueueTrait;

    public function doJob($uids, $paybs, $luck_no)
    {
        try {
            $ids = implode(',', $uids);
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $list = $userServices->getAboveAgents($ids);
            $brokerageOne = sys_config('store_brokerage_ratio');
            $brokerageTwo = sys_config('store_brokerage_two');
            $agents_a = [];
            $agents_b = [];
            foreach ($list as $item => $value) {
                $a = isset($value['auid']) ? $value['auid'] : 0;
                $b = isset($value['buid']) ? $value['buid'] : 0;
                if ($a != 0) {
                    if (isset($agents_a[$a])) {
                        $agents_a[$a] = $agents_a[$a] + ($paybs / $brokerageOne);
                    } else {
                        $agents_a[$a] = $paybs / $brokerageOne;
                    }
                }
                if (!empty($b)) {
                    if (isset($agents_a[$b])) {
                        $agents_a[$a] = $agents_a[$a] + ($paybs / $brokerageTwo);
                    } else {
                        if (isset($agents_b[$b])) {
                            $agents_b[$b] = $agents_b[$b] + ($paybs / $brokerageOne);
                        } else {
                            $agents_b[$b] = $paybs / $brokerageOne;
                        }
                    }
                }
            }
            Log::record("===================a", "debug");
            Log::record($agents_a, "debug");
            Log::record("===================b", "debug");
            Log::record($agents_b, "debug");
            //支付二级推广佣金
            foreach ($agents_a as $uid => $bonus) {
                $bonus = number_format($bonus, 2);
                if ($uid > 0) {

                    LuckPay::dispatch([$uid, $bonus, 0, $luck_no, 'get_pink_agent']);
                }
            }
            //支付二级推广佣金
            foreach ($agents_b as $uid => $bonus) {
                $bonus = number_format($bonus, 2);
                if ($uid > 0) {
                    LuckPay::dispatch([$uid, $bonus, 0, $luck_no, 'get_pink_agent']);
                }
            }

        } catch (\Exception $e) {

            Log::record($e->getMessage(), "debug");
        }
        return true;
    }
}
