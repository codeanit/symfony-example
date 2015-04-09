<?php

namespace BackendBundle\Library\Queue\Worker;


use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Queue\AbstractQueueWorker as BaseWorker;

class IntermexWorker extends BaseWorker
{
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
        $credentials = $this->prepareCreateCredentials($queue);

        try {
            $this->sendHttpRequest($credentials);
        } catch(\Exception $e) {
        }
    }

    /**
     * @param \BackendBundle\Entity\OperationsQueue $queue
     * @return array
     */
    private function prepareCreateCredentials(OperationsQueue $queue)
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

        if ($payoutId == '2') {
            $isBankDeposit = 1;
            $bankBranch = $transaction->getBeneficiaryBankBranch();
            $bankAccount = $transaction->getBeneficiaryAccountNumber();
        }

        $credentials = [
            'iIdAgencia'          => $this->conectar(),
            'vReferencia'         => $transaction->getTransactionCode(),
            'dtFechaEnvio'        => $remittanceDate,
            'iConsecutivoAgencia' => '12345678',
            'mMonto'              => $transaction->getPayoutAmount(),//$data['transaction']->payout_amount,
            'fTipoCambio'         => $transaction->getExchangeRate(),//$data['transaction']->exchange_rate,
            'mMontoPago'          => '',//$data['transaction']->remitting_amount,
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
     *
     */
    private function conectar()
    {
        $params = [

        ];
        $client = new \SoapClient($url, $params);
    }

    /**
     * @param array $credentials
     */
    private function sendHttpRequest(array $credentials)
    {

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
}