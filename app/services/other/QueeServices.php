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
declare (strict_types=1);

namespace app\services\other;

use app\services\BaseServices;
use crmeb\services\CacheService;
use think\facade\Log;

/**
 *
 * Class QueeServices
 * @package app\services\other
 */
class QueeServices extends BaseServices
{
    const HOST = 'http://192.168.2.30';

    public function createQuee($data){

        $res = $this->execute('post', 'http://192.168.2.30:7001/createQuee', $data);
        Log::record("createQuee=====run time : $time=====>$res", 'debug');
        return true;
    }

    /**
     * 分配奖金、退款
     * @param array $data
     * @return array
     */
    public function redundAndBonus(array $data, $time)
    {
        $orderCacheKey = 'pk100_' . $data['order_id'];
        CacheService::redisHandler()->set($orderCacheKey, json_encode($data));
        $cTime = time();
        $ExTime = ($data['expiration_time'] * 60) * 1000;
        $sendData = [
            "orderCacheKey" => $orderCacheKey,
            "completed_time"=>date("Y-m-d H:i:s",$cTime),
            "callbackUrl" => self::HOST . "/api/v2/pink/auto",
            "expiration_time" => $time
        ];
        Log::record("$orderCacheKey=====allocation bonus===run time : $time=====>", 'debug');
        Log::record($sendData, 'debug');
        $res = $this->execute('post', 'http://192.168.2.30:7001/order', $sendData);
        Log::record($res, 'debug');
    }

    /**
     * 自动参团
     * @param array $data
     * @return array
     */
    public function continuPintuan(array $data)
    {
        $orderCacheKey = 'pk200_' . $data['order_id'];
        CacheService::redisHandler()->set($orderCacheKey, json_encode($data));
        $cTime = time();
        $ExTime = ($data['expiration_time'] * 60) * 1000;
        $nextT = $cTime+$ExTime;
        $sendData = [
            "orderCacheKey" => $orderCacheKey,
            "createTime"=>date("Y-m-d H:i:s",$cTime),
            "next_time"=>date("Y-m-d H:i:s",$nextT),
            "callbackUrl" => self::HOST . "/api/v2/pink/auto",
            "expiration_time" => $ExTime
        ];
        Log::record("$orderCacheKey=== auto pkin order==========>", 'debug');
        Log::record($sendData, 'debug');
        $res = $this->execute('post', 'http://192.168.2.30:7001/order', $sendData);
        Log::record($res, 'debug');
    }

    public function execute($method, $url, $fields = '', $userAgent = '', $httpHeaders = '', $username = '', $password = '')
    {
        $ch = curl_init();
        if (is_string($url) && strlen($url)) {
            $ret = curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            return false;
        }
        //是否显示头部信息
        curl_setopt($ch, CURLOPT_HEADER, false);
        //
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($username != '') {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        }
        $method = strtolower($method);
        if ('post' == $method) {
            curl_setopt($ch, CURLOPT_POST, true);
            if (is_array($fields)) {
                $sets = array();
                foreach ($fields as $key => $val) {
                    $sets[] = $key . '=' . $val;
                }
                $fields = implode('&', $sets);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        } else if ('put' == $method) {
            curl_setopt($ch, CURLOPT_PUT, true);
        }
        //curl_setopt($ch, CURLOPT_PROGRESS, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLOPT_MUTE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);//设置curl超时秒数
        if (strlen($userAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }
        if (is_array($httpHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        $ret = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return array(curl_error($ch), curl_errno($ch));
        } else {
            curl_close($ch);
            if (!is_string($ret) || !strlen($ret)) {
                return false;
            }
            return $ret;
        }
    }

}
