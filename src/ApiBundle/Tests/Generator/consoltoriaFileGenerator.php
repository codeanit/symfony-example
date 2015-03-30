<?php 
namespace Api\PayoutBundle\Tests\Generator;

use Api\PayoutBundle\Library\Greenbelt;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConsultoriaFileGeneratorTest extends WebTestCase
{

  private $GB; 
  private $container;
  private $path;

  public function __construct() 
  { 
    $this->path='/var/www/dex-api/web/generated_files/';  
    $client = $this->createClient(); 
    $this->container = $client->getContainer();
    $this->GB=$this->container->get('consultoria');
  }

  public function testGenerator()
  { 
    $testData=array("TRANSMMITER CODE"=>"84",
                    "CORRESPONDENT CODE"=>"C0025",
                    "ORDER CLAIM REFERENCE"=>"8402500010",
                    "SECUENTIAL REFERENCE"=>"1",
                    "PAYMENT OFFICE CODE"=>"BG",
                    "PAYMENT OFFICE NAME"=>"SUC. AGUASCALIENTES",
                    "TRANSACTION DATE"=>"04/08/2008",
                    "TRANSACTION TIME"=>"18:40",
                    "AMOUNT SENT"=>"50.00",
                    "EXCHANGE RATE"=>"1",
                    "PAYMENT CURRENCY"=>"USD",
                    "AMOUNT TO PAY"=>"50.00",
                    "SENDER NAME"=>"AUGUSTO",
                    "SENDER FIRST NAME"=>"RODRIGUEZ",
                    "SENDER LAST NAME"=>"PEREZ",
                    "SENDER ADDRESS"=>"5TH AVE.",
                    "SENDER CITY"=>"DENVER",
                    "SENDER STATE"=>"ILLINOIS",
                    "SENDER COUNTRY"=>"USA",
                    "SENDER ZIP CODE"=>"12546",
                    "SENDER MAIN PHONE"=>"123246548",
                    "SENDER  PHONE 2"=>"564654654",
                    "SENDER IDENTIFICATION TYPE"=>"LICENCIA",
                    "SENDER IDENTIFICATION NUMBER"=>"546654654",
                    "SENDER MESSAGE"=>"SALUDOS A TODOS",
                    "BENEFICIARY NAME"=>"JUAN CARLOS",
                    "BENEFICIARY FIRST NAME"=>"RODRIGUEZ",
                    "BENEFICIARY LAST NAME"=>"PEREZ",
                    "BENEFICIARY ADDRESS"=>"AV. REYES 3434",
                    "BENEFICIARY CITY"=>"AGUASCALIENTES",
                    "BENEFICIARY STATE"=>"AGUASCALIENTES",
                    "BENEFICIARY ZIP CODE"=>"09034",
                    "BENEFICIARY COUNTRY"=>"MEX",
                    "BENEFICIARY COUNTRY NAME"=>"MEXICO",
                    "BENEFICIARY PHONE 1"=>"456546543",
                    "BENEFICIARY PHONE 2"=>"344324234",
                    "TRANSACTION TYPE"=>"02",
                    "ACCOUNT TYPE"=>"",
                    "BENEFICIARY ACCOUNT NUMBER"=>"",
                    "BANK NAME"=>"",
                    "ADITIONAL DATA 1"=>"",
                    "ADITIONAL DATA 2"=>"",
                    "ADITIONAL DATA 3"=>"",
                    "ADITIONAL DATA 4"=>"",
                    "ORIGIN CURRENCY"=>"USD",);
    $result=$this->GB->generate($testData,$this->path);       
    $expected='1';
    $actual=$result;
    $this->assertEquals($expected, $actual);
  }


}
?>