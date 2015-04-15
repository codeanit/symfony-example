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
        echo 1234; die;
        $settings = $this->getWorkerSetting();
        $parameters = [];
        $url = (isset($settings['url'])) ? $settings['url'] : false;
        $dataPattern = '/(\<diffgr:diffgram)[\s\S]+(\<\/diffgr:diffgram>)/';
        $outputToSend = [
            'operation' => 'create',
            'message' => '' ,
            'notify_source' => $queue->getTransactionSource(),
            'source' => 'intermex',
            'status' => '' ,
            'confirmation_number' => $queue->getTransaction()->getTransactionCode(),
        ];
        $serverResponse = null;

        try {
            $parameters = $this->prepareCreateParameters($queue);
            $response = $this->sendHttpRequest($url, $parameters, 'AltaEnvioN');

            preg_match_all(
                $dataPattern,
                $response->AltaEnvioNResult->any, //$response_main->AltaEnvioNResult->any
                $matches
            );

            $xmlFinal = simplexml_load_string(
               $matches[0][0],
               'SimpleXMLElement',
               LIBXML_NOCDATA | LIBXML_PARSEHUGE
            );

            $serverResponse = json_encode((array) $xmlFinal);

            if ($xmlFinal->NewDataSet->ENVIO->tiExito == '1') {
                $outputToSend['message'] = 'Transaction Successfully Created.';
                $outputToSend['status'] = 'paid';

            } else {
                $outputToSend['message'] = 'Unable to create Transaction.';
                $outputToSend['status'] = 'failed';
            }

            $this->updateExecutedQueue($queue);

        } catch(\Exception $e) {
            $this->logger->error('main', [$e->getMessage()]);
        }

        $this->em->getRepository('BackendBundle:Log')
            ->addLog(
                $settings['service_id'],
                'AltaEnvioN',
                json_encode($parameters),
                $serverResponse
            );

        return $outputToSend;
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

    public function create()
    {
        echo $this->getEncryptedPassword("bdoRemit1!");
        echo $this->getSignedData();
        die;
    }

    public function pickupCash($data=null){
        $xml=$this->xml($data ,'pickupCash');
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE, )
        );
        $actual = $soap_client->__soapCall('PickUpCash',$xml);
        if($actual->responseCode=='00' || $actual->responseCode=='0'){
            $this->log->addInfo($this->service_id, 'pickupCash', $xml, $actual);
        }else{
            $this->log->addError($this->service_id, 'pickupCash', $xml, $actual);
        }

        return $actual;



    }
    public function pickupCebuana($data=null){
        $xml=$this->xml($data,'pickupCebuana');
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE, )
        );
        $actual = $soap_client->__soapCall('PickUpCebuana',(array)$xml);
        if($actual->responseCode=='00' || $actual->responseCode=='0'){
            $this->log->addInfo($this->service_id, 'pickupCebuana', $xml, $actual);
        }else{
            $this->log->addError($this->service_id, 'pickupCebuana', $xml, $actual);
        }

        return $actual;


    }
    public function pickupMLLhuillier($data=null){
        $xml=$this->xml($data,'pickupMLLhuillier');
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE, )
        );
        $actual = $soap_client->__soapCall('PickUpMLLhuillier',(array)$xml);
        if($actual->responseCode=='00' || $actual->responseCode=='0'){
            $this->log->addInfo($this->service_id, 'pickupMLLhuillier', $xml, $actual);
        }else{
            $this->log->addError($this->service_id, 'pickupMLLhuillier', $xml, $actual);
        }

        return $actual;

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
        return $actual;

    }

    /**
     * [xml String Generator]
     * @param  [array] $data
     * @return [String]       [xml string]
     */
    public function xml($data=null,$type=null){
        //$acc=(strtolower($data['transaction']->transaction_type)=='bank')?
        //   $data['transaction']->beneficiary_account_number:'';
        $methodData=$this->type[$type];
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
            'transactionType'=>$methodData['transactionType'],
            'payableCode'=>$methodData['payableCode'],
            'bankCode'=>'BDO',
            'branchName'=>$bankBranch,
            'accountNo'=>$acc,
            'landedCurrency'=>$data['transaction']->payout_currency,
            'landedAmount'=>$data['transaction']->payout_amount,
            'messageToBene1'=>'messsage',
            'messageToBene2'=>'message',
        );
        print_r($wsdl);
        return $wsdl;

    }
}