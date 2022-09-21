<?php declare(strict_types=1);

namespace Sqcloud\Abstracts;

/**
 * Notes:
 * User: azhuo
 * Company:APM monaco
 * Auther:Wayen zhuo
 * Email wayenzhuo@gmail.com
 * DateTime: 2022/9/8 14:51
 * @return
 */

/**
 * Class AbstractMapper
 */
abstract class AbstractMapper
{
    use MapperTrait;

    /**
     * @var MineModel
     */
    public $model;

    abstract public function assignModel();

    public function __construct()
    {
        $this->assignModel();
    }

    /**
     * 把数据设置为类属性
     * @param array $data
     */
    public static function setAttributes(array $data)
    {
        Context::set('attributes', $data);
    }

    /**
     * 魔术方法，从类属性里获取数据
     * @param string $name
     * @return mixed|string
     */
    public function __get(string $name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    /**
     * 获取数据
     * @return array
     */
    public function getAttributes(): array
    {
        return Context::get('attributes', []);
    }
}