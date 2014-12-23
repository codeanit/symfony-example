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
class MLhuillierController extends SoapController
{
    /**
     * [__construct description]
     */
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * [ShowRemittanceDetail ]
     * 
     * @param String $sessionID [session id ]
     * @param String $username  [username]
     * @param String $password  [password]
     * @param String $refno     [control number]
     * @param String $signature [signature]
     * 
     * @return String          
     *
     * @Soap\Method("ShowRemittanceDetail")
     * @Soap\Param("sessionID", phpType = "string")
     * @Soap\Param("username", phpType = "string")
     * @Soap\Param("password", phpType = "string")
     * @Soap\Param("refno", phpType = "string")
     * @Soap\Param("signature", phpType = "string")    
     * @Soap\Result(phpType = "string")     
     */
    public function showRemittanceDetail($sessionID, $username, $password, $refno, $signature )
    {
        $data = array(
            'model' => 'MLhuillier',
            'operation' => 'showRemittanceDetail',            
            'sessionID' => $sessionID,
            'username' => $username,
            'password' => $password,
            'refNo' => $refno,
            'signature'=> $signature);

        $getResult = $this->TBConnection->curlTransborder($data);

        return $this->container->get('besimple.soap.response')
            ->setReturnValue(sprintf('%s', $getResult));
    }

   
    /**
     * [ It checks whether status is paid or not]
     * 
     * @param String $sessionID [session id]
     * @param String $username  [username]
     * @param String $password  [password]
     * @param String $refno     [control number]
     * @param String $traceno   [trace number]
     * @param String $signature [signature]
     * 
     * @return String            
     * 
     * @Soap\Method("InquireTagAsCompleted")
     * @Soap\Param("sessionID", phpType = "string")
     * @Soap\Param("username", phpType = "string")
     * @Soap\Param("password", phpType = "string")     
     * @Soap\Param("refno", phpType = "string") 
     * @Soap\Param("traceno", phpType = "string")    
     * @Soap\Param("signature", phpType = "string")     
     * @Soap\Result(phpType = "string")
     */

    public function inquireTagAsCompleted($sessionID, $username, $password, $refno, $traceno,$signature)
    {
        //select
        $authData = array(
            'model' => 'MLhuillier',            
            'sessionID' => $sessionID,
            'username' => $username,
            'password' => $password,
            'operation' => 'inquireTagAsCompleted',
            'refNo' => $refno,
            'traceNo'=>$traceno,
            'signature'=> $signature);

        $getResults=$this->TBConnection->curlTransborder($authData);

        return $this->container->get('besimple.soap.response')
            ->setReturnValue(sprintf('%s', $getResults));
    }

    

    /**
     * [It checks  whether status is approved or paid ]
     * 
     * @param String $sessionID [session id ]
     * @param String $username  [username]
     * @param String $password  [password]
     * @param String $refno     [control number]
     * @param String $traceno   [trace number]
     * @param String $signature [signature]    
     * 
     * @return String           
     * 
     * @Soap\Method("TagAsCompleted")
     * @Soap\Param("sessionID", phpType = "string")
     * @Soap\Param("username", phpType = "string")
     * @Soap\Param("password", phpType = "string")     
     * @Soap\Param("refno", phpType = "string") 
     * @Soap\Param("traceno", phpType = "string")    
     * @Soap\Param("signature", phpType = "string")    
     * @Soap\Result(phpType = "string")
     */
    public function tagAsCompleted($sessionID, $username, $password, $refno, $traceno,$signature)
    {
        //update
        $authData = array(
            'model' => 'MLhuillier', 
            'operation' => 'tagAsCompleted',
            'sessionID' => $sessionID,
            'username' => $username,
            'password' => $password,
            'refNo' => $refno,
            'traceNo'=>$traceno,
            'signature'=> $signature);

        $getResults=$this->TBConnection->curlTransborder($authData);

        return $this->container->get('besimple.soap.response')
            ->setReturnValue(sprintf('%s', $getResults));
    }

     
    /**
     * [changePassword description]
     * 
     * @param String $sessionID   [session id]
     * @param String $username    [username]
     * @param String $password    [password]
     * @param String $newPassword [new password]
     * @param String $signature   [signature]
     * 
     * @return String              
     *
     * @Soap\Method("changePassword")
     * @Soap\Param("sessionID", phpType = "string")
     * @Soap\Param("username", phpType = "string")
     * @Soap\Param("password", phpType = "string")     
     * @Soap\Param("newPassword", phpType = "string") 
     * @Soap\Param("signature", phpType = "string")     
     * @Soap\Result(phpType = "string")
     */
    public function changePassword($sessionID, $username, $password, $newPassword, $signature)
    {
        $authData = array(
            'model' => 'MLhuillier',
            'operation' => 'changePassword',           
            'sessionID' => $sessionID,
            'username' => $username,
            'password' => $password,
            'newPassword' => $newPassword,
            'signature'=> $signature);

        $getResults=$this->TBConnection->curlTransborder($authData);

        return $this->container->get('besimple.soap.response')
            ->setReturnValue(sprintf('%s', $getResults));
    }    
   
}
