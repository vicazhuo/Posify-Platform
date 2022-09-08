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

namespace app\api\controller\v2\order;

use app\services\order\StoreOrderServices;
use app\services\other\FollowGroupServices;
use crmeb\services\CacheService;
use think\Request;

/**
 * Class StoreOrderController
 * @package app\api\controller\v2\order
 */
class StoreOrderController
{

    /**
     * 自动开团
     * @param Request $request
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function open_group(Request $request)
    {
        [$pid, $cid, $uid, $pink_id,$access_key] = $request->postMore([
            ['p_id', 0],
            ['c_id', 0],
            ['u_id', 0],
            ['pink_id', 0],
            ['access_key', '']
        ], true);
        if ($access_key != 'wx274954312642199552') {
            return app('json')->fail('访问失败');
        }
        /** @var FollowGroupServices $followGroupServices */
        $followGroupServices = app()->make(FollowGroupServices::class);
        if ($followGroupServices->open((int) $uid,(int)$pid,(int)$cid,(int)$pink_id)) {
            return app('json')->successful('自动开团成功');
        }
        return app('json')->fail("开团失败");
    }

    /**
     * 用户自动跟团
     * @param Request $request
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function follow_group(Request $request)
    {
        [$pink_id, $access_key] = $request->postMore([
            ['pink_id', 0],
            ['access_key', '']
        ], true);
        if ($access_key != 'wx274954312642199552') {
            return app('json')->fail('访问失败');
        }

        /** @var FollowGroupServices $followGroupServices */
        $followGroupServices = app()->make(FollowGroupServices::class);
        if ($followGroupServices->follower((int)$pink_id)) {
            return app('json')->successful('自动参团成功');
        }
        return app('json')->fail('自动参团成功失败');
    }
}
