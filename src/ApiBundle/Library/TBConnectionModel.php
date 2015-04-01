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

namespace Api\PayoutBundle\Library;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;

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

class TBConnectionModel
{

    public $url;

    protected $container;

    function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Send Data to TB 
     * 
     * @param array 
     *        
     * @return  array
     */
    public function notify(array $postedData)
    { 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://172.16.1.50/secure/cdex");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postedData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resultPOST = curl_exec($curl);       
        var_dump($resultPOST);die;
        return (array) json_decode($resultPOST);    
    }   

}