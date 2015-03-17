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

    /**
     * [Method used to generate ID like session ID]
     * 
     * @return [string] [Agency Token. It will be used in all the methods that
     * have permission to invoke.]
     */
    public function conectar()
    { 
      $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
      $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/conectar.log.txt' , Logger::INFO));
      $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );
      $cred =array('vUsuario'=>'308901','vPassword'=>'ixrue308901p');    
      $actual = $soap_client->Conectar($cred);
      if($actual->ConectarResult){
        $log->addInfo('Id Generation Successful',array('code'=>'200','ID'=>$actual->ConectarResult,'InputData'=>$cred));
        return $actual->ConectarResult;
      }else{
        $log->addError('Failed ID Generation',array('code'=>'400','InputData'=>$cred));
        return;
      }
    }

    /**
     * [Method to add remittances and account deposits using
     * account number, transfer code or card number]
     * 
     * @param  [array] $txn [txn data from TB]
     * 
     * @return [Array]      [which is added to queue]
     */
    public function altaEnvioT($txn=null)
    {
      $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
      $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/altaEnvioT.log.txt' , Logger::INFO));      
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
            $log->addInfo('Transaction Successful',array('Code'=>'200','Parameter'=>$param,'Response'=>$response));      
        } else {
            $return = array('code' => '400',
                            'operation'=>'create',
                            'message' => $xmlFinal->NewDataSet->ENVIO->iIdTipoError.'-'.
                                         $xmlFinal->NewDataSet->ENVIO->vMensajeError,
                            'notify_source'=>$data['source'],
                            'status' => 'failed' ,
                            'confirmation_number' =>$data['transaction']->transaction_code
                           );
            $log->addError('Transaction Failed',array('Code'=>'400','Parameter'=>$param,'Response'=>$response));
        }
     
      return $return;

    }

    /**
     * Method for consulting paid remittances today.
     * 
     * @return void
     */
    public function consultaPagados()
    {
      $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
      $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/consultaPagados.log.txt' , Logger::INFO));    
      $iIdAgencia=$this->conectar();      
      $param=array('iIdAgencia'=>$iIdAgencia);
      $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->ConsultaPagados($param);
      $extractedData =explode('</xs:schema>',$response->ConsultaPagadosResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );      
      $response = json_decode(json_encode((array)$xmlFinal), true);   
      if(isset($response['NewDataSet']['PAGADOS']))
      {
        $output=(object)$response['NewDataSet']['PAGADOS'];        
        $arr=array('iIdTipoError'=>$output->iIdTipoError,'error_msg'=>$output->vMensajeError);
        $log->addInfo('Consulting Paid Remittance',array('Parameter'=>$param,'Response'=>$response));      
      }else{
        $arr=array('code'=>400,'msg'=>'No paid remittance today from this iIdAgencia');
        $log->addError('Consulting Paid Remittance Failed',array('Parameter'=>$param,'Response'=>$response));      

      }      
    }

    /**
     * Method for confirming paid remittances.
     * 
     * @param  varchar $vReferencia         [reference number of txn]
     * @param  varchar $iConsecutivoAgencia [Remittances consecutive number]
     * @return void                 
     */
    public function confirmaPagado($vReferencia=null,$iConsecutivoAgencia=null)
    {
        $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/confirmaPagado.log.txt' , Logger::INFO)); 
        $iIdAgencia=$this->conectar();
        $param=array('iIdAgencia'=>$iIdAgencia,'vReferencia'=>$vReferencia,'iConsecutivoAgencia'=>$iConsecutivoAgencia);
        $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->ConfirmaPagado($param);
      $extractedData =explode('</xs:schema>',$response->ConfirmaPagadoResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );      
      $response = json_decode(json_encode((array)$xmlFinal), true);   
      if(isset($response['NewDataSet']['CONFIRMADOS']))
      {
        $output=(object)$response['NewDataSet']['CONFIRMADOS'];        
        $arr=array('tiExito'=>$output->tiExito,'iIdTipoError'=>$output->iIdTipoError,'error_msg'=>$output->vMensajeError);
        $log->addInfo('Consulting Paid Remittance',array('Parameter'=>$param,'Response'=>$response));
      }else{
        $arr=array('code'=>400,'msg'=>'confirming paid remittance failed');
        $log->addError('confirming paid remittance Failed',array('Parameter'=>$param,'Response'=>$response));     

      }
          
    }
    
    /**
     * Method for requesting change or modification of Receiver’s name for a particular remittance
     * @param  varchar $vReferencia         [Reference number of txn]
     * @param  varchar $vNuevoBeneficiario  [New Receiver’s name]
     * @param  string $vMotivoModificacion [Reason for changing]
     * @return void                     
     */
    public function cambiaBeneficiario($vReferencia=null,$vNuevoBeneficiario=null,$vMotivoModificacion=null)
    {
        $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/cambiaBeneficiario.log.txt' , Logger::INFO)); 
        $iIdAgencia=$this->conectar();
        $param=array(
                    'iIdAgencia'=>$iIdAgencia,
                    'vReferencia'=>$vReferencia,
                    'vNuevoBeneficiario'=>$vNuevoBeneficiario,
                    'vMotivoModificacion'=>$vMotivoModificacion
                    );
        $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->CambiaBeneficiario($param);
      $extractedData =explode('</xs:schema>',$response->CambiaBeneficiarioResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );      
      $response = json_decode(json_encode((array)$xmlFinal), true);   
      if(isset($response['DocumentElement']['RESP']))
      {
        $output=(object)$response['DocumentElement']['RESP'];        
        $arr=array('tiExito'=>$output->tiExito,'error_msg'=>$output->vMensajeError);
        if($output->tiExito=='1'){
            $log->addInfo('requesting modification of Receiver’s name',array('Parameter'=>$param,'Response'=>$response));
        }else{
            $log->addError('requesting modification of Receiver’s name Failed',array('Parameter'=>$param,'Response'=>$response));               
        }

      }else{
        $arr=array('code'=>400,'msg'=>'confirming paid remittance failed');
        $log->addError('requesting modification of Receiver’s name Failed',array('Parameter'=>$param,'Response'=>$response));
      }
         
    }

    /**
     * Method for requesting change or modification of Sender’s name for a particular remittanc
     * @param  varchar $vReferencia         [Reference number of txn]
     * @param  varchar $vNuevoRemitente  [New Sender’s name]
     * @param  string $vMotivoModificacion [Reason for changing]
     * @return void                     
     */
    public function cambiaRemitente($vReferencia=null,$vNuevoRemitente=null,$vMotivoModificacion=null)
    {
        $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/cambiaRemitente.log.txt' , Logger::INFO)); 
        $iIdAgencia=$this->conectar();
        $param=array(
                    'iIdAgencia'=>$iIdAgencia,
                    'vReferencia'=>$vReferencia,
                    'vNuevoBeneficiario'=>$vNuevoRemitente,
                    'vMotivoModificacion'=>$vMotivoModificacion
                    );
        $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->CambiaRemitente($param);
      $extractedData =explode('</xs:schema>',$response->CambiaRemitenteResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );      
      $response = json_decode(json_encode((array)$xmlFinal), true);   
      if(isset($response['DocumentElement']['RESP']))
      {
        $output=(object)$response['DocumentElement']['RESP'];        
        $arr=array('tiExito'=>$output->tiExito,'error_msg'=>$output->vMensajeError);
        if($output->tiExito=='1'){
            $log->addInfo('requesting modification of Sender’s name',array('Parameter'=>$param,'Response'=>$response));
        }else{
            $log->addError('requesting modification of Sender’s name Failed',array('Parameter'=>$param,'Response'=>$response));            
        }
      }else{
        $arr=array('code'=>400,'msg'=>'confirming paid remittance failed');
        $log->addError('requesting modification of Sender’s name Failed',array('Parameter'=>$param,'Response'=>$response));
      }      
    }

     /**
     * Method for requesting the change or modification of Receiver’s phone for a particular remittance     * 
     * @param  varchar $vReferencia         [Reference number of txn]
     * @param  varchar $vNuevoTelefon  [New Receiver’s phone number]
     * @param  string $vMotivoModificacion [Reason for changing]
     * @return void                     
     */
    public function cambiaTelBeneficiario($vReferencia=null,$vNuevoTelefon=null,$vMotivoModificacion=null)
    {
        $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/cambiaTelBeneficiario.log.txt' , Logger::INFO)); 
        $iIdAgencia=$this->conectar();
        $param=array(
                    'iIdAgencia'=>$iIdAgencia,
                    'vReferencia'=>$vReferencia,
                    'vNuevoBeneficiario'=>$vNuevoTelefon,
                    'vMotivoModificacion'=>$vMotivoModificacion
                    );
        $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->CambiaTelBeneficiario($param);
      $extractedData =explode('</xs:schema>',$response->CambiaTelBeneficiarioResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );      
      $response = json_decode(json_encode((array)$xmlFinal), true);   
      if(isset($response['DocumentElement']['RESP']))
      {
        $output=(object)$response['DocumentElement']['RESP'];        
        $arr=array('tiExito'=>$output->tiExito,'error_msg'=>$output->vMensajeError);
        if($output->tiExito=='1'){
            $log->addInfo('requesting modification of Receiver Phone number Successful',array('Parameter'=>$param,'Response'=>$response));            
        }else{
            $log->addError('requesting modification of Receiver Phone number Failed',array('Parameter'=>$param,'Response'=>$response));
        }
      }else{
        $arr=array('code'=>400,'msg'=>'confirming paid remittance failed');
        $log->addError('requesting modification of Receiver Phone number Failed',array('Parameter'=>$param,'Response'=>$response));        
      }       
    }

    /**
     * Method for requesting a remittance cancelation.   
     * @param  varchar $vReferencia         [Reference number of txn]   
     * @param  string $vMotivoCancelacion [Reason for changing]
     * @return void                     
     */
    public function anulaEnvio($vReferencia=null,$vMotivoCancelacion=null)
    {
        $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/anulaEnvio.log.txt' , Logger::INFO)); 
        $iIdAgencia=$this->conectar();
        $param=array(
                    'iIdAgencia'=>$iIdAgencia,
                    'vReferencia'=>$vReferencia,                  
                    'vMotivoModificacion'=>$vMotivoCancelacion
                    );
        $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->AnulaEnvio($param);
      $extractedData =explode('</xs:schema>',$response->AnulaEnvioResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );      
      $response = json_decode(json_encode((array)$xmlFinal), true);   
      if(isset($response['DocumentElement']['RESP']))
      {
        $output=(object)$response['DocumentElement']['RESP']; 
        if($output->tiExito=='1'){
            $log->addInfo('Cancellation Successful',array('Parameter'=>$param,'Response'=>$response));            
        }else{
            $log->addError('Cancellation Failed',array('Parameter'=>$param,'Response'=>$response));
        }     
        $arr=array('tiExito'=>$output->tiExito,'error_msg'=>$output->vMensajeError);      
      }else{
        $arr=array('code'=>400,'msg'=>'Cancellation Failed');
        $log->addError('Cancellation Failed',array('Parameter'=>$param,'Response'=>$response));
      }
   
    }
 
    /**
     * Method to show the changes already made
     * 
     * @return void
     */
    public function consultaCambios()
    {
      $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
      $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/consultaCambios.log.txt' , Logger::INFO)); 
      $iIdAgencia=$this->conectar();      
      $param=array('iIdAgencia'=>$iIdAgencia);
      $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->ConsultaCambios($param);
      $extractedData =explode('</xs:schema>',$response->ConsultaCambiosResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );      
      $response = json_decode(json_encode((array)$xmlFinal), true);
      if(isset($response['NewDataSet']['CAMBIOS']))
      {
        $output=$response['NewDataSet']['CAMBIOS'];        
        foreach ($output as $key => $value) {
           $data=$this->confirmaCambio($value['iIdOrden']);
           if($data=='200'){
                $log->addInfo('Changes Successful',array('Parameter'=>$param,'Response'=>$response,'confirmaCambioResponse'=>$data));            
            }else{
                $log->addError('Changes Failed',array('Parameter'=>$param,'Response'=>$response,'confirmaCambioResponse'=>$data));
            }   
        }
      }else{
        $arr=array('code'=>400,'msg'=>'No paid remittance today from this iIdAgencia');
        $log->addError('Changes Failed',array('Parameter'=>$param,'Response'=>$response));
      }
     
    }

    /**
     * Method to Confirm a change requested
     * 
     * @return void
     */
    public function confirmaCambio($iIdOrden=null)
    {
      $log = new \Symfony\Bridge\Monolog\Logger('Intermex');
      $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/Intermex/confirmaCambio.log.txt' , Logger::INFO)); 
      $iIdAgencia=$this->conectar();      
      $param=array('iIdAgencia'=>$iIdAgencia,'iIdOrden'=>$iIdOrden);
      $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );    
      $response = $soap_client->ConfirmaCambio($param);
      $extractedData =explode('</xs:schema>',$response->ConfirmaCambioResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );      
      $response = json_decode(json_encode((array)$xmlFinal), true);     
      if(isset($response['NewDataSet']['CONFIRMADOS']))
      {
        $output=$response['NewDataSet']['CONFIRMADOS'];              
        if($output['tiExito']=='1'){
            $arr=array('code'=>'200','iIdOrden'=>$iIdOrden,'msg'=>'Successful');
        }else{
            $arr=array('code'=>'400','iIdOrden'=>$iIdOrden,'msg'=>'Failed');            
        }       
      }else{
        $arr=array('code'=>'400','iIdOrden'=>$iIdOrden,'msg'=>'Failed');
      }
      return $arr;
    }



    public function process($operation, $args)
    {         
        return call_user_func_array(array($this, $this->operationMap[$operation]), [$args]);
    }

   
}

