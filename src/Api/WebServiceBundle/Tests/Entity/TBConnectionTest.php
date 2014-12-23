<?php 
namespace Api\WebServiceBundle\Tests\Entity;

use Api\WebServiceBundle\Model\TBConnectionModel as TBConnection;

class TBConnectionTest extends \PHPUnit_Framework_TestCase
{

  private $model;

  public function __construct() 
  {
		$this->model = new TBConnection();
  }

  /**
   * [Test Case for PAID status in GetRemittance Method]
   */
  public function  testCurlTransborder() 
  {
    $data = array(
            'model' => 'MLhuillier',
            'operation' => 'transactionTest',            
            'sessionID' => '$sessionID',
            'username' => '$username',
            'password' => '$password',
            'refNo' => '$refno',
            'signature'=> '$signature');
    
    $result = $this->model->curlTransborder($data);

   var_dump($result);
  }

}
?>