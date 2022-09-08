<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\jobs;

use GuzzleHttp\Client;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class RequestJob extends BaseJobs
{
    use QueueTrait;

    public function doJob($data)
    {
        try {

            $client = new \GuzzleHttp\Client();
            $res = $client->request("POST",$data['url'],['json'=>$data]);
            if($res->getStatusCode()==200){
                 Log::record("reques has been sent succesfuly","debug");
            }else{
                 Log::record("request fail","error");
            }

        } catch (\Exception $e) {

            Log::record($e->getMessage(), "debug");
        }
        return true;
    }
}
