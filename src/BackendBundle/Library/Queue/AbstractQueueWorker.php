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
        if (! empty($this->settings)) {
            return $this->settings;
        }
        $service = $this->em->getRepository('BackendBundle:Services')
                            ->findOneBy([
                                'serviceName' => $this->getWorkerServiceName()
                            ]);

        if (! $service) {
            $this->settings = [];
        } else {
            $this->settings = json_decode(base64_decode($service->getCredentials()), true);
            $this->settings['service_id'] = $service->getId();
        }

        return $this->settings;
    }

    /**
     * @param OperationsQueue $queue
     * @return bool
     */
    protected function updateExecutedQueue(OperationsQueue $queue)
    {
        $queue->setIsExecuted(true);
        $queue->setExecutionTimestamp(new \DateTime());

        $this->em->persist($queue);
        $this->em->flush();

        return true;
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
     * @param array $arg
     * @return mixed
     */
    abstract public function confirmTransaction(array $arg = []);

    /**
     * @return string
     */
    abstract protected function getWorkerServiceName();
}