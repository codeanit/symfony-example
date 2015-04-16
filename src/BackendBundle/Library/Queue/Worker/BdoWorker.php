<?php

namespace BackendBundle\Library\Queue\Worker;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Queue\AbstractQueueWorker as BaseWorker;

/**
 * Class IntermexWorker
 * @package BackendBundle\Library\Queue\Worker
 */
class BdoWorker extends BaseWorker
{
    private $url;
    private $type;

    /**
     * [__construct description]
     */
    function __construct() {               
        $this->url = "https://203.177.92.217/RemittanceWSApi/RemitAPIService?wsdl";
        $this->type=array(
            'pickupCash'=>array('transactionType'=>'01','payableCode'=>'BPMM'),
            'pickupMLLhuillier'=>array('transactionType'=>'42','payableCode'=>'BPMM'),
            'pickupCebuana'=>array('transactionType'=>'CL','payableCode'=>'BPMM'),
            );
    }
    /**
     * @param array $arg
     * @return mixed
     */
    public function confirmTransaction()
    {
        echo 1234; die;
    }

    /**
     * @return string
     */
    protected function getWorkerServiceName()
    {
        return 'bdo';
    }

    /**
     * @param Transactions $transaction
     * @param array $args
     * @return mixed|void
     */
    public function enqueueTransaction(Transactions $transaction, $args = [])
    {
        // TODO: Implement enqueueTransaction() method.
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function createTransaction(OperationsQueue $queue, $args = [])
    {   
        $transaction = $queue->getTransaction();
        $settings = $this->getWorkerSetting();      
        if($settings['pickup_cash'] == $transaction->getPayoutPayerName()){           
            $this->pickupCash($transaction);
        }
        if($settings['pickup_cebuana'] == $transaction->getPayoutPayerName()){
            $this->pickupMLLhuillier($transaction);
        }
        if($settings['pickup_mllhuillier'] == $transaction->getPayoutPayerName()){
            $this->pickupCebuana($transaction);
        }
       return;
    }

    /**
     * @param array $credentials
     */
    private function sendHttpRequest($wsdlUrl, array $credentials, $action)
    {
        $params = [
            'trace' => true,
            'exception' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
        ];
        $client = new \SoapClient($wsdlUrl, $params);
        $response = $client->{$action}($credentials);

        return $response;
    }


    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function cancelTransaction(OperationsQueue $queue, $args = [])
    {
        // TODO: Implement cancelTransaction() method.
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function changeTransaction(OperationsQueue $queue, $args = [])
    {
        echo 'Changing Transactions!!', PHP_EOL;
        // TODO: Implement changeTransaction() method.
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

    public function pickupCash($data=null){ 

        try {
          $xml=$this->xml($data ,'pickupCash');        
          $actual = $this->sendHttpRequest($this->url, $xml, 'PickUpCash');     
          $status=($actual->responseCode=='00' || $actual->responseCode=='0')?'SUCCESS':'ERROR'; 
                  
          $this->em->getRepository('BackendBundle:Log')
               ->addLog(
                  $this->getWorkerSetting('service_id'),
                  'pickupCash',
                  json_encode($xml),
                  json_encode($actual),
                  $status               
              );        
        } catch (\Exception $e) {
           echo $e->getMessage(),$e->getFile(),$e->getLine();die;
        }
        return;
    }

    public function pickupCebuana($data=null){
        $xml=$this->xml($data,'pickupCebuana');
        $actual = $this->sendHttpRequest($this->url, $xml, 'pickupCebuana'); 

        $status=($actual->responseCode=='00' || $actual->responseCode=='0')?'SUCCESS':'ERROR';
        $this->em->getRepository('BackendBundle:Log')
             ->addLog(
                $this->getWorkerSetting('service_id'),
                'pickupCebuana',
                json_encode($xml),
                json_encode($actual),
                $status               
            );  
        return;
    }
    
    public function pickupMLLhuillier($data=null){        
        $xml=$this->xml($data,'pickupMLLhuillier');
        $actual = $this->sendHttpRequest($this->url, $xml, 'pickupMLLhuillier'); 

        $status=($actual->responseCode=='00' || $actual->responseCode=='0')?'SUCCESS':'ERROR';
        $this->em->getRepository('BackendBundle:Log')
             ->addLog(
                $this->getWorkerSetting('service_id'),
                'pickupMLLhuillier',
                json_encode($xml),
                json_encode($actual),
                $status               
            );  
        return;
    }   

    /**
     * [xml String Generator]
     * @param  [array] $data
     * @return [String]       [xml string]
     */
    public function xml($transaction=null,$type=null){

        $methodData=$this->type[$type];       
        if (strtolower($transaction->getTransactionType())=='bank') {
            $acc=$transaction->getBeneficiaryAccountNumber();
            $bankBranch=$transaction->getBeneficiaryBankBranch();
            $bankAccountNumber=$transaction->getBeneficiaryAccountNumber();
        }else{
            $acc=$bankBranch=$bankAccountNumber='';
        }
        $param = array(
            "SignatureType" => "TXN",
            "CLEAR_BRS_PASSWORD" => "bdoRemit1!",
            "TRANSACTION_REFERENCE_NUMBER" =>
                $transaction->getTransactionCode(),
            "LANDED_AMOUNT" => $transaction->getPayoutAmount(),
            "TRANSACTION_DATE"=> $transaction->getRemittanceDate()->format('Y-m-d'),
            "ACCOUNT_NUMBER" => $acc);

        $wsdl=  array(
            'userName'=>'220FGOFC1',
            'password'=>$this->getEncryptedPassword("bdoRemit1!"),
            'signedData'=>$this->getSignedData($param),
            'conduitCode'=>'FG',
            'locatorCode'=>'220',
            'referenceNo'=>$transaction->getTransactionCode(),
            'transDate'=>$transaction->getRemittanceDate()->format('Y-m-d'),
            'senderFirstname'=>$transaction->getRemitterfirstName(),
            'senderLastname'=>$transaction->getRemitterLastName(),
            'senderMiddlename'=>$transaction->getRemitterMiddleName(),
            'senderAddress1'=>$transaction->getRemitterAddress(),
            'senderAddress2'=>'',
            'senderPhone'=>$transaction->getRemitterPhoneMobile(),
            'receiverFirstname'=>$transaction->getBeneficiaryFirstName(),
            'receiverLastname'=>$transaction->getBeneficiaryLastName(),
            'receiverMiddlename'=>$transaction->getBeneficiaryMiddleName(),
            'receiverAddress1'=>$transaction->getBeneficiaryAddress(),
            'receiverAddress2'=>'',
            'receiverMobilePhone'=>$transaction->getBeneficiaryPhoneMobile(),
            'receiverBirthDate'=>'1991-07-24',
            'receiverGender'=>'',
            'transactionType'=>$methodData['transactionType'],
            'payableCode'=>$methodData['payableCode'],
            'bankCode'=>'BDO',
            'branchName'=>$bankBranch,
            'accountNo'=>$acc,
            'landedCurrency'=>$transaction->getPayoutCurrency(),
            'landedAmount'=>$transaction->getPayoutAmount(),
            'messageToBene1'=>'messsage',
            'messageToBene2'=>'message',
        );     
        return $wsdl;
    }
}