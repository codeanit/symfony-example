<?php
/**
 * Mlhuillier Controller functional Test
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Anit Shrestha Manandhar <ashrestha@firstglobalmoney.com>
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

namespace Api\WebServiceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Mlhuillier Controller functional Test
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Anit Shrestha Manandhar <ashrestha@firstglobalmoney.com>
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

class GCashControllerTest extends WebTestCase
{
        private $_url;        
        private $_client;

     /**
      * Creates SOAP client object for all test methods 
      */
    public function __construct()
    {
            ini_set("soap.wsdl_cache_enabled", false); 

            $this->_url = "http://api.firstglobalmoney.com.local/gcash";
            $this->_client = new \SoapClient($this->_url, array("trace" => 0,'exceptions' => 0,'cache_wsdl' => WSDL_CACHE_NONE,'features' => SOAP_SINGLE_ELEMENT_ARRAYS));
    }
     /**
     * [Test Change Password]  
     *
     *@return void 
     */
    public function testChangePassword()
    {   
        $params = array(        
        'username' => 'KEDIR',
        'password'=> '123456',
        'newPassword' => 'manish',
        );        
        $expected='0';       
        $actual = $this->_client->__soapCall('changePassword', $params);            
        $this->assertEquals(trim($expected), trim($actual));       
    }
    /**
     * [Test ShowRemittanceDetail with Database Error]  
     *
     *@return void 
     */
    public function testShowRemittanceDetailWithBadConnection()
    {   
        $params = array(        
        'username' => 'SAFARI2',
        'password'=> '12',
         'refno' => '502000019',
        );        
        $expected='5';       
        $actual = $this->_client->__soapCall('showRemittanceDetail', $params);            
        $this->assertEquals(trim($expected), trim($actual));       
    }

    /**
     * [Test ShowRemittanceDetail with bad refno]     
     *
     * @return void
     */
    public function testShowRemittanceDetailWithBadRefNo()
    {   
        $params = array(       
        'username' => 'SAFARI2',
        'password'=> '123456',
         'refno' => '5020000ff',              
        );        
        $expected='4';       
        $actual = $this->_client->__soapCall('showRemittanceDetail', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }
    /**
     * [Test ShowRemittanceDetail with refno having $status equals Approved]  
     *
     * @return void  
     */
    public function testShowRemittanceDetailWithApprvedRefNo()
    {   
        $params = array(
        'username' => 'SAFARI2',
        'password'=> '123456',
        'refno' => '502000008',
        );        
        $expected='502000008|0|20.00|Ali|Nadia||null|null|null|687 Main|';
        $actual = $this->_client->__soapCall('showRemittanceDetail', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }
    /**
     * [Test ShowRemittanceDetail with refno having $status equals Approved]   
     *
     * @return void
     */
    public function testShowRemittanceDetailWithPaidRefNo()
    {   
        $params = array(        
        'username' => 'SAFARI2',
        'password'=> '123456',
        'refno' => '502000098',            
        );        
        $expected='502000098|1|200.00|Bhjkgfj|Teest||null|null|null|Ertertwe|' ;               
        $actual = $this->_client->__soapCall('showRemittanceDetail', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }

    /**
     * [Test ShowRemittanceDetail with refno having invalid Transaction]    
     * 
     * @return void 
     */
    public function testShowRemittanceDetailWithRefNoHavingInvalidTransaction()
    {   
        $params = array(      
        'username' => 'SAFARI2',
        'password'=> '123456',
        'refno' => '502000003', 
        );        
        $expected='3';       
        $actual = $this->_client->__soapCall('showRemittanceDetail', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }
    
    /**
     * [Test TagAsCompleted with refno having $status equals Approved]  
     *
     *@return void 
     */
    public function testTagAsCompletedWithPaidRefNo()
    {   
        $params = array(            
        'username' => 'SAFARI2',
        'password'=> '123456',
         'refno' => '502000098',
         'tagNo' => 'TN1',             
        );        
        $expected='2';       
        $actual = $this->_client->__soapCall('tagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }

   
    /**
     * [Test inquireTagAsCompleted with bad refno]     
     *
     * @return void
     */
    public function testinquireTagAsCompletedWithBadRefNo()
    {   
        $params = array(            
        'username' => 'SAFARI2',
        'password'=> '123456',
         'refno' => '5020000ff',
         'tagNo' => 'TN1',             
        );        
        $expected='4';       
        $actual = $this->_client->__soapCall('inquireTagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }
    
    /**
     * [Test inquireTagAsCompleted with refno having $status equals Approved]   
     *
     *@return void 
     */
    public function testinquireTagAsCompletedWithPaidRefNo()
    {   
        $params = array(            
        'username' => 'SAFARI2',
        'password'=> '123456',
         'refno' => '502000098',
         'tagNo' => 'TN1',             
        );        
        $expected='TN1|502000098|0|200.00|Bhjkgfj|Teest||null|null|null|Ertertwe|' ;       
               
        $actual = $this->_client->__soapCall('inquireTagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }

    /**
     * [Test inquireTagAsCompleted with refno having invalid Transaction]    
     *
     *@return void 
     */
    public function testInquireTagAsCompletedWithRefNoHavingInvalidTransaction()
    {   
        $params = array(            
        'username' => 'SAFARI2',
        'password'=> '123456',
         'refno' => '502000003',
         'tagNo' => 'TN1',             
        );        
        $expected='1';       
        $actual = $this->_client->__soapCall('inquireTagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));        

    }

}
