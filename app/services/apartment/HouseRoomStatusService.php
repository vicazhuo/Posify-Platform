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

use app\dao\apartment\ApartmentHousePriceStatusDao;
use app\services\BaseServices;
use Cassandra\Numeric;

/**
 *
 * Class HouseRoomStatusService
 * @package app\services\apartment
 */
class HouseRoomStatusService extends BaseServices
{

    public $_couponType = [0 => "通用券", 1 => "品类券", 2 => '商品券'];

    /**
     * ApartmentHousePriceStatusDao constructor.
     * @param ApartmentHousePriceStatusDao $dao
     */
    public function __construct(ApartmentHousePriceStatusDao $dao)
    {
        $this->dao = $dao;
    }


}