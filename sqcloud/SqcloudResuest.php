<?php
/**
 * Notes:
 * Company:SqCloud
 * Auther:Wayen zhuo
 * Email:wayenzhuo@gmail.com
 * DateTime: 2022/9/28 13:49
 * @return
 */

namespace Sqcloud;


use Swoft\Http\Message\Request;

class SqcloudResuest extends Request
{
    public function __construct()
    {

        echo "这里是请求统一处理中心 \n";

    }

}