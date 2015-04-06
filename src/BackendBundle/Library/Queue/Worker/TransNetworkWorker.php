<?php

namespace BackendBundle\Library\Queue\Worker;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Queue\AbstractQueueWorker as BaseWorker;

/**
 * Class TransNetworkWorker
 * @package BackendBundle\Library\Queue\Worker
 */
class TransNetworkWorker extends BaseWorker
{
    /**
     * @param OperationsQueue $queue
     * @param array $args
     */
    public function processQueue(OperationsQueue $queue, $args = [])
    {
        // TODO: Implement processQueue() method.
    }

    /**
     * @param Transactions $transaction
     * @param array $args
     */
    public function enqueueTransaction(Transactions $transaction, $args = [])
    {
        // TODO: Implement enqueueTransaction() method.
    }

} 