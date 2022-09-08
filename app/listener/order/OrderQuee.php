<?php


namespace app\listener\order;

use crmeb\interfaces\ListenerInterface;
use think\facade\Log;

class OrderQuee implements ListenerInterface
{
    public function handle($event): void
    {
        try {
            [$data, $type] = $event;
            Log::record("=============== $type", 'debug');
            Log::record($data, 'debug');
<<<<<<< HEAD
            $host = 'http://api.shuqiaodb.cn';
=======
            $host = 'http://www.apm.com';
>>>>>>> a7bc3caa043eaea2c2211d9b6dd913ded5f13596
            $host2 = 'http://127.0.0.1:7001';
            $data['access_key'] = 'wx274954312642199552';
            switch ($type) {
                case 'open':
                    $data['callbackUrl'] = $host . "/api/v2/pink/open_group";
                    $data['expiration_time'] = (intval($data['expiration_time']) * 60) * 1000;
                    $res = $this->execute('post', $host2 . '/open', $data);
                    break;
                case 'follow':
                    $data['callbackUrl'] = $host . "/api/v2/pink/follow_group";
                    $res = $this->execute('post', $host2 . '/follow', $data);
                    break;
                case 'close':
                    $data['expiration_time'] = (intval($data['expiration_time']) * 60) * 1000;
                    $res = $this->execute('post', $host2 . '/close', $data);
                    break;
                default:
            }
            Log::record("$type ======>", 'debug');

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);//设置curl超时秒数
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
