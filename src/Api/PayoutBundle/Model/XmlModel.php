<?php 

/**
 * First Global Data
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Manish Chalise
 * @license  http://firstglobalmoney.com/license description
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

namespace Api\PayoutBundle\Model;

use Symfony\Component\HttpFoundation\Response;

/**
 * Bridge to call TB DBAL
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Manish Chalise
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

class XmlModel
{
    /**
     * Send Data to TB 
     * 
     * @param array 
     *        
     * @return  array
     */
    public function createTXN(array $data)
    {       
      
        $rootNode = new \SimpleXMLElement( "<?xml version='1.0' encoding='UTF-8' standalone='yes'?><DATA></DATA>" );

        $itemNode = $rootNode->addChild('CONFIRMATION_NM','7901234922');
        $itemNode = $rootNode->addChild('ORDER_STATUS','Pending');
        $itemNode = $rootNode->addChild('SERVICE_CD','MTR');
        $itemNode = $rootNode->addChild('PAYMENT_TYPE_CD','CSA');
        $itemNode = $rootNode->addChild('ORIG_COUNTRY_CD','USA');
        $itemNode = $rootNode->addChild('ORIG_CURRENCY_CD','USD');
        $itemNode = $rootNode->addChild('DEST_COUNTRY_CD','MEX');
        $itemNode = $rootNode->addChild('DEST_CURRENCY_CD','MXP');
        $itemNode = $rootNode->addChild('R_ACCOUNT_TYPE_CD');
        $itemNode = $rootNode->addChild('R_ACCOUNT_NM');
        $itemNode = $rootNode->addChild('R_AGENT_CD','TLC');
        $itemNode = $rootNode->addChild('R_AGENT_REGION_SD');
        $itemNode = $rootNode->addChild('R_AGENT_BRANCH_SD');      
        $itemNode = $rootNode->addChild('ORIGIN_AM','100.0000');
        $itemNode = $rootNode->addChild('DESTINATION_AM','252.0000');
        $itemNode = $rootNode->addChild('EXCH_RATE_FX','12.60000');
        $itemNode = $rootNode->addChild('WHOLESALE_FX');      
        $itemNode = $rootNode->addChild('FEE_AM','00.0000');
        $itemNode = $rootNode->addChild('DISCOUNT_AM');      
        $itemNode = $rootNode->addChild('DISCOUNT_REASON_CD');      
        $itemNode = $rootNode->addChild('S_PAYMENT_TYPE_CD','CSH');      
        $itemNode = $rootNode->addChild('S_ACCOUNT_TYPE_CD');      
        $itemNode = $rootNode->addChild('S_ACCOUNT_NM');      
        $itemNode = $rootNode->addChild('S_BANK_CD');      
        $itemNode = $rootNode->addChild('S_BANK_REF_NM');      
        $itemNode = $rootNode->addChild('R_SMS_MSG_REQ');      
        $itemNode = $rootNode->addChild('O_SMS_MSG_REQ');

        //AGENT NODE
        $itemNode2 = $rootNode->addChild('AGENT');
        $itemNode2->addChild( 'ORDER_NM', '120300110153' );
        $itemNode2->addChild( 'REGION_SD', 'N/A' );
        $itemNode2->addChild( 'BRANCH_SD', 'N/A' );
        $itemNode2->addChild( 'STATE_CD', 'CA' );
        $itemNode2->addChild( 'COUNTRY_CD', 'USA' );
        $itemNode2->addChild( 'SUP_USER_NAME', 'N/A' );
        $itemNode2->addChild( 'TERMINAL', 'N/A' );
        $itemNode2->addChild( 'AGENT_DT', '20120311' );
        $itemNode2->addChild( 'AGENT_TM', '231634' );

        //SENDER NODE
        $itemNode3 = $rootNode->addChild('SENDER');
        $itemNode3->addChild( 'CUSTOMER_ID');
        $itemNode3->addChild( 'FIRST_NAME', 'Francisco' );
        $itemNode3->addChild( 'MIDDLE_NAME');
        $itemNode3->addChild( 'LAST_NAME', 'Cervantes' );
        $itemNode3->addChild( 'MOTHER_M_NAME', 'Resendiz' );
        $addr=$itemNode3->addChild( 'ADDRESS');
            $addr->addChild('ADDRESS','1225 Norte Street, Esciondido, California, United States, 95026');
            $addr->addChild('CITY','Esciondido');
            $addr->addChild('STATE_CD','CA');
            $addr->addChild('COUNTRY_CD','USA');
            $addr->addChild('ZIP_CODE','95026');
            $addr->addChild('PHONE','7607558283');
            $addr->addChild('CELL_PHONE');
            $addr->addChild('EMAIL');

        //SENDER ON BEHALF OF NODE
        $itemNode4 = $rootNode->addChild('SENT_ON_BEHALF_OF');
        $itemNode4->addChild( 'FIRST_NAME');
        $itemNode4->addChild( 'MIDDLE_NAME');
        $itemNode4->addChild( 'LAST_NAME');
        $itemNode4->addChild( 'MOTHER_M_NAME');
        $addr2=$itemNode4->addChild( 'ADDRESS');
            $addr2->addChild('ADDRESS');
            $addr2->addChild('CITY');
            $addr2->addChild('STATE_CD');
            $addr2->addChild('ZIP_CODE');
            $addr2->addChild('PHONE');
            $addr2->addChild('CELL_PHONE');
            $addr2->addChild('EMAIL');

        //RECIPIENT NODE
        $itemNode5 = $rootNode->addChild('RECIPIENT');
        $itemNode5->addChild( 'FIRST_NAME','Manish');
        $itemNode5->addChild( 'MIDDLE_NAME');
        $itemNode5->addChild( 'LAST_NAME','Chalise');
        $itemNode5->addChild( 'MOTHER_M_NAME','Rana');
        $itemNode5->addChild( 'IDENTIF_TYPE_CD');
        $itemNode5->addChild( 'IDENTIF_NM');
        $foreign=$itemNode5->addChild( 'FOREIGN_NAME');
            $foreign->addChild('FIRST_NAME');
            $foreign->addChild('MIDDLE_NAME');
            $foreign->addChild('LAST_NAME');
            $foreign->addChild('MOTHER_M_NAME');
        $addr3=$itemNode5->addChild( 'ADDRESS');
            $addr3->addChild('ADDRESS','Ezequiel Montes');
            $addr3->addChild('CITY','Ezequiel Montes');
            $addr3->addChild('STATE_CD','MEX');
            $addr3->addChild('COUNTRY_CD','MEX');
            $addr3->addChild('ZIP_CODE','85696');
            $addr3->addChild('PHONE','9999999999');
            $addr3->addChild('CELL_PHONE');
            $addr3->addChild('EMAIL');

        //SENDER IDENTIFICATION NODE
        $itemNode6 = $rootNode->addChild('SENDER_IDENTIFICATION');
        $itemNode6->addChild( 'TYPE_CD');
        $itemNode6->addChild( 'ISSUER_CD');
        $itemNode6->addChild( 'ISSUER_STATE_CD');
        $itemNode6->addChild( 'ISSUER_COUNTRY_CD');
        $itemNode6->addChild( 'IDENTIF_NM');
        $itemNode6->addChild( 'EXPIRATION_DT');

         //ADDITIONAL INFORMATION NODE
        $itemNode6 = $rootNode->addChild('ADDITIONAL_INFO');
        $itemNode6->addChild( 'DOB_DT');
        $itemNode6->addChild( 'OCCUPATION','Project Manager');
        $itemNode6->addChild( 'SSN');
        $itemNode6->addChild( 'SOURCE_OF_FUNDS_DS');
        $itemNode6->addChild( 'REASON_OF_TRANS_DS');    
        
        $xml=$rootNode->asXML();
        
        //To display XML structure in browser
       //return new Response($rootNode->asXML());        
       //die;

        return $xml;
    }
}