<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 管理员账号
 * Class Admin
 *
 * @since 2.0
 *
 * @Entity(table="admin")
 */
class Admin extends Model
{
    /**
     * 0 禁用，1启用
     *
     * @Column(name="admin_bs", prop="adminBs")
     *
     * @var int|null
     */
    private $adminBs;

    /**
     * 
     * @Id()
     * @Column(name="admin_id", prop="adminId")
     *
     * @var int
     */
    private $adminId;

    /**
     * 
     *
     * @Column(name="admin_nickname", prop="adminNickname")
     *
     * @var string
     */
    private $adminNickname;

    /**
     * 
     *
     * @Column(name="admin_pwd", prop="adminPwd")
     *
     * @var string|null
     */
    private $adminPwd;

    /**
     * 
     *
     * @Column(name="admin_username", prop="adminUsername")
     *
     * @var string|null
     */
    private $adminUsername;

    /**
     * 
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

    /**
     * 
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string|null
     */
    private $updatedAt;


    /**
     * @param int|null $adminBs
     *
     * @return void
     */
    public function setAdminBs(?int $adminBs): void
    {
        $this->adminBs = $adminBs;
    }

    /**
     * @param int $adminId
     *
     * @return void
     */
    public function setAdminId(int $adminId): void
    {
        $this->adminId = $adminId;
    }

    /**
     * @param string $adminNickname
     *
     * @return void
     */
    public function setAdminNickname(string $adminNickname): void
    {
        $this->adminNickname = $adminNickname;
    }

    /**
     * @param string|null $adminPwd
     *
     * @return void
     */
    public function setAdminPwd(?string $adminPwd): void
    {
        $this->adminPwd = $adminPwd;
    }

    /**
     * @param string|null $adminUsername
     *
     * @return void
     */
    public function setAdminUsername(?string $adminUsername): void
    {
        $this->adminUsername = $adminUsername;
    }

    /**
     * @param string|null $createdAt
     *
     * @return void
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param string|null $updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int|null
     */
    public function getAdminBs(): ?int
    {
        return $this->adminBs;
    }

    /**
     * @return int
     */
    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    /**
     * @return string
     */
    public function getAdminNickname(): ?string
    {
        return $this->adminNickname;
    }

    /**
     * @return string|null
     */
    public function getAdminPwd(): ?string
    {
        return $this->adminPwd;
    }

    /**
     * @return string|null
     */
    public function getAdminUsername(): ?string
    {
        return $this->adminUsername;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

}
