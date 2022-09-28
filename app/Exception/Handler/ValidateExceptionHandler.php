<?php declare(strict_types=1);

namespace App\Exception\Handler;


use const APP_DEBUG;
use function get_class;
use ReflectionException;
use function sprintf;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Error\Annotation\Mapping\ExceptionHandler;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Exception\Handler\AbstractHttpErrorHandler;
use Swoft\Http\Server\Exception\HttpServerException;
use Swoft\Http\Session\HttpSession;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Throwable;

/**
 * Class ValidateExceptionHandler
 *
 * @ExceptionHandler(\Throwable::class)
 */
class ValidateExceptionHandler extends AbstractHttpErrorHandler
{

    /**
     * @param Throwable $e
     * @param Response $response
     * @return Response
     * @throws Throwable
     * @throws \Swoft\Exception\SwoftException
     */
    public function handle(Throwable $e, Response $response): Response
    {
        echo "ValidateExceptionHandler ============================>";
            print_r($e);
    }

}
