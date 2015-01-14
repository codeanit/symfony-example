<?php

namespace Api\PayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations\View;
use Acme\BlogBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

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
  

    /** 
     * @return array
     * @View()
     * @Route(requirements={"_format"="json"})
     *
     * @param String $sessionID [session_id ]     
     * 
     * */    
    public function postCreateAction(Request $request)
    { 
        $this->DB = $this->get('connection');     
        $postData=$request->getContent();
        $decodedData=(array) json_decode($postData);

        $status=$this->DB->operateTransaction($decodedData,$postData,'create');

        if($status[0]==1 && $status[1]==1)
        {
            $result = array('Code'=>'200','Msg'=>'Successfully Received');

        }elseif($status[0]==2 && $status[1]==2) {

            $result = array('Code'=>'702','Msg'=>'Dublicate Transaction Key.');

        }else {
            $result = array('Code'=>'701','Msg'=>'Error In Log Insertion Process.');
        }
       
        return $result;    
    }
    
    public function postModifyAction(Request $request)
    {
        $this->DB = $this->get('connection');     
        $postData=$request->getContent();
        $decodedData=(array) json_decode($postData);

        $status=$this->DB->operateTransaction($decodedData,$postData,'modify');

       if($status[0]==3 && $status[1]==3) {

            $result = array('Code'=>'200','Msg'=>'Successfully Modified Transaction.');

        }else {
            $result = array('Code'=>'701','Msg'=>'Error In Transaction Modification.');
        }
       
        return $result;    
    }


    public function postCancelAction(Request $request)
    {
        $this->DB = $this->get('connection');     
        $postData=$request->getContent();
        $decodedData=(array) json_decode($postData);

        $status=$this->DB->operateTransaction($decodedData,$postData,'cancel');

        if($status[0]==4 && $status[1]==4 && !array_key_exists(3, $status))
        {
            $result = array('Code'=>'200','Msg'=>'Status Successfully Changed To Cancel');

        }elseif($status[0]==4 && $status[1]==4 && isset($status[3])){

            $result=array('Code'=>'200','Msg'=>'Change Status to Cancel','status'=>'cancelled','confirmation_no'=>$status[3]);

        }else {

            $result = array('Code'=>'701','Msg'=>'Error In Status Change.');
        }
       
        return $result;    
    }


}
