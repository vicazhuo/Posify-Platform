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
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * 异步检查是否存在超时未结算的拼团数据
 * 异步检查拼团返佣产品的延迟队列是否停止
 * Class LucklyCheckJob
 * @package app\jobs
 */
class LucklyCheckJob extends BaseJobs
{
    use QueueTrait;

    public function doJob($id,$luck_no)
    {
        //Log::record("开始检查", "debug");
        /** @var StorePinkLucklyServices $storePinkLucklyServices */
        $storePinkLucklyServices = app()->make(StorePinkLucklyServices::class);
        $list = $storePinkLucklyServices->getPinkLucklyList(['cid' => $cid, ['luck_no','<', $luck_no], 'status' => 0]);
        $count =  count($list);
        //Log::record("checkTotal: $count --  $luck_no 期", "debug");
        foreach ($list as &$item){
            $storePinkLucklyServices->payRefund((int)$item['uid'], $item['pay_total'], $item['id'], $item['luck_no'], 'pay_pinkf_product_refund');
        }

        return true;
    }
}
