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

namespace app\dao\apartment;

use app\dao\BaseDao;
use app\model\apartment\ApartmentHousePictrue;

/**
 * 民宿房屋价格房态
 * Class ApartmentHousePriceStatusDao
 * @package app\dao\other
 */
class ApartmentHousePriceStatusDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return ApartmentHousePictrue::class;
    }

    /**
     * 初始化房价、房态
     * @param array $arr
     * @param $price
     * @return boolean
     */
    public function init(array $arr, $price)
    {
        $data = [
            'unitId' => $arr['unitId'],
            'roomNo' => $arr['roomNo']
        ];
        if ($this->dao->getOne($data)) {
            return false;
        }
        $prices = [];
        $status = [];
        for ($day = 1; $day < 368; $day++) {
            $prices[] = $price;
            $status[] = 1;
        }
        $data['price'] = $prices;
        $data['status'] = $status;
        if ($this->save($data)) {
            return true;
        }
    }


}
