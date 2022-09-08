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

namespace app\jobs;


use app\services\activity\combination\StoreCombinationLucklyServices;
use app\services\activity\combination\StorePinkLucklyServices;
use crmeb\basic\BaseJobs;
use crmeb\services\CacheService;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class LuckPay extends BaseJobs
{
    use QueueTrait;

    public function doJob($uid, $paybs, $id, $luck_no, $tpl)
    {
        try {
            /** @var StorePinkLucklyServices $storePinkLucklyServices */
            $storePinkLucklyServices = app()->make(StorePinkLucklyServices::class);
            if ($tpl == 'get_pink_brokerage') {
                $key = "paying_" . $uid . "_" . $id . "_" . $luck_no;
                if (CacheService::get($key)) {
                    return true;
                }
                CacheService::set($key, 1, 60);
            } else {

                $key = "payagenting_" . $uid . "_" . $id . "_" . $luck_no;
                if (CacheService::get($key)) {
                    return true;
                }
                CacheService::set($key, 1, 60);
            }
            Log::record("g=========== $key", "debug");
            $storePinkLucklyServices->payBonus($uid, $paybs, $id, $luck_no, $tpl);

        } catch (\Exception $e) {

            Log::record($e->getMessage(), "debug");
        }
        return true;
    }
}
