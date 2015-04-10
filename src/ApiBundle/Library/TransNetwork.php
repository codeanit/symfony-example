<?php
/**
 * First Global Data
 *
 * @category DEX_API
 * @package  Api\PayoutBundle\Library
 * @author   Manish Chalise <mchalise@gmail.com>
 * @license  http://firstglobalmoney.com/licensee description
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */
namespace ApiBundle\Library;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * TransNetwork TPS XML Webservice Interface for Money Transmitters
 *
 * @category DEX_API
 * @package  Api\PayoutBundle\Library
 * @author   Manish Chalise <mchalise@gmail.com>
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

class TransNetwork
{

    protected $container; 
    protected $url;
    protected $log;
    protected $service_id;
    protected $database;


    // protected $operationMap = array(
    //                            'create' => 'createTransfer',
    //                            'modify' => '',
    //                            'update' => '',
    //                            'cancel' => '',
    //                           );

    /**
     * Constructor creates Service Container interface and assign WSDL address
     * 
     * @param ContainerInterface $container Service Container DI
     */

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->url       = 'https://devsit.transnetwork.com/TNCWS33/TransmitterInterface.asmx?wsdl';  
        $this->log=$this->container->get('log');      
        $connection=$this->container->get('connection');
        $result=$connection->getCred('transnetwork');      
        //$this->database = json_decode(base64_decode($result[0]['credentials']));        
        //$this->url=$this->database->url;        
        $this->service_id=$result[0]['id'];      
    }


    /**
     * Used to map the method according to operation 
     * 
     * @param string $operation [Operation name]
     * @param array  $args      [Parameters]
     * 
     * @return void            
     */
    public function process($operation, $args)
    {
        return call_user_func_array(array($this, $this->operationMap[$operation]), [$args]);

    }

    /**
     * The CreateTransfer WebMethod allows a transmitter to create a new funds 
     * transfer order for payment by a Transnetwork associated payer
     * 
     * @param  array $txn [requied fields for create transaction]
     * 
     * @return void
     */
    public function create($txn = null)
    {       
        $data=$txn;
        $param=array(
            'Username'=>'samsos',
            'Password'=>'TNC1234!',
            'TransferDetails'=>array('AccountInst'=>'',
                                     'AccountNo'=>'',
                                     'AccountType'=>'',
                                     'AgentCountry'=>'',
                                     'AgentID'=>'',
                                     'AgentPostalCode'=>'',
                                     'AgentState'=>'',
                                     'BenAddress'=>'',
                                     'BenCOB'=>'',
                                     'BenCity'=>'',
                                     'BenCountry'=>'',
                                     'BenDOB'=>'',
                                     'BenFirst'=>'',
                                     'BenIDCountry'=>'',
                                     'BenIDNumber'=>'',
                                     'BenIDType'=>'',
                                     'BenMLast'=>'',
                                     'BenNationality'=>'',
                                     'BenOccupation'=>'',
                                     'BenPLast'=>'',
                                     'BenPostalCode'=>'',
                                     'BenSSN'=>'',
                                     'BenState'=>'',
                                     'BenTel'=>'',
                                     'ClaimCode'=>'',
                                     'ClientAddress'=>'',
                                     'ClientCOB'=>'',
                                     'ClientCity'=>'',
                                     'ClientCountry'=>'',
                                     'ClientDOB'=>'',
                                     'ClientFirst'=>'',
                                     'ClientGender'=>'',
                                     'ClientIDCountry'=>'',
                                     'ClientIDExpDate'=>'',
                                     'ClientIDNumber'=>'',
                                     'ClientIDState'=>'',
                                     'ClientIDType'=>'',
                                     'ClientMLast'=>'',
                                     'ClientMiddleName'=>'',
                                     'ClientNationality'=>'',
                                     'ClientOccupation'=>'',
                                     'ClientPLast'=>'',
                                     'ClientPostalCode'=>'',
                                     'ClientSOB'=>'',
                                     'ClientSSN'=>'',
                                     'ClientState'=>'',
                                     'ClientTel'=>'',
                                     'ConfCode'=>'',
                                     'CustomField1'=>'',
                                     'CustomField2'=>'',
                                     'InternalRefNumber'=>'',
                                     'Note'=>'',
                                     'OriginationAmount'=>'',
                                     'OriginationCountry'=>'',
                                     'OriginationCurrency'=>'',
                                     'OriginationState'=>'',
                                     'PayerLocationID'=>'',
                                     'PayerName'=>'',
                                     'PaymentAmount'=>'',
                                     'PaymentCountry'=>'',
                                     'PaymentCurrency'=>'',
                                     'PaymentType'=>'',
                                     'TransactionDate'=>'',)
            );
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );
        $response = $soap_client->CreateTransfer($param);
        
    }

    /**
     * The CreateTransfer WebMethod allows a transmitter to create a new funds 
     * transfer order for payment by a Transnetwork associated payer
     * 
     * @param  array $txn [requied fields for create transaction]
     * 
     * @return void
     */
    public function queryUpdate($txn = null)
    {  
        $data=$txn;
        $param=array(
            'Username'=>'samsos',
            'Password'=>'TNC1234!',
            'NumberOfUpdates'=>'1',           
            );
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );
        $response = $soap_client->GetUpdates($param);

        $extractedData = explode('</xs:schema>', $response->GetUpdatesResult->any);
        $xmlFinal   = simplexml_load_string(
                $extractedData[1],
                'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
            );


        $response = json_decode(json_encode((array) $xmlFinal), true);
        
        if (array_key_exists('UpdatesList', $response))
        {
            $this->__processUpdatedList($response['UpdatesList']['Updates']);
        }
   
        return;
       
    }
    public function addToQueue($data)
    {      
        $conn=$this->container->get('database_connection');       
        $queueData = array(
                            'transaction_source' => 'cdex',
                            'transaction_service' => 'tb',
                            'operation' => 'notify', 
                            'parameter' => json_encode($data),
                            'is_executed' => 0,
                            'creation_datetime' => date('Y-m-d H:i:s')
                          );
       
        $check_queue = $conn->insert('operations_queue', $queueData);
        return $check_queue;
    }
    
    private function __processUpdatedList($list)
    {
        $count=0;
        foreach ($list as $value) {   
            if(true || $value['Update_Code']=='1000' || $value['Update_Code']=='1001')
            {
                $paramConfirm=array(
                          "Username"=>"samsos",
                          "Password"=>"TNC1234!",
                          "UpdateID"=>$value['Update_Number'],
                          "ClaimNumber"=>$value['Claim_Number']
                    );
                $confData=$this->confirmUpdate($paramConfirm);

                $this->log->addInfo($this->service_id, 'queryUpdate', $param, $list[$count]);
                
                if($confData->ReturnCode == '1000'){
                    $return = array('code' => '200',
                                'operation'=>'notify',
                                'message' => $list[$count]['Message'],
                                'notify_source'=>'tb',
                                'source'=>'transnetwork',                            
                                'status' => 'complete' , 
                                'change_status'=>'',                           
                                'confirmation_number' =>$list[$count]['Update_Number']                           
                               );
                    $this->addToQueue($return); 
                }             
            }else{
                $this->log->addError($this->service_id, 'queryUpdate', $param, $list[$count]);
                $return = array('code' => '400',
                            'operation'=>'notify',
                            'message' => $list[$count]['Message'],
                            'notify_source'=>'tb',
                            'source'=>'transnetwork',
                            'status' => 'failed' , 
                            'change_status'=>'',                           
                            'confirmation_number' =>$list[$count]['Update_Number']                           
                           );
                $this->addToQueue($return);
            }
            $count++;
        } 
    }

    public function confirmUpdate($param)
    {
         $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );
        $response = $soap_client->ConfirmUpdate($param);
        $result=$response->ConfirmUpdateResult;
        if($result->ReturnCode == '1000'){
            $this->log->addInfo($this->service_id, 'ConfirmUpdate', $param, $result);            
        }else{
            $this->log->addError($this->service_id, 'ConfirmUpdate', $param, $result);
        }

        return $result;   
      
    }

    /**
     * The createCancel WebMethod allows a transmitter to cancel
     * transfer order for payment by a Transnetwork associated payer
     * 
     * @param  array $txn [requied fields for create transaction]
     * 
     * @return void
     */
    public function cancel($ClaimNumber=null,$date=null)
    {  
        $param=array(
            'Username'=>'samsos',
            'Password'=>'TNC1234!',
            'ClaimNumber'=>$ClaimNumber,           
            'CancellationDate'=>$date,           
            );
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );
        $response = $soap_client->CreateCancel($param);
        $result = $response->CreateCancelResult;    
        if($result->ReturnCode=='1000'){
             $this->log->addInfo($this->service_id, 'cancel', $param, $result);
             $return = array('code' => '200',
                            'operation'=>'notify',
                            'message' => $result->Message,
                            'notify_source'=>'tb',
                            'source'=>'transnetwork',                            
                            'status' => 'complete' , 
                            'change_status'=>'',                           
                            'confirmation_number' => ''                           
                           );
        }else{
            $this->log->addError($this->service_id, 'cancel', $param, $result);
            $return = array('code' => '400',
                            'operation'=>'notify',
                            'message' => $result->Message,
                            'notify_source'=>'tb',
                            'source'=>'transnetwork',
                            'status' => 'failed' , 
                            'change_status'=>'',                           
                            'confirmation_number' =>''                           
                           );
        }
        
        return $return;
    }
}
