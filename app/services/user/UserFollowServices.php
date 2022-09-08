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

namespace app\services\user;

use app\dao\user\UserFollowDao;
use app\services\activity\combination\StorePinkLucklyServices;
use app\services\BaseServices;
use think\facade\Log;

/**
 *
 * Class UserBillServices
 * @package app\services\user
 */
class UserFollowServices extends BaseServices
{

    /**
     * UserFollowServices constructor.
     * @param UserFollowDao $dao
     */
    public function __construct(UserFollowDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * TODO 获取用户记录总和
     * @param string getFollower
     * @return mixed
     */
    public function getPinkList($map)
    {
       return $this->dao->getUserFollowList($map);
    }

    /**
     * TODO 获取用户记录总和
     * @param string getFollower
     * @return mixed
     */


    public function getFollowIdList(int $uid)
    {
        $lst = $this->dao->getFollowIdList($uid);
        $data = [];
        foreach ($lst as $item) {
            $item['is_follow'] == 1 ? $data[] = $item['cid'] : null;
        }
        return $data;
    }

    /**
     * @param $uid
     * @param $cid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserFollow($uid, $cid)
    {

        if ($res = $this->dao->get(['cid' => $cid, 'uid' => $uid])) {
            return $res->is_follow == 1 ? true : false;
        }
        return false;
    }

    public function save($data, $uid)
    {
        $data['num'] = isset($data['num']) ? $data['num'] : 1;
        $res = $this->dao->get(['cid' => $data['cid'], 'uid' => $uid]);
        if ($res && $res->is_follow != $data['is_follow']) {
            $res->is_follow = $data['is_follow'];
            $res->num = isset($data['num']) ? $data['num'] : 1;
            $res->save();
        } else {
            $this->dao->save(['cid' => $data['cid'], 'uid' => $uid, 'is_follow' => $data['is_follow'], 'num' => $data['num']]);
        }
    }

    /**
     * 停止跟团
     * @param $uid
     * @return void
     */
    public function stopFollow($id){
        $this->dao->update($id,['is_follow'=>0]);
    }
}
