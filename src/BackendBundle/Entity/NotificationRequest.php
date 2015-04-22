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
     * @var \BackendBundle\Entity\Transactions
     */
    private $transaction;


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
     * @param string $payload
     * @return NotificationRequest
     */
    public function setPayload($payload)
    {
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
        return $this->payload;
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
     * @param string $lastResponse
     * @return NotificationRequest
     */
    public function setLastResponse($lastResponse)
    {
        $this->lastResponse = $lastResponse;

        return $this;
    }

    /**
     * Get lastResponse
     *
     * @return string 
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
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
     * Set transaction
     *
     * @param \BackendBundle\Entity\Transactions $transaction
     * @return NotificationRequest
     */
    public function setTransaction(\BackendBundle\Entity\Transactions $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \BackendBundle\Entity\Transactions 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
