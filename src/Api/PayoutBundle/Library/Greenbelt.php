<?php 

namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class Greenbelt extends Common
{
    protected $container;
    protected $connection;
    
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
                    $inputFileType = \PHPExcel_IOFactory::identify($path);                  
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($path);  
                    $ws = $objPHPExcel->getSheet(0);              
                    $rows = $ws->toArray();                 
                    $total=count($rows)-1;
                    unset($rows[0]);
                    unset($rows[$total]);
                    $count=count($rows);   
                    $this->_insertIntoQueue($count,$service_name,$rows);                                 
                } catch (\Exception $e) {                   
                    unset($results[0]['id']);                                                            
                    $this->connection->insert('file_queue',$results[0]);                                      
                    $log->addError('File Parsing Failed',array('Exception At'=>$e->getMessage(),'Filename'=>$file_name,'ServiceName'=>$service_name,'Action'=>$action));
                    $result=array('code'=>'400');
                }                              
            } 
        return $result;        
<<<<<<< HEAD
    }   
=======
    } 

    public function generate($data=null,$p=null){        
        if($p==null){
            $path= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/generated_files/'.'Greenbelt.xlsx';
        }else{
            $path= $p.'Greenbelt.xlsx';
        }
        $objPHPExcel = new \PHPExcel();

        // Set properties        
        $objPHPExcel->getProperties()->setCreator("CDEX");
        $objPHPExcel->getProperties()->setLastModifiedBy("CDEX");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Greenbelt Document"); 
        // $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFont()->setBold(true)->setSize(5);   
        $objPHPExcel->getActiveSheet()->getStyle("A:M")->getFont()->setSize(9);       


        $objPHPExcel->setActiveSheetIndex(0);

        //Title
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'RMT_NO');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'FR_AGENT');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'FAX_NO');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'DATE');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'BENEFICIARY');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'BEN_PHONE');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'TO_AGENT');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'B_ADDRESS');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'AMOUNT');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'COMM');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'SENDER');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'SENDER_TEL');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'SENDER_ADDRESS');

        $c=2;
        foreach ($data as $key => $value) {               
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$c,$value['RMT_NO']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$c,$value['FR_AGENT']);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$c,$value['FAX_NO']);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$c,$value['DATE']);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$c,$value['BENEFICIARY']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$c,$value['BEN_PHONE']);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$c,$value['TO_AGENT']);
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$c,$value['B_ADDRESS']);
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$c,$value['AMOUNT']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$c,$value['COMM']);
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$c,$value['SENDER']);
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$c,$value['SENDER_TEL']);
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$c,$value['SENDER_ADDRESS']);
        $c++;
        }
        
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Greenbelt');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);    
        $objWriter->save($path);

        $check=file_exists($path);
        return $check;

    }  
>>>>>>> bdo-integration
   
}

