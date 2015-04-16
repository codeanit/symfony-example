<?php

namespace BackendBundle\Library\Queue\Worker;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Queue\AbstractQueueWorker as BaseWorker;

/**
 * Class TransNetworkWorker
 * @package BackendBundle\Library\Queue\Worker
 */
class TransNetworkWorker extends BaseWorker
{
    /**
     * @param Transactions $transaction
     * @param array $args
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
        $data=$txn;
        if (strtolower($data['transaction']->transaction_type)=='bank') {
            $accType='';
            $acc=$data['transaction']->beneficiary_account_number;
            $bankBranch=$data['transaction']->beneficiary_bank_branch;
            $bankAccountNumber=$data['transaction']->beneficiary_account_number;
            $bankName=$data['transaction']->beneficiary_bank_name;
            $paymentType = 'D';
        }else{
            $acc=$bankBranch=$bankName=$bankAccountNumber='';
            $accType='';
            $paymentType = 'C';

        }
        $param=array(
            'Username'=>'samsos',
            'Password'=>'TNC1234!',
            'TransferDetails'=>array('AccountInst'=>$bankName,
                'AccountNo'=>$acc,
                'AccountType'=>$accType,
                'AgentCountry'=>$data['transaction']->remitting_agent_country,
                'AgentID'=>$data['transaction']->remitting_agent_id,
                'AgentPostalCode'=>$data['transaction']->remitting_agent_postal_code,
                'AgentState'=>$data['transaction']->remitting_agent_state,
                'BenAddress'=>$data['transaction']->beneficiary_address,
                'BenCOB'=>'',
                'BenCity'=>$data['transaction']->beneficiary_city,
                'BenCountry'=>$data['transaction']->beneficiary_country,
                'BenDOB'=>'',
                'BenFirst'=>$data['transaction']->beneficiary_first_name,
                'BenIDCountry'=>'',
                'BenIDNumber'=>'',
                'BenIDType'=>'',
                'BenMLast'=>'',
                'BenNationality'=>'',
                'BenOccupation'=>'',
                'BenPLast'=>$data['transaction']->beneficiary_last_name,
                'BenPostalCode'=>$data['transaction']->beneficiary_postal_code,
                'BenSSN'=>'',
                'BenState'=>$data['transaction']->beneficiary_state,
                'BenTel'=>$data['transaction']->beneficiary_phone_mobile,
                'ClaimCode'=>'SYSTEM',
                'ClientAddress'=>$data['transaction']->remitter_address,
                'ClientCOB'=>$data['transaction']->remitter_COB,
                'ClientCity'=>$data['transaction']->remitter_city,
                'ClientCountry'=>$data['transaction']->remitter_country,
                'ClientDOB'=>$data['transaction']->remitter_DOB,
                'ClientFirst'=>$data['transaction']->remitter_first_name,
                'ClientIDCountry'=>$data['transaction']->remitter_id_issued_country,
                'ClientIDNumber'=>$data['transaction']->remitter_id_number,
                'ClientIDType'=>$data['transaction']->remitter_id_type,

                'ClientGender'=>'',
                'ClientIDExpDate'=>'',
                'ClientIDState'=>'',
                'ClientMLast'=>'',
                'ClientMiddleName'=>'',
                'ClientNationality'=>$data['transaction']->remitter_country,
                'ClientOccupation'=>'',
                'ClientPLast'=>$data['transaction']->remitter_last_name,
                'ClientPostalCode'=>$data['transaction']->remitter_postal_code,
                'ClientSOB'=>'',
                'ClientSSN'=>'',
                'ClientState'=>$data['transaction']->remitter_state,
                'ClientTel'=>$data['transaction']->remitter_phone_mobile,
                'ConfCode'=>'SYSTEM',
                'CustomField1'=>'',
                'CustomField2'=>'',
                'InternalRefNumber'=>'SYSTEM is Optional',
                'Note'=>'',
                'OriginationAmount'=>$data['transaction']->remitting_amount,
                'OriginationCountry'=>$data['transaction']->remitter_country,
                'OriginationCurrency'=>$data['transaction']->remitting_currency,
                'OriginationState'=>$data['transaction']->remitter_state,
                'PayerLocationID'=>$data['transaction']->payer_location,
                'PayerName'=>$data['transaction']->payer_name,
                'PaymentAmount'=>$data['transaction']->remitting_amount,
                'PaymentCountry'=>$data['transaction']->remitting_amount,
                'PaymentCurrency'=>$data['transaction']->remitting_amount,
                'PaymentType'=>$paymentType,
                'TransactionDate'=>$data['transaction']->remittance_date)
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
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function cancelTransaction(OperationsQueue $queue, $args = [])
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
    public function confirmTransaction(array $arg = [])
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
    }

    /**
     * @param $list
     */
    private function __processUpdatedList($list)
    {
        $count=0;

        foreach ($list as $value) {

            if( $value['Update_Code']=='1000'|| $value['Update_Code']=='1001')
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

    /**
     * Send status confirmation to the TransNetwork
     *
     * @param array $param
     *
     * @return void
     */
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
//            $this->log->addInfo($this->service_id, 'ConfirmUpdate', $param, $result);

        }else{
//            $this->log->addError($this->service_id, 'ConfirmUpdate', $param, $result);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getWorkerServiceName()
    {
        return 'transnetwork';
    }
}