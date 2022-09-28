<?php
/**
 * I am what iam
 * Class Descript : 对rule操作后删除缓存.
 * User: ehtan
 * Date: 2019-11-28
 * Time: 11:50
 */

namespace App\Aspect;
use Sqcloud\SqcloudResuest;
use App\Http\Controller\Cashier\StoreController;
use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\Before;
use Swoft\Aop\Annotation\Mapping\PointBean;
use Swoft\Aop\Point\JoinPoint;

/**
 * Class MenuAspect
 * @package App\Aspect
 * @Aspect(order=1)
 * @PointBean(
 *   include={SqcloudResuest::class},
 *   exclude={}
 *  )
 */
class TestAspect
{

    /** @var float 执行开始 */
    private $time_begin;

    /**
     * 前置通知
     *
     * @Before()
     */
    public function beforeAdvice()
    {
        // 起点时间
        $this->time_begin = microtime(true);
        echo "TestAspect触发切点=============> \n";
    }


    /**
     * 后置通知
     *
     * @After()
     *
     * @param JoinPoint $joinPoint
     */
    public function afterAdvice(JoinPoint $joinPoint)
    {
        /** @var float 执行结束 */
        $timeFinish = microtime(true);
        $method = $joinPoint->getMethod();
        $runtime = round(($timeFinish - $this->time_begin) * 1000, 3);
        echo "{$method} 方法，本次执行时间为: {$runtime}ms \n";
    }
}