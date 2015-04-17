<?php 

namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Sanmartin extends Common
{
    protected $container;
    protected $connection;
    protected $status=array();


    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->connection = $this->container->get('database_connection');        
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
                        $path= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/upload/'.$file_name;
                }else{
                        $path= $p.$file_name;
                     }               
                $data=file_get_contents($path);
                $txnData=explode("\n", str_replace("\r", '', trim($data)));
                unset($txnData[count($txnData)]);

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
        for ($txn=0; $txn < count($txnData) ; $txn++) {
                $parseData=explode('|', trim($txnData[$txn]));
                $queueData = array(
                        'transaction_source' => 'CDEX',
                        'transaction_service' => $service_name,
                        'operation' => 'update', 
                        'parameter' => json_encode(array('code'=>'200','operation'=>'modify','confirmation_number'=>$parseData[0],'status'=>'successful','change_status'=>'PAID')),
                        'is_executed' => 0,
                        'creation_datetime' => date('Y-m-d H:i:s')
                        );
                $check_queue = $this->connection->insert('operations_queue', $queueData);
            }  
            
        return;
    }

     public function generate($data=null,$p=null){          
        $output='';
        if($p==null){
        $path= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/generated_files/'.'Sanmartin.txt';                 
            }
        else{
              $path= $p.'Sanmartin.txt';
            }    
        
        foreach ($data as $key => $value) {                
            $output .= $value.'|';
        }        
        file_put_contents($path,$output.PHP_EOL,FILE_APPEND | LOCK_EX);           
        $check=file_exists($path);
        return $check;

        /**
         * @todo  validate output data
         */
        //$this->validate($output);
    }
}

