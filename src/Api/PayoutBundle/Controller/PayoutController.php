<?php

namespace Api\PayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations\View;
use Acme\BlogBundle\Entity\Page;
use  Api\PayoutBundle\Model\TBConnectionModel as TBConnection;
use Symfony\Component\HttpFoundation\Request;
use  Api\PayoutBundle\Model\BTSModel as BTS;


class PayoutController extends Controller
{

    private $TBConnection;
    private $BTSConn;

    public function __construct() 
    {
    }
  
    /** 
     * @return array
     * @View()
     * @Route(requirements={"_format"="json"})
     *
     * @param String $sessionID [session id ]
     * @param String $username  [username]
     * @param String $password  [password]
     * @param String $refno     [control number]
     * @param String $signature [signature]     
     * 
     * */
    
    public function getTransactionsAction(Request $request)
    { 
         $getData = $request->getRequest();       
         return $_GET;
    }

    /** 
     * @return array
     * @View()
     * @Route(requirements={"_format"="json"})
     *
     * @param String $sessionID [session_id ]     
     * 
     * */    
    public function postTransactionAction(Request $request)
    {
        $this->TBConnection = new TBConnection($this->container);     
        $postData=$request->getContent();
        $decodedData=(array) json_decode($postData);       
        $log=$this->TBConnection->addLog($decodedData);
        if($log==1)
        {
            $result = array('Code'=>'200','Msg'=>'Log Insertion Success.');            
            //$result = $this->TBConnection->curlTransborder($decodedData);  
        }else {
            $result = array('Code'=>'777','Msg'=>'Error In Log Insertion Process.');
        }
        return $result;    
    }


}
