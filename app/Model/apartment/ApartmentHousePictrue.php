<?php

namespace app\model\apartment;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;
/**
* TODO 房屋图片Model
* Class ApartmentHousePictrue
* @package app\model\apartment
*/
class ApartmentHousePictrue extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'unitId';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'apartment_house_picture';


}
