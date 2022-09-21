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
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;

/**
 * Class StoreController
 * @package App\Http\Controller\Cashier
 *
 * @Controller(prefix="/cashierapi/cart")
 */
class CartController
{
    /**
     * get shop cart data 获取收银台购物车信息
     * @RequestMapping (route="get_cart[/{uid}/{cashierid}]",method={RequestMethod::GET})
     * @param int $uid
     * @param int $cashierid
     * @param Request $request
     * @return Response
     */
    public function get_cart(int $uid, int $cashierid,Request $request): Response
    {
        $param = $request->query();
        return successWithJson("", "", [$uid, $cashierid,$param]);
    }

    /**
     * delete a order for users on cashier desk 在收银台上删除购物车订单
     * @RequestMapping (route="del_cart[/{uid}]",method={RequestMethod::DELETE})
     * @param int $uid
     * @return Request
     */
    public function del_cart(int $uid):Request
    {

    }

}