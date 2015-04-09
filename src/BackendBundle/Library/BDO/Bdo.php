<?php
/**
 * Created by PhpStorm.
 * User: anit
 * Date: 2/25/15
 * Time: 4:06 PM.
 */
namespace BackendBundle\Library\BDO;


/**
 * Class Bdo
 * @package LibraryBundle\BusinessLogics
 */
class Bdo
{   
    private $url;
    private $type;

    /**
     * [__construct description]
     */
    function __construct() {
        $this->url = "https://203.177.92.217/RemittanceWSApi/RemitAPIService?wsdl";
        $this->type=array(
            'pickup'=>array('transactionType'=>'01','payableCode'=>'BPMM'),
            'pickupMLhullier'=>array('transactionType'=>'42','payableCode'=>'BPMM'),
            'pickupCebuana'=>array('transactionType'=>'CL','payableCode'=>'BPMM'),
            );
    }

    /**
     * Returns encrypted password
     *
     * @param string $password BDO Password
     *
     * @return string
     */
    public function getEncryptedPassword($password)
    {
        $queryString = "java -cp "
            . "src/BackendBundle/Library/BDO/RemittanceAPITool.jar:."
            . " src.BackendBundle.Library.BDO.EncryptPassword password=" . $password;
        $ePass = "";

        try {
            $ePass = shell_exec($queryString);
        } catch(\Exception $e)
        {
            $ePass = "ERROR";
        }

        return $ePass;
    }

    /**
     * Return TXN Signed Data
     *
     * @param string $SignatureType
     * @param string $CLEAR_BRS_PASSWORD
     * @param string $TRANSACTION_REFERENCE_NUMBER
     * @param string $KEYSTORE_FILE
     * @param string $KEYSTORE_PASSWORD
     * @param string $KEY_NAME
     * @param string $KEY_PASSWORD
     * @param string $LANDED_AMOUNT
     * @param string $TRANSACTION_DATE
     * @param string $ACCOUNT_NUMBER
     *
     * @return string
     */
    public function getSignedData($para)
    {
        $queryString = "java -cp src/BackendBundle/Library/BDO/RemittanceAPITool.jar:."
            ." src/BackendBundle/Library/BDO/SignedData"
            ." SignatureType=" . $para['SignatureType']
            .",CLEAR_BRS_PASSWORD=" . $para['CLEAR_BRS_PASSWORD']
            .",TRANSACTION_REFERENCE_NUMBER=" . $para['TRANSACTION_REFERENCE_NUMBER']
            .",KEYSTORE_FILE=src/BackendBundle/Library/BDO/certificate-and-jks/220FGOFC1"
            .",KEYSTORE_PASSWORD=FGM#374040w"
            .",KEY_NAME=fgdc"
            .",KEY_PASSWORD=FGM#374040w"
            .",LANDED_AMOUNT=" . $para['LANDED_AMOUNT']
            .",TRANSACTION_DATE=" . $para['TRANSACTION_DATE']
            .",ACCOUNT_NUMBER=" . $para['ACCOUNT_NUMBER'];


        return shell_exec($queryString);
    }

    public function create()
    {
        echo $this->getEncryptedPassword("bdoRemit1!");
        echo $this->getSignedData();
        die;
    }

     public function pickupCash($data=null){
                $xml=$this->xml($data);
                //print_r($xml);die;
                $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE, )
                );
                $actual = $soap_client->__soapCall('PickUpCash',$xml);
                print_r($actual);die;
                
                $response = json_encode((array)$actual);

     
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

    /**
     * [xml String Generator]
     * @param  [array] $data 
     * @return [String]       [xml string]
     */
    public function xml($data=null){
        //$acc=(strtolower($data['transaction']->transaction_type)=='bank')?
         //   $data['transaction']->beneficiary_account_number:'';

        $mainDate=explode(' ',$data['transaction']->remittance_date);
        $date=explode('-',$mainDate[0]);
        if (strtolower($data['transaction']->transaction_type)=='bank') {
        $acc=$data['transaction']->beneficiary_account_number;        
        $bankBranch=$data['transaction']->beneficiary_bank_branch;
        $bankAccountNumber=$data['transaction']->beneficiary_account_number;
        }else{
            $acc=$bankBranch=$bankAccountNumber='';
        }
        $param = array(
                    "SignatureType" => "TXN",
                    "CLEAR_BRS_PASSWORD" => "bdoRemit1!",
                    "TRANSACTION_REFERENCE_NUMBER" => 
                            $data['transaction']->transaction_code,
                    "LANDED_AMOUNT" => $data['transaction']->payout_amount,
                    "TRANSACTION_DATE"=> $date[0].'-'.$date[1].'-'.$date[2],
                    "ACCOUNT_NUMBER" => $acc);

        $wsdl=  array(
                    'userName'=>'220FGOFC1',
                    'password'=>$this->getEncryptedPassword("bdoRemit1!"),
                    'signedData'=>$this->getSignedData($param),
                    'conduitCode'=>'FG',
                    'locatorCode'=>'220',
                    'referenceNo'=>$data['transaction']->transaction_code,
                    'transDate'=>$date[0].'-'.$date[1].'-'.$date[2],
                    'senderFirstname'=>$data['transaction']->remitter_first_name,
                    'senderLastname'=>$data['transaction']->remitter_last_name,
                    'senderMiddlename'=>$data['transaction']->remitter_middle_name,
                    'senderAddress1'=>$data['transaction']->remitter_address,
                    'senderAddress2'=>'',
                    'senderPhone'=>$data['transaction']->remitter_phone_mobile,
                    'receiverFirstname'=>$data['transaction']->beneficiary_first_name,
                    'receiverLastname'=>$data['transaction']->beneficiary_last_name,
                    'receiverMiddlename'=>$data['transaction']->beneficiary_middle_name,
                    'receiverAddress1'=>$data['transaction']->beneficiary_address,
                    'receiverAddress2'=>'',
                    'receiverMobilePhone'=>$data['transaction']->beneficiary_phone_mobile,
                    'receiverBirthDate'=>'1991-07-24',                   
                    'transactionType'=>'01',                   
                    'payableCode'=>'BPMM',                   
                    'bankCode'=>'BDO',                   
                    'branchName'=>$bankBranch,                   
                    'accountNo'=>$acc,                   
                    'landedCurrency'=>$data['transaction']->payout_currency,                   
                    'messageToBene1'=>'messsage',                   
                    'messageToBene2'=>'message',                   
                     );
                
            return $wsdl;

    } 

}
