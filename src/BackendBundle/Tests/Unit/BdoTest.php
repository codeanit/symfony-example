<?php 
namespace BackendBundle\Tests\Unit;

use Symfony\Component\DependencyInjection\ContainerInterface;
use BackendBundle\Library\BDO\Bdo;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BdoTest extends WebTestCase {

  private $bdo; 
  private $container;
  
  public function __construct() 
  {
    $client = $this->createClient(); 
    $this->container = $client->getContainer();
    $this->bdo=$this->container->get('bdo');
  }

  public function testBDOPicuup()
  {
    // $bdo = new Bdo();
    $data=array(
    "source"=> "tb",
    "service"=> "bdo",
    "transaction"=> (object) array(
        "transaction_code"=> 221,
        "tracking_number"=> 12412421412,
        "transaction_type"=>"bank",
        "remitting_currency"=> "CAD",
        "payout_currency"=> "USD",
        "remitting_amount"=> 26.00,
        "payout_amount"=>21.00,
        "exchange_rate"=> 0.90,
        "fee"=> 4.00,
        "remittance_date"=> "2015-01-28 11=>05=>13",
        "beneficiary_first_name"=> "Alejandro",
        "beneficiary_middle_name"=> "Test",
        "beneficiary_last_name"=> "Rodriguez",
        "beneficiary_email"=> "alex.r@gmail.com",
        "beneficiary_city"=> "BOCA",
        "beneficiary_state"=> "BA",
        "beneficiary_country"=> "ARG",
        "beneficiary_phone_mobile"=> "7897987987",
        "beneficiary_postal_code"=> "O5V2B1",
        "beneficiary_address"=> "Buenos Aires",
        "beneficiary_account_number"=> "8596556",
        "beneficiary_bank_routing_no"=> "85469",
        "beneficiary_bank_branch"=> "California-test-bank",
        "beneficiary_bank_name"=> "XYZ Bank",
        "beneficiary_id_number"=>"",
        "beneficiary_id_type"=>"",
        "beneficiary_id_issued_country"=>"",
        "beneficiary_id_issued_city"=>"",
        "beneficiary_id_issued_state"=>"",
        "beneficiary_id_issued_date"=>"",
        "beneficiary_id_expiry_date"=>"",
        "payout_agent_id"=>"321",
        "payout_agent_name"=>"MLA",
        "payout_agent_country"=>"USA",
        "payout_agent_state"=>"NY",
        "payout_agent_city"=>"flo rida",
        "payout_payer_name"=>"BDO",
        "remitter_first_name"=> "Visha",
        "remitter_middle_name"=> "Bharati",
        "remitter_last_name"=> "Shanghvi",
        "remitter_email"=> "visha.r@gmail.com",
        "remitter_country"=> "CAN",
        "remitter_state"=> "ON",
        "remitter_city"=> "Toronto",
        "remitter_address"=> "555 Richmond Street West",
        "remitter_phone_mobile"=> "(866)504-3813",
        "remitter_postal_code"=> "M5V3B1",
        "remitter_account_number"=> "8596556",
        "remitter_bank_routing_no"=> "85469",
        "remitter_bank_branch"=> "California-test-bank",
        "remitter_bank_name"=> "XYZ Bank",
        "remitter_id_number"=>"",
        "remitter_id_type"=>"",
        "remitter_id_issued_country"=>"",
        "remitter_id_issued_city"=>"",
        "remitter_id_issued_state"=>"",
        "remitter_id_issued_date"=>"",
        "remitter_id_expiry_date"=>"",
        "remitter_agent_id"=>"123",
        "remitting_agent_name"=>"Bibek Baucha", 
        "remitting_agent_country"=>"USA",
        "remitting_agent_state"=>"CA",
        "remitting_agent_city"=>"hawa city"
    ));
    
    print_r($this->bdo->pickupCash($data));
    print_r($this->bdo->pickupMLLhuillier($data));
    print_r($this->bdo->pickupCebuana($data));
    die;
  }
}
