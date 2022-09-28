<?php
/**
 * Notes:
 * Company:SqCloud
 * Auther:Wayen zhuo
 * Email:wayenzhuo@gmail.com
 * DateTime: 2022/9/28 13:44
 * @return
 */

namespace Sqcloud\Aspect;
use Sqcloud\Annotation\Promission;
use App\Http\Controller\Cashier\StoreController;
use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\Before;
use Swoft\Aop\Annotation\Mapping\PointBean;
use Swoft\Aop\Point\JoinPoint;

/**
 * Class PermissionAspect
 * @Aspect(order=1)
 * @PointBean(
 *   include={Promission::class,StoreController::recharge_info},
 *   exclude={}
 *  )
 * @package Sqcloud\Aspect
 */
class PermissionAspect
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
        echo "PermissionAspect触发切点=============> \n";
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