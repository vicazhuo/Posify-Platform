<?php
/**
 * Notes:
 * User: azhuo
 * Company:APM monaco
 * Auther:Awin zhuo
 * DateTime: 2022/7/13 15:14
 * @return
 */

namespace app\services\activity\lottery;


use crmeb\services\CacheService;

class LuckTotterFastServices
{
    protected $redis;

    public function __construct()
    {
        $this->redis = CacheService::handler();
    }

    public function index()

    {

        $people = 1000000;

        $prizes = [

            ['id' => 0, 'name' => '遗憾，您未抽中任何奖品'],//没抽中

            ['id' => 1, 'name' => '一等奖，iPhone X', 'num' => 1],

            ['id' => 2, 'name' => '二等奖，华为Mate10', 'num' => 3],

            ['id' => 3, 'name' => '三等奖，三星note8', 'num' => 5],

            ['id' => 4, 'name' => '四等奖，一加5', 'num' => 10],

        ];

        $this->prize_draw($people, $prizes);

    }

    /**
     * 抽奖
     * @param $people 预估的抽奖人数
     * @param $prizes 读取奖项设置，可以从数据库读，记得先在redis中初始化库存
     */

    private function prize_draw($people, $prizes)

    {

        $redis = get_redis();

        foreach ($prizes as $key => $value) {

            if ($prizes[$key]['id'] != 0) {
                $count = $redis->get('prize:count:' . $value['id']);
                if ($count !== false && $count <= 0) {
                    $people = $people - $prizes[$key]['num'];
                    unset($prizes[$key]);
                }

            }

        }

//重新下标数组

        $prizes = array_values($prizes);

        dump($prizes);

        $rate = [];

//计算区间

        foreach ($prizes as $key => $item) {

            if ($key == 0)

                $rate[$key] = [0, $item['num']];

            else if ($key == count($prizes) - 1)

                $rate[$key] = [$rate[$key - 1][1], $people];

            else

                $rate[$key] = [$rate[$key - 1][1], $rate[$key - 1][1] + $item['num']];

        }

        dump($rate);

//抽奖

        $rd = mt_rand(0, $people);

        dump($rd);

        foreach ($rate as $key => $item) {

            if ($item[0] <= $rd && $rd < $item[1]) {

                if ($prizes[$key]['id'] != 0) {

                    $newcount = $redis->incrBy('prize:count:' . $prizes[$key]['id'], -1);

                    if ($newcount !== false && $newcount >= 0) {

                        return $prizes[$key];

                    }

                }

                return $prizes[0];

            }

        }

    }


    public function test()
    {

//模拟100W次抽奖随机数分布

        for ($i = 0; $i < 1000000; $i++) {

            $rd = mt_rand(0, $people);

            foreach ($rate as $key => $item) {

                if ($item[0] <= $rd && $rd < $item[1]) {

                    if ($prizes[$key]['count']) {

                        $prizes[$key]['count'] += 1;

                    } else {

                        $prizes[$key]['count'] = 1;

                    }

                }

            }

        }

        dump($prizes);
    }


//    public function indexs()
//
//    {
//
//        $prizes = [
//
//            ['id' => 0, 'name' => '遗憾，您未抽中任何奖品'],//没抽中
//
//            ['id' => 1, 'name' => '一等奖，iPhone X', 'num' => [54]],
//
//            ['id' => 2, 'name' => '二等奖，华为Mate10', 'num' => [100, 386, 999]],
//
//            ['id' => 3, 'name' => '三等奖，三星note8', 'num' => [798, 6333, 48795]],
//
//            ['id' => 4, 'name' => '四等奖，一加5', 'num' => [159, 357, 8432, 789456, 123147, 256528, 764565, 999663, 744121, 546478]],
//
//        ];
//
//        dump($this->prize_draw($prizes));
//
//    }

//
//    private function prize_draw($prizes)
//    {
//
//        $nothing = $prizes[0];
//
//        unset($prizes[0]);
//
//        $redis = get_redis();
//
//        $count = $redis->incr('prize:count');
//
//        foreach ($prizes as $prize) {
//
//            foreach ($prize['num'] as $num) {
//
//                if ($num == $count) {
//
//                    return $prize;
//
//                }
//
//            }
//
//        }
//
//        return $nothing;
//
//    }


}