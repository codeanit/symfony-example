<?php 

namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Common
{
    protected $container;
    protected $connection;
    
    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->connection = $this->container->get('database_connection');
    }

    public function update($data){ 
        $log = new \Symfony\Bridge\Monolog\Logger('FILE_QUEUE');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/FILE_QUEUE_LOG.txt' , Logger::INFO));
        try {
              $result =$this->connection->update('transactions',array('status'=>$data['change_status']), array('transaction_code' => $data['confirmation_number']));
                if($result==1){
                    $queueData = array(
                            'transaction_source' => 'CDEX',
                            'transaction_service' => 'TB',
                            'operation' => 'notify', 
                            'parameter' => json_encode(array('code'=>'200','operation'=>'modify','confirmation_number'=>$data['confirmation_number'],'status'=>'successful','change_status'=>$data['change_status'])),
                            'is_executed' => 0,
                            'creation_datetime' => date('Y-m-d H:i:s')
                            );
                    $check_queue = $this->connection->insert('operations_queue', $queueData);
                }else{                    
                    $log->addError('File Parsing Failed',array('error_msg'=>'Error while updating in cdex table due to INVALID Confirmation NUmber','Data'=>$data));               
                    $check_queue=array('Error while updating in cdex table.');
                }  

            }catch (\Exception $e) { 
                // echo $e->getMessage();die;               
                $log->addError('File Parsing Failed',array('Exception At'=>$e->getMessage(),'Data'=>$data));               
            }
        return;
    }

     public function _insertIntoQueue($count=null,$service_name=null,$rows=null){
        for ($txn=1; $txn < $count+1; $txn++) {
                    $queueData = array(
                            'transaction_source' => 'CDEX',
                            'transaction_service' => $service_name,
                            'operation' => 'update', 
                            'parameter' => json_encode(array('code'=>'200','operation'=>'modify','confirmation_number'=>$rows[$txn][0],'status'=>'successful','change_status'=>$rows[$txn][8])),
                            'is_executed' => 0,
                            'creation_datetime' => date('Y-m-d H:i:s')
                            );
            $check_queue = $this->connection->insert('operations_queue', $queueData);
        }
        return;
    }
}

