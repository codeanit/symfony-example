<?php

namespace BackendBundle\Entity\Repository;

use BackendBundle\Entity\NotificationRequest;
use Doctrine\ORM\EntityRepository;

/**
 * Class NotificationRequestRepository
 * @package BackendBundle\Entity\Repository
 */
class NotificationRequestRepository extends EntityRepository
{
    /**
     * @param array $filters
     * @param array $orderBy
     * @return array
     */
    public function getNotificationRequests($filters = [], $orderBy = [])
    {
        $qb = $this->createQueryBuilder('n');
        $validNotiStat = [];
        $filterType = (isset($filters['type'])) ? $filters['type'] : 'both';

        if ($filterType == 'failed') {
            $validNotiStat[] = NotificationRequest::STATUS_FAILED;

        } elseif ('queued' == $filterType) {
            $validNotiStat[] = NotificationRequest::STATUS_QUEUED;

        } else {
            $validNotiStat = [
                NotificationRequest::STATUS_QUEUED,
                NotificationRequest::STATUS_FAILED,
            ];
        }

        $qb->where('n.notificationStatus IN (:validNotiStats)')
            ->setParameter('validNotiStats', $validNotiStat)
        ;

        return $qb->getQuery()->getResult();
    }
}
