<?php
namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;


class Intermex
{
    protected $container;
    protected $url;
    protected $log;
    protected $service_id;
    protected $database;
    protected $operationMap = array(
        'create' => 'altaEnvioT',
        'modify' => 'processUpdate',
        'update' => 'processUpdate',
        'cancel' => 'anulaEnvio'
    );

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->log=$this->container->get('log');      
        $connection=$this->container->get('connection');
        $result=$connection->getCred('intermex');
        $this->database = json_decode(base64_decode($result[0]['credentials']));        
        $this->url=$this->database->url;        
        $this->service_id=$result[0]['id'];
<<<<<<< HEAD

=======
>>>>>>> 174315bababbb8e89e0bdb1ecd70d29465e8d8dd
    }

    /**
     * [Method used to generate ID like session ID]
     *
     * @return string [Agency Token. It will be used in all the methods that
     *                  have permission to invoke.]
     */
    public function conectar()
    {
          $soap_client = new \SoapClient(
              $this->url,
              array(
                   "trace" => 1,
                   'exceptions' => 1,
                   'cache_wsdl' => WSDL_CACHE_NONE,)
          );
          $cred =array('vUsuario'=>$this->database->username, 'vPassword'=>$this->database->password);
          $actual = $soap_client->Conectar($cred);      
        if ($actual->ConectarResult) {
            $this->log->addInfo($this->service_id, 'Conectar', $cred, $actual->ConectarResult);
            return $actual->ConectarResult;
        } else {           
            $this->log->addError($this->service_id, 'Conectar', $cred, 'Null');
            return;
        }
    }

    /**
     * [Method to add remittances and account deposits using
     * account number, transfer code or card number]
     *
     * @param [array] $txn [txn data from TB]
     *
     * @return [Array] [which is added to queue]
     */
    public function altaEnvioT($txn=null)
    {      
      $data=$txn;
      $currencyId=(strtoupper($data['transaction']->receiver_currency) == "USD" ||
                   strtoupper($data['transaction']->receiver_currency) == "CAD")?'2':'1';
      $payoutId=(strtoupper($data['transaction']->payout_channel) == "BANK")?'2':'1';

      if ($payoutId=='2') {
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
      $response_main = $soap_client->AltaEnvioN($param);
      $extractedData =explode('</xs:schema>',$response_main->AltaEnvioNResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );

      $response = json_decode(json_encode((array) $xmlFinal), true);
      if ($xmlFinal->NewDataSet->ENVIO->tiExito == '1') {
            $return = array('code' => '200',
                            'operation'=>'create',
                            'message' => 'Transaction Create Successful.' ,
                            'notify_source'=>$data['source'],
                            'status' => 'complete' ,                            
                            'confirmation_number' =>
                              $data['transaction']->transaction_code
                           );
            $this->log->addInfo($this->service_id, 'altaEnvioT', $param, $response_main);
        } else {
            $return = array('code' => '400',
                            'operation'=>'create',
                            'message' => $xmlFinal->NewDataSet
                              ->ENVIO->iIdTipoError.'-'.$xmlFinal
                              ->NewDataSet->ENVIO->vMensajeError,
                            'notify_source'=>$data['source'],
                            'status' => 'failed' ,
                            'confirmation_number' =>
                              $data['transaction']->transaction_code
                           );
            $this->log->addError($this->service_id, 'altaEnvioT', $param, $response);            
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
      $iIdAgencia=$this->conectar();
      $param=array('iIdAgencia'=>$iIdAgencia);
      $soap_client = new \SoapClient(
                    $this->url,
                    array(
                        "trace" => 1,
                        'exceptions' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,)
                );
      $response_main = $soap_client->ConsultaPagados($param);
      $extractedData =explode('</xs:schema>',$response_main->ConsultaPagadosResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );
      $response = json_decode(json_encode((array) $xmlFinal), true);
      if (isset($response['NewDataSet']['PAGADOS'])) {
        $output=(object) $response['NewDataSet']['PAGADOS'];
        $arr=array('iIdTipoError'=>$output->iIdTipoError,'error_msg'=>$output->vMensajeError);
        $this->log->addInfo($this->service_id, 'consultaPagados', $param, $response_main);        
      } else {
        $arr=array('code'=>400,'msg'=>'No paid remittance today from this iIdAgencia');
        $this->log->addError($this->service_id, 'consultaPagados', $param, $response_main);

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
        $iIdAgencia=$this->conectar();
        $param=array('iIdAgencia'=>$iIdAgencia,'vReferencia'=>$vReferencia,'iConsecutivoAgencia'=>$iConsecutivoAgencia);
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
        $output=(object) $response['NewDataSet']['CONFIRMADOS'];
        $arr=array('tiExito'=>$output->tiExito,'iIdTipoError'=>$output->iIdTipoError,'error_msg'=>$output->vMensajeError);
        $this->log->addInfo($this->service_id, 'confirmaPagado', $param, $response_main);        
        
      } else {
        $arr=array('code'=>400,'msg'=>'confirming paid remittance failed');
        $this->log->addInfo($this->service_id, 'confirmaPagado', $param, $response_main);
      }

    }

    /**
     * Method for requesting change or modification of Receiver’s name for a particular remittance
     * @param  varchar $vReferencia         [Reference number of txn]
     * @param  varchar $vNuevoBeneficiario  [New Receiver’s name]
     * @param  string  $vMotivoModificacion [Reason for changing]
     * @return void
     */
    public function cambiaBeneficiario($vReferencia=null,$vNuevoBeneficiario=null,$vMotivoModificacion=null)
    {
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
      $response_main = $soap_client->CambiaBeneficiario($param);
      $extractedData =explode('</xs:schema>',$response_main->CambiaBeneficiarioResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );
      $response = json_decode(json_encode((array) $xmlFinal), true);  
      if (isset($response['DocumentElement']['RESP'])) {
        $output=(object) $response['DocumentElement']['RESP'];
        $arr=array('tiExito'=>$output->tiExito,'error_msg'=>$output->vMensajeError);

        if ($output->tiExito=='1') {
              $return = array('code' => '200',
                              'operation'=>'modify',
                              'message' => 'Receiver Name Change Successful.' ,
                              'notify_source'=>'tb',
                              'status' => 'complete' ,
                              'data' => array('receiver_first_name'=>$vNuevoBeneficiario) ,
                              'confirmation_number' => $vReferencia,
                           );
<<<<<<< HEAD
              $this->log->addInfo($this->service_id, 'cambiaBeneficiario', $param, $response_main);
=======
              $this->log->addInfo($this->service_id[0], 'cambiaBeneficiario', $param, $response_main);
>>>>>>> 174315bababbb8e89e0bdb1ecd70d29465e8d8dd
              return $return;         
        } else {
              $this->log->addError($this->service_id, 'cambiaBeneficiario', $param, $response_main);
        }

      } else {
        $arr=array('code'=>400,'msg'=>'confirming paid remittance failed');
        $this->log->addError($this->service_id, 'cambiaBeneficiario', $param, $response_main);        
      }
      $return = array('code' => '400',
                      'operation'=>'modify',
                      'message' => 'Receiver Name Change Failed.' ,
                      'notify_source'=>'tb',
                      'status' => 'failed' ,
                      'data' => '' ,
                      'confirmation_number' => $vReferencia,
                           );

      return $return;
    }

    /**
     * Method for requesting change or modification of Sender’s name for a particular remittanc
     * @param  varchar $vReferencia         [Reference number of txn]
     * @param  varchar $vNuevoRemitente     [New Sender’s name]
     * @param  string  $vMotivoModificacion [Reason for changing]
     * @return void
     */
    public function cambiaRemitente($vReferencia=null,$vNuevoRemitente=null,$vMotivoModificacion=null)
    {
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
      $response_main = $soap_client->CambiaRemitente($param);
      $extractedData =explode('</xs:schema>',$response_main->CambiaRemitenteResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );
      $response = json_decode(json_encode((array) $xmlFinal), true);
      if (isset($response['DocumentElement']['RESP'])) {
        $output=(object) $response['DocumentElement']['RESP'];
        $arr=array('tiExito'=>$output->tiExito,'error_msg'=>$output->vMensajeError);
        if ($output->tiExito=='1') {
              $return = array('code' => '200',
                              'operation'=>'modify',
                              'message' => 'Sender Name Change Successful.' ,
                              'notify_source'=>'tb',
                              'status' => 'complete' ,
                              'data' => array('sender_first_name'=>$vNuevoRemitente) ,
                              'confirmation_number' => $vReferencia,
                           );
<<<<<<< HEAD
              $this->log->addInfo($this->service_id, 'cambiaRemitente', $param, $response_main);
=======
              $this->log->addInfo($this->service_id[0], 'cambiaRemitente', $param, $response_main);
>>>>>>> 174315bababbb8e89e0bdb1ecd70d29465e8d8dd
              return $return;
        } else {
              $this->log->addError($this->service_id, 'cambiaRemitente', $param, $response_main);
        }
      } else {
        $arr=array('code'=>400,'msg'=>'confirming paid remittance failed');
              $this->log->addError($this->service_id, 'cambiaRemitente', $param, $response_main);
      }
      $return = array('code' => '400',
                      'operation'=>'modify',
                      'message' => 'Sender Name Change Failed.' ,
                      'notify_source'=>'tb',
                      'status' => 'failed' ,
                      'data' => '' ,
                      'confirmation_number' => $vReferencia,
                           );

      return $return;
    }

     /**
     * Method for requesting the change or modification of Receiver’s phone for a particular remittance     *
     * @param  varchar $vReferencia         [Reference number of txn]
     * @param  varchar $vNuevoTelefon       [New Receiver’s phone number]
     * @param  string  $vMotivoModificacion [Reason for changing]
     * @return void
     */
    public function cambiaTelBeneficiario($vReferencia=null,$vNuevoTelefon=null,$vMotivoModificacion=null)
    {
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
      $response_main = $soap_client->CambiaTelBeneficiario($param);
      $extractedData =explode('</xs:schema>',$response_main->CambiaTelBeneficiarioResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );
      $response = json_decode(json_encode((array) $xmlFinal), true);
      if (isset($response['DocumentElement']['RESP'])) {
        $output=(object) $response['DocumentElement']['RESP'];
        $arr=array('tiExito'=>$output->tiExito,'error_msg'=>$output->vMensajeError);
        if ($output->tiExito=='1') {
               $return = array('code' => '200',
                              'operation'=>'modify',
                              'message' => 'Receiver Phone Number Change Successful.' ,
                              'notify_source'=>'tb',
                              'status' => 'complete' ,
                              'data' => array('receiver_phone_mobile'=>$vNuevoTelefon) ,
                              'confirmation_number' => $vReferencia,
                           );
<<<<<<< HEAD
              $this->log->addInfo($this->service_id, 'cambiaTelBeneficiario', $param, $response_main); 
=======
              $this->log->addInfo($this->service_id[0], 'cambiaTelBeneficiario', $param, $response_main); 
>>>>>>> 174315bababbb8e89e0bdb1ecd70d29465e8d8dd
              return $return;           
        } else {
              $this->log->addError($this->service_id, 'cambiaTelBeneficiario', $param, $response_main);            
        }
      } else {
        $arr=array('code'=>400,'msg'=>'confirming paid remittance failed');
        $this->log->addError($this->service_id, 'cambiaTelBeneficiario', $param, $response_main);        
      }
       $return = array('code' => '400',
                      'operation'=>'modify',
                      'message' => 'Receiver Phone Number Change Failed.' ,
                      'notify_source'=>'tb',
                      'status' => 'failed' ,
                      'data' => '' ,
                      'confirmation_number' => $vReferencia,
                           );

      return $return;
    }

    /**
     * Method for requesting a remittance cancelation.
     * @param  varchar $vReferencia        [Reference number of txn]
     * @param  string  $vMotivoCancelacion [Reason for changing]
     * @return void
     */
    public function anulaEnvio($txn=null)
    {
        $vReferencia=$txn['confirmation_number'];
        $vMotivoCancelacion=$txn['reason'];
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
      $response_main = $soap_client->AnulaEnvio($param);
      $extractedData =explode('</xs:schema>',$response_main->AnulaEnvioResult->any);
      $xmlFinal   = simplexml_load_string(
            $extractedData[1],
            'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE
        );
      $response = json_decode(json_encode((array) $xmlFinal), true);
      if (isset($response['DocumentElement']['RESP'])) {
        $output=(object) $response['DocumentElement']['RESP'];
        if ($output->tiExito=='1') {
              $return = array('code' => '200',
                              'operation'=>'modify',
                              'message' => 'Transaction Successfully Cancelled.' ,
                              'notify_source'=>'tb',
                              'status' => 'complete' ,
                              'data' => array('status'=>'canceled') ,
                              'confirmation_number' => $vReferencia,
                           );
<<<<<<< HEAD
              $this->log->addInfo($this->service_id, 'anulaEnvio', $param, $response_main);
=======
              $this->log->addInfo($this->service_id[0], 'anulaEnvio', $param, $response_main);
>>>>>>> 174315bababbb8e89e0bdb1ecd70d29465e8d8dd
              return $return;
        } else {
              $this->log->addError($this->service_id, 'anulaEnvio', $param, $response_main);
        }
        $arr=array('tiExito'=>$output->tiExito,'error_msg'=>$output->vMensajeError);
      } else {
        $arr=array('code'=>400,'msg'=>'Cancellation Failed');
        $this->log->addError($this->service_id, 'anulaEnvio', $param, $response_main);
      }
      $return = array('code' => '400',
                      'operation'=>'modify',
                      'message' => 'Transaction Cancel Failed.' ,
                      'notify_source'=>'tb',
                      'status' => 'failed' ,
                      'data' => '' ,
                      'confirmation_number' => $vReferencia,
<<<<<<< HEAD
                     );
=======
                           );

>>>>>>> 174315bababbb8e89e0bdb1ecd70d29465e8d8dd
      return $return;

    }

    /**
     * Method to show the changes already made
     *
     * @return void
     */
    public function consultaCambios()
    {
      $iIdAgencia=$this->conectar();
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
           $data=$this->confirmaCambio($value['iIdOrden']);
           if ($data=='200') {
              $this->log->addInfo($this->service_id, 'consultaCambios', $param, $data);              
            } else {
              $this->log->addError($this->service_id, 'consultaCambios', $param, $data);               
            }
        }
      } else {
        $this->log->addError($this->service_id, 'consultaCambios', $param, 'No paid remittance today from this iIdAgencia');
      }

    }

    /**
     * Method to Confirm a change requested
     *
     * @return void
     */
    public function confirmaCambio($iIdOrden=null)
    {
      $iIdAgencia=$this->conectar();
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
        $output=$response['NewDataSet']['CONFIRMADOS'];
        if ($output['tiExito']=='1') {
            $arr=array('code'=>'200','iIdOrden'=>$iIdOrden,'msg'=>'Successful');            
        } else {
            $arr=array('code'=>'400','iIdOrden'=>$iIdOrden,'msg'=>'Failed');
        }
      } else {
        $arr=array('code'=>'400','iIdOrden'=>$iIdOrden,'msg'=>'Failed');
      }

      return $arr;
    }


    public function process($operation, $args)
    {
        return call_user_func_array(array($this, $this->operationMap[$operation]), [$args]);
    }

    /**
     * Used to map to specific update method according to
     * modified data
     * 
     * @param  Array $txn [fields must be('refNo','newData','reason for change')]
     * 
     * @return void
     */
    public function processUpdate($txn=null)
    {
        if(isset($txn['receiver_first_name']))
        {
          $data =  $this->cambiaBeneficiario($txn['confirmation_number'],$txn['receiver_first_name'],$txn['reason']);
          return $data;
        }
        if(isset($txn['sender_first_name']))
        {
          $data =  $this->cambiaRemitente($txn['confirmation_number'],$txn['sender_first_name'],$txn['reason']);
          return $data;
        }
        if(isset($txn['receiver_phone_mobile']))
        {
          $data =  $this->cambiaTelBeneficiario($txn['confirmation_number'],$txn['receiver_phone_mobile'],$txn['reason']);     
          return $data;
        }
    }

<<<<<<< HEAD
}
=======
}
>>>>>>> 174315bababbb8e89e0bdb1ecd70d29465e8d8dd
