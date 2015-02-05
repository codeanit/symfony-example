<?php
/**
 * First Global Data Corp. Inc.
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Anit Shrestha Manandhar <ashrestha@firstglobalmoney.com>
 * @license  http://firstglobalmoney.com/license description
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

namespace Api\WebServiceBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Api\WebServiceBundle\Model\TBConnectionModel as TBConnection;

/**
 * SOAP web service base class.
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Anit Shrestha Manandhar <ashrestha@firstglobalmoney.com>
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */
abstract class SoapController extends ContainerAware
{
    /**
     * It contains Api\WebServiceBundle\Entity\TBConnection objects instance
     * 
     * @var proctected 
     */
    protected $TBConnection;

    /**
     * [__construct description]
     */
    public function __construct() 
    {
        $this->TBConnection = new TBConnection(); 
    }
}
