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

use app\services\activity\combination\StorePinkLucklyServices;
use app\services\user\UserFollowServices;
use app\services\user\UserServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class PinkFollwerJob extends BaseJobs
{
    use QueueTrait;

    /**
     * 异步创建一条拼团数据
     * @param $combinationId
     * @param $bonus
     * @param $luck_no
     * @param $next_time
     * @param $price
     * @param $people
     * @return bool
     */
    public function doJob($combinationId, $bonus, $luck_no, $next_time, $price, $people)
    {
        //查找跟团的人
        /** @var UserFollowServices $UserFollowServices */
        $UserFollowServices = app()->make(UserFollowServices::class);
        $list = $UserFollowServices->getPinkList(['cid' => $combinationId, 'is_follow' => 1]);
        /** @var StorePinkLucklyServices $storePinkLucklyServices */
        $storePinkLucklyServices = app()->make(StorePinkLucklyServices::class);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        foreach ($list as &$item) {
            $uid = $item['uid'];
            $user = $userServices->getUserInfo($uid, 'uid,now_money');
            $pay_total = $price * intval($item['num']);
            if ($user) {
                $storePinkLucklyServices->autocreatePinkLuckly([
                    'uid' => $uid,
                    'cid' => $combinationId,
                    'num' => $item['num'],
                    'luck_no' => $luck_no,
                    'price' => $price,
                    'total_bonus' => $bonus,
                    'next_time' => date('Y-m-d H:i:s', $next_time),
                    'pay_total' => $pay_total,
                    'people' => $people,
                    'add_time' => time(),
                    'status' => 0,
                ],$user,$item['id']);
            }
        }
        return true;
    }
}
