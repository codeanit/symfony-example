<?php

namespace BackendBundle\Entity\Repository;


use BackendBundle\Entity\OperationsQueue;
use Doctrine\ORM\EntityRepository;

/**
 * Class OperationQueueRepository
 * @package BackendBundle\Entity\Repository
 */
class OperationQueueRepository extends EntityRepository {

    /**
     * @param array $filters
     * @param array $orderBy
     * @param null $perPage
     * @param null $limit
     * @return mixed
     */
    public function getOperationQueues(array $filters = [], $orderBy = [], $perPage = null, $limit = null)
    {
        $qb = $this->createQueryBuilder('q');

        $qb->where('q.isExecutable = 1')
            ->andWhere('q.executionCount < :executionThreshold')
            ->setParameter('executionThreshold', OperationsQueue::MAX_QUEUE_THRESHOLD)
        ;


        return $qb->getQuery()->getResult();
    }
} 