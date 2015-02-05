<?php

namespace Api\PayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/queue/", name="queue")
     * 
     */
    public function indexAction()
    {        
        $Q=$this->get('queue');       
        print_r($Q->executeQueuedOperation());
        die;      
    }

    /**
     * @Route("/notification/", name="notification")
     * 
     */
    public function notificationAction()
    {        
        $Q=$this->get('queue');
        print_r($Q->noti());     
        die;      
    }

    /**
     * @Route("/dashboard/", name="dashboard")
     * @Template()
     */
    public function transactionAction()
    { 
        $DB=$this->get('connection');
        $transactions=$DB->getTransactions();      
        return array('transactions'=>$transactions);     
    }

    /**
     * @Route("/service/", name="service")
     * @Template()
     */
    public function serviceAction()
    { 
        $DB=$this->get('connection');
        $services=$DB->getServices();
        return array('services'=>$services);     
    }

     /**
     * @Route("/addService/{name}", name="addService")
     * @Template()
     */
    public function addServiceAction(Request $request,$name=null)
    {   
        $DB = $this->get('connection');
        if($request->request->get('service_name'))
        {             
            $service_name=$request->request->get('service_name');
            $fields=$request->request->get('fields'); 

            $duplicateServiceCheck=$DB->checkDuplicateServiceName($service_name);

            if(count($duplicateServiceCheck)>1){
                return array('error_msg'=>'Service Name Already Exists','service_name'=>$service_name,'fields'=>$fields);
            }
            
            if(in_array('', $fields)){
                return array('error_msg'=>'Empty Fields Found','service_name'=>$service_name,'fields'=>$fields);                
            }
                                  
            $check=array_diff_assoc($fields, array_unique($fields));

            if(count($check)>0){
                return array('error_msg'=>'Duplicate Fields Found','service_name'=>$service_name,'fields'=>$fields);
            }
            
            $service_id=$request->request->get('service_id');            
            if($service_id !=''){
                $status=$DB->editService($service_name,$fields,$service_id);
            }else{
                $status=$DB->saveService($service_name,$fields);
            }
            return $this->redirect($this->generateUrl('service'));

        }else{
            $field=$DB->getFields($name);            
            return array('service_name'=>$name,'fields'=>$field[0],'service_id'=>$field[1]);
        }

    }

    /**
     * @Route("/credential/{id}", name="credential")
     * @Template()
     */
    public function credentialAction($id)
    {       
        $DB=$this->get('connection');        
        $services=$DB->getServiceCredentials($id);
        if($services)
        {
            $d=(array)json_decode(base64_decode($services[2]));                   
            return array('name'=>$id,'values'=>$d);
        }else{
            return $this->redirect($this->generateUrl('service'));            
        }
             
    }

    /**
     * @Route("/addCredential/", name="addCredential")
     * @Template()
     */
    public function addCredentialAction(Request $request)
    {   
        if($request->request->get('service_name'))
        {            
            $fields=$request->request->get('fields');
            $service_name=$request->request->get('service_name');
            $DB = $this->get('connection');
            $status=$DB->saveCredentials($service_name,$fields);
            return $this->redirect($this->generateUrl('service'));
        }
    }

    /**
     * @Route("/status/{id}/{status}", name="status")
     * 
     */
    public function statusAction($id,$status)
    {   
        $DB = $this->get('connection');
        $DB->changeStatus($id,$status);
        return $this->redirect($this->generateUrl('service'));

    }


   
}
