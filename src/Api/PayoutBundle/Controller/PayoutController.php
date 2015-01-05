<?php

namespace Api\PayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations\View;
use Acme\BlogBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Request;

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
        $this->TBConnection = $this->get('tb_connection');     
        $postData=$request->getContent();
        $decodedData=(array) json_decode($postData);       
        $log=$this->TBConnection->addLog($decodedData);
        if($log==1)
        {
            $result = array('Code'=>'200','Msg'=>'Log Insertion Success.');            
            //$result = $this->TBConnection->curlTransborder($decodedData);  
        }elseif($log==2) {
            $result = array('Code'=>'702','Msg'=>'Dublicate Transaction Key.');
        }else {
            $result = array('Code'=>'701','Msg'=>'Error In Log Insertion Process.');
        }
        return $result;    
    }

    public function postPayoutAction(Request $request)
    {
        $this->TBConnection = $this->get('tb_connection');             
        $this->BTS = $this->get('bts');     
        $postData=$request->getContent();
        $decodedData=(array) json_decode($postData);       
        $log=$this->TBConnection->addLog($decodedData);
        if($log==1)
        {                       
            $result = $this->BTS->doSALE($decodedData);

        }elseif($log==2) {

            $result = array('Code'=>'702','Msg'=>'Dublicate Transaction Key.');

        }else {

            $result = array('Code'=>'701','Msg'=>'Error In Log Insertion Process.');
        }

        return $result;  

    }


}
