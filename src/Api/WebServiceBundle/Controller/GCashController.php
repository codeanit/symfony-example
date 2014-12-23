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

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Api\WebServiceBundle\Controller\SoapController;
use Symfony\Component\HttpFoundation\Request;


/**
 * MLhuillier webservice interface
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Anit Shrestha Manandhar <ashrestha@firstglobalmoney.com>
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */
class GCashController extends SoapController
{
    /**
     * [__construct description]
     */
    public function __construct() 
    {
        parent::__construct();
    }

    /**
    *  Show remittance details with refrno
    * 
    * @param string $username [username]
    * @param string $password [password]
    * @param string $refno    [control number]
    * 
    * @return string
    * 
    * @Soap\Method("ShowRemittanceDetail")     
    * @Soap\Param("username", phpType = "string")
    * @Soap\Param("password", phpType = "string")
    * @Soap\Param("refno", phpType = "string")       
    * @Soap\Result(phpType = "string")
    */
    public function showRemittanceDetail($username, $password, $refno)
    {
        $data = array(
            'model' => 'GCash',
            'operation' => 'showRemittanceDetail', 
            'username' => $username,
            'password' => $password,
            'refNo' => $refno,);

        $getResult = $this->TBConnection->curlTransborder($data);

        return $this->container->get('besimple.soap.response')
            ->setReturnValue(sprintf('%s', $getResult));
    }

    /**
     * [It checks  whether status is approved or paid ]
     * 
     * @param string $username [username]
     * @param string $password [password]
     * @param string $refno    [control number]
     * @param string $traceno  [trace number]
     * 
     * @return String
     * 
     * @Soap\Method("TagAsCompleted")     
     * @Soap\Param("username", phpType = "string")
     * @Soap\Param("password", phpType = "string")     
     * @Soap\Param("refno", phpType = "string") 
     * @Soap\Param("traceno", phpType = "string")  
     * @Soap\Result(phpType = "string")
     */
    public function tagAsCompleted($username, $password, $refno, $traceno)
    {
        //update
        $authData = array(
            'model' => 'GCash', 
            'operation' => 'tagAsCompleted',           
            'username' => $username,
            'password' => $password,
            'refNo' => $refno,
            'traceNo'=>$traceno,
            );

        $getResults=$this->TBConnection->curlTransborder($authData);

        return $this->container->get('besimple.soap.response')
            ->setReturnValue(sprintf('%s', $getResults));
    }

    /**
     * It checks whether status is paid or not
     * 
     * @param string $username [username]
     * @param string $password [password]
     * @param string $refno    [control number]
     * @param string $traceno  [trace number]
     * 
     * @return String
     * 
     * @Soap\Method("InquireTagAsCompleted")
     * @Soap\Param("username", phpType = "string")
     * @Soap\Param("password", phpType = "string")     
     * @Soap\Param("refno", phpType = "string") 
     * @Soap\Param("traceno", phpType = "string") 
     * @Soap\Result(phpType = "string")
     */
    public function inquireTagAsCompleted($username, $password, $refno, $traceno)
    {
        //select
        $authData = array(
            'model' => 'GCash', 
            'username' => $username,
            'password' => $password,
            'operation' => 'inquireTagAsCompleted',
            'refNo' => $refno,
            'traceNo'=>$traceno,
            );

        $getResults=$this->TBConnection->curlTransborder($authData);

        return $this->container->get('besimple.soap.response')
            ->setReturnValue(sprintf('%s', $getResults));
    }

    /**
     * [changePassword ]
     *
     * @param string $username    [username]
     * @param string $password    [password]
     * @param string $newPassword [new password]
     * 
     * @return String
     *
     * @Soap\Method("changePassword")
     * @Soap\Param("username", phpType = "string")
     * @Soap\Param("password", phpType = "string")  
     * @Soap\Param("newPassword", phpType = "string")  
     * @Soap\Result(phpType = "string")
     */
    public function changePassword($username, $password, $newPassword)
    {
        $authData = array(
            'model' => 'GCash',
            'operation' => 'changePassword', 
            'username' => $username,
            'password' => $password,
            'newPassword' => $newPassword,
           );

        $getResults=$this->TBConnection->curlTransborder($authData);

        return $this->container->get('besimple.soap.response')
            ->setReturnValue(sprintf('%s', $getResults));
    }    
   
}