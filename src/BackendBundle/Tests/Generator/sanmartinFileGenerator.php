<?php 
namespace Api\PayoutBundle\Tests\Generator;

use Api\PayoutBundle\Library\Greenbelt;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SanmartinFileGeneratorTest extends WebTestCase
{

  private $GB; 
  private $container;
  private $path;

  public function __construct() 
  { 
    $this->path='/var/www/dex-api/web/generated_files/';  
    $client = $this->createClient(); 
    $this->container = $client->getContainer();
    $this->GB=$this->container->get('sanmartin');
  }

  public function testGenerator()
  { 
   $testData= array("Date of the order"=>"12/12/12",
                    "Number of Shipping"=>"2",
                    "Sender Name"=>"Manish",
                    "Sender Name Paternal"=>"Chalise",
                    "Sender Name Mother"=>"",
                    "Beneficiary Name"=>"Anit",
                    "Recipient Name Paterno"=>"Shrestha",
                    "Recipient Name Mother"=>"manandhar",
                    "Currency Shipping"=>"USD",
                    "Currency of Payment"=>"USD",
                    "Exchange rate"=>"1.5",
                    "Key Branch payment"=>"",
                    "Shipping Amount"=>"1000",
                    "Reference"=>"",
                    "Country code source"=>"USA",
                    "Tel Sender"=>"123123",
                    "Recipient Street"=>"ktm-12 avinue road",
                    "Recipient City"=>"kathmandu",
                    "beneficiary State"=>"bagmati",
                    "Recipient Zip Code"=>"97701",
                    "Recipient Phone"=>"4894894",
                    "Payment Type"=>"02",
                    "Account No. Feed"=>"12313",
                    "Bank"=>"global bank",
                    "Bank Branch"=>"chabahil",
                    "Message or Comment"=>"hello world",
                    "City / Town Sender"=>"Dolkha",
                    "Sender State"=>"Baglung",);
    $result=$this->GB->generate($testData,$this->path);       
    $expected='1';
    $actual=$result;
    $this->assertEquals($expected, $actual);
  }


}
?>