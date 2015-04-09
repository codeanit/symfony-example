<?php

namespace BackendBundle\Library\Queue;


use BackendBundle\Entity\OperationsQueue;

/**
 * Class AbstractQueueWorker
 * @package BackendBundle\Library\Queue
 */
abstract class AbstractQueueWorker implements QueueWorkerInterface
{
    protected $settings = [];

    public function processQueue(OperationsQueue $queue, $args = [])
    {
        $operation = strtolower($queue->getOperation());

        switch ($operation) {
            case 'create':
                return $this->createTransaction($queue, $args);

            case 'modify':
                return $this->modifyTransaction($queue, $args);

            case 'cancel':
                return $this->cancelTransaction($queue, $args);

            default:
                throw new \Exception('Fatal Error :: Undefined operation attempted!!');
        }
    }

    public function getWorkerSetting()
    {

    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    abstract public function createTransaction(OperationsQueue $queue, $args = []);

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    abstract public function modifyTransaction(OperationsQueue $queue, $args = []);

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    abstract public function cancelTransaction(OperationsQueue $queue, $args = []);

    /**
     * @return string
     */
    abstract protected function getWorkerServiceName();
}