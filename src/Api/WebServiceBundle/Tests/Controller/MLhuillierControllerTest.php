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


class MLhuillierControllerTest extends WebTestCase
{   
    private $_url;
    private $_url2;
    private $client;

   public function __construct()
    {
        ini_set("soap.wsdl_cache_enabled", false); 

        $this->url = "http://api.firstglobalmoney.com.local/mlhuillier";
        $this->url2 = "http://api.firstglobalmoney.com.local/mlhuillier";
        
        $this->client = new \SoapClient($this->url, array(
                "trace" => 0,
                'exceptions' => 0,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS));     

    }

     /**
     * [Test Change Password]  
     *
     *@return void 
     */
    public function testChangePasswordWithBadUsername()
    {   
        $params = array(
        'sessionID'=>'77777',        
        'username' => 'SAFARIaa',
        'password'=> '123456',
        'newPassword' => 'manish',
        'signature' => md5('anitshrestha'), 
        );        
        $expected='77777|1';       
        $actual = $this->client->__soapCall('changePassword', $params);            
        $this->assertEquals(trim($expected), trim($actual));       
    }
      /**
     * [Test Change Password]  
     *
     *@return void 
     */
    public function testChangePasswordWithGoodUsername()
    {   
        $params = array(
        'sessionID'=>'77777',        
        'username' => 'SAFARI',
        'password'=> '123456',
        'newPassword' => 'manish',
        'signature' => md5('anitshrestha'), 
        );        
        $expected='77777|0';       
        $actual = $this->client->__soapCall('changePassword', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '12',
         'refno' => '502000019',
         'signature' => md5('anitshrestha'),      
        );        
        $expected='77777|5';       
        $actual = $this->client->__soapCall('showRemittanceDetail', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
         'refno' => '5020000ff',
         'signature' => md5('anitshrestha'),     
        );        
        $expected='77777|4';       
        $actual = $this->client->__soapCall('showRemittanceDetail', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
        'refno' => '502000008',
        'signature' => md5('anitshrestha'), 
        );        
        $expected='77777|0|502000008|20.00|US Dollar|Ali|Nadia||687 Main|';       
        $actual = $this->client->__soapCall('showRemittanceDetail', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
        'refno' => '502000019',
        'signature' => md5('anitshrestha'),       
        );        
        $expected='77777|1';       
        $actual = $this->client->__soapCall('showRemittanceDetail', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
        'refno' => '502000003',
        'signature' => md5('anitshrestha'),  

        );        
        $expected='77777|3';       
        $actual = $this->client->__soapCall('showRemittanceDetail', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }


// //END OF GET REMITTANCE TEST

// // START OF TagAsCompleted

    /**
     * [Test TagAsCompleted with Database Error]   
     *
     *@return void 
     */
    public function testTagAsCompletedWithBadConnection()
    {   
        $params = array(
        'sessionID' => '77777',
        'username' => 'CA47asf2',
        'password'=> '123456',
         'refno' => '502000019',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|5';       
        $actual = $this->client->__soapCall('tagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));       
    }

    /**
     * [Test TagAsCompleted with bad refno]     
     * 
     * @return void
     */
    public function testTagAsCompletedWithBadRefNo()
    {   
        $params = array(
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
         'refno' => '5020000ff',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|4';       
        $actual = $this->client->__soapCall('tagAsCompleted', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
         'refno' => '502000019',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|2';       
        $actual = $this->client->__soapCall('tagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }

    /**
     * [Test TagAsCompleted with refno having invalid Transaction]   
     *
     *@return void 
     */
    public function testTagAsCompletedWithRefNoHavingInvalidTransaction()
    {   
        $params = array(
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
         'refno' => '502000003',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|3';       
        $actual = $this->client->__soapCall('tagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }

// END of TagAsCompleted


// START OF inquireTagAsCompleted   
    /**
     * [Test inquireTagAsCompleted with Database Error]   
     *
     *@return void
     */
    public function testinquireTagAsCompletedWithBadConnection()
    {   
        $params = array(
        'sessionID' => '77777',
        'username' => 'MN137aa',
        'password'=> '123456',
         'refno' => '502000019',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|5';       
        $actual = $this->client->__soapCall('inquireTagAsCompleted', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
         'refno' => '5020000ff',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|4';       
        $actual = $this->client->__soapCall('inquireTagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));
    }
    /**
     * [Test inquireTagAsCompleted with refno having $status equals Approved]     
     *
     * @return void
     */
    public function testinquireTagAsCompletedWithApprvedRefNo()
    {   
        $params = array(
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
         'refno' => '502000008',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|1';       
        $actual = $this->client->__soapCall('inquireTagAsCompleted', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
         'refno' => '502000019',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|0|TN123T1|502000019|40.00|US Dollar|ABDUL|SADAM|HUSSEIM|SOME ADDRESS|zero';       
        $actual = $this->client->__soapCall('inquireTagAsCompleted', $params);            
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
        'sessionID' => '77777',
        'username' => 'MN137',
        'password'=> '123456',
         'refno' => '502000003',
         'tagNo' => 'TN123T1',
         'signature' => md5('anitshrestha')
        );        
        $expected='77777|1';       
        $actual = $this->client->__soapCall('inquireTagAsCompleted', $params);            
        $this->assertEquals(trim($expected), trim($actual));        

    }

// END OF TAG AS COMPLETED

 

   

}
