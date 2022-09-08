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

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\UserFollow;

/**
 * 用户资金&积分&经验
 * Class UserFollowDao
 * @package app\dao\user
 */
class UserFollowDao extends BaseDao
{

    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return UserFollow::class;
    }


    /**
     * 获取签到用户数量
     * @param array $where
     * @return mixed
     */
    public function getUserFollowList(array $where)
    {
        return $this->setModel()::where($where)->select()->toArray();
    }

    public function getFollowIdList($uid){
        $where['uid']=$uid;
        $where['is_follow']=1;
        return $this->setModel()::where($where)->field('cid,is_follow')->select()->toArray();
    }
}
