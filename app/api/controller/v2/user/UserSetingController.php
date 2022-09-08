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

namespace app\api\controller\v2\user;

use app\services\user\UserBrokerageServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserServices;
use think\Request;

/**
 * Class UserSetingController
 * @package app\api\controller\v2\user
 */
class UserSetingController
{
    /**
     * @var UserServices
     */
    protected $services;

    /**
     * UserSetingController constructor.
     * @param UserServices $services
     */
    public function __construct(UserServices $services)
    {
        $this->services = $services;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function commitChange(Request $request)
    {
        [$money] = $request->postMore([
            ['money', 0]
        ], true);

        $uid = (int)$request->uid();
        $userInfo = $this->services->getUserInfo($uid);
        if(floatval($userInfo->now_money)<floatval($money)){
            return app('json')->fail('余额不足');
        }
        $brokerage_price = floatval($userInfo->brokerage_price) + floatval($money);
        /** @var UserBrokerageServices $userBrokerageServices */
        $userBrokerageServices = app()->make(UserBrokerageServices::class);
        //佣金收入
        $res = $this->services->bcInc($uid, 'brokerage_price',$money, 'uid');
        $number = ['brokerage' => $money, 'total_brokerage' => $brokerage_price,'number'=>$money];
        $resB = $userBrokerageServices->income('nowMoney_to_brokerage',$uid,$number,$money,0);
        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        //支出余额
        $res = $this->services->bcDec($uid, 'now_money',$money, 'uid');
        $number = ['num'=>$money,'number'=>$money];
        $resM = $userMoneyServices->income('money_to_brokerage',$uid,$number,$money,$resB->id);
        return app('json')->successful('转入成功');
    }
}
