<?php
/**
 * Created by PhpStorm.
 * User: rikesh
 * Date: 4/9/15
 * Time: 4:16 PM
 */

namespace BackendBundle\Entity\Repository;


use BackendBundle\Entity\Log;
use Doctrine\ORM\EntityRepository;

/**
 * Class TransactonLogRepository
 * @package BackendBundle\Entity\Repository
 */
class TransactionLogRepository extends EntityRepository
{
    /**
     * @param $serviceId
     * @param $method
     * @param string $request
     * @param string $response
     * @param string $status
     * @return bool
     */
    public function addLog($serviceId, $method, $request = '', $response = '', $status = 'UNKNOWN')
    {
        $flag = true;
        $log = new Log();

        $log->setMethod($method);
        $log->setService($serviceId);
        $log->setRequest($request);
        $log->setResponse($response);
        $log->setStatus($status);

        try {
            $this->getEntityManager()->persist($log);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();

            $flag = true;

        } catch(\Exception $e) {
           throw $e;           
        }

        return $flag;
    }
}