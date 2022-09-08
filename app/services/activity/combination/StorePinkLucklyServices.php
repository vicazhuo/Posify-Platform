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

namespace app\services\activity\combination;

use app\dao\activity\combination\StorePinkLucklyDao;
use app\jobs\PinkCreateJob;
use app\services\BaseServices;
use app\services\user\UserAddressServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserFollowServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserServices;
use think\exception\ValidateException;
use think\facade\Log;

/**
 *
 * Class StorePinkServices
 * @package app\services\activity
 * @method getPinkCount(array $where)
 * @method int count(array $where = []) 获取指定条件下的条数
 * @method getPinkOkSumTotalNum()
 * @method isPink(int $id, int $uid) 是否能继续拼团
 * @method getPinkUserOne(int $id) 拼团
 * @method getCount(array $where) 获取某些条件总数
 * @method value(array $where, string $field)
 * @method getColumn(array $where, string $field, string $key)
 * @method update(array $where, array $data)
 */
class StorePinkLucklyServices extends BaseServices
{

    /**
     * StorePinkServices constructor.
     * @param StorePinkLucklyDao $dao
     */
    public function __construct(StorePinkLucklyDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * TODO查询当前拼团返佣产品的参团人数
     * @param $map
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPinkLucklyList($map){

       return $this->dao->selectList($map,'*',1,1000)->toArray();
    }

    /**
     * 查找超时未处理的参团订单
     * @param array $map
     * @return array|\crmeb\basic\BaseModel|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLucklyTimeOutList(array $map, int $now)
    {
        return $this->dao->getLucklyTimeOutList($map,$now);
    }

    /**
     * 创建拼团返佣数据
     * @param array $data
     * @param int $uid
     * @param $user
     * @return mixed
     */
    public function createPinkLuckly(array $data, int $uid, $user)
    {
        $data['add_time'] = time();
        $pay_total = floatval($data['price']) * intval($data['num']);
        $data['pay_total'] = $pay_total;
        $userMoney = floatval($user->now_money);
        if ($userMoney < $pay_total) {
            throw new ValidateException("您当前余额为：$userMoney 元，不足于支付本次订单，请前往个人中心充值！");
        }
        /** @var UserAddressServices $UserAddressServices */
        $UserAddressServices = app()->make(UserAddressServices::class);
        if (!$UserAddressServices->getUserDefaultAddress($uid)) {
            throw new ValidateException('请先添加收货地址！');
        }
        $res = $this->dao->isJoin(['cid' => $data['cid'], 'uid' => $uid, 'luck_no' => $data['luck_no']]);
        if ($res) {
            throw new ValidateException('你已参加了本期！');
        }
        $data['uid'] = $uid;
        $res = $this->dao->save($data);
        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        $num = ['num' => $pay_total, 'no' => $data['luck_no'], 'number' => $pay_total];
        $userMoneyServices->income('pay_pink_product', $uid, $num, $pay_total, $res->id);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userServices->bcDec($uid, 'now_money', $pay_total, 'uid');
        return $res->id;
    }

    /**
     * 自动跟团创建一条数据
     * @param array $data
     * @param $user
     */
    public function autocreatePinkLuckly(array $data, $user,$id)
    {
      //  Log::record("luck_no:".$data['luck_no'],"debug");
        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        $res = $this->dao->isJoin(['cid' => $data['cid'], 'uid' => $data['uid'], 'luck_no' => $data['luck_no']]);

        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $pay_total = $data['pay_total'];
        if (!$res) {
            //Log::record("uid:".$data['uid']."===".$user->now_money,"debug");
            if($user->now_money > $pay_total) {
                $res = $this->dao->save($data);
                $num = ['num' => $pay_total, 'number' => $pay_total, "no" => $data['luck_no']];
                $userMoneyServices->income('pay_auto_product', $data['uid'], $num, $pay_total, $res->id);
                $userServices->bcDec($data['uid'], 'now_money', $pay_total, 'uid');
            }else{
                $num = ['num' => $pay_total, 'number' => 0, "no" => $data['luck_no']];
                $userMoneyServices->income('pay_autof_product', $data['uid'], $num, 0, 0);
                /** @var UserFollowServices $UserFollowServices */
                $UserFollowServices = app()->make(UserFollowServices::class);
                $UserFollowServices->stopFollow($id);
            }
        }
    }

    /**
     * 支付佣金操作
     * @param $uid
     * @param $bonus
     * @param int $linkId
     * @param int $luck_no
     */
    public function payBonus($uid, $bonus, int $linkId = 0, int $luck_no,string $tpl)
    {

        /** @var UserBrokerageServices $BrokerageServices */
        $BrokerageServices = app()->make(UserBrokerageServices::class);
        $num = ['number' => $bonus, 'luck_no' => $luck_no];
        $BrokerageServices->income($tpl, $uid, $num, $bonus, $linkId);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if ($userServices->bcInc($uid, 'brokerage_price', $bonus, 'uid')) {
            $this->dao->update($linkId, ['status' => 1, 'bonus' => $bonus]);
        }

    }



    /**
     * 拼团失败退款操作
     * @param int $uid
     * @param $pay_total
     * @param int $linkId
     * @param int $luck_no
     * @param $tpl
     */
    public function payRefund(int $uid, $pay_total, int $linkId = 0, int $luck_no, $tpl)
    {

        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        $num = ['num' => $pay_total, 'number' => $pay_total, 'luck_no' => $luck_no];
        $userMoneyServices->income($tpl, $uid, $num, $pay_total, $linkId);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if ($userServices->bcInc($uid, 'now_money', $pay_total, 'uid')) {
            $this->dao->update($linkId, ['status' => 2]);
        }
    }

    /**
     * 拼中发货
     * @param $id
     * @return void
     */
    public function goodLuck($uid, $product_id, $combinatoinId, $id, $num,$luck_no)
    {
        PinkCreateJob::dispatchSece(3, [$uid, $product_id, $combinatoinId, $num,$luck_no]);
        $this->dao->update($id, ['status' => 3]);
    }

    /**
     * 批量保存
     * @return void
     */
    public function saveAll()
    {

        $this->dao->saveAll(saveAll);
    }
}
