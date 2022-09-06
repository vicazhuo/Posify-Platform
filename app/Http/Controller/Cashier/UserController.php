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
 * @Controller(prefix="/cashierapi/user")
 */
class UserController
{
    /**
     * get cashier list in the store.
     * @RequestMapping (route="cashier_list",method={RequestMethod::GET})
     */
    public function cashier_list(Request $request):Response
    {
        $param = $request->query();
        return successWithJson("","",[$param]);
       // return 'ok';
    }

    /**
     * switch user on cashier desk 收银台切换顾客
     * @RequestMapping (route="switch[/{cashierId}]",method={RequestMethod::POST})
     * @param int $cashierId
     * @return Request
     */
    public function switch_user(int $cashierId):Request
    {


    }

    /**
     * det a user infomation on cashier desk. 获取收银台用户信息
     * @RequestMapping (route="user_Info",method={RequestMethod::POST})
     * @return Request
     */
    public function user_Info():Request
    {


    }

}