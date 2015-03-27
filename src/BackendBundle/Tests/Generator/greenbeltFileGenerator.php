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
    $this->GB=$this->container->get('greenbelt');
  }

  public function testGenerator()
  { 
    $testData=array(array('RMT_NO'=>'207320',      
                    'FR_AGENT'=>'SSC',    
                    'FAX_NO'=>'153',      
                    'DATE'=>'06/02/2014',        
                    'BENEFICIARY'=>'Cukash Ali Dalal', 
                    'BEN_PHONE'=>'1127826316',   
                    'TO_AGENT'=>'QAH',    
                    'B_ADDRESS'=>'QAHIRA QAHIRA-Greenbelt',   
                    'AMOUNT'=>'200',      
                    'COMM'=>'10',        
                    'SENDER'=>'ABDULLAHI MOHAMED ABDI ',      
                    'SENDER_TEL'=>'+16194366272',  
                    'SENDER_ADDRESS'=>'3810 133 WINONA AVE  San Diego CA 92105'),
                    array('RMT_NO'=>'124214',      
                    'FR_AGENT'=>'GSS',    
                    'FAX_NO'=>'566',      
                    'DATE'=>'06/02/2014',        
                    'BENEFICIARY'=>'Alu Kha', 
                    'BEN_PHONE'=>'1127826316',   
                    'TO_AGENT'=>'QAH',    
                    'B_ADDRESS'=>'QAHIRA QAHIRA-Greenbelt',   
                    'AMOUNT'=>'200',      
                    'COMM'=>'10',        
                    'SENDER'=>'Tashi Dhalek ',      
                    'SENDER_TEL'=>'+15641616',  
                    'SENDER_ADDRESS'=>'New Baneshwor'));   
    $result=$this->GB->generate($testData,$this->path);       
    $expected='1';
    $actual=$result;   
    $this->assertEquals($expected, $actual);
  }


}
?>