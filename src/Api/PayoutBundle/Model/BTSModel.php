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

class BTSModel
{
  
    /**
     * [__construct description]
     */
    public function __construct()
    {
    }

   /**
     * BTS USRL Operation
     *
     * @return string Session ID
     */
    public function doUSRL()
    {
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

    /**
     * BTS SALE Operation
     *
     * @param array $txn Transaction array.
     *
     * @return array [description]
     */
    public function hello()
    {
        return array('name' =>'manish');
    }
    public function doSALE(array $txn)
    {
        $sessionID = $this->doUSRL();
        $data=$txn;
        // var_dump($txn);die;
        $rootNode = new \SimpleXMLElement("<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>");
        $itemNode = $rootNode->addChild('REQUEST');

        $itemNode->addChild('AGENT_CD', 'SEM');
        $itemNode->addChild('AGENT_TRANS_TYPE_CODE', 'SALE');

        $dataNode = $itemNode->addChild('DATA');
        $itemNode = $dataNode->addChild('CONFIRMATION_NM', $data['transaction']->transaction_code);
        $itemNode = $dataNode->addChild('ORDER_STATUS', 'O');
        $itemNode = $dataNode->addChild('SERVICE_CD', $data['service']);
        $itemNode = $dataNode->addChild('PAYMENT_TYPE_CD', $data['transaction']->payment_type);
        $itemNode = $dataNode->addChild('ORIG_COUNTRY_CD', $data['transaction']->sender_country);
        $itemNode = $dataNode->addChild('ORIG_CURRENCY_CD', $data['transaction']->sender_currency);
        $itemNode = $dataNode->addChild('DEST_COUNTRY_CD', $data['transaction']->receiver_country);
        $itemNode = $dataNode->addChild('DEST_CURRENCY_CD', $data['transaction']->receiver_currency);
        $itemNode = $dataNode->addChild('R_ACCOUNT_TYPE_CD');
        $itemNode = $dataNode->addChild('R_ACCOUNT_NM');
        $itemNode = $dataNode->addChild('R_AGENT_CD');
        $itemNode = $dataNode->addChild('R_AGENT_REGION_SD');
        $itemNode = $dataNode->addChild('R_AGENT_BRANCH_SD');
        $itemNode = $dataNode->addChild('ORIGIN_AM', $data['transaction']->sender_amount);
        $itemNode = $dataNode->addChild('DESTINATION_AM', $data['transaction']->receiver_amount);
        $itemNode = $dataNode->addChild('EXCH_RATE_FX', $data['transaction']->exchange_rate);
        $itemNode = $dataNode->addChild('WHOLESALE_FX');
        $itemNode = $dataNode->addChild('FEE_AM','10');
        $itemNode = $dataNode->addChild('DISCOUNT_AM');
        $itemNode = $dataNode->addChild('DISCOUNT_REASON_CD');
        $itemNode = $dataNode->addChild('S_PAYMENT_TYPE_CD',$data['transaction']->payment_type);
        $itemNode = $dataNode->addChild('S_ACCOUNT_TYPE_CD');
        $itemNode = $dataNode->addChild('S_ACCOUNT_NM');
        $itemNode = $dataNode->addChild('S_BANK_CD');
        $itemNode = $dataNode->addChild('S_BANK_REF_NM');
        $itemNode = $dataNode->addChild('R_SMS_MSG_REQ');
        $itemNode = $dataNode->addChild('O_SMS_MSG_REQ');

        $agentNode = $dataNode->addChild('AGENT');
        $agentNode->addChild('ORDER_NM', '120300110153');
        $agentNode->addChild('REGION_SD', 'N/A');
        $agentNode->addChild('BRANCH_SD', 'N/A');
        $agentNode->addChild('STATE_CD', 'CA');
        $agentNode->addChild('COUNTRY_CD', 'USA');
        $agentNode->addChild('USER_NAME', 'N/A');
        $agentNode->addChild('SUP_USER_NAME', 'N/A');
        $agentNode->addChild('TERMINAL', 'N/A');
        $agentNode->addChild('AGENT_DT', '20120311');
        $agentNode->addChild('AGENT_TM', '231634');

        //SENDER NODE
        $itemNode3 = $dataNode->addChild('SENDER');
        $itemNode3->addChild('CUSTOMER_ID');
        $itemNode3->addChild('FIRST_NAME', $data['transaction']->sender_first_name);
        $itemNode3->addChild('MIDDLE_NAME');
        $itemNode3->addChild('LAST_NAME', $data['transaction']->sender_last_name);
        $itemNode3->addChild('MOTHER_M_NAME', $data['transaction']->sender_middle_name);
        $addr = $itemNode3->addChild('ADDRESS');
        $addr->addChild('ADDRESS', '1225 Norte Street, Esciondido, California, United States, 95026');
        $addr->addChild('CITY', $data['transaction']->sender_city);
        $addr->addChild('STATE_CD', $data['transaction']->sender_state);
        $addr->addChild('COUNTRY_CD', $data['transaction']->sender_country);
        $addr->addChild('ZIP_CODE', $data['transaction']->sender_postal_code);
        $addr->addChild('PHONE', $data['transaction']->sender_phone_mobile);
        $addr->addChild('CELL_PHONE');
        $addr->addChild('EMAIL');

        //SENDER ON BEHALF OF NODE
        $itemNode4 = $dataNode->addChild('SENT_ON_BEHALF_OF');
        $itemNode4->addChild('FIRST_NAME');
        $itemNode4->addChild('MIDDLE_NAME');
        $itemNode4->addChild('LAST_NAME');
        $itemNode4->addChild('MOTHER_M_NAME');
        $addr2 = $itemNode4->addChild('ADDRESS');
        $addr2->addChild('ADDRESS');
        $addr2->addChild('CITY');
        $addr2->addChild('STATE_CD');
        $addr2->addChild('ZIP_CODE');
        $addr2->addChild('PHONE');
        $addr2->addChild('CELL_PHONE');
        $addr2->addChild('EMAIL');

        //RECIPIENT NODE
        $itemNode5 = $dataNode->addChild('RECIPIENT');
        $itemNode5->addChild('FIRST_NAME', $data['transaction']->receiver_first_name);
        $itemNode5->addChild('MIDDLE_NAME');
        $itemNode5->addChild('LAST_NAME', $data['transaction']->receiver_last_name);
        $itemNode5->addChild('MOTHER_M_NAME', $data['transaction']->receiver_middle_name);
        $itemNode5->addChild('IDENTIF_TYPE_CD');
        $itemNode5->addChild('IDENTIF_NM');
        $foreign = $itemNode5->addChild('FOREIGN_NAME');
        $foreign->addChild('FIRST_NAME');
        $foreign->addChild('MIDDLE_NAME');
        $foreign->addChild('LAST_NAME');
        $foreign->addChild('MOTHER_M_NAME');
        $addr3 = $itemNode5->addChild('ADDRESS');
        $addr3->addChild('ADDRESS', '');
        $addr3->addChild('CITY', $data['transaction']->receiver_city);
        $addr3->addChild('STATE_CD', $data['transaction']->receiver_state);
        $addr3->addChild('COUNTRY_CD',$data['transaction']->receiver_country);
        $addr3->addChild('ZIP_CODE', $data['transaction']->receiver_postal_code);
        $addr3->addChild('PHONE', $data['transaction']->receiver_phone_mobile);
        $addr3->addChild('CELL_PHONE');
        $addr3->addChild('EMAIL');

        //SENDER IDENTIFICATION NODE
        $itemNode6 = $dataNode->addChild('SENDER_IDENTIFICATION');
        $itemNode6->addChild('TYPE_CD');
        $itemNode6->addChild('ISSUER_CD');
        $itemNode6->addChild('ISSUER_STATE_CD');
        $itemNode6->addChild('ISSUER_COUNTRY_CD');
        $itemNode6->addChild('IDENTIF_NM');
        $itemNode6->addChild('EXPIRATION_DT');

        //ADDITIONAL INFORMATION NODE
        $itemNode6 = $dataNode->addChild('ADDITIONAL_INFO');
        $itemNode6->addChild('DOB_DT');
        $itemNode6->addChild('OCCUPATION', 'student');
        $itemNode6->addChild('SSN');
        $itemNode6->addChild('SOURCE_OF_FUNDS_DS');
        $itemNode6->addChild('REASON_OF_TRANS_DS');

        $xml = $rootNode->asXML();

      
        // $logFilePath = rtrim(FCPATH, '/\\') . '/assets/uploads/bts-'
        //     .$dataTXN['CONFIRMATION_NM'] . '.xml';
        // @file_put_contents($logFilePath, $xml);
        $xmlString = ((string) $xml);

        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString);

        $xmlString = str_replace('</ExecTR>', '', $xmlString);

        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>"
            .trim($xmlString)."</ExecTR>", XSD_ANYXML);

        $url = "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL";
        $soap_client = new \SoapClient(
            $url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE, )
        );

        $headers = array();
        $headera = array(
            'SESSION_ID' => $sessionID,
            'USER_NAME'  => 'GPWSSAMSOS',
            'USER_DOMAIN' => 'BTS_AGENTS',
            'USER_PASS' => 'samsosexpress', );
        $headerb = array(
            'FROM' => '',
            'TO' => '', );
        $headers[] = new \SOAPHeader(
            'http://www.btsincusa.com/gp/',
            'SECURITY',
            $headera
        );
        $headers[] = new \SOAPHeader(
            'http://www.btsincusa.com/gp/',
            'ADDRESSING',
            $headerb
        );
        $soap_client->__setSoapHeaders($headers);

        $response = $soap_client->ExecTR($paramXMLString);
    
        $xmlFinal   = simplexml_load_string(
            $response->ExecTRResult->any,
            'SimpleXMLElement', LIBXML_NOCDATA
        );
        $response = json_decode(json_encode((array)$xmlFinal), true);

        $return = "";
        if ($response['OPCODE'] == '0001'
            || $response['OPCODE'] == '0002'
        ) {
            $return = array('status' => '200', 'message' => 'Transaction Successful.');
        } else {
            $return = array(
                'status' => '400',
                'message' => $response['PROCESS_MSG']
                    .$response['ERROR_PARAM_FULL_NAME']);
        }

        $request = "\n SALE REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        $response = "\n SALE RESPONSE XML :\n" . $response['PROCESS_MSG'].$response['ERROR_PARAM_FULL_NAME'];        
        $line = "\n ---------------------------- \n ";
        $file = 'BTSRequestResponse.txt';        
        file_put_contents($file, $request . $response . $line, FILE_APPEND | LOCK_EX);
        return $return;


    }

    /**
     * Query previous transaction details.
     *
     * This transaction will be used to inquire from BTS the status
     * of a money transfer transaction. BTS will review its database to
     * retrieve the information and status of the transaction.After
     * processing the Money Transfer Query, BTS will provide AGT‟s money
     * transfer system with an immediate response to inform the results.
     *
     * @return [type] [description]
     */
    public function doQRTR($confirmationNumber)
    {
        $sessionID = $this->doUSRL();

        $rootNode = new \SimpleXMLElement("<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>");
        $requestNode = $rootNode->addChild('REQUEST');

        $requestNode->addChild('AGENT_CD', 'SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE', 'QRTR');

        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('CONFIRMATION_NM', $confirmationNumber);

        //AGENT NODE
        $agentNode = $dataNode->addChild('AGENT');
        $agentNode->addChild('ORDER_NM', '120300110153');
        $agentNode->addChild('REGION_SD', 'N/A');
        $agentNode->addChild('BRANCH_SD', 'N/A');
        $agentNode->addChild('STATE_CD', 'CA');
        $agentNode->addChild('COUNTRY_CD', 'USA');
        $agentNode->addChild('USER_NAME', 'N/A');
        $agentNode->addChild('SUP_USER_NAME', 'N/A');
        $agentNode->addChild('TERMINAL', 'N/A');
        $agentNode->addChild('AGENT_DT', '20120311');
        $agentNode->addChild('AGENT_TM', '231634');

        $senderIdentificationNode = $dataNode->addChild('SENDER_IDENTIFICATION');
        $senderIdentificationNode->addChild('TYPE_CD');
        $senderIdentificationNode->addChild('ISSUER_CD');
        $senderIdentificationNode->addChild('ISSUER_STATE_CD');
        $senderIdentificationNode->addChild('ISSUER_COUNTRY_CD');
        $senderIdentificationNode->addChild('IDENTIF_NM');
        $senderIdentificationNode->addChild('EXPIRATION_DT');

        $additionalIdentificationNode = $dataNode->addChild('ADDITIONAL_INFO');
        $additionalIdentificationNode->addChild('DOB_DT');
        $additionalIdentificationNode->addChild('OCCUPATION', 'Project Manager');
        $additionalIdentificationNode->addChild('SSN');
        $additionalIdentificationNode->addChild('SOURCE_OF_FUNDS_DS');
        $additionalIdentificationNode->addChild('REASON_OF_TRANS_DS');

        $xml = $rootNode->asXML();
        $xmlString = ((string) $xml);
        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString);

        $xmlString = str_replace('</ExecTR>', '', $xmlString);

        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>".trim($xmlString)."</ExecTR>", XSD_ANYXML);

        $url = "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL";
        $soap_client = new \SoapClient($url, array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE, ));

        $headers = array();
        $headera = array(
                                'SESSION_ID' => $sessionID,
                                'USER_NAME'  => 'GPWSSAMSOS',
                                'USER_DOMAIN' => 'BTS_AGENTS',
                                'USER_PASS' => 'samsosexpress', );
        $headerb = array(
                                'FROM' => '',
                                'TO' => '', );

        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);

        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);

        $soap_client->__setSoapHeaders($headers);
        $salvRes = $soap_client->ExecTR($paramXMLString);

        $response = $soap_client->ExecTR($paramXMLString);
        $xmlFinal   = simplexml_load_string(
            $saleRes->ExecTRResult->any,
            'SimpleXMLElement', LIBXML_NOCDATA
        );

        $response = json_decode(json_encode((array) $xmlFinal), true);
        $return = "";

        if ($response['OPCODE'] == '1300') {
            $return = array('status' => '200', 'message' => 'Transaction Successful.');
        } else {
            $return = array(
                'status' => '400',
                'message' => $response['PROCESS_MSG']
                . ' ' . $response['ERROR_PARAM_FULL_NAME']);
        }

        return $return;
    }

    /**
     * Cancel BTS Transaction
     *
     * This transaction will be used to instruct BTS to stop payment
     * of a previously sent Money Transfer transaction. BTS will
     * verify its database to determine if the transaction is in a
     * status still permitting cancellation. If the transaction can
     * be canceled, BTS will change the status of the transaction and
     * update its database. Regardless of the outcome of the process,
     * BTS will communicate its response back to AGT immediately.
     *
     * PROCESS_REASON_CD Values:
     *     PAE CNLI PAYMENT AGENT ERROR
     *     RBC CNLI REQUESTED BY CUSTOMER
     *     SYE CNLI SYSTEM ERROR
     *     TEE CNLI TELLER ERROR
     *
     * RESPONSE OPCODE 0702, TRANS_STATUS_CD OCA
     *
     * @param $string $confirmationNumber TXN confirmation number
     *
     * @return array array('status', 'message')
     */
    public function doCNLI(array $cnliData)
    {
        $sessionID = $this->doUSRL();

        $rootNode = new \SimpleXMLElement("<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>");
        $requestNode = $rootNode->addChild('REQUEST');

        $requestNode->addChild('AGENT_CD', 'SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE', 'CNLI');

        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('CONFIRMATION_NM', $cnliData['CONFIRMATION_NM']);

        $dataNode->addChild('PROCESS_REASON_CD', 'RBC');
        $dataNode->addChild('WHOLESALE_FX');
        $dataNode->addChild('FEE_AM', $cnliData['FEE_AM']);
        $dataNode->addChild('DISCOUNT_AM');
        $dataNode->addChild('DISCOUNT_REASON_CD');

        $agentNode = $dataNode->addChild('AGENT');
        $agentNode->addChild('ORDER_NM', '120300110153');
        $agentNode->addChild('REGION_SD', 'N/A');
        $agentNode->addChild('BRANCH_SD', 'N/A');
        $agentNode->addChild('STATE_CD', 'CA');
        $agentNode->addChild('COUNTRY_CD', 'USA');
        $agentNode->addChild('USER_NAME', 'N/A');
        $agentNode->addChild('SUP_USER_NAME', 'N/A');
        $agentNode->addChild('TERMINAL', 'N/A');
        $agentNode->addChild('AGENT_DT', '20120311');
        $agentNode->addChild('AGENT_TM', '231634');

        $senderIdentificationNode = $dataNode->addChild('SENDER_IDENTIFICATION');
        $senderIdentificationNode->addChild('TYPE_CD');
        $senderIdentificationNode->addChild('ISSUER_CD');
        $senderIdentificationNode->addChild('ISSUER_STATE_CD');
        $senderIdentificationNode->addChild('ISSUER_COUNTRY_CD');
        $senderIdentificationNode->addChild('IDENTIF_NM');
        $senderIdentificationNode->addChild('EXPIRATION_DT');

        $xml = $rootNode->asXML();
        $xmlString = ((string) $xml);
        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString);

        $xmlString = str_replace('</ExecTR>', '', $xmlString);

        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>".trim($xmlString)."</ExecTR>", XSD_ANYXML);

        $url = "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL";
        $soap_client = new \SoapClient($url, array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE, ));

        $headers = array();
        $headera = array(
                                'SESSION_ID' => $sessionID,
                                'USER_NAME'  => 'GPWSSAMSOS',
                                'USER_DOMAIN' => 'BTS_AGENTS',
                                'USER_PASS' => 'samsosexpress', );
        $headerb = array(
                                'FROM' => '',
                                'TO' => '', );
        $headers[] = new \SOAPHeader(
            'http://www.btsincusa.com/gp/',
            'SECURITY',
            $headera
        );
        $headers[] = new \SOAPHeader(
            'http://www.btsincusa.com/gp/',
            'ADDRESSING',
            $headerb
        );
        $soap_client->__setSoapHeaders($headers);
       $response = $soap_client->ExecTR($paramXMLString);
    
        $xmlFinal   = simplexml_load_string(
            $response->ExecTRResult->any,
            'SimpleXMLElement', LIBXML_NOCDATA
        );
        $response = json_decode(json_encode((array)$xmlFinal), true);

        $return = "";
        if ($response['OPCODE'] == '0702') {
            $return = array('status' => '200', 'message' => 'Transaction Successful.');
        } else {
            $return = array(
                'status' => '400',
                'message' => $response['PROCESS_MSG']
                    .$response['ERROR_PARAM_FULL_NAME']);
        }

        $request = "\n CNLI REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        $response = "\n CNLI RESPONSE XML :\n" . $response['PROCESS_MSG'].$response['ERROR_PARAM_FULL_NAME'];        
        $line = "\n ---------------------------- \n ";
        $file = 'BTSRequestResponse.txt';        
        file_put_contents($file, $request . $response . $line, FILE_APPEND | LOCK_EX);
        

        return $return;
    }

    /**
     * Change any transaction detail.
     *
     * This transaction will be used by AGT to Confirm a Change of
     * instruction Request transaction to BTS. BTS will verify the
     * information and apply BTS‟ business rules. After processing
     * a COI Request, BTS will provide AGT with a Response to that
     * COI Request to inform the results. As part of this process,
     * BTS will verify that each transaction is compliant with the
     * Anti-money laundering, Bank Secrecy Act and OFAC regulations.
     *
     * @param array $txnChanges Data nodes changed are RECIPIENT,
     *                          RECEPIENT->ADDRESS, SENDER_IDENTIFICATION
     *
     * @return [type] [description]
     */
    public function doCORI($txn)
    {
        $txnChanges = $txn['REQUEST']['DATA'];

        $sessionID = $this->doUSRL();
        $rootNode = new \SimpleXMLElement("<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>");
        $requestNode = $rootNode->addChild('REQUEST');

        $requestNode->addChild('AGENT_CD', 'SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE', 'CORI');

        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('CONFIRMATION_NM', $txnChanges['CONFIRMATION_NM']);

        // PROCESS_REASON_CD values
        // PAE  PAYMENT AGENT ERROR
        // RBC  REQUESTED BY CUSTOMER
        // SYE  SYSTEM ERROR
        // TEE  TELLER ERROR
        // SUCCESS RESPONSE OPCODE 0902

        $dataNode->addChild('PROCESS_REASON_CD', 'RBC');
        $dataNode->addChild('WHOLESALE_FX');
        $dataNode->addChild('FEE_AM', $txnChanges['FEE_AM']);
        $dataNode->addChild('DISCOUNT_AM');
        $dataNode->addChild('DISCOUNT_REASON_CD');
        $dataNode->addChild('R_SMS_MSG_REQ');
        $dataNode->addChild('EXCH_RATE_FX', $txnChanges['EXCH_RATE_FX']);

        $agentNode = $dataNode->addChild('AGENT');
        $agentNode->addChild('ORDER_NM', '120300110153');
        $agentNode->addChild('REGION_SD', 'N/A');
        $agentNode->addChild('BRANCH_SD', 'N/A');
        $agentNode->addChild('STATE_CD', 'CA');
        $agentNode->addChild('COUNTRY_CD', 'USA');
        $agentNode->addChild('USER_NAME', 'N/A');
        $agentNode->addChild('SUP_USER_NAME', 'N/A');
        $agentNode->addChild('TERMINAL', 'N/A');
        $agentNode->addChild('AGENT_DT', '20120311');
        $agentNode->addChild('AGENT_TM', '231634');

        $recepientNode = $dataNode->addChild('RECIPIENT');
        $recepientNode->addChild('FIRST_NAME', $txnChanges['RECIPIENT']['FIRST_NAME']);
        $recepientNode->addChild('MIDDLE_NAME', $txnChanges['RECIPIENT']['MIDDLE_NAME']);
        $recepientNode->addChild('LAST_NAME', $txnChanges['RECIPIENT']['LAST_NAME']);
        $recepientNode->addChild('MOTHER_M_NAME', $txnChanges['RECIPIENT']['MOTHER_M_NAME']);
        $recepientNode->addChild('IDENTIF_TYPE_CD', $txnChanges['RECIPIENT']['IDENTIF_TYPE_CD']);
        $recepientNode->addChild('IDENTIF_NM', $txnChanges['RECIPIENT']['IDENTIF_NM']);

        $recepientForeignNameNode = $recepientNode->addChild('FOREIGN_NAME');
        $recepientForeignNameNode->addChild('FIRST_NAME');
        $recepientForeignNameNode->addChild('MIDDLE_NAME');
        $recepientForeignNameNode->addChild('LAST_NAME');
        $recepientForeignNameNode->addChild('MOTHER_M_NAME');

        $recepientAddressNode = $recepientNode->addChild('ADDRESS');
        $recepientAddressNode->addChild('ADDRESS', $txnChanges['RECIPIENT']['ADDRESS']['ADDRESS']);
        $recepientAddressNode->addChild('CITY', $txnChanges['RECIPIENT']['ADDRESS']['CITY']);
        $recepientAddressNode->addChild('STATE_CD', $txnChanges['RECIPIENT']['ADDRESS']['STATE_CD']);
        $recepientAddressNode->addChild('COUNTRY_CD', $txnChanges['RECIPIENT']['ADDRESS']['COUNTRY_CD']);
        $recepientAddressNode->addChild('ZIP_CODE', $txnChanges['RECIPIENT']['ADDRESS']['ZIP_CODE']);
        $recepientAddressNode->addChild('PHONE', $txnChanges['RECIPIENT']['ADDRESS']['PHONE']);
        $recepientAddressNode->addChild('CELL_PHONE', $txnChanges['RECIPIENT']['ADDRESS']['CELL_PHONE']);
        $recepientAddressNode->addChild('EMAIL', $txnChanges['RECIPIENT']['ADDRESS']['EMAIL']);

        $senderIdentificationNode = $dataNode->addChild('SENDER_IDENTIFICATION');
        $senderIdentificationNode->addChild('TYPE_CD');
        $senderIdentificationNode->addChild('ISSUER_CD');
        $senderIdentificationNode->addChild('ISSUER_STATE_CD');
        $senderIdentificationNode->addChild('ISSUER_COUNTRY_CD');
        $senderIdentificationNode->addChild('IDENTIF_NM');
        $senderIdentificationNode->addChild('EXPIRATION_DT');

        $additionalIdentificationNode = $dataNode->addChild('ADDITIONAL_INFO');
        $additionalIdentificationNode->addChild('DOB_DT');
        $additionalIdentificationNode->addChild('OCCUPATION');
        $additionalIdentificationNode->addChild('SSN');
        $additionalIdentificationNode->addChild('SOURCE_OF_FUNDS_DS');
        $additionalIdentificationNode->addChild('REASON_OF_TRANS_DS');

        $xml = $rootNode->asXML();
        $xmlString = ((string) $xml);
        $xmlString = str_replace('<?xml version="1.0"?>
<ExecTR xmlns="http://www.btsincusa.com/gp/">', '', $xmlString);

        $xmlString = str_replace('</ExecTR>', '', $xmlString);

        $paramXMLString = new \SoapVar("<ExecTR xmlns='http://www.btsincusa.com/gp/'>".trim($xmlString)."</ExecTR>", XSD_ANYXML);

        $url = "https://test.globalplatform.ws/ts/gpcs/gpts/transactionservice.asmx?WSDL";
        $soap_client = new \SoapClient($url, array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE, ));

        $headers = array();
        $headera = array(
                        'SESSION_ID' => $sessionID,
                        'USER_NAME'  => 'GPWSSAMSOS',
                        'USER_DOMAIN' => 'BTS_AGENTS',
                        'USER_PASS' => 'samsosexpress', );
        $headerb = array(
                        'FROM' => '',
                        'TO' => '', );
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'SECURITY', $headera);
        $headers[] = new \SOAPHeader('http://www.btsincusa.com/gp/', 'ADDRESSING', $headerb);
        $soap_client->__setSoapHeaders($headers);

       $response = $soap_client->ExecTR($paramXMLString);
    
        $xmlFinal   = simplexml_load_string(
            $response->ExecTRResult->any,
            'SimpleXMLElement', LIBXML_NOCDATA
        );
        $response = json_decode(json_encode((array)$xmlFinal), true);

        $return = "";
        if ($response['OPCODE'] == '0902') {
            $return = array('status' => '200', 'message' => 'Transaction Successful.');
        } else {
            $return = array(
                'status' => '400',
                'message' => $response['PROCESS_MSG']
                    .$response['ERROR_PARAM_FULL_NAME']);
        }

        $request = "\n CORI REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        $response = "\n CORI RESPONSE XML :\n" . $response['PROCESS_MSG'].$response['ERROR_PARAM_FULL_NAME'];        
        $line = "\n ---------------------------- \n ";
        $file = 'BTSRequestResponse.txt';        
        file_put_contents($file, $request . $response . $line, FILE_APPEND | LOCK_EX);
        

        return $return;
    }

    /**
     * List previous transactions with status details
     *
     * This Transaction will be used by AGT to get all the Notifications
     * that are in Not Synchronized status by BTS. This Transaction is
     * to inform AGT the details about the payment, payment reversal of
     * a money transfer or the successful execution of the deposit
     * instruction of a Money Transfer transaction. Also this process
     * will be used by BTS to inform AGT that a Money Transfer transaction
     * that was on a pending status because of OFAC and/or BSA and/or
     * Deny List has been released, rejected or seized by BTS.
     *
     * @param int $transactions Number
     *
     * @return array array(status, message)
     *
     * if status == 200,    message =
     * array (size=1)
     *                               'NOTIFICATION_ID' => string '271209' (length=6)
     *                               'CONFIRMATION_NM' => string '11630997134' (length=11)
     *                               'AGENT_ORDER_NM' => string 'WF720928200' (length=11)
     *                               'MOVEMENT_TYPE_CODE' => string 'PAYI' (length=4)
     *                               'OPCODE' => string '1100' (length=4)
     *                               'MOVEMENT_DT' => string '20121130' (length=8)
     *                               'MOVEMENT_TM' => string '152033' (length=6)
     *                               'AGENT_CD' => string 'FCR' (length=3)
     *                               'AGENT_REGION_SD' => string '1' (length=1)
     *                               'AGENT_BRANCH_SD' => string '9600' (length=4)
     *                               'AGENT_USER_NAME' => string 'GPWSFEDECREDITO' (length=15)
     *                               'AGENT_TERMINAL' => string 'TERMINAL' (length=8)
     *                               'IDENTIF_TYPE_CD' => string 'DUI' (length=3)
     *                               'IDENTIF_ISSUER_CD' => string 'STA' (length=3)
     *                               'IDENTIF_ISSUER_STATE_CD' => string '999' (length=3)
     *                               'IDENTIF_NM' => string '021363433' (length=9)
     *                               'IDENTIF_ISSUER_COUNTRY_CD' => string 'SLV' (length=3)
     *                               'BANK_REF_NM' => string '0960000990990003' (length=16)
     */
    public function doNOTI($numberOfTxn = 10)
    {
        $sessionID = $this->doUSRL();
        
        $rootNode = new \SimpleXMLElement( "<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>" );
        $requestNode = $rootNode->addChild('REQUEST');
        
        $requestNode->addChild('AGENT_CD','SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE','NOTI');

        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('ROW_COUNT', $numberOfTxn);

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

        $response = $soap_client->ExecTR($paramXMLString);
        
        $xmlFinal   = simplexml_load_string(
            $response->ExecTRResult->any,
            'SimpleXMLElement', LIBXML_NOCDATA
        );
        $responseArray = json_decode(json_encode((array)$xmlFinal), true);

        $notiReturnArray = array();
        $notiCount = count($responseArray['NOTIFICATIONS']['NOTIFICATION']);

        for( $i = 0; $i < $notiCount; $i++) {
            $notiReturnArray[$i] = $responseArray['NOTIFICATIONS']
                ['NOTIFICATION'][$i]['@attributes'];
        }
        
        $return = "";
        if ($responseArray['OPCODE'] == '1304') {
            $return = array('status' => '200', 'message' => $notiReturnArray);
        } else {
            $return = array(
                'status' => '400',
                'message' => $responseArray['PROCESS_MSG']
                    . ' ' . $responseArray['ERROR_PARAM_FULL_NAME']);
        }

        $request = "\n NOTI REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        $response = "\n NOTI RESPONSE XML :\n" . json_encode($responseArray);        
        $line = "\n ---------------------------- \n ";
        $file = 'BTSRequestResponse.txt';        
        file_put_contents($file, $request . $response . $line, FILE_APPEND | LOCK_EX);

        return $return;
    }

   /**
     * BTS NOTC
     *
     * Change BTS TXN status.
     * 
     * @param  array  $notcData Array of NOTC DATA
     * 
     * $notcData = array(
     *     'NOTIFICATION_ID' => string '271209'
     *     'CONFIRMATION_NM' => string '11630997134'
     *     'AGENT_ORDER_NM' => string 'WF720928200'
     *     'MOVEMENT_TYPE_CODE' => string 'PAYI'
     *     'OPCODE' => string '1100'
     *     )
     * 
     * @return array          array(status, message)
     */
    public function doNOTC(array $notcData)
    {
        $sessionID = $this->doUSRL();
        
        $rootNode = new \SimpleXMLElement( "<ExecTR xmlns='http://www.btsincusa.com/gp/'> </ExecTR>" );
        $requestNode = $rootNode->addChild('REQUEST');
        
        $requestNode->addChild('AGENT_CD','SEM');
        $requestNode->addChild('AGENT_TRANS_TYPE_CODE','NOTC');
        
        $dataNode = $requestNode->addChild('DATA');
        $dataNode->addChild('NOTIFICATION_ID', $notcData['NOTIFICATION_ID']);
        $dataNode->addChild('CONFIRMATION_NM', $notcData['CONFIRMATION_NM']);
        $dataNode->addChild('AGENT_ORDER_NM', $notcData['AGENT_ORDER_NM']);
        $dataNode->addChild('OPCODE', $notcData['OPCODE']);

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
         $response = $soap_client->ExecTR($paramXMLString);
        
        $xmlFinal   = simplexml_load_string(
            $response->ExecTRResult->any,
            'SimpleXMLElement', LIBXML_NOCDATA
        );
        $responseArray = json_decode(json_encode((array)$xmlFinal), true);
        $return = "";
        if ($responseArray['OPCODE'] == '1305') {
            $return = array('status' => '200', 'message' => $responseArray['PROCESS_MSG']);
        } else {
            $return = array(
                'status' => '400',
                'message' => $responseArray['PROCESS_MSG']
                    . ' ' . $responseArray['ERROR_PARAM_FULL_NAME']);
        }

        $request = "\n NOTC REQUEST XML:\n" . $soap_client->__getLastRequest() . "\n"; 
        $response = "\n NOTC RESPONSE XML :\n" . json_encode($responseArray);        
        $line = "\n ---------------------------- \n ";
        $file = 'BTSRequestResponse.txt';        
        file_put_contents($file, $request . $response . $line, FILE_APPEND | LOCK_EX);

        return $return;
    }
}

