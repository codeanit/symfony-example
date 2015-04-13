<?php

namespace BackendBundle\Library\Queue;

use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;


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
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @param EntityManager $em
     * @param Logger $logger
     * @param QueueWorkerFactory $queueWorkerFactory
     */
    public function __construct(EntityManager $em, Logger $logger, QueueWorkerFactory $queueWorkerFactory)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->queueFactory = $queueWorkerFactory;
    }

    /**
     * @param \BackendBundle\Entity\Transactions $transaction
     * @throws \Exception
     * @return bool
     */
    public function enqueue(Transactions $transaction, $isBulk = false)
    {
        $queue = new OperationsQueue();
        $flag = false;

        $queue->setTransactionSource($transaction->getTransactionSource());
        $queue->setTransactionService($transaction->getTransactionService());
        $queue->setOperation($transaction->getQueueOperation());
        $queue->setCreationDatetime(new \DateTime());
        $queue->setTransaction($transaction);

        $transaction->setQueueOperation(Transactions::QUEUE_OPERATION_ENQUEUE);

        try {
            $this->em->persist($queue);
            $this->em->persist($transaction);

            $this->em->flush();
            $this->em->clear();

            $flag = true;
        } catch(\Exception $e) {
            $this->logger->error('TRANSACTION_ENQUEUE_ERROR', [$e->getMessage()]);
            throw new \Exception('Fatal Error :: Unable to queue the transaction.');
        }

        return $flag;
    }

    /**
     * @param \BackendBundle\Entity\OperationsQueue $queue
     */
    public function processQueue(OperationsQueue $queue)
    {
        $status = $this->queueFactory->forgeWorkerForQueue($queue)
                        ->processQueue($queue);

        return $status;
    }

    /**
     * @throws \Exception
     */
    public function enqueueAll()
    {
        $counter = 0;
        $transactions = $this->em->getRepository('BackendBundle:Transactions')
                        ->getTransactionsToEnqueue();


        $_queue = new OperationsQueue();

        try {
            foreach ($transactions as $transaction) {
                $queue = clone $_queue;

                $queue->setTransactionSource($transaction->getTransactionSource());
                $queue->setTransactionService($transaction->getTransactionService());
                $queue->setOperation($transaction->getQueueOperation());
                $queue->setCreationDatetime(new \DateTime());
                $queue->setTransaction($transaction);

                $transaction->setQueueOperation(Transactions::QUEUE_OPERATION_ENQUEUE);

                $this->em->persist($transaction);
                $this->em->persist($queue);
                ++$counter;
            }


            $this->em->flush();
            $this->em->clear();
        } catch(\Exception $e) {
            $this->logger->error('TRANSACTION_ENQUEUE_ERROR', [$e->getMessage(), $e->getFile(), $e->getLine()]);
            $counter = 0;
        }

        return $counter;
    }


    /**
     * @return int
     */
    public function processAll()
    {
        $counter = 0;
        $queues = $this->em->getRepository('BackendBundle:OperationsQueue')
                    ->findAll();


        foreach ($queues as $queue) {
            if ($this->processQueue($queue)) {
                $counter++;
            }
        }

        return $counter;
    }
}