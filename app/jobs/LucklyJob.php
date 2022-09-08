<?php
namespace app\jobs;

use app\services\activity\combination\StoreCombinationLucklyServices;
use app\services\activity\combination\StorePinkLucklyServices;
use crmeb\basic\BaseJobs;
use crmeb\services\CacheService;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class LucklyJob extends BaseJobs
{
    use QueueTrait;

    public function doJob($param)
    {
        try {

            $id = (int)$param['id'];
            $cid = (int)$param['cid'];
            $curranty_time = date("Y-m-d H:i:s", time());
            $cron_type = isset($param['cron_type']) ? $param['cron_type'] : "old";
            Log::record("运行时间============>> $curranty_time ========> $cid ===============> $cron_type", "debug");
            if ($cron_type == 'Redis') {
                self::dispatchSece($param['runTime'], [$param]);
            }

            /** @var StoreCombinationLucklyServices $storeCombinationLucklyServices */
            $storeCombinationLucklyServices = app()->make(StoreCombinationLucklyServices::class);
            $luckly = $storeCombinationLucklyServices->get($id);
            $bonus = $luckly->bonus;
            $luck_no = $luckly->luck_no;
            $people = $luckly->people;
            $luck_total = $luckly->luck_total;
            $product_id = $luckly->product_id;
            $price = $luckly->price;

            /** @var StorePinkLucklyServices $storePinkLucklyServices */
            $storePinkLucklyServices = app()->make(StorePinkLucklyServices::class);
            $list = $storePinkLucklyServices->getPinkLucklyList(['cid' => $cid, 'luck_no' => $luck_no, 'status' => 0]);
            $total = count($list);
            if ($total > $luck_total && $total >= $people) {
                //拼中商品的人数
                $luckCount = 0;
                $refundCount = 0;
                $totalGroup = 0;
                if ($total > $people) {
                    $totalGroup = $total / $people;
                    $luckCount = $luck_total * intval($totalGroup);
                    $refundCount = $total % $people;
                } else {
                    $luckCount = $luck_total;
                }
                if ($refundCount > 0) {
                    $das = $list;
                    for ($i = $total; $i > ($total - $refundCount); $i--) {
                        $j = rand(0, ($total - 1));
                        $v = $das[$j];
                        LuckRefund::dispatchSece(1,[$v['uid'], $v['pay_total'], $v['id'], $v['luck_no'], 'pay_pinkf_product_refund']);
                        unset($list[$j]);
                    }
                }
                // Log::record("luck_no:" . $luck_no . "---total: $total ---people:" . $people . "---luck_total:" . $luck_total, "debug");
                if ($luckCount > 0) {
                    $luckIds = $total > 2 && $luckCount > 1 ? array_rand($list, $luckCount) : rand(0, 1);
                    $luckCount = is_array($luckIds) ? count($luckIds) : 1;
                    $bs = $people - $luck_total;
                    $paybs = number_format(($bonus / $bs), 2);
                    Log::record("totao :  $total   totalGroup:" . $totalGroup . "---luckCount: $luckCount ---bonus:" . $bonus . "--pay:" . $paybs, "debug");
                    $uids = [];
                    foreach ($list as $k => $v) {
                        $isLuck = false;
                        $isLuck = is_array($luckIds) ? in_array($k, $luckIds) : $k == $luckIds;
                        if ($isLuck) {
                            $storePinkLucklyServices->goodLuck($v['uid'], $product_id, $cid, $v['id'], $v['num'], $v['luck_no']);
                        } else {
                            LuckPay::dispatch([$v['uid'], $paybs, $v['id'], $v['luck_no'], 'get_pink_brokerage']);
                            LuckRefund::dispatchSece(3,[$v['uid'], $v['pay_total'], $v['id'], $v['luck_no'], 'pay_pinks_product_refund']);
                            $uids[] = $v['uid'];
                        }
                    }
                    LuckPayAgent::dispatch([$uids, $paybs, $luck_no]);
                }

            } else if (count($list) > 0) {
                //退款
                foreach ($list as $k => $v) {
                    if ($v['status'] == 0) {
                        LuckRefund::dispatch([$v['uid'], $v['pay_total'], $v['id'], $v['luck_no'], 'pay_pinkf_product_refund']);
                    }
                }
            }
            if ($luckly) {
                $luckly->luck_no = $luck_no + 1;
                if ($luckly->stop_time >= time()) {
                    $luckly->next_time = time() + ($luckly->exprect_time * 60);
                    $luckly->save();
                    CacheService::set("pink_luckly_" . $id, $luckly->luck_no, 3600);
                    PinkFollwerJob::dispatchSece(3, [$cid, $bonus, $luckly->luck_no, $luckly->next_time, $price, $people]);
                }
                $reluck_no = $luckly->luck_no - 1;
                Log::record("开始检查 :  ". $luckly->luck_no."   检查期数: $reluck_no  ", "debug");
                LucklyCheckJob::dispatchSece(10, [$cid, $reluck_no]);
            }

        } catch (\Exception $e) {

            Log::record($e->getMessage(), "debug");
        }
        return true;
    }
}
