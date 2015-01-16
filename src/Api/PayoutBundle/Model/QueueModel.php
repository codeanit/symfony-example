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

class QueueModel
{  

    protected $container;    

    function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function executeQueuedOperation()
    { 
        $source='MOCK';
        $connection = $this->container->get('database_connection');
        // $getUnexecutedOp = "SELECT * FROM operations_queue"
        //     . "  WHERE is_executed = 0 AND transaction_service = '"
        //     . $source . "'ORDER BY id DESC LIMIT 1 ";
        $getUnexecutedOp = "SELECT * FROM operations_queue"
            . "  WHERE is_executed = 0  ORDER BY id DESC LIMIT 1 ";
        $result = $connection->fetchAll($getUnexecutedOp);  
        if(count($result)<1)
        {
            return array('code'=>'405','message'=>'No Operation Found In Queue');
        }    
        $operation = '';
        $parameter = '';
      
        foreach ($result as $result) {
            $operation = $result['operation'];
            $parameter = (array) json_decode($result['parameter']);
            $service=$result['transaction_service'];
            $id=$result['id'];
        }
            //@todo change status to processing 
            try {
                $serviceObj=$this->container->get($service);
            } catch (Exception $e) {
                $e->getMessage();
            }
            if($serviceObj) {
                $result=$serviceObj->{$operation}($parameter);

                if($operation=='create'){
                    if($result['code']==200){
                        // change status to complete 
                        try {                     
                             $check=$conn->update('transactions',array('transaction_status'=>'complete'), array('transaction_key' => $data['transaction']->transaction_key));                             
                            } catch (\Exception $e) {
                             $e->getMessage();
                            } 
                    }else{
                        try {                     
                             $check=$conn->update('transactions',array('transaction_status'=>'failed'), array('transaction_key' => $data['transaction']->transaction_key));
                            } catch (\Exception $e) {
                             $e->getMessage();
                            } 
                    }
                    // add queue to notify to source that is complete
                    $queueData = array(
                        'transaction_source' => 'CDEX',
                        'transaction_service' => $result['notify_source'],
                        'operation' => 'notify', 
                        'parameter' => json_encode(array('confirmation_number'=>$result['confirmation_number'],'status'=>$result['status'])),
                        'is_executed' => 0,
                        'creation_datetime' => date('Y-m-d H:i:s')
                        );
                    $check_queue = $connection->insert('operations_queue', $queueData);
                }


                if($operation=='modify'){
                    if($result['code']==204){
                        //create notification queue to source
                        $queueData = array(
                        'transaction_source' => 'CDEX',
                        'transaction_service' => $result['notify_source'],
                        'operation' => 'notify', 
                        'parameter' => json_encode(array('confirmation_number'=>$result['confirmation_number'],'status'=>$result['status'])),
                        'is_executed' => 0,
                        'creation_datetime' => date('Y-m-d H:i:s')
                        );
                    }
                    if($result['code']==901){
                        $queueData = array(
                        'transaction_source' => 'CDEX',
                        'transaction_service' => $result['notify_source'],
                        'operation' => 'notify', 
                        'parameter' => json_encode(array('confirmation_number'=>$result['confirmation_number'],'status'=>$result['status'])),
                        'is_executed' => 0,
                        'creation_datetime' => date('Y-m-d H:i:s')
                        );
                    }
                    $check_queue = $connection->insert('operations_queue', $queueData);
                   
                }

                $connection->update(
                    'operations_queue',
                    array('is_executed' => '1'),
                    array('id' => $id)
                );
                return $result;
            } else {
                $msg=$service.' service not found';
                return array('code'=>'900','message'=>$msg);
            }

            //@todo Change status to complete/failed
        
    }

}