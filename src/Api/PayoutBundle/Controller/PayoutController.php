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

    public function __construct() 
    {

    }

    /** 
     * It Handels all post request from TB for Create(approved TxN) Operation
     * @return JSON 
     * @View()
     *
     * @param JSON Post Data     
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
            $result = array('code'=>'200','message'=>'Successfully Received');

        }elseif($status[0]==2 && $status[1]==2) {

            $result = array('code'=>'400','message'=>'Duplicate Confirmation Number.');

        }else {
            $result = array('code'=>'400','message'=>'Error In Log Insertion Process.');
        }
       
        return $result;    
    }

    /** 
     * It Handels all post request from TB for Update Operation
     * @return JSON 
     * @View()
     *
     * @param JSON Post Data     
     * 
     * */    
    
    public function postModifyAction(Request $request)
    {
        $this->DB = $this->get('connection');     
        $postData=$request->getContent();
        $decodedData=(array) json_decode($postData);

        $status=$this->DB->operateTransaction($decodedData,$postData,'modify');

       if($status[0]==3 && $status[1]==3) {

            $result = array('code'=>'200','message'=>'Successfully Added Transaction for Modification.');

        }else {
            $result = array('code'=>'400','message'=>'Error In Adding Transaction Modification.');
        }
       
        return $result;    
    }  

    /** 
     * It Handels all post request from TB for Cancel Operation
     * @return JSON
     * @View()
     *
     * @param JSON Post Data     
     * 
     * */    
    public function postCancelAction(Request $request)
    {
        $this->DB = $this->get('connection');     
        $postData=$request->getContent();
        $decodedData=(array) json_decode($postData);

        $status=$this->DB->operateTransaction($decodedData,$postData,'cancel');

       if($status[0]==4 && $status[1]==4) {

            $result = array('code'=>'200','message'=>'Successfully Added Transaction for Cancel.');

        }else {
            $result = array('code'=>'400','message'=>'Error In Canceling Transaction.');
        }
       
        return $result;    
    } 


}
