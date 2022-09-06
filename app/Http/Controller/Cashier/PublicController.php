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
 * @Controller(prefix="/cashierapi")
 */
class PublicController
{
    /**
     * get a order data of user list in the store.
     * @RequestMapping (route="login",method={RequestMethod::POST})
     */
    public function login(Request $request)
    {

        return 'login';
    }

    /**
     * compute a result for shop cat.
     * @RequestMapping (route="logout",method={RequestMethod::GET})
     */
    public function logout()
    {
        return 'logout';
    }

    /**
     * get hold on order list.
     * @RequestMapping (route="login/info",method={RequestMethod::GET})
     */
    public function site_info(){

        return 'site_info';

    }

    /**
     * get order list.
     * @RequestMapping (route="logo",method={RequestMethod::GET})
     */
    public function logo()
    {
        return 'logo';

    }


}