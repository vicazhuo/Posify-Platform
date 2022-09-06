<?php
/**
 * Notes:
 * User: azhuo
 * Company:APM monaco
 * Auther:Awin zhuo
 * DateTime: 2022/9/2 11:14
 * @return
 */

namespace App\Http\Controller\Cashier;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\Middlewares;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Http\Session\HttpSession;
use Swoft\Redis\Redis;
/**
 * Class StoreController
 * @package App\Http\Controller\Cashier
 *
 * @Controller(prefix="/cashierapi/erp")
 */
class ErpController
{
    /**
     * get ERP configuration
     * @RequestMapping (route="config",method={RequestMethod::GET})
     */
    public function get_cart(Request $request)
    {

        return 'ok';
    }

}