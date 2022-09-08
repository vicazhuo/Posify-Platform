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

namespace app\model\apartment;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO 民宿房态房间Model
 * Class ApartmentHousePriceStatus
 * @package app\model\apartment
 */
class ApartmentHousePriceStatus extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'unitId';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'apartment_housePriceRoom';

    /**
     * @param $value
     * @return string
     */
    public function setPriceAttr($value)
    {
        return implode(",", $value);
    }

    /**
     * @param $value
     * @return false|string[]
     */
    public function getPriceAttr($value)
    {
        return explode(",", $value);
    }

    /**
     * @param $value
     * @return string
     */
    public function setStatusAttr($value)
    {
        return implode(",", $value);
    }

    /**
     * @param $value
     * @return false|string[]
     */
    public function getStatusAttr($value)
    {
        return explode(",", $value);
    }


}
