<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 商品分类表
 * Class StoreCategory
 *
 * @since 2.0
 *
 * @Entity(table="store_category")
 */
class StoreCategory extends Model
{
    /**
     * 
     *
     * @Column(name="add_time", prop="addTime")
     *
     * @var int|null
     */
    private $addTime;

    /**
     * 
     *
     * @Column(name="big_pic", prop="bigPic")
     *
     * @var string|null
     */
    private $bigPic;

    /**
     * 
     *
     * @Column(name="cate_name", prop="cateName")
     *
     * @var string|null
     */
    private $cateName;

    /**
     * 
     *
     * @Column()
     *
     * @var int|null
     */
    private $id;

    /**
     * 
     *
     * @Column(name="is_show", prop="isShow")
     *
     * @var int|null
     */
    private $isShow;

    /**
     * 
     *
     * @Column()
     *
     * @var string|null
     */
    private $pic;

    /**
     * 
     *
     * @Column()
     *
     * @var int|null
     */
    private $pid;

    /**
     * 
     *
     * @Column()
     *
     * @var int|null
     */
    private $sort;


    /**
     * @param int|null $addTime
     *
     * @return void
     */
    public function setAddTime(?int $addTime): void
    {
        $this->addTime = $addTime;
    }

    /**
     * @param string|null $bigPic
     *
     * @return void
     */
    public function setBigPic(?string $bigPic): void
    {
        $this->bigPic = $bigPic;
    }

    /**
     * @param string|null $cateName
     *
     * @return void
     */
    public function setCateName(?string $cateName): void
    {
        $this->cateName = $cateName;
    }

    /**
     * @param int|null $id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int|null $isShow
     *
     * @return void
     */
    public function setIsShow(?int $isShow): void
    {
        $this->isShow = $isShow;
    }

    /**
     * @param string|null $pic
     *
     * @return void
     */
    public function setPic(?string $pic): void
    {
        $this->pic = $pic;
    }

    /**
     * @param int|null $pid
     *
     * @return void
     */
    public function setPid(?int $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * @param int|null $sort
     *
     * @return void
     */
    public function setSort(?int $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return int|null
     */
    public function getAddTime(): ?int
    {
        return $this->addTime;
    }

    /**
     * @return string|null
     */
    public function getBigPic(): ?string
    {
        return $this->bigPic;
    }

    /**
     * @return string|null
     */
    public function getCateName(): ?string
    {
        return $this->cateName;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getIsShow(): ?int
    {
        return $this->isShow;
    }

    /**
     * @return string|null
     */
    public function getPic(): ?string
    {
        return $this->pic;
    }

    /**
     * @return int|null
     */
    public function getPid(): ?int
    {
        return $this->pid;
    }

    /**
     * @return int|null
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

}
