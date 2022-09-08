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
namespace app\api\controller\v2\activity;

use app\jobs\LucklyJob;
use app\Request;
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\combination\StorePinkLucklyServices;
use app\services\user\UserAddressServices;
use app\services\user\UserFollowServices;
use crmeb\services\CacheService;
use think\exception\ValidateException;

/**
 * 拼团类
 * Class StoreCombinationController
 * @package app\api\controller\activity
 */
class StoreCombinationController
{
    protected $services;

    public function __construct(StoreCombinationServices $services)
    {
        $this->services = $services;
    }

    /**
     * 拼团列表
     * @return mixed
     */
    public function lst(Request $request)
    {
        $uid = (int)$request->uid();
        $list = $this->services->getCombinationListV2($uid);
        $list = get_thumb_water($list);
        /** @var UserFollowServices $UserFollowServices */
        $UserFollowServices = app()->make(UserFollowServices::class);
        $follows = $UserFollowServices->getFollowIdList($uid);
        $luck_person = [];
        return app('json')->successful("ok", ["list" => $list, 'follows' => $follows, 'luck_person' => $luck_person]);
    }


    /**
     * 拼团列表
     * @return mixed
     */
    public function follow(Request $request)
    {
        $data = $request->postMore([
            ['id', 0],
            ['cid', 0],
            ['luck_no', 0],
            ['luck_total', 0],
            ['people', 0],
            ['price', 0],
            ['num', 1],
            ['next_time', ''],
            ['total_bonus', 0]
        ]);
        $userInfo = $request->user();
        $uid = (int)$request->uid();
        /** @var StorePinkLucklyServices $service */
        $service = app()->make(StorePinkLucklyServices::class);
        $service->createPinkLuckly($data, $uid, $userInfo);
        return app('json')->successful('参团成功');
    }


    /**
     * 添加自动跟团
     * @return mixed
     */
    public function add_follow(Request $request)
    {
        $data = $request->postMore([
            ['cid', 0],
            ['is_follow', 0],
        ]);
        $userInfo = $request->user();
        $uid = (int)$request->uid();
        /** @var UserAddressServices $UserAddressServices */
        $UserAddressServices = app()->make(UserAddressServices::class);
        if (!$UserAddressServices->getUserDefaultAddress($uid)) {
            throw new ValidateException('请先添加收货地址！');
        }
        /** @var UserFollowServices $UserFollowServices */
        $UserFollowServices = app()->make(UserFollowServices::class);
        $UserFollowServices->save($data, $uid);
        return app('json')->successful($data['is_follow'] == 1 ? '追团成功' : '取消成功');
    }


    /**
     * 添加自动跟团
     * @return mixed
     */
    public function pink(Request $request)
    {
        $data = $request->post();
        LucklyJob::dispatchSece(1, [$data]);
        if ($luckno = CacheService::get("pink_luckly_" . $data['id'])) {
            $data['luck_no'] = $luckno;
        }
        return app('json')->successful('ok', $data);

    }
}
