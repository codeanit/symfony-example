<?php

namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Intermex
{
    protected $container;    
    protected $url;
    private $operationMap = array(
        'create' => 'altaEnvioT',
        'modify' => '',
        'update' => '',
        'cancel' => ''
    );   
 

    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->url='http://187.157.136.71/SIINetAg/SIINetAg.asmx?wsdl';
    } 

    public function conectar()
    {      
      $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $actual = $soap_client->Conectar(array('vUsuario'=>'308901','vPassword'=>'ixrue308901p'));
      return $actual->ConectarResult;
    }

    public function altaEnvioT($txn=null)
    {
      $data=$txn;           
      $currencyId=(strtoupper($data['transaction']->receiver_currency) == "USD" ||
                   strtoupper($data['transaction']->receiver_currency) == "CAD")?'2':'1';
      $payoutId=(strtoupper($data['transaction']->payout_channel) == "BANK")?'2':'1';

      if($payoutId=='2'){
        $siIdTipoDeposito=1;
        $bankBranch=$data['transaction']->receiver_bank_branch;
        $bankAccountNumber=$data['transaction']->receiver_account_number;
      }
      $param=array(  
          'iIdAgencia'=>$this->conectar(),
          'vReferencia'=>$data['transaction']->transaction_code,
          'dtFechaEnvio'=>$data['transaction']->remittance_date,
          'iConsecutivoAgencia'=>'12345678',
          'mMonto'=>$data['transaction']->receiver_amount,
          'fTipoCambio'=>$data['transaction']->exchange_rate,
          'mMontoPago'=>$data['transaction']->sender_amount,
          'siIdDivisaPago'=>$currencyId,
          'tiIdTipoPagoEnvio'=> $payoutId,
          'vNomsRemitente'=>$data['transaction']->sender_first_name,
          'vApedosRemitente'=>$data['transaction']->sender_last_name,
          'vDireccionRem'=>$data['transaction']->sender_address,
          'vTelefonoRem'=>$data['transaction']->sender_phone_mobile,
          'vCondadoRem'=>$data['transaction']->sender_country,
          'vEstadoRem'=>$data['transaction']->sender_state,
          'vNomsBeneficiario'=>$data['transaction']->receiver_first_name,
          'vApedosBeneficiario'=>$data['transaction']->receiver_last_name,
          'vDireccionBen'=>$data['transaction']->receiver_address,
          'vTelefonoBen'=>$data['transaction']->receiver_phone_mobile,
          'vCiudadBenef'=>$data['transaction']->receiver_country,
          'vEstadoBenef'=>$data['transaction']->receiver_state,
          'iIdDestino'=>$data['transaction']->payer_id,
          'vMensaje'=>'message to receiver',
          'vInstruccionPago'=>'agency comment',
          'vSucursal'=>isset($siIdTipoDeposito)?$siIdTipoDeposito:'',
          'vCuenta'=>isset($bankBranch)?$bankBranch:'',
          'vClabe'=>'',
          'siIdTipoDeposito'=>isset($bankAccountNumber)?$bankAccountNumber:'',
          'vNumerotarjeta'=>'',
          'vMontoCom'=>$data['transaction']->fee);       
        $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->AltaEnvioN($param);
      $extractedData =explode('</xs:schema>',$response->AltaEnvioNResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );
      
      $response = json_decode(json_encode((array)$xmlFinal), true);      
      if ($xmlFinal->NewDataSet->ENVIO->tiExito == '1')
        {
            $return = array('code' => '200',
                            'operation'=>'create',
                            'message' => 'Transaction Successful.' ,
                            'notify_source'=>$data['source'],
                            'status' => 'complete' ,
                            'confirmation_number' =>$data['transaction']->transaction_code
                           );
        } else {
            $return = array('code' => '400',
                            'operation'=>'create',
                            'message' => $xmlFinal->NewDataSet->ENVIO->iIdTipoError.'-'.
                                         $xmlFinal->NewDataSet->ENVIO->vMensajeError,
                            'notify_source'=>$data['source'],
                            'status' => 'failed' ,
                            'confirmation_number' =>$data['transaction']->transaction_code
                           );
        }
     
      return $return;

    }

    public function process($operation, $args)
    {         
        return call_user_func_array(array($this, $this->operationMap[$operation]), [$args]);
    }

   
}

