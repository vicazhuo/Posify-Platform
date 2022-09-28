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
use Sqcloud\Annotation\Promission;
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
 * @Controller(prefix="/cashierapi/order")
 */
class OrderController
{
    /**
     * get a cashier order by users. 获取收银订单用户
     * @RequestMapping (route="get_user_list[/{cashierid}]",method={RequestMethod::GET})
     * @param int $cashierid
     * @return Response
     */
    public function get_user_list(int $cashierid):Response
    {

        return 'ok';
    }

    /**
     * calculate amount for order on cashier desk. 收银台计算订单金额
     * @RequestMapping (route="compute[/{uid}]",method={RequestMethod::POST})
     * @param int $uid
     * @return Response
     */
    public function compute(int $uid):Response
    {


    }

    /**
     * get hold on order list.
     * @RequestMapping (route="get_hang_list",method={RequestMethod::GET})
     * @Promission(authGroup="asdasdasd")
     */
    public function get_hang_list(){

       return "get_hang_list";
    }

    /**
     * get order list. 收银台订单列表
     * @RequestMapping (route="get_order_list[/{orderType}]",method={RequestMethod::GET})
     * @param int $orderType
     */
    public function get_order_list(int $orderType)
    {


    }

    /**
     * get verify order list.
     * @RequestMapping (route="get_verify_list",method={RequestMethod::POST})
     */
    public function get_verify_list()
    {


    }

    /**
     * get verify order infomation.
     * @RequestMapping (route="verify_cart_info",method={RequestMethod::GET})
     */
    public function verify_cart_info()
    {


    }

    /**
     * get refund order list.
     * @RequestMapping (route="get_refund_list",method={RequestMethod::GET})
     */
    public function get_refund_list()
    {


    }

    /**
     * create a order on cashier desk. 在收银台创建一个订单
     * @RequestMapping (route="get_refund_list",method={RequestMethod::POST})
     * @param int $uid user ID
     * @param Request $request
     * @return Request
     */
    public function create(int $uid, Request $request):Request
    {


    }


}