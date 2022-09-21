<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class AdminPostLog
 *
 * @since 2.0
 *
 * @Entity(table="admin_post_log")
 */
class AdminPostLog extends Model
{
    /**
     * 
     *
     * @Column(name="client_ip", prop="clientIp")
     *
     * @var string
     */
    private $clientIp;

    /**
     * 创建时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 
     *
     * @Column(name="request_data", prop="requestData")
     *
     * @var string
     */
    private $requestData;

    /**
     * 
     *
     * @Column(name="response_data", prop="responseData")
     *
     * @var string
     */
    private $responseData;

    /**
     * 
     *
     * @Column(name="status_code", prop="statusCode")
     *
     * @var int
     */
    private $statusCode;

    /**
     * 
     *
     * @Column()
     *
     * @var string
     */
    private $uri;

    /**
     * 
     *
     * @Column(name="user_id", prop="userId")
     *
     * @var int
     */
    private $userId;


    /**
     * @param string $clientIp
     *
     * @return void
     */
    public function setClientIp(string $clientIp): void
    {
        $this->clientIp = $clientIp;
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
     * @param int $id
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $requestData
     *
     * @return void
     */
    public function setRequestData(string $requestData): void
    {
        $this->requestData = $requestData;
    }

    /**
     * @param string $responseData
     *
     * @return void
     */
    public function setResponseData(string $responseData): void
    {
        $this->responseData = $responseData;
    }

    /**
     * @param int $statusCode
     *
     * @return void
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param string $uri
     *
     * @return void
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @param int $userId
     *
     * @return void
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRequestData(): ?string
    {
        return $this->requestData;
    }

    /**
     * @return string
     */
    public function getResponseData(): ?string
    {
        return $this->responseData;
    }

    /**
     * @return int
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

}
