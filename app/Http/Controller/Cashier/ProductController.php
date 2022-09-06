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
 * @Controller(prefix="/cashierapi/product")
 */
class ProductController
{
    /**
     * get a store prodution category
     * @RequestMapping (route="get_one_category",method={RequestMethod::GET})
     */
    public function get_one_category(Request $request)
    {

        return 'ok';
    }

    /**
     * get product list data in the store
     * @RequestMapping (route="get_list",method={RequestMethod::GET})
     */
    public function get_list()
    {


    }


    /**
     * get product description data in the store 获取收银台商品详情
     * @RequestMapping (route="get_info[/{gid}/{uid}]",method={RequestMethod::GET})
     * @param int $gid 商品ID
     * @param int $uid  用户ID
     * @return Response
     */
    public function get_info(int $gid ,int $uid):Response
    {
        return successWithJson("","",[$gid,$uid]);
    }

}