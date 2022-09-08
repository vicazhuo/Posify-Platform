<?php
/**
 * Notes:
 * User: azhuo
 * Company:APM monaco
 * Auther:Awin zhuo
 * DateTime: 2021/10/25 12:00
 * @return
 */

namespace app\services\apartment;

use app\dao\apartment\ApartmentHousePictrueDao;
use app\services\BaseServices;

/**
 *
 * Class HousePictrueService
 * @package app\services\apartment
 * @method getUserIssuePrice(string $price) 获取金大于额的优惠卷金额
 * @method getCouponInfo($id)
 * @method getColumn(array $where, string $field, ?string $key)
 * @method productCouponList(array $where, string $field)
 */
class HousePictrueService extends BaseServices
{

    public $_couponType = [0 => "通用券", 1 => "品类券", 2 => '商品券'];

    /**
     * ApartmentHousePictrueDao constructor.
     * @param ApartmentHousePictrueDao $dao
     */
    public function __construct(ApartmentHousePictrueDao $dao)
    {
        $this->dao = $dao;
    }

}