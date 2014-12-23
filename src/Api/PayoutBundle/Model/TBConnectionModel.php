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

class TBConnectionModel
{

    public $url;
    /**
     * Send Data to TB 
     * 
     * @param array 
     *        
     * @return  array
     */
    public function curlTransborder(array $postedData)
    { 
         $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://firstglobalmoney.com.local/secure/dexdbal");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postedData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resultPOST = curl_exec($curl);       

        return (array) json_decode($resultPOST);
    
    }

    public function doAuthenticate(){
       ini_set("soap.wsdl_cache_enabled", false); 
       $this->url= "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL"; 
       $soap_client = new \SoapClient($this->url, array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE));  

        $headers = array();
        $headera = array(
                                'SESSION_ID' =>'',
                                'USER_NAME'  => 'GPWSSAMSOS',
                                'USER_DOMAIN' => 'BTS_AGENTS',
                                'USER_PASS' => 'samsosexpress');        
        $headerb = array(
                                'FROM' => '',
                                'TO' => '');
                            
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);                
        
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);                
        
        $soap_client->__setSoapHeaders($headers);

        $param = array(
                        'AGENT_CD' => 'PGS', 
                        'AGENT_TRANS_TYPE_CODE' => 'USRL');

        $vard ="
                    <AGENT_CD>PGS</AGENT_CD>
                    <AGENT_TRANS_TYPE_CODE>USRL</AGENT_TRANS_TYPE_CODE>
                    <DATA/>
                    ";
        $params = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'><REQUEST><AGENT_CD>PGS</AGENT_CD><AGENT_TRANS_TYPE_CODE>USRL</AGENT_TRANS_TYPE_CODE></REQUEST></ExecTR>", XSD_ANYXML);

        $actual =$soap_client->ExecTR($params);
        
        $xmla   = simplexml_load_string($actual->ExecTRResult->any, 'SimpleXMLElement', LIBXML_NOCDATA);

        $array = json_decode(json_encode((array)$xmla), TRUE);
        
        // echo "\n USRL REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        // echo "USRL RESPONSE <pre/>";var_dump($array);
         // echo "USRLREQUEST:\n" . $soap_client->__getLastRequest() . "\n"; 
        return $array['SESSION_ID']; 
    }


    public function doSALE()
    {
        $sessionID = $this->doAuthenticate();
        $rootNode = new \SimpleXMLElement( "<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>" );
        $itemNode = $rootNode->addChild('REQUEST');
        
        $itemNode->addChild('AGENT_CD','SEM');
        $itemNode->addChild('AGENT_TRANS_TYPE_CODE','SALE');
        
        $dataNode = $itemNode->addChild('DATA');
        $itemNode = $dataNode->addChild('CONFIRMATION_NM','11637576352');        
        $itemNode = $dataNode->addChild('ORDER_STATUS','O');
        $itemNode = $dataNode->addChild('SERVICE_CD','MTR');
        $itemNode = $dataNode->addChild('PAYMENT_TYPE_CD','CSA');
        $itemNode = $dataNode->addChild('ORIG_COUNTRY_CD','USA');
        $itemNode = $dataNode->addChild('ORIG_CURRENCY_CD','USD');
        $itemNode = $dataNode->addChild('DEST_COUNTRY_CD','MEX');
        $itemNode = $dataNode->addChild('DEST_CURRENCY_CD','MXP');
        $itemNode = $dataNode->addChild('R_ACCOUNT_TYPE_CD');
        $itemNode = $dataNode->addChild('R_ACCOUNT_NM');
        $itemNode = $dataNode->addChild('R_AGENT_CD','TLC');
        $itemNode = $dataNode->addChild('R_AGENT_REGION_SD');
        $itemNode = $dataNode->addChild('R_AGENT_BRANCH_SD');      
        $itemNode = $dataNode->addChild('ORIGIN_AM','25.00');
        $itemNode = $dataNode->addChild('DESTINATION_AM','286.00');
        $itemNode = $dataNode->addChild('EXCH_RATE_FX','11.44000');
        $itemNode = $dataNode->addChild('WHOLESALE_FX');      
        $itemNode = $dataNode->addChild('FEE_AM','10.00');
        $itemNode = $dataNode->addChild('DISCOUNT_AM'); 
        $itemNode = $dataNode->addChild('DISCOUNT_REASON_CD');      
        $itemNode = $dataNode->addChild('S_PAYMENT_TYPE_CD','CSH');      
        $itemNode = $dataNode->addChild('S_ACCOUNT_TYPE_CD');      
        $itemNode = $dataNode->addChild('S_ACCOUNT_NM');      
        $itemNode = $dataNode->addChild('S_BANK_CD');      
        $itemNode = $dataNode->addChild('S_BANK_REF_NM');      
        $itemNode = $dataNode->addChild('R_SMS_MSG_REQ');      
        $itemNode = $dataNode->addChild('O_SMS_MSG_REQ');

        //AGENT NODE
        $itemNode2 = $dataNode->addChild('AGENT');
        $itemNode2->addChild( 'ORDER_NM', '120300110153' );
        $itemNode2->addChild( 'REGION_SD', 'N/A' );
        $itemNode2->addChild( 'BRANCH_SD', 'N/A' );
        $itemNode2->addChild( 'STATE_CD', 'CA' );
        $itemNode2->addChild( 'COUNTRY_CD', 'USA' );
        $itemNode2->addChild( 'USER_NAME', 'N/A' );
        $itemNode2->addChild( 'SUP_USER_NAME', 'N/A' );
        $itemNode2->addChild( 'TERMINAL', 'N/A' );
        $itemNode2->addChild( 'AGENT_DT', '20120311' );
        $itemNode2->addChild( 'AGENT_TM', '231634' );

        //SENDER NODE
        $itemNode3 = $dataNode->addChild('SENDER');
        $itemNode3->addChild( 'CUSTOMER_ID');
        $itemNode3->addChild( 'FIRST_NAME', 'Francisco' );
        $itemNode3->addChild( 'MIDDLE_NAME');
        $itemNode3->addChild( 'LAST_NAME', 'Cervantes' );
        $itemNode3->addChild( 'MOTHER_M_NAME', 'Resendiz' );
        $addr=$itemNode3->addChild( 'ADDRESS');
            $addr->addChild('ADDRESS','1225 Norte Street, Esciondido, California, United States, 95026');
            $addr->addChild('CITY','Esciondido');
            $addr->addChild('STATE_CD','CA');
            $addr->addChild('COUNTRY_CD','USA');
            $addr->addChild('ZIP_CODE','95026');
            $addr->addChild('PHONE','7607558283');
            $addr->addChild('CELL_PHONE');
            $addr->addChild('EMAIL');

        //SENDER ON BEHALF OF NODE
        $itemNode4 = $dataNode->addChild('SENT_ON_BEHALF_OF');
        $itemNode4->addChild( 'FIRST_NAME');
        $itemNode4->addChild( 'MIDDLE_NAME');
        $itemNode4->addChild( 'LAST_NAME');
        $itemNode4->addChild( 'MOTHER_M_NAME');
        $addr2=$itemNode4->addChild( 'ADDRESS');
            $addr2->addChild('ADDRESS');
            $addr2->addChild('CITY');
            $addr2->addChild('STATE_CD');
            $addr2->addChild('ZIP_CODE');
            $addr2->addChild('PHONE');
            $addr2->addChild('CELL_PHONE');
            $addr2->addChild('EMAIL');

        //RECIPIENT NODE
        $itemNode5 = $dataNode->addChild('RECIPIENT');
        $itemNode5->addChild( 'FIRST_NAME','Manish');
        $itemNode5->addChild( 'MIDDLE_NAME');
        $itemNode5->addChild( 'LAST_NAME','Chalise');
        $itemNode5->addChild( 'MOTHER_M_NAME','Rana');
        $itemNode5->addChild( 'IDENTIF_TYPE_CD');
        $itemNode5->addChild( 'IDENTIF_NM');
        $foreign=$itemNode5->addChild( 'FOREIGN_NAME');
            $foreign->addChild('FIRST_NAME');
            $foreign->addChild('MIDDLE_NAME');
            $foreign->addChild('LAST_NAME');
            $foreign->addChild('MOTHER_M_NAME');
        $addr3=$itemNode5->addChild( 'ADDRESS');
            $addr3->addChild('ADDRESS','Ezequiel Montes');
            $addr3->addChild('CITY','Ezequiel Montes');
            $addr3->addChild('STATE_CD','MEX');
            $addr3->addChild('COUNTRY_CD','MEX');
            $addr3->addChild('ZIP_CODE','85696');
            $addr3->addChild('PHONE','9999999999');
            $addr3->addChild('CELL_PHONE');
            $addr3->addChild('EMAIL');

        //SENDER IDENTIFICATION NODE
        $itemNode6 = $dataNode->addChild('SENDER_IDENTIFICATION');
        $itemNode6->addChild( 'TYPE_CD');
        $itemNode6->addChild( 'ISSUER_CD');
        $itemNode6->addChild( 'ISSUER_STATE_CD');
        $itemNode6->addChild( 'ISSUER_COUNTRY_CD');
        $itemNode6->addChild( 'IDENTIF_NM');
        $itemNode6->addChild( 'EXPIRATION_DT');

         //ADDITIONAL INFORMATION NODE
        $itemNode6 = $dataNode->addChild('ADDITIONAL_INFO');
        $itemNode6->addChild( 'DOB_DT');
        $itemNode6->addChild( 'OCCUPATION','Project Manager');
        $itemNode6->addChild( 'SSN');
        $itemNode6->addChild( 'SOURCE_OF_FUNDS_DS');
        $itemNode6->addChild( 'REASON_OF_TRANS_DS');    
        
        $xml = $rootNode->asXML();
        $xmlString = ((string)$xml);

        // $xmlString  = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 

        // $xmlArray = json_decode(json_encode((array)$xmlString), TRUE);
        // var_dump($xmlArray); die;
        $xmlString = str_replace('<?xml version="1.0"?>
        <ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString); 

        $xmlString = str_replace('</ExecTR>', '', $xmlString); 

        // var_dump(substr($xmlString, 0, 20)); die;
        // echo; die;
        // $params = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'><REQUEST><AGENT_CD>PGS</AGENT_CD><AGENT_TRANS_TYPE_CODE>USRL</AGENT_TRANS_TYPE_CODE></REQUEST></ExecTR>", XSD_ANYXML);
    
        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>" . trim($xmlString) . "</ExecTR>", XSD_ANYXML);

             
            $url= "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL"; 
            $soap_client = new \SoapClient($url, array(
                    "trace" => 1,
                    'exceptions' => 1,
                    'cache_wsdl' => WSDL_CACHE_NONE));  

            $headers = array();
            $headera = array(
                                    'SESSION_ID' => $sessionID,
                                    'USER_NAME'  => 'GPWSSAMSOS',
                                    'USER_DOMAIN' => 'BTS_AGENTS',
                                    'USER_PASS' => 'samsosexpress');        
            $headerb = array(
                                    'FROM' => '',
                                    'TO' => '');
                                
            $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);                
            
            $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);                
            
            $soap_client->__setSoapHeaders($headers);
            $salvRes = $soap_client->ExecTR($paramXMLString);
        
        //     echo $xmlFinal   = simplexml_load_string($actual->ExecTRResult->any, 'SimpleXMLElement', LIBXML_NOCDATA); die;

        // $array = json_decode(json_encode((array)$xmlFinal), TRUE);
        

        //     return $array['SESSION_ID'];         
        // $actual = $soap_client->ExecTR($arrayss);
        // echo "\n SALV REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        // echo "\n SALV RESPONSE XML :\n"; var_dump($salvRes);
    }

    public function doQRTR()
    {
        $sessionID = $this->doAuthenticate();
        
        $rootNode = new \SimpleXMLElement( "<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>" );
        $requestNode = $rootNode->addChild('REQUEST');
        
        $requestNode->addChild('AGENT_CD','SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE','QRTR');
        
        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('CONFIRMATION_NM','11637576352');        

        //AGENT NODE
        $agentNode = $dataNode->addChild('AGENT');
        $agentNode->addChild( 'ORDER_NM', '120300110153' );
        $agentNode->addChild( 'REGION_SD', 'N/A' );
        $agentNode->addChild( 'BRANCH_SD', 'N/A' );
        $agentNode->addChild( 'STATE_CD', 'CA' );
        $agentNode->addChild( 'COUNTRY_CD', 'USA' );
        $agentNode->addChild( 'USER_NAME', 'N/A' );
        $agentNode->addChild( 'SUP_USER_NAME', 'N/A' );
        $agentNode->addChild( 'TERMINAL', 'N/A' );
        $agentNode->addChild( 'AGENT_DT', '20120311' );
        $agentNode->addChild( 'AGENT_TM', '231634' );
        
        $senderIdentificationNode = $dataNode->addChild('SENDER_IDENTIFICATION');
        $senderIdentificationNode->addChild( 'TYPE_CD');
        $senderIdentificationNode->addChild( 'ISSUER_CD');
        $senderIdentificationNode->addChild( 'ISSUER_STATE_CD');
        $senderIdentificationNode->addChild( 'ISSUER_COUNTRY_CD');
        $senderIdentificationNode->addChild( 'IDENTIF_NM');
        $senderIdentificationNode->addChild( 'EXPIRATION_DT');

        $additionalIdentificationNode = $dataNode->addChild('ADDITIONAL_INFO');
        $additionalIdentificationNode->addChild( 'DOB_DT');
        $additionalIdentificationNode->addChild( 'OCCUPATION','Project Manager');
        $additionalIdentificationNode->addChild( 'SSN');
        $additionalIdentificationNode->addChild( 'SOURCE_OF_FUNDS_DS');
        $additionalIdentificationNode->addChild( 'REASON_OF_TRANS_DS');   

        $xml = $rootNode->asXML();
        $xmlString = ((string)$xml);
        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString); 

        $xmlString = str_replace('</ExecTR>', '', $xmlString);        
    
        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>" . trim($xmlString) . "</ExecTR>", XSD_ANYXML);

             
            $url= "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL"; 
            $soap_client = new \SoapClient($url, array(
                    "trace" => 1,
                    'exceptions' => 1,
                    'cache_wsdl' => WSDL_CACHE_NONE));  

            $headers = array();
            $headera = array(
                                    'SESSION_ID' => $sessionID,
                                    'USER_NAME'  => 'GPWSSAMSOS',
                                    'USER_DOMAIN' => 'BTS_AGENTS',
                                    'USER_PASS' => 'samsosexpress');        
            $headerb = array(
                                    'FROM' => '',
                                    'TO' => '');
                                
            $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);                
            
            $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);                
            
            $soap_client->__setSoapHeaders($headers);
            $salvRes = $soap_client->ExecTR($paramXMLString);
        
        //     echo $xmlFinal   = simplexml_load_string($actual->ExecTRResult->any, 'SimpleXMLElement', LIBXML_NOCDATA); die;

        // $array = json_decode(json_encode((array)$xmlFinal), TRUE);
        

        //     return $array['SESSION_ID'];         
        // $actual = $soap_client->ExecTR($arrayss);
        echo "\n QRTR REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        echo "\n QRTR RESPONSE XML :\n"; var_dump($salvRes);

        //1300 is accepted 
    }


    public function doCNLI() 
    {
        $sessionID = $this->doAuthenticate();
        
        $rootNode = new \SimpleXMLElement( "<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>" );
        $requestNode = $rootNode->addChild('REQUEST');
        
        $requestNode->addChild('AGENT_CD','SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE','CNLI');
        
        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('CONFIRMATION_NM','11637576352');
        
        // PROCESS_REASON_CD values
        // PAE CNLI PAYMENT AGENT ERROR
        // RBC CNLI REQUESTED BY CUSTOMER
        // SYE CNLI SYSTEM ERROR
        // TEE CNLI TELLER ERROR
        // RESPONSE OPCODE 0702, TRANS_STATUS_CD OCA
        
        $dataNode->addChild('PROCESS_REASON_CD','PAE');
        $dataNode->addChild('WHOLESALE_FX');      
        $dataNode->addChild('FEE_AM','10.00');
        $dataNode->addChild('DISCOUNT_AM'); 
        $dataNode->addChild('DISCOUNT_REASON_CD');
        
        $agentNode = $dataNode->addChild('AGENT');
        $agentNode->addChild( 'ORDER_NM', '120300110153' );
        $agentNode->addChild( 'REGION_SD', 'N/A' );
        $agentNode->addChild( 'BRANCH_SD', 'N/A' );
        $agentNode->addChild( 'STATE_CD', 'CA' );
        $agentNode->addChild( 'COUNTRY_CD', 'USA' );
        $agentNode->addChild( 'USER_NAME', 'N/A' );
        $agentNode->addChild( 'SUP_USER_NAME', 'N/A' );
        $agentNode->addChild( 'TERMINAL', 'N/A' );
        $agentNode->addChild( 'AGENT_DT', '20120311' );
        $agentNode->addChild( 'AGENT_TM', '231634' );

        $senderIdentificationNode = $dataNode->addChild('SENDER_IDENTIFICATION');
        $senderIdentificationNode->addChild( 'TYPE_CD');
        $senderIdentificationNode->addChild( 'ISSUER_CD');
        $senderIdentificationNode->addChild( 'ISSUER_STATE_CD');
        $senderIdentificationNode->addChild( 'ISSUER_COUNTRY_CD');
        $senderIdentificationNode->addChild( 'IDENTIF_NM');
        $senderIdentificationNode->addChild( 'EXPIRATION_DT');

        $xml = $rootNode->asXML();
        $xmlString = ((string)$xml);
        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString); 

        $xmlString = str_replace('</ExecTR>', '', $xmlString);        
    
        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>" . trim($xmlString) . "</ExecTR>", XSD_ANYXML);

             
        $url= "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL"; 
        $soap_client = new \SoapClient($url, array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE));  

        $headers = array();
        $headera = array(
                                'SESSION_ID' => $sessionID,
                                'USER_NAME'  => 'GPWSSAMSOS',
                                'USER_DOMAIN' => 'BTS_AGENTS',
                                'USER_PASS' => 'samsosexpress');        
        $headerb = array(
                                'FROM' => '',
                                'TO' => '');
                            
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);                
        
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);                
        
        $soap_client->__setSoapHeaders($headers);
        $return = $soap_client->ExecTR($paramXMLString);
       
        echo "\n CNLI REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        echo "\n CNLi RESPONSE XML :\n"; var_dump($return);
    }

    public function doCORI()
    {
        $sessionID = $this->doAuthenticate();
        
        $rootNode = new \SimpleXMLElement( "<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>" );
        $requestNode = $rootNode->addChild('REQUEST');
        
        $requestNode->addChild('AGENT_CD','SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE','CORI');
        
        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('CONFIRMATION_NM','11637576931');
        
        // PROCESS_REASON_CD values
        // PAE  PAYMENT AGENT ERROR
        // RBC  REQUESTED BY CUSTOMER
        // SYE  SYSTEM ERROR
        // TEE  TELLER ERROR
        // SUCCESS RESPONSE OPCODE 0902
        
        $dataNode->addChild('PROCESS_REASON_CD','RBC');
        $dataNode->addChild('WHOLESALE_FX');      
        $dataNode->addChild('FEE_AM','10.00');
        $dataNode->addChild('DISCOUNT_AM'); 
        $dataNode->addChild('DISCOUNT_REASON_CD');
        $dataNode->addChild('R_SMS_MSG_REQ');
        $dataNode->addChild('EXCH_RATE_FX','11.44000');

        $agentNode = $dataNode->addChild('AGENT');
        $agentNode->addChild( 'ORDER_NM', '120300110153');
        $agentNode->addChild( 'REGION_SD', 'N/A' );
        $agentNode->addChild( 'BRANCH_SD', 'N/A' );
        $agentNode->addChild( 'STATE_CD', 'CA' );
        $agentNode->addChild( 'COUNTRY_CD', 'USA' );
        $agentNode->addChild( 'USER_NAME', 'N/A' );
        $agentNode->addChild( 'SUP_USER_NAME', 'N/A' );
        $agentNode->addChild( 'TERMINAL', 'N/A' );
        $agentNode->addChild( 'AGENT_DT', '20120311' );
        $agentNode->addChild( 'AGENT_TM', '231634' );

        $recepientNode = $dataNode->addChild('RECIPIENT');
        $recepientNode->addChild( 'FIRST_NAME','Manisha');
        $recepientNode->addChild( 'MIDDLE_NAME');
        $recepientNode->addChild( 'LAST_NAME','Pradhan');
        $recepientNode->addChild( 'MOTHER_M_NAME','Rana');
        $recepientNode->addChild( 'IDENTIF_TYPE_CD');
        $recepientNode->addChild( 'IDENTIF_NM');
        
        $recepientForeignNameNode=$recepientNode->addChild( 'FOREIGN_NAME');
        $recepientForeignNameNode->addChild('FIRST_NAME');
        $recepientForeignNameNode->addChild('MIDDLE_NAME');
        $recepientForeignNameNode->addChild('LAST_NAME');
        $recepientForeignNameNode->addChild('MOTHER_M_NAME');

        $recepientAddressNode = $recepientNode->addChild( 'ADDRESS');
        $recepientAddressNode->addChild('ADDRESS','Ezequiel Montes');
        $recepientAddressNode->addChild('CITY','Ezequiel Montes');
        $recepientAddressNode->addChild('STATE_CD','MEX');
        $recepientAddressNode->addChild('COUNTRY_CD','MEX');
        $recepientAddressNode->addChild('ZIP_CODE','85696');
        $recepientAddressNode->addChild('PHONE','9999999999');
        $recepientAddressNode->addChild('CELL_PHONE');
        $recepientAddressNode->addChild('EMAIL');

        $senderIdentificationNode = $dataNode->addChild('SENDER_IDENTIFICATION');
        $senderIdentificationNode->addChild( 'TYPE_CD');
        $senderIdentificationNode->addChild( 'ISSUER_CD');
        $senderIdentificationNode->addChild( 'ISSUER_STATE_CD');
        $senderIdentificationNode->addChild( 'ISSUER_COUNTRY_CD');
        $senderIdentificationNode->addChild( 'IDENTIF_NM');
        $senderIdentificationNode->addChild( 'EXPIRATION_DT');

        $additionalIdentificationNode = $dataNode->addChild('ADDITIONAL_INFO');
        $additionalIdentificationNode->addChild( 'DOB_DT');
        $additionalIdentificationNode->addChild( 'OCCUPATION','Project Manager');
        $additionalIdentificationNode->addChild( 'SSN');
        $additionalIdentificationNode->addChild( 'SOURCE_OF_FUNDS_DS');
        $additionalIdentificationNode->addChild( 'REASON_OF_TRANS_DS');

        $xml = $rootNode->asXML();
        $xmlString = ((string)$xml);
        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString); 

        $xmlString = str_replace('</ExecTR>', '', $xmlString);        
    
        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>" . trim($xmlString) . "</ExecTR>", XSD_ANYXML);

             
        $url= "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL"; 
        $soap_client = new \SoapClient($url, array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE));  

        $headers = array();
        $headera = array(
                        'SESSION_ID' => $sessionID,
                        'USER_NAME'  => 'GPWSSAMSOS',
                        'USER_DOMAIN' => 'BTS_AGENTS',
                        'USER_PASS' => 'samsosexpress');        
        $headerb = array(
                        'FROM' => '',
                        'TO' => '');
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);                
        
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);                
        
        $soap_client->__setSoapHeaders($headers);
        $return = $soap_client->ExecTR($paramXMLString);
       
        echo "\n doCORI REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        echo "\n doCORI RESPONSE OBJECT :\n"; var_dump($return);
    }


    public function doNOTI()
    {
        $sessionID = $this->doAuthenticate();
        
        $rootNode = new \SimpleXMLElement( "<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>" );
        $requestNode = $rootNode->addChild('REQUEST');
        
        $requestNode->addChild('AGENT_CD','SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE','NOTI');
        
        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('ROW_COUNT','2');

        $xml = $rootNode->asXML();
        $xmlString = ((string)$xml);
        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString); 

        $xmlString = str_replace('</ExecTR>', '', $xmlString);        
    
        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>" . trim($xmlString) . "</ExecTR>", XSD_ANYXML);

             
        $url= "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL"; 
        $soap_client = new \SoapClient($url, array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE));  

        $headers = array();
        $headera = array(
                        'SESSION_ID' => $sessionID,
                        'USER_NAME'  => 'GPWSSAMSOS',
                        'USER_DOMAIN' => 'BTS_AGENTS',
                        'USER_PASS' => 'samsosexpress');        
        $headerb = array(
                        'FROM' => '',
                        'TO' => '');
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);                
        
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);                
        
        $soap_client->__setSoapHeaders($headers);
        $return = $soap_client->ExecTR($paramXMLString);
       
        echo "\n doNOTI REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        echo "\n doNOTI RESPONSE OBJECT :\n"; var_dump($return);

        //1304 success opcode
        // will retruns row_count last ous BTS TXN
        // <NOTIFICATION NOTIFICATION_ID="271210" CONFIRMATION_NM="11630997142" AGENT_ORDER_NM="WF720928200" MOVEMENT_TYPE_CODE="PAYI" OPCODE="1100" MOVEMENT_DT="20121130" MOVEMENT_TM="152036" AGENT_CD="FCR" AGENT_REGION_SD="1" AGENT_BRANCH_SD="9600" AGENT_USER_NAME="GPWSFEDECREDITO" AGENT_TERMINAL="TERMINAL" IDENTIF_TYPE_CD="DUI" IDENTIF_ISSUER_CD="STA" IDENTIF_ISSUER_STATE_CD="999" IDENTIF_NM="021363433" IDENTIF_ISSUER_COUNTRY_CD="SLV" BANK_REF_NM="0960000990990008"/>
        // <NOTIFICATION NOTIFICATION_ID="271210" CONFIRMATION_NM="11630997142" AGENT_ORDER_NM="WF720928200" MOVEMENT_TYPE_CODE="PAYI" OPCODE="1100" MOVEMENT_DT="20121130" MOVEMENT_TM="152036" AGENT_CD="FCR" AGENT_REGION_SD="1" AGENT_BRANCH_SD="9600" AGENT_USER_NAME="GPWSFEDECREDITO" AGENT_TERMINAL="TERMINAL" IDENTIF_TYPE_CD="DUI" IDENTIF_ISSUER_CD="STA" IDENTIF_ISSUER_STATE_CD="999" IDENTIF_NM="021363433" IDENTIF_ISSUER_COUNTRY_CD="SLV" BANK_REF_NM="0960000990990008"/>
    }

    public function doNOTC()
    {
        $sessionID = $this->doAuthenticate();
        
        $rootNode = new \SimpleXMLElement( "<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>" );
        $requestNode = $rootNode->addChild('REQUEST');
        
        $requestNode->addChild('AGENT_CD','SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE','NOTC');
        
        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('NOTIFICATION_ID','11637576931');
        $dataNode->addChild('CONFIRMATION_NM','11637576931');
        $dataNode->addChild('AGENT_ORDER_NM','120300110153');
        $dataNode->addChild('OPCODE','11637576931');

        $xml = $rootNode->asXML();
        $xmlString = ((string)$xml);
        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString); 

        $xmlString = str_replace('</ExecTR>', '', $xmlString);        
    
        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>" . trim($xmlString) . "</ExecTR>", XSD_ANYXML);

             
        $url= "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL"; 
        $soap_client = new \SoapClient($url, array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE));  

        $headers = array();
        $headera = array(
                        'SESSION_ID' => $sessionID,
                        'USER_NAME'  => 'GPWSSAMSOS',
                        'USER_DOMAIN' => 'BTS_AGENTS',
                        'USER_PASS' => 'samsosexpress');        
        $headerb = array(
                        'FROM' => '',
                        'TO' => '');
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);                
        
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);                
        
        $soap_client->__setSoapHeaders($headers);
        $return = $soap_client->ExecTR($paramXMLString);
       
        echo "\n doNOTC REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        echo "\n doNOTC RESPONSE OBJECT :\n"; var_dump($return);
    }

// $bts = new BTSConnectionDemo();
// echo "\n Calling BTS QRTR..";
// $bts->doQRTR();
// echo "\n Calling BTS CNLI..";
// $bts->doCNLI();
// echo "\n Calling BTS CORI..";
// $bts->doCORI();
// echo "\n Calling BTS NOTI..";
// $bts->doNOTI();
// echo "\n Calling BTS NOTC..";
// $bts->doNOTC();

}