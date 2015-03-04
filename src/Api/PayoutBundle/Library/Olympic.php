<?php 


namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Olympic extends Common
{
    protected $container;
    protected $connection;        

    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->connection = $this->container->get('database_connection');              
    }

    public function parse($results){        
        $log = new \Symfony\Bridge\Monolog\Logger('FILE_QUEUE');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/FILE_QUEUE_LOG.txt' , Logger::INFO));  
        $operation = '';
        $parameter = ''; 
        $result=array('successful');
        $action = $results[0]['action'];
        $service_name= $results[0]['service_name'];       
        $file_name=$results[0]['file'];
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
                    $count=count($rows); 
                    $this->_insertIntoQueue($count,$service_name,$rows);
                } catch (\Exception $e) {
                    unset($results[0]['id']);                                        
                    $connection->insert('file_queue',$results[0]);                                      
                    $log->addError('File Parsing Failed',array('Exception At'=>$e->getMessage(),'Filename'=>$file_name,'ServiceName'=>$service_name,'Action'=>$action));
                }            
            return $result;        
        }
    }
   
}

