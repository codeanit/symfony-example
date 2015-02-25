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

namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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

class Greenbelt
{
    protected $container;
    /**
     * [__construct description]
     */

    function __construct(ContainerInterface $container) {
        $this->container = $container;      
    }

    public function update($data){
        $conn = $this->container->get('database_connection');
        $result =$conn->update('transactions',array('status'=>$data['change_status']), array('transaction_code' => $data['confirmation_number']));
        if($result==1){
            $queueData = array(
                    'transaction_source' => 'CDEX',
                    'transaction_service' => 'TB',
                    'operation' => 'notify', 
                    'parameter' => json_encode(array('code'=>'200','operation'=>'modify','confirmation_number'=>$data['confirmation_number'],'status'=>'successful','change_status'=>$data['change_status'])),
                    'is_executed' => 0,
                    'creation_datetime' => date('Y-m-d H:i:s')
                    );
            $check_queue = $conn->insert('operations_queue', $queueData);
        }
        return $check_queue;
    }



  

   
}

