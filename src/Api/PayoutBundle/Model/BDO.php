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

class BDO
{
    protected $container;
    private $url;
    /**
     * [__construct description]
     */

    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->url = "https://203.177.92.217/RemittanceWSApi/RemitAPIService?wsdl";

    }

    public function pickupCash($data=null){
                $xml='<?xml version="1.0" encoding="UTF-8"?>
                        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.bdo.com.ph/RemitAPI">
                          <SOAP-ENV:Body>
                            <ns1:apiRequest>
                              <ns1:userName>?</ns1:userName>
                              <ns1:password>?</ns1:password>
                              <ns1:signedData>?</ns1:signedData>
                              <ns1:conduitCode>?</ns1:conduitCode>
                              <ns1:locatorCode>?</ns1:locatorCode>
                              <ns1:referenceNo>?</ns1:referenceNo>
                              <ns1:transDate>?</ns1:transDate>
                              <ns1:senderFirstname>?</ns1:senderFirstname>
                              <ns1:senderLastname>?</ns1:senderLastname>
                              <ns1:senderMiddlename>?</ns1:senderMiddlename>
                              <ns1:senderAddress1>?</ns1:senderAddress1>
                              <ns1:senderAddress2>?</ns1:senderAddress2>
                              <ns1:senderPhone>?</ns1:senderPhone>
                              <ns1:receiverFirstname>?</ns1:receiverFirstname>
                              <ns1:receiverLastname>?</ns1:receiverLastname>
                              <ns1:receiverMiddlename>?</ns1:receiverMiddlename>
                              <ns1:receiverAddress1>?</ns1:receiverAddress1>
                              <ns1:receiverAddress2>?</ns1:receiverAddress2>
                              <ns1:receiverMobilePhone>?</ns1:receiverMobilePhone>
                              <ns1:receiverGender>?</ns1:receiverGender>
                              <ns1:receiverBirthDate>?</ns1:receiverBirthDate>
                              <ns1:transactionType>?</ns1:transactionType>
                              <ns1:payableCode>?</ns1:payableCode>
                              <ns1:bankCode>?</ns1:bankCode>
                              <ns1:branchName>?</ns1:branchName>
                              <ns1:accountNo>?</ns1:accountNo>
                              <ns1:landedCurrency>?</ns1:landedCurrency>
                              <ns1:landedAmount>?</ns1:landedAmount>
                              <ns1:messageToBene1>?</ns1:messageToBene1>
                              <ns1:messageToBene2>?</ns1:messageToBene2>
                            </ns1:apiRequest>
                          </SOAP-ENV:Body>
                        </SOAP-ENV:Envelope>
                        ';
                $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE, )
                );
                $actual = $soap_client->__soapCall('PickUpCash',(array)$xml);
                $response = json_encode((array)$actual);

                print_r($response);die;
     
    }
    public function pickupCebuana($data=null){      
                $xml=$this->xml($data);
                 $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE, )
                );
                $actual = $soap_client->__soapCall('PickUpCebuana',(array)$xml);
        
    }
    public function pickupMLLhuillier($data=null){      
                $xml=$this->xml($data);
                 $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE, )
                );
                $actual = $soap_client->__soapCall('PickUpMLLhuillier',(array)$xml);

    }
    public function BdoAKRemitter($data=null){        
                $xml=$this->xml($data);
                 $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE, )
                );
                $actual = $soap_client->__soapCall('BDOAKRemitter',(array)$xml);

    }

    public function xml($data=null){
        $wsdl='<?xml version="1.0" encoding="UTF-8"?>
                <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.bdo.com.ph/RemitAPI">
                  <SOAP-ENV:Body>
                    <ns1:apiRequest>
                      <ns1:userName>'.$data["userName"].'</ns1:userName>
                      <ns1:password>'.$data["password"].'</ns1:password>
                      <ns1:signedData>'.$data["signedData"].'</ns1:signedData>
                      <ns1:conduitCode>'.$data["conduitCode"].'</ns1:conduitCode>
                      <ns1:locatorCode>'.$data["locatorCode"].'</ns1:locatorCode>
                      <ns1:referenceNo>'.$data["referenceNo"].'</ns1:referenceNo>
                      <ns1:transDate>'.$data["transDate"].'</ns1:transDate>
                      <ns1:senderFirstname>'.$data["senderFirstname"].'</ns1:senderFirstname>
                      <ns1:senderLastname>'.$data["senderLastname"].'</ns1:senderLastname>
                      <ns1:senderMiddlename>'.$data["senderMiddlename"].'</ns1:senderMiddlename>
                      <ns1:senderAddress1>'.$data["senderAddress1"].'</ns1:senderAddress1>
                      <ns1:senderAddress2>'.$data["senderAddress2"].'</ns1:senderAddress2>
                      <ns1:senderPhone>'.$data["senderPhone"].'</ns1:senderPhone>
                      <ns1:receiverFirstname>'.$data["receiverFirstname"].'</ns1:receiverFirstname>
                      <ns1:receiverLastname>'.$data["receiverLastname"].'</ns1:receiverLastname>
                      <ns1:receiverMiddlename>'.$data["receiverMiddlename"].'</ns1:receiverMiddlename>
                      <ns1:receiverAddress1>'.$data["receiverAddress1"].'</ns1:receiverAddress1>
                      <ns1:receiverAddress2>'.$data["receiverAddress2"].'</ns1:receiverAddress2>
                      <ns1:receiverMobilePhone>'.$data["receiverMobilePhone"].'</ns1:receiverMobilePhone>
                      <ns1:receiverGender>'.$data["receiverGender"].'</ns1:receiverGender>
                      <ns1:receiverBirthDate>'.$data["receiverBirthDate"].'</ns1:receiverBirthDate>
                      <ns1:transactionType>'.$data["transactionType"].'</ns1:transactionType>
                      <ns1:payableCode>'.$data["payableCode"].'</ns1:payableCode>
                      <ns1:bankCode>'.$data["bankCode"].'</ns1:bankCode>
                      <ns1:branchName>'.$data["branchName"].'</ns1:branchName>
                      <ns1:accountNo>'.$data["accountNo"].'</ns1:accountNo>
                      <ns1:landedCurrency>'.$data["landedCurrency"].'</ns1:landedCurrency>
                      <ns1:landedAmount>'.$data["landedAmount"].'</ns1:landedAmount>
                      <ns1:messageToBene1>'.$data["messageToBene1"].'</ns1:messageToBene1>
                      <ns1:messageToBene2>'.$data["messageToBene2"].'</ns1:messageToBene2>
                    </ns1:apiRequest>
                  </SOAP-ENV:Body>
                </SOAP-ENV:Envelope>';

            return $wsdl;

    }
  

  

   
}

