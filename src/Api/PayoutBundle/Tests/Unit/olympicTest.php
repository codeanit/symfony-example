<?php 
namespace Api\PayoutBundle\Tests\Unit;

use Api\PayoutBundle\Library\Greenbelt;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OlympicTest extends WebTestCase
{

  private $GB; 
  private $container;
  private $path;

  public function __construct() 
  { 
    $this->path='/var/www/dex-api/web/upload/';  
    $client = $this->createClient(); 
    $this->container = $client->getContainer();
    $this->GB=$this->container->get('olympic');
  }

  public function testParserWithGoodData()
  { 
    $result=array(array(
                    'id' => '4',
                    'service_id' =>'9',
                    'action' =>'IN',
                    'file' =>'test.xlsx',
                    'service_name' => 'olympic',
                    'creation_date' => null,
                    'is_executed' =>'0'));      
 
    $result=$this->GB->parse($result,$this->path);    
    $expected='200';
    $actual=$result['code'];
    $this->assertEquals($expected, $actual);
  }

  
  public function testParserWithBadData()
  { 
    $result=array(array(
                    'id' => '4',
                    'service_id' =>'9',
                    'action' =>'IN',
                    'file' =>'test123.xlsx',
                    'service_name' => 'olympic',
                    'creation_date' => null,
                    'is_executed' =>'0'));      
 
    $result=$this->GB->parse($result,$this->path);      
    $expected='400';
    $actual=$result['code'];
    $this->assertEquals($expected, $actual);
  }

}
?>