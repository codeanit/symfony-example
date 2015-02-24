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

class Queue
{  

    protected $container;    

    function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function noti()
    {
        $s=$this->container->get('bts');
        $s->doNotiWithNotc();
        die;
    }

    public function executeQueuedOperation()
    {         
        $connection = $this->container->get('database_connection');
        $getUnexecutedOp = "SELECT * FROM operations_queue"
            . "  WHERE is_executed = 0  ORDER BY id ASC LIMIT 1 ";
        $results = $connection->fetchAll($getUnexecutedOp);  
        if(count($results)<1)
        {
            return array('code'=>'400','message'=>'No Operation Found In Queue');
        }    
        $operation = '';
        $parameter = '';
        

        foreach ($results as $result) {
            $operation = $result['operation'];
            $parameter = (array) json_decode($result['parameter']);
            $service=strtolower($result['transaction_service']);            
            $id=$result['id'];            
        }

        if(isset($parameter['transaction']->transaction_code)){ 
            $CN=$parameter['transaction']->transaction_code; // Confirmation NUmber  
            $connection->update('transactions',array('transaction_status'=>'inprogress'), array('transaction_code' => $CN));                 
        } 

        $serviceObj = null;
        try {
            $serviceObj=$this->container->get($service);       

            if($serviceObj != '') {            
                if(strtolower($service)=='bts') {                
                    $result=$serviceObj->process($operation, $parameter);
                }else {                
                    $result=$serviceObj->{$operation}($parameter);
                }

                if($operation=='create'){
                    if($result['code']==200){
                        $check=$connection->update('transactions',array('transaction_status'=>'successful'), array('transaction_code' => $result['confirmation_number']));                           
                    }else{                                             
                        $check=$connection->update('transactions',array('transaction_status'=>'failed'), array('transaction_code' => $result['confirmation_number']));                            
                    }
                }

                if($operation=='modify'){ 
                    if($result['code']==200){
                        // change status to complete 
                        $updateTransaction=$connection->update('transactions',$result['data'], array('transaction_code' => $result['confirmation_number']));                                              
                    }else{                
                        $check=$connection->update('transactions',array('transaction_status'=>'failed'), array('transaction_code' => $result['confirmation_number']));
                    }
                }

                if($operation=='cancel'){                
                    if($result['code']==200){
                        $check=$connection->update('transactions',array('transaction_status'=>'successful','status'=>'cancel'), array('transaction_code' => $result['confirmation_number']));                             
                    }else{               
                        $check=$connection->update('transactions',array('transaction_status'=>'failed'), array('transaction_code' => $result['confirmation_number']));
                    }
                }

                if($operation == 'create' || $operation == 'modify' || $operation == 'cancel'){
                    // add queue to notify to source
                    if(array_key_exists('change_status', $result)){
                        $change_status=$result['change_status'];
                    }else{
                        $change_status='';
                    }
                    $queueData = array(
                        'transaction_source' => 'CDEX',
                        'transaction_service' => $result['notify_source'],
                        'operation' => 'notify', 
                        'parameter' => json_encode(array('code'=>$result['code'],'operation'=>$result['operation'],'confirmation_number'=>$result['confirmation_number'],'status'=>$result['status'],'change_status'=>$change_status)),
                        'is_executed' => 0,
                        'creation_datetime' => date('Y-m-d H:i:s')
                        );
                    $check_queue = $connection->insert('operations_queue', $queueData);
                }            
                    
            }   
        } catch (\Exception $e) {
            //$e->getMessage();
            $result=array('code'=>'400','message'=>'Error in Queue Processing.');
        }

        $connection->update(
            'operations_queue',
            array('is_executed' => '1'),
            array('id' => $id)
        );     
        return $result;   
    }

    public function executeFileOperation()
    {   
        $log = new \Symfony\Bridge\Monolog\Logger('FILE_QUEUE');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/FILE_QUEUE_LOG.txt' , Logger::INFO));   
        $connection = $this->container->get('database_connection');
        $getUnexecutedOp = "SELECT * FROM file_queue"
            . "  WHERE is_executed = 0  ORDER BY id ASC LIMIT 1 ";
        $results = $connection->fetchAll($getUnexecutedOp);  
        if(count($results)<1)
        {
            return array('code'=>'400','message'=>'No Operation Found In Queue');
        }    
        $operation = '';
        $parameter = '';
       
        foreach ($results as $result) {
            $file_id=$result['id'];
            $id = $result['service_id'];
            $action = $result['action'];
            $service_name= $result['service_name'];       
            $file_name=$result['file'];            
        }

        /**
         * Parse File Data
         */
        if($action == "IN"){
                try {
                    $excel=$this->container->get('parser'); 
                    $path= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/upload/'.$file_name;      
                    $reader = $excel->load($path);
                    $ws = $reader->getSheet(0);
                    $rows = $ws->toArray();
                    $total=count($rows)-1;
                    unset($rows[0]);
                    unset($rows[$total]);
                    $count=count($rows);                 
                } catch (\Exception $e) {
                    $log->addError('File Parsing Failed',array('Exception At'=>$e->getMessage(),'Filename'=>$file_name,'ServiceName'=>$service_name,'Action'=>$action));
                }
                
                try {
                    for ($txn=1; $txn < $count+1; $txn++) {
                    $queueData = array(
                            'transaction_source' => 'CDEX',
                            'transaction_service' => $service_name,
                            'operation' => 'update', 
                            'parameter' => json_encode(array('code'=>'200','operation'=>'modify','confirmation_number'=>$rows[$txn][0],'status'=>'successful','change_status'=>$rows[$txn][8])),
                            'is_executed' => 0,
                            'creation_datetime' => date('Y-m-d H:i:s')
                            );
                    $check_queue = $connection->insert('operations_queue', $queueData);
                    }
                } catch (\Exception $e) {
                    $log->addError('Queue Adding Failed',array('Exception At'=>$e->getMessage(),'Filename'=>$file_name,'ServiceName'=>$service_name,'Action'=>$action));
                }
                              
            } 
            if($action=="OUT")
            {
                $result=array('msg'=>"OUT action implementation in progress... ");
            } 
        /**
         * @todo  OUT case implementation
         */
                
        $connection->update(
            'file_queue',
            array('is_executed' => '1'),
            array('id' => $file_id)
        );     
        return $result;   
    }
}