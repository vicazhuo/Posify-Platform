<?php declare(strict_types=1);
namespace Sqcloud\Annotation;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
/**
 * Notes:
 * Company:SqCloud
 * Auther:Wayen zhuo
 * Email:wayenzhuo@gmail.com
 * DateTime: 2022/9/28 10:47
 * @Annotation
 * @Target("METHOD")
 * @since 2.0
 */

class Promission
{
    /**
     * Route group authPath for the authorization
     *
     * @Required()
     *
     * @var string
     */
    private $authGruop = '';

    /**
     * Controller constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->authGruop = $values['value'];
        }
        if (isset($values['authGruop'])) {
            $this->authGruop = $values['authGruop'];
        }
    }

    /**
     * @return string
     */
    public function getAuthGruop(): string
    {
        echo "============>验证路径为：".$this->authGruop." \n";
        return $this->authGruop;
    }

}