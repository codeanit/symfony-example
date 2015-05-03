<?php

namespace BackendBundle\Library\Queue\Worker;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Queue\AbstractQueueWorker as BaseWorker;

/**
 * Class BdoWorker
 * @package BackendBundle\Library\Queue\Worker
 */
class BdoWorker extends BaseWorker
{
    private $encryptedPassowrd;

    /**
     * @var array
     */
    private $type = [
        'pickupCash'=> ['transactionType'=>'01','payableCode'=>'BPMM'],
        'pickupMLLhuillier' => ['transactionType'=>'42','payableCode'=>'BPMM'],
        'pickupCebuana' => ['transactionType'=>'CL','payableCode'=>'BPMM']
    ];

    /**
     * [__construct description]
     */
    function __construct() {}

    /**
     * Find BDO create function
     */
    private function mapBDOCreate($payoutPayerName)
    {
        $settings = $this->getWorkerSetting();
        $bdoCreateFunction = array_search($payoutPayerName, $settings, true);

        if ($bdoCreateFunction && array_key_exists($bdoCreateFunction, $this->type)) {
            return $bdoCreateFunction;
        }
    }

    /**
     * @return string
     */
    protected function getWorkerServiceName()
    {
        return 'bdo';
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function createTransaction(OperationsQueue $queue, $args = [])
    {
        $bdoCreateFunction = $this->mapBDOCreate(
            $queue->getTransaction()->getPayoutPayerName()
        );

        $createData = $this->prepareCreateData(
            $queue->getTransaction()->getTransactionCode(),
            $bdoCreateFunction
        );

        $actual = $this->sendWSDLHttpRequest(
            $this->url,
            $createData,
            $bdoCreateFunction
        );

        $status = ( $actual->responseCode == '00'
            || $actual->responseCode == '0' ) ? 'SUCCESS' : 'ERROR';

        $this->em->getRepository('BackendBundle:Log')
            ->addLog(
                $this->getWorkerSetting('service_id'),
                'pickupCebuana',
                json_encode($xml),
                json_encode($actual),
                $status
            );

    }



    /**
     * @param OperationsQueue $queue
     * @param array $argss
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
        // TODO: Implement changeTransaction() method.
    }

    /**
     * @param array $arg
     * @return mixed
     */
    public function confirmTransaction()
    {
        // TODO: Implement changeTransaction() method.
    }

    /**
     * Returns encrypted password
     *
     * @param string $password BDO Password
     *
     * @return string
     */
    private function getEncryptedPassword($password)
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

    /**
     * @param null $data
     */
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

    /**
     * @param null $data
     */
    public function pickupCebuana($data=null){

        return;
    }

    /**
     * @param null $data
     */
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
     *
     * @param  [array] $data
     *
     * @return [array]
     */
    private function prepareCreateData($transaction, $type= null) {

        $relatedFunctionData = $this->type[$type];
        $bankAccountNumber = $bankBranch = $bankAccountNumber = '';

        if ( strtolower($transaction->getTransactionType()) =='bank' ) {
            $bankAccountNumber=$transaction->getBeneficiaryAccountNumber();
            $bankBranch=$transaction->getBeneficiaryBankBranch();
        }

        $param = array(
            "SignatureType"                => "TXN",
            "CLEAR_BRS_PASSWORD"           => $this->password,
            "TRANSACTION_REFERENCE_NUMBER" => $transaction->getTransactionCode(),
            "LANDED_AMOUNT"                => $transaction->getPayoutAmount(),
            "TRANSACTION_DATE"             => $transaction->getRemittanceDate()->format('Y-m-d'),
            "ACCOUNT_NUMBER"               => $bankAccountNumber
        );

        return array(
            'userName'              =>  $this->password,
            'password'              =>  $this->encryptedPassowrd,
            'signedData'            =>  $this->getSignedData($param),
            'conduitCode'           =>  'FG',
            'locatorCode'           =>  '220',
            'referenceNo'           =>  $transaction->getTransactionCode(),
            'transDate'             =>  $transaction->getRemittanceDate()->format('Y-m-d'),
            'senderFirstname'       =>  $transaction->getRemitterfirstName(),
            'senderLastname'        =>  $transaction->getRemitterLastName(),
            'senderMiddlename'      =>  $transaction->getRemitterMiddleName(),
            'senderAddress1'        =>  $transaction->getRemitterAddress(),
            'senderAddress2'        =>  '',
            'senderPhone'           =>  $transaction->getRemitterPhoneMobile(),
            'receiverFirstname'     =>  $transaction->getBeneficiaryFirstName(),
            'receiverLastname'      =>  $transaction->getBeneficiaryLastName(),
            'receiverMiddlename'    =>  $transaction->getBeneficiaryMiddleName(),
            'receiverAddress1'      =>  $transaction->getBeneficiaryAddress(),
            'receiverAddress2'      =>  '',
            'receiverMobilePhone'   =>  $transaction->getBeneficiaryPhoneMobile(),
            'receiverBirthDate'     =>  $transaction->getBeneficiary->format('Y-m-d'),
            'receiverGender'        =>  '',
            'transactionType'       =>  $relatedFunctionData['transactionType'],
            'payableCode'           =>  $relatedFunctionData['payableCode'],
            'bankCode'              =>  'BDO',
            'branchName'            =>  $bankBranch,
            'accountNo'             =>  $bankAccountNumber,
            'landedCurrency'        =>  $transaction->getPayoutCurrency(),
            'landedAmount'          =>  $transaction->getPayoutAmount(),
            'messageToBene1'        =>  'messsage',
            'messageToBene2'        =>  'message',
        );
    }

    /**
     * @return mixed
     */
    public function prepareCredentials()
    {
        $this->url                  = $this->getWorkerSetting('url');
        $this->username             = $this->getWorkerSetting('username');
        $this->password             = $this->getWorkerSetting('password');
        $this->encryptedPassowrd    = $this->getWorkerSetting('encrypted_password')
            ? $this->getWorkerSetting('encrypted_password')
            : $this->getEncryptedPassword($this->password);
    }

    /**
     * @param Transactions $transaction
     * @param array $args
     * @return mixed
     */
    public function enqueueTransaction(Transactions $transaction, $args = [])
    {
        // TODO: Implement enqueueTransaction() method.
    }
}