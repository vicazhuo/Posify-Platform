<?php
/**
 * I am what iam
 * Class Descript : 对rule操作后删除缓存.
 * User: ehtan
 * Date: 2019-11-28
 * Time: 11:50
 */

namespace App\Aspect;
use App\Exception\Handler\HttpExceptionHandler;
use Sqcloud\Annotation\Promission;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\Before;
use Swoft\Aop\Annotation\Mapping\PointAnnotation;
use Swoft\Aop\Point\JoinPoint;
/**
 * Class PromissionAspect
 * @package App\Aspect\PromissionAspect
 * @Aspect(order=1)
 * @PointAnnotation(
 *   include={Promission::class},
 *   exclude={}
 *  )
 */
class PromissionAspect
{

    /** @var float 执行开始 */
    private $time_begin;

    /**
     * 前置通知
     * @Before()
     * @param JoinPoint $joinPoint
     */
    public function beforeAdvice(JoinPoint $joinPoint)
    {
        throw new HttpExceptionHandler('api of ExceptionController');
    }
}