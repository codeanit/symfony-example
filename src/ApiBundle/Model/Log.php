<?php 

/**
 * First Global Data
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Manish Chalise
 * @license  http://firstglobalmoney.com/license description
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

namespace Api\PayoutBundle\Model;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Bridge to call TB DBAL
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Manish Chalise
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

class Log
{  

    protected $container;
    protected $connection;    

    function __construct(ContainerInterface $container) {
        
        $this->container = $container;
        $this->connection = $this->container->get('database_connection');       

    }

    public function addInfo($service,$method,$req,$res)
    {
        $logData=array(
              'Service'=>$service,
              'Method'=>$method,
              'Status'=>'Success',
              'Request'=>json_encode($req),
              'Response'=>json_encode($res));
    
        $result = $this->connection->insert('log',$logData);        
        return $result;
    }

    public function addError($service,$method,$req,$res)
    {
        $logData=array(
              'Service'=>$service,
              'Method'=>$method,
              'Status'=>'Error',
              'Request'=>json_encode($req),
              'Response'=>json_encode($res));    
        $result = $this->connection->insert('log',$logData);        
        return $result;
    }
  
}