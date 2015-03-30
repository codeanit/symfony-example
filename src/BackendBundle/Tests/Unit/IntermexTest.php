<?php 
namespace BackendBundle\Tests\Unit;

use Symfony\Component\DependencyInjection\ContainerInterface;
#use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IntermexTest extends \PHPUnit_Framework_TestCase 
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

    public function __construct()
    {
        $this->container = $container;
        $this->log=$this->container->get('log');      
        $connection=$this->container->get('connection');
        $result=$connection->getCred('intermex');
        $this->database = json_decode(base64_decode($result[0]['credentials']));        
        $this->url=$this->database->url;        
        $this->service_id=$result[0]['id'];

    }

    public function ptestForBankAccountPayment()
    {
      $param=array("source"=> "tb",
                 "service"=> "intermex",
                 "transaction"=>(object) array(     
                    "transaction_code"=> rand(1,10000),    
                    "receiver_first_name"=> "Alejandro",
                    "receiver_mother_name"=> "Test",
                    "receiver_last_name"=> "Rodriguez",
                    "receiver_currency"=> "USD",
                    "receiver_country"=> "ARG",
                    "receiver_state"=> "BA",
                    "receiver_city"=> "BOCA",
                    "receiver_address"=> "Buenos Aires",
                    "receiver_account_number"=> "8596556",
                    "receiver_bank_routing_no"=> "85469",
                    "receiver_phone_mobile"=> "7897987987",
                    "receiver_bank_branch"=> "California-test-bank",
                    "receiver_bank_name"=> "XYZ Bank",    
                    "sender_first_name"=> "Visha",
                    "sender_mother_name"=> "Bharati",
                    "sender_last_name"=> "Shanghvi",    
                    "sender_currency"=> "CAD",
                    "sender_country"=> "CAN",
                    "sender_state"=> "ON",
                    "sender_city"=> "Toronto",
                    "sender_address"=> "555 Richmond Street West",
                    "sender_phone_mobile"=> "(866)504-3813",    
                    "sender_postal_code"=> "M5V3B1",
                    "sender_account_number"=> "8596556",
                    "sender_bank_routing_no"=> "85469",
                    "sender_bank_branch"=> "California-test-bank",
                    "sender_bank_name"=> "XYZ Bank",
                    "sender_amount"=> 26.00,
                    "receiver_amount"=> 23.40,
                    "exchange_rate"=> 0.9,
                    "fee"=> 10.00,    
                    "payout_channel"=>"bank",
                    "payer_id"=>"135",
                    "remittance_date"=>"14/03/2015",
                    "additional_information"=> ""));
     
      $resultData=$this->GB->process('create',$param);
      $expected='200';
      $this->assertEquals($expected,$resultData['code']);
    }
    public function ptestConectar()
    {
      $resultData=$this->GB->conectar();
    }
    public function testConsultaCambios()
    {
      $resultData=$this->GB->consultaCambios();
    }

    public function ptestCambieTelBeneficiario()
    {
      $resultData=$this->GB->cambiaTelBeneficiario('789456123','464654654','cwetewtwe'); 
    }

    public function ptestCambiaRemitente()
    {
      $resultData=$this->GB->cambiaRemitente('789456123','manman','qfasdfwerwet');  
    }

    public function ptestCambiaBeneficiario()
    {
      $resultData=$this->GB->cambiaBeneficiario('789456123','assswerer','hjlhjlhj');
    }

    public function ptestConfirmaPagado()
    {
      $resultData=$this->GB->confirmaPagado('789456123','12345678');   
    }

    public function ptestConsultaPagados()
    {
      $resultData=$this->GB->consultaPagados();    
    }
    public function ptestAnulaEnvio()
    {
      $resultData=$this->GB->anulaEnvio('789456132','124241');
      
    }
    
    public function ptestForNotBankAccountType()
    {
       $param=array("source"=> "tb",
                   "service"=> "intermex",
                   "transaction"=>(object) array(     
                      "transaction_code"=> rand(1,10000),    
                      "receiver_first_name"=> "Alejandro",
                      "receiver_mother_name"=> "Test",
                      "receiver_last_name"=> "Rodriguez",
                      "receiver_currency"=> "USD",
                      "receiver_country"=> "ARG",
                      "receiver_state"=> "BA",
                      "receiver_city"=> "BOCA",
                      "receiver_address"=> "Buenos Aires",
                      "receiver_account_number"=> "8596556",
                      "receiver_bank_routing_no"=> "85469",
                      "receiver_phone_mobile"=> "7897987987",
                      "receiver_bank_branch"=> "California-test-bank",
                      "receiver_bank_name"=> "XYZ Bank",    
                      "sender_first_name"=> "Visha",
                      "sender_mother_name"=> "Bharati",
                      "sender_last_name"=> "Shanghvi",    
                      "sender_currency"=> "CAD",
                      "sender_country"=> "CAN",
                      "sender_state"=> "ON",
                      "sender_city"=> "Toronto",
                      "sender_address"=> "555 Richmond Street West",
                      "sender_phone_mobile"=> "(866)504-3813",    
                      "sender_postal_code"=> "M5V3B1",
                      "sender_account_number"=> "8596556",
                      "sender_bank_routing_no"=> "85469",
                      "sender_bank_branch"=> "California-test-bank",
                      "sender_bank_name"=> "XYZ Bank",
                      "sender_amount"=> 26.00,
                      "receiver_amount"=> 23.40,
                      "exchange_rate"=> 0.9,
                      "fee"=> 10.00,    
                      "payout_channel"=>"cash",
                      "payer_id"=>"138",
                      "remittance_date"=>"14/03/2015",
                      "additional_information"=> ""));    
      $resultData=$this->GB->process('create',$param);
      $expected='200';
      $this->assertEquals($expected,$resultData['code']);
    }
}
