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

class Olympic
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

    public function parse($results){
        $connection = $this->container->get('database_connection');        
        $log = new \Symfony\Bridge\Monolog\Logger('FILE_QUEUE');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/FILE_QUEUE_LOG.txt' , Logger::INFO));  
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
                    $path= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/upload/'.$file_name;      
                    $inputFileType = \PHPExcel_IOFactory::identify($path);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($path);  
                    $ws = $objPHPExcel->getSheet(0);
                    $rows = $ws->toArray();
                    $total=count($rows)-1;
                    unset($rows[0]);
                    // unset($rows[$total]);
                    $count=count($rows);                 
                } catch (\Exception $e) {
                    unset($results[0]['id']);                                        
                    $connection->insert('file_queue',$results[0]);                                      
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
            
            /**
             * @todo  OUT case implementation
             */
            if($action=="OUT")
            {
                $result=array('msg'=>"OUT action implementation in progress... ");
            } 
            return $result;
        
    }
   
}

