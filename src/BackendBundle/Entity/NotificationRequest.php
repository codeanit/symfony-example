<?php

namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationRequest
 */
class NotificationRequest
{
    const STATUS_SUCCESS = 'success';
    const STATUS_QUEUED = 'queued';
    const STATUS_FAILED = 'failed';

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $reciever;

    /**
     * @var string
     */
    private $payload;

    /**
     * @var string
     */
    private $notificationStatus;

    /**
     * @var string
     */
    private $notificationAction;

    /**
     * @var string
     */
    private $request;

    /**
     * @var string
     */
    private $response;

    /**
     * @var string
     */
    private $lastResponse;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $transactionCode;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set reciever
     *
     * @param string $reciever
     * @return NotificationRequest
     */
    public function setReciever($reciever)
    {
        $this->reciever = $reciever;

        return $this;
    }

    /**
     * Get reciever
     *
     * @return string 
     */
    public function getReciever()
    {
        return $this->reciever;
    }

    /**
     * Set payload
     *
     * @param array $payload
     * @return NotificationRequest
     */
    public function setPayload($payload)
    {
        $payload = (array) $payload;
        $payload = json_encode($payload);

        $this->payload = $payload;

        return $this;
    }

    /**
     * Get payload
     *
     * @return string 
     */
    public function getPayload()
    {
        $payload = $this->payload;
        $payload = json_decode($payload, true);

        return $payload;
    }

    /**
     * Set notificationStatus
     *
     * @param string $notificationStatus
     * @return NotificationRequest
     */
    public function setNotificationStatus($notificationStatus)
    {
        $this->notificationStatus = $notificationStatus;

        return $this;
    }

    /**
     * Get notificationStatus
     *
     * @return string 
     */
    public function getNotificationStatus()
    {
        return $this->notificationStatus;
    }

    /**
     * Set notificationAction
     *
     * @param string $notificationAction
     * @return NotificationRequest
     */
    public function setNotificationAction($notificationAction)
    {
        $this->notificationAction = $notificationAction;

        return $this;
    }

    /**
     * Get notificationAction
     *
     * @return string 
     */
    public function getNotificationAction()
    {
        return $this->notificationAction;
    }

    /**
     * Set request
     *
     * @param string $request
     * @return NotificationRequest
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return string 
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set response
     *
     * @param string $response
     * @return NotificationRequest
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set lastResponse
     *
     * @param mixed $lastResponse
     * @return NotificationRequest
     */
    public function setLastResponse($lastResponse)
    {
        $lastResponse = (array) $lastResponse;
        $this->lastResponse = json_encode($lastResponse);

        return $this;
    }

    /**
     * Get lastResponse
     *
     * @return string 
     */
    public function getLastResponse()
    {
        $lastResp = $this->lastResponse;

        return json_decode($lastResp, true);
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return NotificationRequest
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set transactionCode
     *
     * @param string $transactionCode
     * @return NotificationRequest
     */
    public function setTransactionCode($transactionCode)
    {
        $this->transactionCode = $transactionCode;

        return $this;
    }

    /**
     * Get transactionCode
     *
     * @return string 
     */
    public function getTransactionCode()
    {
        return $this->transactionCode;
    }
}
