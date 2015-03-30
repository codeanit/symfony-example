<?php 
namespace Api\WebServiceBundle\Tests\Unit;

use Api\PayoutBundle\Model\BTSModel;

class BTSModelTest extends \PHPUnit_Framework_TestCase
{

  private $bts;

  public function __construct() 
  {
      $this->bts= new BTSModel();      
  }

  public function testdoUSRL()
  {
    $actual='';
    if($this->bts->doUSRL())
    {      
      $actual=(strlen($this->bts->doUSRL())>0)?true:false;
    }
    
    $this->assertTrue($actual);

  }
  /**
   * [Test Case for Payout in BTS with bad parameter ]
   */
   
  public function  testDoSALEwithoutTransactionCode() 
  {
      $data = array(
      'source' => 'wallet',
      'service' => 'MTR',
      'transaction' =>
        (object)array(
              'transaction_key' => 'xyz0009',
              'transaction_code' => '',
              'transaction_type' => '001',
              'payment_type' => 'CSH',
              'receiver_id_number' => '8596325',
              'receiver_id_type' => 'Passport',
              'receiver_id_issued_country' => 'USA',
              'receiver_first_name' => 'James',
              'receiver_middle_name' => 'XYZ',
              'receiver_last_name' => 'William',
              'receiver_email' => 'testing@xyz.com',
              'receiver_account_type' => 'Saving',
              'receiver_currency' => 'USD',
              'receiver_country' => 'USA',
              'receiver_state' => 'California',
              'receiver_phone_mobile' => ' - 555 - 8956',
              'receiver_phone_landline' => '+85 -  - 555 - 8956',
              'receiver_postal_code' => '85963',
              'receiver_account_number' => '8596556',
              'receiver_bank_routing_no' => '85469',
              'receiver_bank_branch' => 'California-test-bank',
              'receiver_bank_name' => 'XYZ Bank',
              'sender_id_number' => '8596325',
              'sender_id_type' => 'Passport',
              'sender_id_issued_country' => 'USA',
              'sender_first_name' => 'James',
              'sender_middle_name' => 'XYZ',
              'sender_last_name' => 'William',
              'sender_email' => 'testing@xyz.com',
              'sender_account_type' => 'Saving',
              'sender_currency' => 'USD',
              'sender_country' => 'USA',
              'sender_state' => 'California',
              'sender_phone_mobile' => ' - 555 - 8956',
              'sender_phone_landline' => '+85 -  - 555 - 8956',
              'sender_postal_code' => '85963',
              'sender_account_number' => '8596556',
              'sender_bank_routing_no' => '85469',
              'sender_bank_branch' => 'California-test-bank',
              'sender_bank_name' => 'XYZ Bank',
              'sender_city' => 'texas',
              'sender_amount' => '100',
              'receiver_amount' => '150',
              'receiver_city' => 'abc state',
              'exchange_rate' => '10.12',
              'additional_information' => '' 
            )
      );
      
      $actual=$this->bts->doSALE($data);
      $expected=array('status' => 400,'message' => 'REQUIRED PARAMETER IS MISSINGDATA/CONFIRMATION_NM');
      $this->assertEquals($expected, $actual);

  }

  public function  testDoSALEwithTransactionCode() 
  {
      $data = array(
      'source' => 'wallet',
      'service' => 'MTR',
      'transaction' =>
        (object)array(
              'transaction_key' => 'xyz0009',
              'transaction_code' => '11637576352',
              'transaction_type' => '001',
              'payment_type' => 'CSH',
              'receiver_id_number' => '8596325',
              'receiver_id_type' => 'Passport',
              'receiver_id_issued_country' => 'USA',
              'receiver_first_name' => 'James',
              'receiver_middle_name' => 'XYZ',
              'receiver_last_name' => 'William',
              'receiver_email' => 'testing@xyz.com',
              'receiver_account_type' => 'Saving',
              'receiver_currency' => 'USD',
              'receiver_country' => 'USA',
              'receiver_state' => 'California',
              'receiver_phone_mobile' => ' - 555 - 8956',
              'receiver_phone_landline' => '+85 -  - 555 - 8956',
              'receiver_postal_code' => '85963',
              'receiver_account_number' => '8596556',
              'receiver_bank_routing_no' => '85469',
              'receiver_bank_branch' => 'California-test-bank',
              'receiver_bank_name' => 'XYZ Bank',
              'sender_id_number' => '8596325',
              'sender_id_type' => 'Passport',
              'sender_id_issued_country' => 'USA',
              'sender_first_name' => 'James',
              'sender_middle_name' => 'XYZ',
              'sender_last_name' => 'William',
              'sender_email' => 'testing@xyz.com',
              'sender_account_type' => 'Saving',
              'sender_currency' => 'USD',
              'sender_country' => 'USA',
              'sender_state' => 'California',
              'sender_phone_mobile' => ' - 555 - 8956',
              'sender_phone_landline' => '+85 -  - 555 - 8956',
              'sender_postal_code' => '85963',
              'sender_account_number' => '8596556',
              'sender_bank_routing_no' => '85469',
              'sender_bank_branch' => 'California-test-bank',
              'sender_bank_name' => 'XYZ Bank',
              'sender_city' => 'texas',
              'sender_amount' => '100',
              'receiver_amount' => '150',
              'receiver_city' => 'abc state',
              'exchange_rate' => '10.12',
              'additional_information' => '' 
            )
      );
      
      $actual=$this->bts->doSALE($data);      
      $expected=array('status' => 400,'message' => 'INVALID PARAMETERDATA/SENDER/ADDRESS/STATE_CD');
      $this->assertEquals($expected, $actual);

  }

}
?>