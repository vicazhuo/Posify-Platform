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

use app\dao\activity\combination\StoreCombinationLucklyDao;
use app\jobs\LucklyJob;
use app\jobs\RequestJob;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 *
 * Class StoreCombinationLucklyServices
 * @package app\services\activity
 * @method getPinkIdsArray(array $ids, array $field)
 * @method getOne(array $where, ?string $field = '*', array $with = []) 根据条件获取一条数据
 * @method get(int $id, array $field) 获取一条数据
 */
class StoreCombinationLucklyServices extends BaseServices
{

    /**
     * StoreCombinationLucklyDao constructor.
     * @param StoreCombinationLucklyDao $dao
     */
    public function __construct(StoreCombinationLucklyDao $dao)
    {
        $this->dao = $dao;
    }

    public function getLoseList($id)
    {
        return $this->dao->getList($id);
    }

    public function del_Luck($id){

        $this->dao->deleteLuck(['cid'=>$id]);

    }

    public function getLucklyCombinationList(int $last_time){

       return  $this->dao->getNextTimeOutList($last_time);
    }

    /**
     *创建拼团返佣
     * @param $data
     * @return mixed
     */
    public function createLucklyData($data)
    {
        /** @var StoreCombinationServices $sevice */
        $sevice = app()->make(StoreCombinationServices::class);
        $pink = $sevice->get($data['cid']);
        if ($pink->start_time < time()) {
            $runTime = ($data['exprect_time'] * 60);
        } else {
            $runTime = $pink->start_time - time();
        }
        $data['price'] = $pink->price;
        $data['product_id'] = $pink->product_id;
        $data['people'] = $pink->people;
        $data['stop_time'] = $pink->stop_time;
        $data['start_time'] = $pink->start_time;

        if ($data['luck_total'] > $pink->people) {
            throw new AdminException('拼中人数不能大于参团总人数');
        }

        if ($data['id'] > 0) {
            $id = $data['id'];
            $res = $this->dao->get($id);
            unset($data['id']);
            $this->dao->update($id, $data);
            $data['luck_no'] = $res->luck_no;
        } else {
            unset($data['id']);
            $data['next_time'] = $pink->start_time;
            $data['luck_no'] = 1;//创建第一期
            $res = $this->dao->save($data);
            $id = $res->id;
        }
        if ($runTime > 0) {
            $data['id'] = $id;
            $data['runTime'] = $runTime * 1000;
            $data['cron_type'] = "RabbitMq";
            $data['callbackUrl'] = app()->config->get('luckpink.callbackUrl');
            $data['url'] = app()->config->get('luckpink.open_url');
            RequestJob::dispatchSece(1, [$data]);
            //redis定时
            $runTime = $runTime +20;
            $data['cron_type'] = "Redis";
            $data['runTime'] = $runTime;
            LucklyJob::dispatchSece($runTime,[$data]);
        }
        return true;
    }


    /**
     * 更新数据
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function saveData(int $id, array $data)
    {
        return $this->dao->update($id, $data);

    }

}
