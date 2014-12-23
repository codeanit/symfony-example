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

class BTSModel
{
    public function createTransaction(array $txn) 
    {
        // if($this->doAuthenticate());
        //     if($this->doSLV())
        //         if($this->doSALE());
    }

    public function doAuthenticate()
    {
        $username = "test";
        $password = "test";
        
        $rootNode = new \SimpleXMLElement( "<?xml version='1.0' encoding='UTF-8' standalone='yes'?><DATA></DATA>" );

        // $itemNode = $rootNode->addChild('CONFIRMATION_NM','7901234922');

    }

    public function doSLV()
    {

    }

    public function doSALE()
    {

    }

}