<?php

namespace BackendBundle\Library\Queue\Worker;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Queue\AbstractQueueWorker as BaseWorker;

/**
 * Class IntermexWorker
 * @package BackendBundle\Library\Queue\Worker
 */
class IntermexWorker extends BaseWorker
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
        return 'intermex';
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
     * @param \BackendBundle\Entity\OperationsQueue $queue
     * @throws \Exception
     * @return array
     */
    private function prepareCreateParameters(OperationsQueue $queue)
    {
        $transaction    = $queue->getTransaction();
        $payoutId       = $transaction->getTransactionType();
        $payoutId       = (strtoupper($payoutId) == "BANK") ? '2' : '1';
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
            'iIdAgencia'          => $this->conectar($url, $username, $password),
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
            'iIdDestino'          => $transaction->getPayoutAgentId(),// $data['transaction']->payout_agent_id,
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
     * @param $url
     * @param $username
     * @param $password
     * @return mixed
     * @throws \Exception
     */
    private function conectar($url, $username, $password)
    {
        $params = [
            'trace' => 1,
            'exception' => 1,
            'cache_wsdl' => WSDL_CACHE_NONE,
        ];

        $client = new \SoapClient($url, $params);
        $response = $client->Conectar([
            'vUsuario' => $username,
            'vPassword' => $password
        ]);

        if (! $response->ConectarResult) {
            throw new \Exception('Internal Error :: Invalid response from "Conectar"!!');
        }

        return $response->ConectarResult;
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
    public function modifyTransaction(OperationsQueue $queue, $args = [])
    {
        // TODO: Implement modifyTransaction() method.
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
}