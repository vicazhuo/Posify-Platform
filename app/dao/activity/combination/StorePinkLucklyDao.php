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
use app\model\activity\combination\StorePinkLuckly;

/**
 *
 * Class StorePinkLucklyDao
 * @package app\dao\activity
 */
class StorePinkLucklyDao extends BaseDao
{

    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return StorePinkLuckly::class;
    }

    /**
     * @param array $where
     * @return array|\crmeb\basic\BaseModel|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLucklyTimeOutList(array $where,$time){

        return $this->setModel()::whereTime('next_time','<',$time)->where($where)->order('id asc')->select()->toArray();
    }

    /**
     * 判断用户是否已参团
     * @param $where
     * @return mixed
     */
    public function isJoin($where){

        return $this->setModel()::where($where)->find();

    }

}
