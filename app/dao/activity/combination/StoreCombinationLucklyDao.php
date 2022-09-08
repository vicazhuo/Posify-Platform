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

namespace app\dao\activity\combination;

use app\dao\BaseDao;
use app\model\activity\combination\StoreCombinationLuckly;


/**
 *
 * Class StoreCombinationLucklyDao
 * @package app\dao\activity
 */
class StoreCombinationLucklyDao extends BaseDao
{

    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return StoreCombinationLuckly::class;
    }

    /**
     * 查找超时为执行定时结算的拼团返佣产品
     * @param $where
     * @param $time
     * @return mixed
     */
    public function getNextTimeOutList($time){
        return $this->setModel()::whereTime('next_time', '<', date('Y-m-d H:i:s',$time))->select()->toArray();
    }

    public function getList($id){

       return $this->setModel()::whereTime('next_time', '<', date('Y-m-d H:i:s',time()))->where('id','<>',$id)->select()->toArray();
    }

    public function deleteLuck($where){

        return $this->setModel()::where($where)->delete();
    }
}
