<?php
/**
 * Created by PhpStorm.
 * User: rikesh
 * Date: 4/2/15
 * Time: 11:27 AM
 */

namespace BackendBundle\Library\Queue;
use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;


/**
 * Interface QueueWorkerInterface
 * @package BackendBundle\Library\Queue
 */
interface QueueWorkerInterface
{
    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function processQueue(OperationsQueue $queue, $args = []);

    /**
     * @param Transactions $transaction
     * @param array $args
     * @return mixed
     */
    public function enqueueTransaction(Transactions $transaction, $args = []);
} 