<?php 

namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class MCB extends Common
{
    protected $container;
    protected $connection;
    
    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->connection = $this->container->get('database_connection');
    }   

    public function generate($data=null,$p=null){        
        if($p==null){
            $path= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/generated_files/'.'MCB.xlsx';
        }else{
            $path= $p.'MCB.xlsx';
        }
        $objPHPExcel = new \PHPExcel();

        // Set properties        
        $objPHPExcel->getProperties()->setCreator("CDEX");
        $objPHPExcel->getProperties()->setLastModifiedBy("CDEX");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("MCB Document"); 
        // $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFont()->setBold(true)->setSize(5);   
        $objPHPExcel->getActiveSheet()->getStyle("A:Q")->getFont()->setSize(9);       


        $objPHPExcel->setActiveSheetIndex(0);

        //Title
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'BNF_NAME');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'BNF_LAST');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'BNF_ADDRESS1');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'BNF_ADDRESS2');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'DEPOSIT_ACCOUNT_NUMBER');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'BNF_TELEPHONE');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'SENDER_CUSTOMER_NUMBER');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'SENDER_NAME');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'SENDER_LAST_NAME');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'SENDER_ADDRESS');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'SENDER_CITY');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'SENDER_STATE');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'SENDER_ZIP');
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'BRANCH_NUMBER');
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'BRANCH_LOCATION');
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'CURRENCY_CODE');
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'TO_PAY_BNF');

        $c=2;
        foreach ($data as $key => $value) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$c,$value['BNF_NAME']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$c,$value['BNF_LAST']);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$c,$value['BNF_ADDRESS1']);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$c,$value['BNF_ADDRESS2']);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$c,$value['DEPOSIT_ACCOUNT_NUMBER']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$c,$value['BNF_TELEPHONE']);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$c,$value['SENDER_CUSTOMER_NUMBER']);
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$c,$value['SENDER_NAME']);
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$c,$value['SENDER_LAST_NAME']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$c,$value['SENDER_ADDRESS']);
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$c,$value['SENDER_CITY']);
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$c,$value['SENDER_STATE']);
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$c,$value['SENDER_ZIP']);
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$c,$value['BRANCH_NUMBER']);
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$c,$value['BRANCH_LOCATION']);
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$c,$value['CURRENCY_CODE']);
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$c,$value['TO_PAY_BNF']);
        $c++;
        }
        
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('MCB');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);    
        $objWriter->save($path);

        $check=file_exists($path);
        return $check;

    }  
   
}

