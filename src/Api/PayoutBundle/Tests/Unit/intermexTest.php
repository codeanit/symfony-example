<?php 
namespace Api\PayoutBundle\Tests\Unit;

use Api\PayoutBundle\Library\Greenbelt;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IntermexTest extends WebTestCase
{

  private $GB; 
  private $container;
  private $path;

  public function __construct() 
  { 
    $this->path='/var/www/dex-api/web/upload/';  
    $client = $this->createClient(); 
    $this->container = $client->getContainer();
    $this->GB=$this->container->get('intermex');
  }

  public function testForBankAccountPayment()
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
  public function testForNotBankAccountType()
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

  public function ptestParserWithGoodData()
  { 
    $result=array(array(
                    'id' => '4',
                    'service_id' =>'11',
                    'action' =>'IN',
                    'file' =>'intermex-paid.txt',
                    'service_name' => 'intermex',
                    'creation_date' => null,
                    'is_executed' =>'0'));      
 
    $result=$this->GB->parse($result,$this->path);    
    $expected='200';
    $actual=$result['code'];
    $this->assertEquals($expected, $actual);
  }

  
  public function ptestParserWithBadData()
  { 
    $result=array(array(
                    'id' => '4',
                    'service_id' =>'11',
                    'action' =>'IN',
                    'file' =>'intermex-paid123.txt',
                    'service_name' => 'intermex',
                    'creation_date' => null,
                    'is_executed' =>'0'));      
 
    $result=$this->GB->parse($result,$this->path);      
    $expected='400';
    $actual=$result['code'];
    $this->assertEquals($expected, $actual);
  }

}
?>