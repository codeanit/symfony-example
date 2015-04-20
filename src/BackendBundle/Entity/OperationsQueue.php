<?php

namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OperationsQueue
 *
 * @ORM\Table(name="operations_queue")
 * @ORM\Entity
 */
class OperationsQueue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_source", type="string", length=10, nullable=false)
     */
    private $transactionSource;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_service", type="string", length=45, nullable=false)
     */
    private $transactionService;

    /**
     * @var string
     *
     * @ORM\Column(name="operation", type="string", length=25, nullable=false)
     */
    private $operation;

    /**
     * @var string
     *
     * @ORM\Column(name="parameter", type="text", nullable=true)
     */
    private $parameter;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_executed", type="boolean", nullable=true)
     */
    private $isExecuted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_datetime", type="datetime", nullable=true)
     */
    private $creationDatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="execution_timestamp", type="datetime", nullable=true)
     */
    private $executionTimestamp;



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
     * Set isExecuted
     *
     * @param boolean $isExecuted
     * @return OperationsQueue
     */
    public function setIsExecuted($isExecuted)
    {
        $this->isExecuted = $isExecuted;

        return $this;
    }

    /**
     * Get isExecuted
     *
     * @return boolean 
     */
    public function getIsExecuted()
    {
        return $this->isExecuted;
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
     * @var \BackendBundle\Entity\Transactions
     */
    private $transaction;


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
    /**
     * @var integer
     */
    private $executionCount;


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
}
