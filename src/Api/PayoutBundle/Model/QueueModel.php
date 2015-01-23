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
        $connection = $this->container->get('database_connection');
        $getUnexecutedOp = "SELECT * FROM operations_queue"
            . "  WHERE is_executed = 0  ORDER BY id ASC LIMIT 1 ";
        $results = $connection->fetchAll($getUnexecutedOp);  
        if(count($results)<1)
        {
            return array('code'=>'405','message'=>'No Operation Found In Queue');
        }    
        $operation = '';
        $parameter = '';
        

        foreach ($results as $result) {
            $operation = $result['operation'];
            $parameter = (array) json_decode($result['parameter']);
            $service=$result['transaction_service'];            
            $id=$result['id'];            
        }

        if(isset($parameter['transaction']->transaction_code)){ 
            $CN=$parameter['transaction']->transaction_code; // Confirmation NUmber  
            $connection->update('transactions',array('transaction_status'=>'inprogress'), array('transaction_code' => $CN));                 
        } 

        $serviceObj = null;
        try {
            $serviceObj=$this->container->get($service);
        } catch (\Exception $e) {
            $e->getMessage();
        }

        if($serviceObj != '') {            
            if(strtolower($service)=='bts') {                
                $result=$serviceObj->process($operation, $parameter);
            }else {                
                $result=$serviceObj->{$operation}($parameter);
            }

            if($operation=='create'){
                if($result['code']==200){                    
                    try {                     
                         $check=$connection->update('transactions',array('transaction_status'=>'successful'), array('transaction_code' => $result['confirmation_number']));                             
                        } catch (\Exception $e) {
                         $e->getMessage();
                        } 
                }else{
                    try {                     
                         $check=$connection->update('transactions',array('transaction_status'=>'failed'), array('transaction_code' => $result['confirmation_number']));
                        } catch (\Exception $e) {
                         $e->getMessage();
                        } 
                }
            }

            if($operation=='modify'){                
                if($result['code']==200){
                    // change status to complete 
                    try {
                         $updateTransaction=$connection->update('transactions',$result['data'], array('transaction_code' => $result['confirmation_number']));                             
                        } catch (\Exception $e) {
                         $e->getMessage();
                        } 
                }else{
                    try {                     
                         $check=$connection->update('transactions',array('transaction_status'=>'failed'), array('transaction_code' => $result['confirmation_number']));
                        } catch (\Exception $e) {
                         $e->getMessage();
                        } 
                }
            }

            if($operation=='cancel'){                
                if($result['code']==200){                    
                    try {                     
                         $check=$connection->update('transactions',array('transaction_status'=>'successful','status'=>'cancel'), array('transaction_code' => $result['confirmation_number']));                             
                        } catch (\Exception $e) {
                         $e->getMessage();
                        } 
                }else{
                    try {                     
                         $check=$connection->update('transactions',array('transaction_status'=>'failed'), array('transaction_code' => $result['confirmation_number']));
                        } catch (\Exception $e) {
                         $e->getMessage();
                        } 
                }
            }

            if($operation == 'create' || $operation == 'modify' || $operation == 'cancel'){
                // add queue to notify to source
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
                
        }   

        $connection->update(
            'operations_queue',
            array('is_executed' => '1'),
            array('id' => $id)
        );

        return $result;   
    }
}