<?php

namespace BackendBundle\Library\Queue;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Queue\Worker\IntermexWorker;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class QueueWorkerFactory
 * @package BackendBundle\Library\Queue
 */
class QueueWorkerFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

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
        $forgeMethod = 'forge' . ucfirst($service) . 'Worker';

        if (! method_exists($this, $forgeMethod)) {
            throw new \Exception("Fatal Error :: Unable to locate the worker for '{$service}'.");
        }

        return $this->$forgeMethod();
    }

    public function forgeIntermexWorker()
    {
        return $this->container->get('cdex_queue_worker.intrermex');
    }

    public function forgeBdoWorker()
    {
        return $this->container->get('cdex_queue_worker.bdo');
    }

    public function forgeSanmartinWorker()
    {
        return $this->container->get('cdex_queue_worker.sanmartin');
    }
}