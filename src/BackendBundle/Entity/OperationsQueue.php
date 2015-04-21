<?php

namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OperationsQueue
 */
class OperationsQueue
{
    const MAX_QUEUE_THRESHOLD = 3;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $transactionSource;

    /**
     * @var string
     */
    private $transactionService;

    /**
     * @var string
     */
    private $operation;

    /**
     * @var string
     */
    private $parameter;

    /**
     * @var boolean
     */
    private $isExecutable = true;

    /**
     * @var integer
     */
    private $executionCount = 0;

    /**
     * @var \DateTime
     */
    private $creationDatetime;

    /**
     * @var \DateTime
     */
    private $executionTimestamp;

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
     * Set transactionSource
     *
     * @param string $transactionSource
     * @return OperationsQueue
     */
    public function setTransactionSource($transactionSource)
    {
        $this->transactionSource = $transactionSource;

        return $this;
    }

    /**
     * Get transactionSource
     *
     * @return string 
     */
    public function getTransactionSource()
    {
        return $this->transactionSource;
    }

    /**
     * Set transactionService
     *
     * @param string $transactionService
     * @return OperationsQueue
     */
    public function setTransactionService($transactionService)
    {
        $this->transactionService = $transactionService;

        return $this;
    }

    /**
     * Get transactionService
     *
     * @return string 
     */
    public function getTransactionService()
    {
        return $this->transactionService;
    }

    /**
     * Set operation
     *
     * @param string $operation
     * @return OperationsQueue
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return string 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set parameter
     *
     * @param string $parameter
     * @return OperationsQueue
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Get parameter
     *
     * @return string 
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Set isExecutable
     *
     * @param boolean $isExecutable
     * @return OperationsQueue
     */
    public function setIsExecutable($isExecutable)
    {
        $this->isExecutable = $isExecutable;

        return $this;
    }

    /**
     * Get isExecutable
     *
     * @return boolean 
     */
    public function getIsExecutable()
    {
        return $this->isExecutable;
    }

    /**
     * Set executionCount
     *
     * @param integer $executionCount
     * @return OperationsQueue
     */
    public function setExecutionCount($executionCount)
    {
        $this->executionCount = $executionCount;

        return $this;
    }

    /**
     * Get executionCount
     *
     * @return integer 
     */
    public function getExecutionCount()
    {
        return $this->executionCount;
    }

    /**
     * Set creationDatetime
     *
     * @param \DateTime $creationDatetime
     * @return OperationsQueue
     */
    public function setCreationDatetime($creationDatetime)
    {
        $this->creationDatetime = $creationDatetime;

        return $this;
    }

    /**
     * Get creationDatetime
     *
     * @return \DateTime 
     */
    public function getCreationDatetime()
    {
        return $this->creationDatetime;
    }

    /**
     * Set executionTimestamp
     *
     * @param \DateTime $executionTimestamp
     * @return OperationsQueue
     */
    public function setExecutionTimestamp($executionTimestamp)
    {
        $this->executionTimestamp = $executionTimestamp;

        return $this;
    }

    /**
     * Get executionTimestamp
     *
     * @return \DateTime 
     */
    public function getExecutionTimestamp()
    {
        return $this->executionTimestamp;
    }

    /**
     * Set transaction
     *
     * @param \BackendBundle\Entity\Transactions $transaction
     * @return OperationsQueue
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
