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

use app\jobs\PinkJob;
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\combination\StorePinkServices;
use app\services\BaseServices;
use app\services\order\StoreCartServices;
use app\services\order\StoreOrderComputedServices;
use app\services\order\StoreOrderCreateServices;
use app\services\order\StoreOrderServices;
use app\services\pay\YuePayServices;
use app\services\user\UserServices;
use crmeb\services\CacheService;
use think\facade\Log;

/**
 * 用户跟团
 * Class FollowGroupServices
 * @package app\services\other
 */
class FollowGroupServices extends BaseServices
{

    public $cartService;

    protected $userInfo;

    public function __construct(StoreCartServices $cartService, UserServices $userServices)
    {

        $this->cartService = $cartService;
        $this->userServices = $userServices;
    }

    /**
     * 团长开团
     * @param int $uid
     * @param int $productId
     * @param int $combinationId
     * @param $pinkId
     * @param $k_id
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function open(int $uid, int $productId, int $combinationId, $pinkId)
    {
        if ($open_res = $this->copyCart($uid, $productId, $combinationId, $pinkId)) {
            Log::record("自动开团成功", 'debug');
            /** @var StoreCombinationServices $storeCombinationServices */
            $storeCombinationServices = app()->make(StoreCombinationServices::class);
            $product = $storeCombinationServices->getOne(['id' => $combinationId], 'expiration_time');
            /** @var StorePinkServices $storePinkServices */
            $storePinkServices = app()->make(StorePinkServices::class);
            $lastOrder = $storePinkServices->getLastOrder($uid, $combinationId);
            if (!$lastOrder) {
                return false;
            }
            list($pinkAll) = $storePinkServices->getPinkMemberAndPinkK(['k_id' => $lastOrder->id]);
            if (count($pinkAll) > 0) {
                event('order.orderQuee', [[
                    'u_id' => $uid,
                    'p_id' => $pinkAll['pid'],
                    'c_id' => $pinkAll['cid'],
                    'pink_id' => $pinkAll['k_id'],
                    'expiration_time' => 20000
                ], 'follow']);
            }
            event('order.orderQuee', [[
                'u_id' => $uid,
                'p_id' => $productId,
                'c_id' => $combinationId,
                'pink_id' => 0,
                'expiration_time' => $product->expiration_time
            ], 'open']);
            return true;
        }
        return false;

    }

    /**
     * 自动跟团者
     * @param int $uid
     * @param int $productId
     * @param int $combinationId
     * @param $pinkId
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function follower($pinkId)
    {

        /** @var StorePinkServices $pinkServices */
        $pinkServices = app()->make(StorePinkServices::class);
        list($pinkAll) = $pinkServices->getPinkMemberAndPinkK(['k_id' => $pinkId]);
        if (count($pinkAll) > 0) {
            foreach ($pinkAll as $k => $v) {
                if ($v['is_follow'] == 1) {
                    $res = $this->copyCart($v['uid'], $v['pid'], $v['cid'], $pinkId);
                    if ($res) {
                        Log::record("用户ID：" . $v['uid'] . " 自动参团成功", 'debug');
                    }
                }
            }
        }
    }


    /**
     * 拷贝order数据
     * @return void
     */
    private function copyOrder($pinkId, $uid, $cartGroup, $luck_no)
    {
        $cartInfo = $cartGroup['cartInfo'];
        $user = $this->userInfo;
        $addressId = $cartGroup['addressInfo']['id'];
        $mark = '拼团返佣';
        $combinationId = $cartGroup['combination_id'];
        $real_name = $user['real_name'];
        $phone = $user['phone'];
        /** @var StoreOrderCreateServices $createServices */
        $createServices = app()->make(StoreOrderCreateServices::class);
        $orderInfo = $createServices->createOrdeV2($uid, $cartGroup['orderKey'], $cartGroup, $user, $addressId, 'yue', false, 0, $mark, $combinationId, $pinkId, 0, 0, false, 1, $real_name, $phone, 0, 1, 0, 0, '');
        $orderInfo['luck_no'] = $luck_no;
        /** @var YuePayServices $yueServices */
        $yueServices = app()->make(YuePayServices::class);
        $pay = $yueServices->yueOrderPayV2($orderInfo->toArray(), $uid);
        if ($pay['status'] == 1) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $uid
     * @param $productId
     * @param $cartNum
     * @param $uniqueId
     * @param $combinationId
     * @return mixed|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function copyCart($uid, $productId, $combinationId, $num, $luck_no, $pinkId = 0)
    {
        $userInfo = $this->userServices->getUserInfo($uid);
        $this->userInfo =  $userInfo = $userInfo->toArray();
        $cartGroup = $this->cartService->setCartv2($uid, $productId, $num, '', 3, true, $combinationId, 0, 0, 0);
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $res = $storeOrderServices->getOrderConfirmData($userInfo, $cartGroup['id'], true, 0, 1);
        if ($res) {
            /** @var StoreOrderComputedServices $computedServices */
            $computedServices = app()->make(StoreOrderComputedServices::class);
            $priceGroup = $computedServices->setParamData([
                'combinationId' => $combinationId,
                'pinkId' => $pinkId,
                'seckill_id' => 0,
                'bargainId' => 0,
            ])->computedOrder($uid, $userInfo, $res, $res['addressInfo']['id'], 'yue', false, 0, false, 0);
            if ($this->copyOrder($pinkId, $uid, $res, $luck_no)) {
                return true;
            }
        }

    }

}
