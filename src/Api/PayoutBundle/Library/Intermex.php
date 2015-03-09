<?php

namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Intermex
{
    protected $container;    
    protected $connection;    
    protected $fileLocation;
    protected $parsedData=array();
    protected $intermexHeaders=array(
        'Identifier of Agency'=>'1,3',    
        'Types of output registries'=>'4,7',     
        'Detail when field 2'=>'8,15',    
        'Number of remittance'=>'16,45',   
        'Consecutive of remittance'=>'46,55',   
        'Date of reported event'=>'56,74',   
        'Message or aditional information '=>'75,224',  
        'identification key'=>'225,226', 
        'transaction_id'=>'227,276', 
        'Beneficiary name'=>'277,356', 
        'Payed amount'=>'357,366', 
        'Currency payed'=>'367,367', 
        'Branch code'=>'368,372', 
        'Type of work order'=>'373,374', 
    );

    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->connection = $this->container->get('database_connection');      
    }  

    protected function _parseHeaderLine($data)
    {
        foreach ($this->intermexHeaders as $key => $digitMap) {           
            list($a, $b) = explode(',', $digitMap);
            $a--;
            $this->parsedData[$key] = substr($data, $a, ($b-$a));
        }        
    }

    public function parse($results,$p=null){                
        $log = new \Symfony\Bridge\Monolog\Logger('FILE_QUEUE');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/FILE_QUEUE_LOG.txt' , Logger::INFO));  
        $operation = '';
        $parameter = ''; 
        $result=array('code'=>'200');        
        $action = $results[0]['action'];
        $service_name= $results[0]['service_name'];       
        $file_name=$results[0]['file'];
        if($action == "IN"){
            try {                
                    if($p==null){
                         $this->fileLocation= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/upload/'.$file_name;
                    }else{
                        $this->fileLocation= $p.$file_name;
                    }                 
                    $data=file_get_contents($this->fileLocation);
                    $txnData=explode("\n", str_replace("\r", '', trim($data)));                                 
                    $this->insertIntoQueue($service_name,$txnData);

                } catch (\Exception $e) {
                    unset($results[0]['id']);                                                    
                    $this->connection->insert('file_queue',$results[0]);                                      
                    $log->addError('File Parsing Failed',array('Exception At'=>$e->getMessage(),'Filename'=>$file_name,'ServiceName'=>$service_name,'Action'=>$action));                
                    $result=array('code'=>'400');
                }              
        }
        return $result;        
    }

     public function insertIntoQueue($service_name=null,$txnData=null){
            for ($txn=0; $txn < count($txnData); $txn++) {
                    $this->_parseHeaderLine($txnData[$txn]);
                    $queueData = array(
                            'transaction_source' => 'CDEX',
                            'transaction_service' => $service_name,
                            'operation' => 'update', 
                            'parameter' => json_encode(array('code'=>'200','operation'=>'modify','confirmation_number'=>trim($this->parsedData['transaction_id']),'status'=>'successful','change_status'=>'paid')),
                            'is_executed' => 0,
                            'creation_datetime' => date('Y-m-d H:i:s')
                            );                    
                    $check_queue = $this->connection->insert('operations_queue', $queueData);
                }           
                
            return;
        }
   
}

