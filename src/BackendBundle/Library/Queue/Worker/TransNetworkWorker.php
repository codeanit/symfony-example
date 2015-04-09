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
     * @param Transactions $transaction
     * @param array $args
     */
    public function enqueueTransaction(Transactions $transaction, $args = [])
    {
        // TODO: Implement enqueueTransaction() method.
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function createTransaction(OperationsQueue $queue, $args = [])
    {
        // TODO: Implement createTransaction() method.
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function modifyTransaction(OperationsQueue $queue, $args = [])
    {
        // TODO: Implement modifyTransaction() method.
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function cancelTransaction(OperationsQueue $queue, $args = [])
    {
        // TODO: Implement cancelTransaction() method.
    }
} 