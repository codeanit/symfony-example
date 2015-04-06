<?php

namespace BackendBundle\Library\Queue;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;

class QueueWorkerFactory
{
    /**
     * @param Transactions $transaction
     * @return QueueWorkerInterface
     * @throws \Exception
     */
    public function forgeWorkerForTransaction(Transactions $transaction)
    {
        $service = $transaction->getTransactionService();

        return $this->forgeWorker($service);
    }


    /**
     * @param OperationsQueue $queue
     * @return QueueWorkerInterface
     * @throws \Exception
     */
    public function forgeWorkerForQueue(OperationsQueue $queue)
    {
        $service = $queue->getTransactionService();
        return $this->forgeWorker($service);
    }

    /**
     * @param $service
     * @return QueueWorkerInterface
     * @throws \Exception
     */
    public function forgeWorker($service)
    {
        $service = strtolower($service);
        $forgeMethod = 'forge' . ucfirst($service);

        if (! method_exists($this, $forgeMethod)) {
            throw new \Exception('Fatal Error :: Unable to locate the worker.');
        }

        return $this->$forgeMethod();
    }


} 