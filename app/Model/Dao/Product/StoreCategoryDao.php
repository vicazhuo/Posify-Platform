<?php
/**
 * Notes:
 * User: azhuo
 * Company:APM monaco
 * Auther:Awin zhuo
 * DateTime: 2022/9/8 14:40
 * @return
 */

namespace App\Model\Dao\Product;


use App\Model\Dao\BaseDao;
use App\Model\Entity\StoreCategory;

/**
 * Class StoreCategoryDao
 * @package App\Model\Dao\Product
 */
class StoreCategoryDao extends BaseDao
{

    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return StoreCategory::class;
    }

}