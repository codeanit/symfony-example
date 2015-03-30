<?php 
namespace Api\PayoutBundle\Tests\Generator;

use Api\PayoutBundle\Library\Greenbelt;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GreenbeltFileGeneratorTest extends WebTestCase
{

  private $GB; 
  private $container;
  private $path;

  public function __construct() 
  { 
    $this->path='/var/www/dex-api/web/generated_files/';  
    $client = $this->createClient(); 
    $this->container = $client->getContainer();
    $this->GB=$this->container->get('mcb');
  }

  public function testGenerator()
  { 
    $testData=array(
                array('BNF_NAME'=>'Obaid',
                      'BNF_LAST'=>'Ahmed',
                      'BNF_ADDRESS1'=>'HBL A/C  05457901233403  Code',
                      'BNF_ADDRESS2'=>'0545 Model Town',
                      'DEPOSIT_ACCOUNT_NUMBER'=>'1231564564',
                      'BNF_TELEPHONE'=>'4564646465',
                      'SENDER_CUSTOMER_NUMBER'=>'3064099601',
                      'SENDER_NAME'=>'Manish',
                      'SENDER_LAST_NAME'=>'Chalise',
                      'SENDER_ADDRESS'=>'K-Town',
                      'SENDER_CITY'=>'KTM',
                      'SENDER_STATE'=>'blah blah',
                      'SENDER_ZIP'=>'97701',
                      'BRANCH_NUMBER'=>'90812312123',
                      'BRANCH_LOCATION'=>'Chabahil',
                      'CURRENCY_CODE'=>'USD',
                      'TO_PAY_BNF'=>'144000',
                      ),
                array('BNF_NAME'=>'Anit',
                      'BNF_LAST'=>'Shrestha',
                      'BNF_ADDRESS1'=>'HBL A/C  05457901233403  Code',
                      'BNF_ADDRESS2'=>'0545 Model Town',
                      'DEPOSIT_ACCOUNT_NUMBER'=>'1231564564',
                      'BNF_TELEPHONE'=>'4564646465',
                      'SENDER_CUSTOMER_NUMBER'=>'3064099601',
                      'SENDER_NAME'=>'Manish',
                      'SENDER_LAST_NAME'=>'Chalise',
                      'SENDER_ADDRESS'=>'K-Town',
                      'SENDER_CITY'=>'KTM',
                      'SENDER_STATE'=>'blah blah',
                      'SENDER_ZIP'=>'97701',
                      'BRANCH_NUMBER'=>'90812312123',
                      'BRANCH_LOCATION'=>'Chabahil',
                      'CURRENCY_CODE'=>'USD',
                      'TO_PAY_BNF'=>'144000',
                      )
                );
              
    $result=$this->GB->generate($testData,$this->path);       
    $expected='1';
    $actual=$result;   
    $this->assertEquals($expected, $actual);
  }


}
?>