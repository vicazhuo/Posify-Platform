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

use app\services\other\FollowGroupServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class PinkCreateJob extends BaseJobs
{
    use QueueTrait;

    public function doJob($uid, $productId, $combinationId,$num,$luck_no)
    {
        /** @var FollowGroupServices $service */
        $service = app()->make(FollowGroupServices::class);
        try {
            $service->copyCart($uid, $productId, $combinationId, $num, $luck_no);
        } catch (\Exception $e) {
            Log::record($e->getMessage(), "debug");
        }
        return true;
    }
}
