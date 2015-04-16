<?php 
namespace ApiBundle\Tests\Unit;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransNetworkTest extends WebTestCase
{

  private $GB; 
  private $container;
  private $path;

  public function __construct() 
  { 
    $this->path='/var/www/dex-api/web/upload/';  
    $client = $this->createClient(); 
    $this->container = $client->getContainer();
    $this->GB=$this->container->get('transnetwork');
  }
  public function ptestForCancel()
  {
    $data=$this->GB->cancel('X230007222288','2015-04-03');
    print_r($data);die;
  }

  public function testForUpdate()
  { 
    $data=$this->GB->queryUpdate();
    print_r($data);die;
  }
























}
?>