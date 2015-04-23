<?php

namespace BackendBundle\Library\Queue\Worker;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Notification\Notifier\TbNotifier;
use BackendBundle\Library\Queue\AbstractQueueWorker as BaseWorker;
use JMS\Serializer\Serializer;

/**
 * Class IntermexWorker
 * @package BackendBundle\Library\Queue\Worker
 */
class IntermexWorker extends BaseWorker
{
    /**
     * Third party token $iIdAgencia
     */
    private $token;

    /**
     * @var TbNotifier
     */
    private $tbNotifier;

    /**
     * @var \JMS\Serializer\Serializer
     */
    protected $serializer;

    public function __construct()
    {
        $this->url = $this->getWorkerSetting('url');
        $this->username = $this->getWorkerSetting('username');
        $this->pasword = $this->getWorkerSetting('password');

        $this->token = $this->getWorkerSetting('token')
            ? $this->getWorkerSetting('token') : $this->generateToken();
    }

    /**
     * Calls Conectar and sets token if not set.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function generateToken()
    {
        $response = $this->sendWSDLHttpRequest(
            $this->url,
            ['vPassword' => $this->password, 'vUsuario' => $this->username],
            'Conectar'
        );

        if (! $response->ConectarResult) {
            throw new \Exception('Internal Error :: Invalid response from "Conectar"!!');
        }

        $token = $response->ConectarResult;

        $this->setWorkerSetting('token', $token);

        return $token;
    }

    /**
     * @return string
     */
    protected function getWorkerServiceName()
    {
        return 'intermex';
    }

    /**
     * @param TbNotifier $tbNotifier
     */
    public function setTbNotifier($tbNotifier)
    {
        $this->tbNotifier = $tbNotifier;
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function createTransaction(OperationsQueue $queue, $args = [])
    {
        $parameters = [];
        $flag = false;
        $dataPattern = '/(\<diffgr:diffgram)[\s\S]+(\<\/diffgr:diffgram>)/';
        $outputToSend = [
            'message'             => '' ,
            'status'              => '' ,
            'transaction_code' => $queue->getTransaction()->getTransactionCode(),
        ];
        $serverResponse = null;

        try {
            $parameters = $this->prepareCreateParameters($queue);
            $response = $this->sendHttpRequest($this->url, $parameters, 'AltaEnvioN');

            preg_match_all(
                $dataPattern,
                $response->AltaEnvioNResult->any,
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
                $outputToSend['status'] = 'processing';
                $flag = true;

            } else {
                $outputToSend['message'] = 'Unable to create Transaction.';
                $outputToSend['status'] = 'error';
            }

            $this->tbNotifier->notify(
                'create',
                $outputToSend['status'],
                $outputToSend['message'],
                $queue->getTransaction()->getTransactionCode()
            );

        } catch(\Exception $e) {
            $this->logger->error('main', [$e->getMessage()]);
        }

        /**
         * if $flag = true > then reExecute > false
         * if $flag = false > then reExecute > true
         *
         */
        $reExecute = !$flag;
        $this->updateExecutedQueue($queue, $reExecute);
        $this->em->getRepository('BackendBundle:Log')
            ->addLog(
                $this->getWorkerSetting('service_id'),
                'AltaEnvioN',
                json_encode($parameters),
                $serverResponse
            );

        return $outputToSend;
    }

    /**
     * @param \BackendBundle\Entity\OperationsQueue $queue
     * @throws \Exception
     * @return array
     */
    private function prepareCreateParameters(OperationsQueue $queue)
    {
        $transaction    = $queue->getTransaction();
        $payoutId       = $transaction->getTransactionType();
        $payoutId       = (strtoupper($payoutId) == "BANK") ? '3' : '1';
        $iIdDestino     = $payoutId == 1 ? 49 : $transaction->getPayoutAgentId();
        $currencyId     = $transaction->getPayoutCurrency();
        $currencyId     = (strtoupper($currencyId) == "USD" ||
                            strtoupper($currencyId) == "CAD") ? '2' : '1';
        $remittanceDate = $transaction->getRemittanceDate()->format('d/m/Y');
        $isBankDeposit  = '';
        $bankBranch     = '';
        $bankAccount    = '';

        $settings = $this->getWorkerSetting();
        $url = (isset($settings['url'])) ? $settings['url'] : false;
        $username = (isset($settings['username'])) ? $settings['username'] : false;
        $password = (isset($settings['password'])) ? $settings['password'] : false;

        if (! $url){
            throw new \Exception('Fatal Error :: Url not specified for the Intermex Api!!');
        }

        if (! $username or ! $password) {
            throw new \Exception('Fatal Error :: Username or Password not specified for the Intermex Api!!');
        }

        if ($payoutId == '2') {
            $isBankDeposit = 1;
            $bankBranch = $transaction->getBeneficiaryBankBranch();
            $bankAccount = $transaction->getBeneficiaryAccountNumber();
        }

        $credentials = [
            'iIdAgencia'          => $this->token,
            'vReferencia'         => $transaction->getTransactionCode(),
            'dtFechaEnvio'        => $remittanceDate,
            'iConsecutivoAgencia' => $transaction->getId(),
            'mMonto'              => $transaction->getPayoutAmount(),//$data['transaction']->payout_amount,
            'fTipoCambio'         => $transaction->getExchangeRate(),//$data['transaction']->exchange_rate,
            'mMontoPago'          => $transaction->getRemittingAmount(),//$data['transaction']->remitting_amount,
            'siIdDivisaPago'      => $currencyId,
            'tiIdTipoPagoEnvio'   => $payoutId,
            'vNomsRemitente'      => $transaction->getRemitterFirstName(),// $data['transaction']->remitter_first_name,
            'vApedosRemitente'    => $transaction->getRemitterLastName(),// $data['transaction']->remitter_last_name,
            'vDireccionRem'       => $transaction->getRemitterAddress(),// $data['transaction']->remitter_address,
            'vTelefonoRem'        => $transaction->getRemitterPhoneMobile(),// $data['transaction']->remitter_phone_mobile,
            'vCondadoRem'         => $transaction->getRemitterCountry(),// $data['transaction']->remitter_country,
            'vEstadoRem'          => $transaction->getRemitterState(),// $data['transaction']->remitter_state,
            'vNomsBeneficiario'   => $transaction->getBeneficiaryFirstName(),// $data['transaction']->beneficiary_first_name,
            'vApedosBeneficiario' => $transaction->getBeneficiaryLastName(),// $data['transaction']->beneficiary_last_name,
            'vDireccionBen'       => $transaction->getBeneficiaryAddress(),// $data['transaction']->beneficiary_address,
            'vTelefonoBen'        => $transaction->getBeneficiaryPhoneMobile(),// $data['transaction']->beneficiary_phone_mobile,
            'vCiudadBenef'        => $transaction->getBeneficiaryCountry(),// $data['transaction']->beneficiary_country,
            'vEstadoBenef'        => $transaction->getBeneficiaryState(),// $data['transaction']->beneficiary_state,
            'iIdDestino'          => $iIdDestino,// $data['transaction']->payout_agent_id,
            'vMensaje'            => 'message to beneficiary',
            'vInstruccionPago'    => 'agency comment',
            'vSucursal'           => $isBankDeposit,
            'vCuenta'             => $bankBranch,
            'vClabe'              => '',
            'siIdTipoDeposito'    => $bankAccount,
            'vNumerotarjeta'      => '',
            'vMontoCom'           => $transaction->getFee(),// $data['transaction']->fee
        ];

        return $credentials;
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function cancelTransaction(OperationsQueue $queue, $args = [])
    {
        $parameters             = [];
        $webServiceResponse     = [];
        $transaction            = $queue->getTransaction();
        $dataPattern            = '/(\<diffgr:diffgram)[\s\S]+(\<\/diffgr:diffgram>)/';
        $cancellationMotivation = 'Cancelled from TB.';
        $notiDump = [
            'message' => '' ,
            'status' => '' ,
            'code' => '',
            'transaction_code' => $queue->getTransaction()->getTransactionCode(),
        ];

        $outputMessage = '';
        $outputStatus = 'Failed';
        $outputStatusCode = 500;
        $outputData = [];
        $flag = false;

        try {
            if (! $transaction or ! $transaction->getTransactionCode()) {
                throw new \Exception('Fatal Error :: Unable to find Transaction!!');
            }

            $parameters = [
                'iIdAgencia' => $this->token,
                'vReferencia' => $transaction->getTransactionCode(),
                'vMotivoModificacion' => $cancellationMotivation,
            ];
            $response = $this->sendHttpRequest($this->url, $parameters, 'AnulaEnvio');
            preg_match_all(
                $dataPattern,
                $response->AnulaEnvioResult->any, //$response_main->AltaEnvioNResult->any
                $matches
            );

            $webServiceResponse = simplexml_load_string(
               $matches[0][0],
               'SimpleXMLElement',
               LIBXML_NOCDATA | LIBXML_PARSEHUGE
            );

            if (! property_exists($webServiceResponse, 'DocumentElement') or
                ! property_exists($webServiceResponse->DocumentElement, 'RESP')) {
                throw new \Exception('Fatal Error :: Unexpected error encountered');
            }

            if ($webServiceResponse->DocumentElement->RESP->tiExito != 1) {
                throw new \Exception('Fatal Error :: Request not successful!!');
            }

//            $this->updateExecutedQueue($queue);

            $outputStatus     = 'Ok';
            $outputMessage    = 'Success!! Transaction successfully sent for Cancellation.';
            $outputStatusCode = 200;
            $outputData['confirmation_number'] = $transaction->getTransactionCode();
            $flag = true;

            $notiDump['message'] = $outputMessage;
            $notiDump['status'] = 'cancelled';

        } catch(\Exception $e) {
            $notiDump['status'] = 'error';
            $notiDump['message'] = 'Unable to cancel transaction';

            $outputData['debug'][] = [$e->getMessage(), $e->getFile(), $e->getLine()];
            $this->logger->addError('INTERMEX_CANCEL_ERROR', [$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

        /**
         * if $flag = true > then reExecute > false
         * if $flag = false > then reExecute > true
         *
         */
        $reExecute = !$flag;
        $this->updateExecutedQueue($queue, $reExecute);
//        $this->notifyTb($notiDump);
        $this->tbNotifier->notify(
            'cancel',
            $notiDump['status'],
            $notiDump['message'],
            $queue->getTransaction()->getTransactionCode()
        );
        $this->em->getRepository('BackendBundle:Log')
                    ->addLog(
                        $this->getWorkerSetting('service_id'),
                        'AnulaEnvio',
                        json_encode($parameters),
                        json_encode($webServiceResponse),
                        $outputStatus
                    );

//        return [
//            'message'     => $outputMessage,
//            'status'      => $outputStatus,
//            'status_code' => $outputStatusCode,
//            'data'        => $outputData
//        ];
        return $flag;
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function changeTransaction(OperationsQueue $queue, $args = [])
    {
        $transaction = $queue->getTransaction();
        $parentTransaction = $transaction->getParentTransaction();
        $fieldsOfInterest = [
            'beneficiary_first_name',
            'remitter_first_name',
            'beneficiary_phone_mobile',
        ];
        $fieldActionMap = [
            'beneficiary_first_name' => 'changeBeneficiaryFirstName',
            'remitter_first_name' => 'changeRemitterFirstName',
            'beneficiary_phone_mobile' => 'changeBeneficiaryMobile',
        ];

        $differences = $this->findTransactionDifferences($parentTransaction, $transaction);
        $isDifferenceFound = false;
        $flag = false;

        try {
            foreach ($fieldsOfInterest as $field) {
                if (array_key_exists($field, $differences)) {
                    $isDifferenceFound = true;

                    $method = $fieldActionMap[$field];

                    if (method_exists($this, $method)) {
                        if (! $this->$method($transaction)) {
                            throw new \Exception("Fatal Error :: Change not successful!!");
                        }
                    }
                }
            }

            if (! $isDifferenceFound) {
                throw new \Exception('Fatal Error :: No difference found!!');
            }

//            $this->updateExecutedQueue($queue);
            $flag = true;

        } catch(\Exception $e) {
            $this->logger->addError('INTERMEX_CHANGE_ERROR', [$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

        /**
         * if $flag = true > then reExecute > false
         * if $flag = false > then reExecute > true
         *
         */
        $reExecute = !$flag;
        $this->updateExecutedQueue($queue, $reExecute);

        return $flag;
    }

    /**
     * @param Transactions $transaction
     * @throws \Exception
     */
    public function changeBeneficiaryFirstName(Transactions $transaction)
    {
        $flag               = false;
        $url                = $this->getWorkerSetting('url');
        $username           = $this->getWorkerSetting('username');
        $password           = $this->getWorkerSetting('password');
        $action             = 'CambiaBeneficiario';
        $webServiceResponse = [];
        $params = [];

        $dataPattern = '/(\<diffgr:diffgram)[\s\S]+(\<\/diffgr:diffgram>)/';

        try {
            $params = [
                'iIdAgencia' => $this->conectar($url, $username, $password),
                'vReferencia' => $transaction->getTransactionCode(),
                'vNuevoBeneficiario' => $transaction->getBeneficiaryFirstName(),//$txn['transaction']->beneficiary_first_name,
                'vMotivoModificacion' => 'reason to change'
            ];

            $response = $this->sendHttpRequest($url, $params, $action);

            preg_match_all(
                $dataPattern,
                $response->CambiaBeneficiarioResult->any, //$response_main->AltaEnvioNResult->any
                $matches
            );

            $webServiceResponse = simplexml_load_string(
               $matches[0][0],
               'SimpleXMLElement',
               LIBXML_NOCDATA | LIBXML_PARSEHUGE
            );

            if (! property_exists($webServiceResponse, 'DocumentElement') or
                ! property_exists($webServiceResponse->DocumentElement, 'RESP')) {
                throw new \Exception('Fatal Error :: Unexpected error encountered');
            }

            if ($webServiceResponse->DocumentElement->RESP->tiExito != 1) {
                throw new \Exception('Fatal Error :: Request not successful!!');
            }

            $flag = true;

        } catch(\Exception $e) {
            $flag = false;
            $this->logger->addError('INTERMEX_CHANGE_ERROR', [$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

        $this->em->getRepository('BackendBundle:Log')
                ->addLog(
                    $this->getWorkerSetting('service_id'),
                    $action,
                    json_encode($params),
                    json_encode($webServiceResponse),
                    ($flag) ? 'Success': 'Failed'
                );

        return $flag;

    }

    /**
     * @param Transactions $transaction
     * @return bool
     * @throws \Exception
     */
    public function changeRemitterFirstName(Transactions $transaction)
    {
        $flag     = false;
        $url      = $this->getWorkerSetting('url');
        $username = $this->getWorkerSetting('username');
        $password = $this->getWorkerSetting('password');
        $action   = 'CambiaRemitente';
        $webServiceResponse = null;

        $dataPattern = '/(\<diffgr:diffgram)[\s\S]+(\<\/diffgr:diffgram>)/';

        try {
            $params = [
                'iIdAgencia' => $this->conectar($url, $username, $password),
                'vReferencia' => $transaction->getTransactionCode(),
                'vNuevoBeneficiario' => $transaction->getRemitterFirstName(),//$txn['transaction']->remitter_first_name,
                'vMotivoModificacion' => 'reason to change'
            ];

            $response = $this->sendHttpRequest($url, $params, $action);

            preg_match_all(
                $dataPattern,
                $response->AltaEnvioNResult->any, //$response_main->AltaEnvioNResult->any
                $matches
            );

            $webServiceResponse = simplexml_load_string(
               $matches[0][0],
               'SimpleXMLElement',
               LIBXML_NOCDATA | LIBXML_PARSEHUGE
            );

            if (! property_exists($webServiceResponse, 'DocumentElement') or
                ! property_exists($webServiceResponse->DocumentElement, 'RESP')) {
                throw new \Exception('Fatal Error :: Unexpected error encountered');
            }

            if ($webServiceResponse->DocumentElement->RESP->tiExito != 1) {
                throw new \Exception('Fatal Error :: Request not successful!!');
            }

            $flag = true;

        } catch(\Exception $e) {
            $flag = false;
            $this->logger->addError('INTERMEX_CHANGE_ERROR', [$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

        $this->em->getRepository('BackendBundle:Log')
                ->addLog(
                    $this->getWorkerSetting('service_id'),
                    $action,
                    json_encode($params),
                    json_encode($webServiceResponse),
                    ($flag) ? 'Success': 'Failed'
                );

        return $flag;
    }

    /**
     * @param Transactions $transaction
     * @throws \Exception
     */
    public function changeBeneficiaryMobile(Transactions $transaction)
    {
        $flag     = false;
        $url      = $this->getWorkerSetting('url');
        $username = $this->getWorkerSetting('username');
        $password = $this->getWorkerSetting('password');
        $action   = 'CambiaTelBeneficiario';
        $webServiceResponse = null;

        $dataPattern = '/(\<diffgr:diffgram)[\s\S]+(\<\/diffgr:diffgram>)/';

        try {
            $params = [
                'iIdAgencia' => $this->conectar($url, $username, $password),
                'vReferencia' => $transaction->getTransactionCode(),
                'vNuevoBeneficiario' => $transaction->getBeneficiaryPhoneMobile(),//$txn['transaction']->beneficiary_first_name,
                'vMotivoModificacion' => 'reason to change'
            ];

            $response = $this->sendHttpRequest($url, $params, $action);
            $this->logger->addError('CHANGE_RESP_LOG', [$response]);

            preg_match_all(
                $dataPattern,
                $response->CambiaTelBeneficiarioResult->any, //$response_main->AltaEnvioNResult->any
                $matches
            );

            $webServiceResponse = simplexml_load_string(
               $matches[0][0],
               'SimpleXMLElement',
               LIBXML_NOCDATA | LIBXML_PARSEHUGE
            );

            if (! property_exists($webServiceResponse, 'DocumentElement') or
                ! property_exists($webServiceResponse->DocumentElement, 'RESP')) {
                throw new \Exception('Fatal Error :: Unexpected error encountered');
            }

            if ($webServiceResponse->DocumentElement->RESP->tiExito != 1) {
                throw new \Exception('Fatal Error :: Request not successful!!');
            }

            $flag = true;

        } catch(\Exception $e) {
            $flag = false;
            $this->logger->addError('INTERMEX_CHANGE_ERROR', [$e->getMessage(), $e->getFile(), $e->getLine()]);
        }

        $this->em->getRepository('BackendBundle:Log')
                ->addLog(
                    $this->getWorkerSetting('service_id'),
                    $action,
                    json_encode($params),
                    json_encode($webServiceResponse),
                    ($flag) ? 'Success': 'Failed'
                );

        return $flag;
    }

    /**
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Transactions $baseTxn
     * @param Transactions $newTxn
     * @return array
     */
    private function findTransactionDifferences(Transactions $baseTxn, Transactions $newTxn)
    {
        $newTxnDump = $this->serializer->serialize($newTxn, 'json');
        $newTxnDump = json_decode($newTxnDump, true);
        $baseTxnDump = $this->serializer->serialize($baseTxn, 'json');
        $baseTxnDump = json_decode($baseTxnDump, true);

        unset($newTxnDump['parent_transaction']);
        unset($baseTxnDump['parent_transaction']);
        unset($newTxnDump['queues']);
        unset($baseTxnDump['queues']);

        return array_diff_assoc($newTxnDump, $baseTxnDump);
    }

    /**
     * @param array $arg
     * @return mixed
     */
    public function confirmTransaction()
    {
        $this->consultaCambios();
        $this->consultaPagados();
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
     * Method for consulting paid remittances today.
     *
     * @return void
     */
    public function consultaPagados()
    {
        $iIdAgencia=$this->conectar(
            'http://187.157.136.71/SIINetAg/SIINetAg.asmx?wsdl',
            '308901',
            'ixrue308901p'
        );
        $param = array('iIdAgencia'=>$iIdAgencia);

        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );

        $response_main = $soap_client->ConsultaPagados($param);
        $extractedData = explode('</xs:schema>', $response_main->ConsultaPagadosResult->any);
        $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );

        $response = json_decode(json_encode((array) $xmlFinal), true);

        if ( isset($response['NewDataSet']['PAGADOS']) ) {

            $output=(object) $response['NewDataSet']['PAGADOS'];

            if( $output->iIdTipoError == '0' ) {

                $confirmResult=$this->confirmaPagado(
                    $output->vReferencia,
                    $output->iConsecutivoAgencia
                );

                if($confirmResult->iIdTipoError=='1'){
                    //@TODO notify TB queue prepare
                }
            }

            $this->em->getRepository('BackendBundle:Log')
                ->addLog(
                    $this->getWorkerSetting('service_id'),
                    'consultaPagados',
                    json_encode($param),
                    $response_main
                );

        }
    }

    public function confirmaPagado($vReferencia, $iConsecutivoAgencia)
    {
        $iIdAgencia=$this->conectar(
            'http://187.157.136.71/SIINetAg/SIINetAg.asmx?wsdl',
            '308901',
            'ixrue308901p'
        );
        $param=array(
            'iIdAgencia'=>$iIdAgencia,
            'vReferencia'=>$vReferencia,
            'iConsecutivoAgencia'=>$iConsecutivoAgencia
        );

        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );

        $response_main = $soap_client->ConfirmaPagado($param);

        $extractedData =explode('</xs:schema>',$response_main->ConfirmaPagadoResult->any);

        $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );

        $response = json_decode(json_encode((array) $xmlFinal), true);

        if (isset($response['NewDataSet']['CONFIRMADOS'])) {
            if ($response['NewDataSet']['CONFIRMADOS']["iIdTipoError"] == '1') {
                $this->tbNotifier->notify(
                    'confirm',
                    'paid',
                    $message = "Transaction Paid",
                    $vReferencia
                );
            }
        }

        $this->em->getRepository('BackendBundle:Log')->addLog(
            $this->getWorkerSetting('service_id'),
            'consultaPagados',
            json_encode($param),
            $response_main
        );

    }

    /**
     * Method to show the changes already made
     *
     * @return void
     */
    public function consultaCambios()
    {
        $iIdAgencia=$this->conectar(
            'http://187.157.136.71/SIINetAg/SIINetAg.asmx?wsdl',
            '308901',
            'ixrue308901p'
        );
        $param=array('iIdAgencia'=>$iIdAgencia);
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );
        $response_main = $soap_client->ConsultaCambios($param);
        $extractedData =explode('</xs:schema>',$response_main->ConsultaCambiosResult->any);
        $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );
        $response = json_decode(json_encode((array) $xmlFinal), true);
        if (isset($response['NewDataSet']['CAMBIOS'])) {
            $output=$response['NewDataSet']['CAMBIOS'];
            foreach ($output as $key => $value) {
                //@TODO nofity queue generate
                $data=$this->confirmaCambio(
                    $value['iIdOrden'],
                    $value['tiIdTipoOrden'],
                    $value['vReferencia']
                );

            }
        } else {
            $this->em->getRepository('BackendBundle:Log')
                ->addLog(
                    $this->getWorkerSetting('service_id'),
                    'consultaCambios',
                    json_encode($param),
                    'No paid remittance today from this iIdAgencia'
                );
        }
        return;
    }

    /**
     * Method to Confirm a change requested
     *
     * @return void
     */
    public function confirmaCambio($iIdOrden=null,$id=null,$ref=null)
    {
        $type = array(
            '5'=>'Receiver Name change',
            '6'=>'Sender Name change',
            '7'=>'Receiver phone Number change',
            '10'=>'Remittance Cancellation',
        );
        $iIdAgencia=$this->conectar(
            'http://187.157.136.71/SIINetAg/SIINetAg.asmx?wsdl',
            '308901',
            'ixrue308901p'
        );
        $param=array('iIdAgencia'=>$iIdAgencia,'iIdOrden'=>$iIdOrden);
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );
        $response_main = $soap_client->ConfirmaCambio($param);
        $extractedData =explode('</xs:schema>',$response_main->ConfirmaCambioResult->any);
        $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );
        $response = json_decode(json_encode((array) $xmlFinal), true);

        if (isset($response['NewDataSet']['CONFIRMADOS'])) {

            $output = $response['NewDataSet']['CONFIRMADOS'];

            if ( $output['tiExito'] == '1' ) {

                $status = $output['tiIdTipoOrden'] == 10 ? "cancel" : "processing";
                $message = $status == "cancel" ? "Transaction is cancelled." : "Transaction is in process.";

                $this->em->getRepository('BackendBundle:Log')
                    ->addLog(
                        $this->getWorkerSetting('service_id'),
                        'consultaCambios',
                        json_encode($param),
                        $response
                    );

                $this->tbNotifier->notify(
                    'confirm',
                    $status,
                    $message,
                    $output['vReferencia']
                );
            }
        }
    }
}