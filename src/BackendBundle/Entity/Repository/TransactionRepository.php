<?php

namespace BackendBundle\Entity\Repository;


use BackendBundle\Entity\Transactions;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Class TransactionRepository
 * @package BackendBundle\Entity\Repository
 */
class TransactionRepository  extends EntityRepository
{
    public function getTransactionsToEnqueue()
    {
        $transactions = [];

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('BackendBundle\Entity\Transactions', 't');
        $processStatPending = Transactions::TRANSACTION_STATUS_PENDING;

        /*
         * Get Latest change Transactions
         */
        $changeTransactions = $this->getEntityManager()
                    ->createNativeQuery("SELECT t1.*
                                    FROM transactions t1
                                    INNER JOIN
                                      (SELECT max(created_at) LatestCreatedDate,
                                              parent_id
                                       FROM transactions
                                       WHERE queue_operation = 'CHANGE'
                                         AND process_status = '{$processStatPending}'
                                       GROUP BY parent_id) t2 ON t2.parent_id = t1.parent_id
                                    AND t1.created_at = t2.LatestCreatedDate
                                    WHERE t1.queue_operation = 'CHANGE'
                                      AND t1.process_status = '{$processStatPending}'", $rsm)
                    ->getResult()
        ;

        /*
         * Get Cancel & Create Txn as well
         */
        $transactions = $this->createQueryBuilder('t')
                            ->select('t')
                            ->where('t.queueOperation IN (:validStats)')
                            ->andWhere('t.processingStatus = :pendingStatus')
                            ->setParameter('pendingStatus', $processStatPending)
                            ->setParameter('validStats', [
                                Transactions::QUEUE_OPERATION_CANCEL,
                                Transactions::QUEUE_OPERATION_CREATE
                            ])
                            ->getQuery()
                            ->getResult()
        ;

        $transactions = array_merge(
            $transactions,
            $changeTransactions
        );
        unset($changeTransactions);

        return $transactions;
    }
}