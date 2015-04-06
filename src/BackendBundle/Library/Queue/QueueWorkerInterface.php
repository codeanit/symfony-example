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
    public function processQueue(OperationsQueue $queue, $args = []);

    public function enqueueTransaction(Transactions $transaction, $args = []);
} 