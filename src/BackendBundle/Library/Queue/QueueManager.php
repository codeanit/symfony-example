<?php

namespace BackendBundle\Library\Queue;
use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;


/**
 * Class QueueManager
 * @package BackendBundle\Library\Queue
 */
class QueueManager
{
    /**
     * @var QueueWorkerFactory
     */
    protected $queueFactory;

    /**
     * @param QueueWorkerFactory $queueFactory
     */
    public function __construct(QueueWorkerFactory $queueFactory)
    {
        $this->queueFactory = $queueFactory;
    }

    /**
     * @param \BackendBundle\Entity\Transactions $transaction
     */
    public function enqueue(Transactions $transaction)
    {
        $status = $this->queueFactory->forgeWorkerForTransaction($transaction)
                        ->enqueueTransaction($transaction);
    }

    /**
     * @param \BackendBundle\Entity\OperationsQueue OperationsQueue $queue
     */
    public function processQueue(OperationsQueue $queue)
    {
        $status = $this->queueFactory->forgeWorkerForQueue($queue)
                        ->processQueue($queue);
    }
} 