<?php 
namespace Api\WebServiceBundle\Tests\Entity;

use Api\WebServiceBundle\Entity\TransactionModel;

class TransactionModelTest extends \PHPUnit_Framework_TestCase
{
   
  private $model;

  public function __construct() 
  {
		$this->model = new TransactionModel();
  }
  	
  /**
   * [Test Case for PAID status in GetRemittance Method]
   */
  public function  testGetRemittancePaid() 
  {
    $expected= array('sessionID' => 2, 'status'=> 1);
    $goodInput = array('status' => 'paid');
    $this->model->setReturn($goodInput);
    $result = $this->model->getRemittance(array('sessionID' =>'2'));    
    $this->assertEquals($expected,$result);
    
  }

  /**
   * [Test Case for Approved status in GetRemittance Method]
   */
  public function testGetRemittanceApproved()
  {
    $expected = array('status'=> 'get','sessionID' => 2);
    $goodInput = array('status' => 'approved');
    $this->model->setReturn($goodInput);
    $result = $this->model->getRemittance(array('sessionID' =>'2'));
    $this->assertEquals($expected,$result);
  }

  /**
   * [Test Case for Other status like (cancelled deleted void hold) in GetRemittance Method]
   */
  public function testGetRemittanceOther()
  {
    $expected = array('sessionID' => 2, 'status'=> 3);
    $badInput = array('status' => 'fasdfdsaf');
    $this->model->setReturn($badInput);
    $result = $this->model->getRemittance(array('sessionID' => 2));   
    $this->assertEquals($expected,$result);
  }

  public function  testupdateStatusPaid() 
  {
    $expected= array('sessionID' => 2, 'status'=> 2);
    $goodInput = array('status' => 'paid');
    $this->model->setReturn($goodInput);
    $result = $this->model->updateStatus(array('sessionID' => 2));    
    $this->assertEquals($expected,$result);
    
  }

  /**
   * [Test Case for Approved status in updateStatus Method]
   * @todo update checking
   */
  public function testupdateStatusApproved()
  {
    $expected = array('status'=> 'update','sessionID' => 2 ,'traceNo' =>123);
    $goodInput = array('status' => 'approved','control_number' => 6100000001);
    $this->model->setReturn($goodInput);
    $result = $this->model->updateStatus(array('sessionID' => 2,'traceNo' => '123'));
    $this->assertEquals($expected,$result);
  }

  /**
   * [Test Case for Other status like (cancelled deleted void hold) in updateStatus Method]
   */
  public function testupdateStatusOther()
  {
    $expected = array('sessionID' => 2, 'status'=> 3);
    $badInput = array('status' => 'fasdfdsaf');
    $this->model->setReturn($badInput);
    $result = $this->model->updateStatus(array('sessionID' => 2));   
    $this->assertEquals($expected,$result);
  }

  public function testCheckStatusOther()
  {
    $expected = array('sessionID' => 2, 'status'=> 3);
    $badInput = array('status' => 'fasdfdsaf');
    $this->model->setReturn($badInput);
    $result = $this->model->checkStatus(array('sessionID' => 2));   
    $this->assertEquals($expected,$result);
  }

  public function testCheckStatusPaid()
  {
    $expected = array('status'=> 'check','sessionID' => 2 ,'traceNo' =>123);
    $badInput = array('status' => 'paid');
    $this->model->setReturn($badInput);
    $result = $this->model->checkStatus(array('sessionID' => 2,'traceNo' => '123'));   
    $this->assertEquals($expected,$result);
  }

  public function testisAuthorized()
  {
    $expected=True;
    $result = $this->model->isAuthorized('manish','pass');
    $this->assertEquals($expected,$result);
  }
  public function testisAuthorizedForBadUsernamePassword()
  {
    $expected=False;
    $result = $this->model->isAuthorized('manisfasfh','pass');
    $this->assertEquals($expected,$result);
  }

  public function testTransactionExistsWithCorrectRefNo()
  {
    $expected=True;
    $result = $this->model->checkRefNoTransactionExists('502000001');
    $this->assertEquals($expected,$result);
  }
  public function testTransactionExistsWithoutCorrectRefNo()
  {
    $expected=False;
    $result = $this->model->checkRefNoTransactionExists('manisfasfh');
    $this->assertEquals($expected,$result);
  }


  public function testConvertToString1()
  {
    $expected='2|2';
    $par = array('sessionID' => 2, 'status' =>'2' );
    $result = $this->model->convertToString($par);
    $this->assertEquals($expected,$result);
  }

  // test for status=update in ConvertToString 
  
  public function testConvertToString2()
  {
    $expected='2|0|TN12|123|100|NPR|chalise|Manish|jpt|221 B Baker Street|zero';
    $badInput = array('control_number' => '123','remitting_amount'=>100,'name'=>'NPR','lastName'=>'chalise','firstName'=>'Manish','middleName'=>'jpt','street'=>'221 B Baker Street');
    $this->model->setReturn($badInput);
    $par = array('sessionID' => 2, 'traceNo' =>'TN12','status'=>'update');
    $result = $this->model->convertToString($par);
    $this->assertEquals($expected,$result);
  }

  // test for status=get in ConvertToString 

  public function testConvertToString3()
  {
    $expected='2|0|123|100|NPR|chalise|Manish|jpt|221 B Baker Street|98746646';
    $badInput = array('control_number' => '123','remitting_amount'=>100,'name'=>'NPR','lastName'=>'chalise','firstName'=>'Manish','middleName'=>'jpt','street'=>'221 B Baker Street','mobile'=>98746646);
    $this->model->setReturn($badInput);
    $par = array('sessionID' => 2, 'status'=>'get');
    $result = $this->model->convertToString($par);
    $this->assertEquals($expected,$result);
  }

  // test for status=check in ConvertToString 

  public function testConvertToString4()
  {
    $expected='2|0|TN12|123|100|NPR|chalise|Manish|jpt|221 B Baker Street|zero';
    $badInput = array('control_number' => '123','remitting_amount'=>100,'name'=>'NPR','lastName'=>'chalise','firstName'=>'Manish','middleName'=>'jpt','street'=>'221 B Baker Street');
    $this->model->setReturn($badInput);
    $par = array('sessionID' => 2, 'traceNo' =>'TN12','status'=>'check');
    $result = $this->model->convertToString($par);
    $this->assertEquals($expected,$result);
  }

  public function testMain()
  {
    
  }
    
}
?>