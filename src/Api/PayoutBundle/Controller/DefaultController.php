<?php

namespace Api\PayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;



class DefaultController extends Controller
{
    /**
     * @Route("/queue/", name="queue")
     * 
     */
    public function indexAction()
    {         
        $Q=$this->get('queue');
        //$result=$Q->executeQueuedOperation();       
        print_r($Q->executeQueuedOperation());
        // $TB=$this->get('tb_connection');
        // $TB->curlTransborder($result);
        die;      
    }

    /**
     * @Route("/bdo/", name="bdo")
     * 
     */
    public function testAction()
    {        
        $Q=$this->get('bdo');
        print_r($Q->pickupCash());     
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
        if($this->get('session')->get('username')){
            $DB=$this->get('connection');
            $transactions=$DB->getTransactions();      
            return array('transactions'=>$transactions);  
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }

           
    }

    /**
     * @Route("/service/", name="service")
     * @Template()
     */
    public function serviceAction()
    { 
        if($this->get('session')->get('username')){
            $DB=$this->get('connection');
            $services=$DB->getServices();
            return array('services'=>$services);
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }
        
    }
     /**
     * @Route("/addService/{name}/{id}", name="addService")
     * @Template()
     */
    public function addServiceAction(Request $request,$name=null,$id=null)
    {   
        if($this->get('session')->get('username')){
            $DB = $this->get('connection');
            if($request->request->get('service_name'))
            {             
                $service_name=$request->request->get('service_name');
                $fieldsArray=$request->request->get('fields');
                $fields = array_map('strtolower', $fieldsArray);
                if(is_null($fields))
                {
                    return array('error_msg'=>'Fields Are Needed','service_name'=>$service_name,'service_id'=>$id);                
                }
                
                if(!is_null($request->request->get('service_id')))
                {
                    $id=$request->request->get('service_id');
                    $duplicateServiceCheck=$DB->checkDuplicateServiceName($service_name,$id);                
                    if(count($duplicateServiceCheck)>1){
                        return array('error_msg'=>'Service Name Already Exists','service_name'=>$service_name,'service_id'=>$id,'fields'=>$fields);
                    }
                }           
                
                if(in_array('', $fields)){
                    return array('error_msg'=>'Empty Fields Found','service_name'=>$service_name,'service_id'=>$id,'fields'=>$fields);                
                }
                                      
                $check=array_diff_assoc($fields, array_unique($fields));

                if(count($check)>0){
                    return array('error_msg'=>'Duplicate Fields Found','service_name'=>$service_name,'service_id'=>$id,'fields'=>$fields,'case'=>'duplicate');
                }
                
                $service_id=$request->request->get('service_id');            
                if($service_id !=''){
                    // var_dump($request->request->all());die;
                    $status=$DB->editService($service_name,$fields,$service_id);
                }else{
                    $status=$DB->saveService($service_name,$fields);
                }
                return $this->redirect($this->generateUrl('service'));

            }else{
                $field=$DB->getFields($name);            
                return array('service_name'=>$name,'fields'=>$field[0],'service_id'=>$field[1]);
            }
  
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }
        
    }

    /**
     * @Route("/credential/{id}", name="credential")
     * @Template()
     */
    public function credentialAction($id)
    {    
        if($this->get('session')->get('username')){
            $DB=$this->get('connection');        
            $services=$DB->getServiceCredentials($id);
            if($services)
            {
                $d=(array)json_decode(base64_decode($services[2]));                   
                return array('name'=>$id,'values'=>$d);
            }else{
                return $this->redirect($this->generateUrl('service'));            
            }
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }   
        
             
    }

    /**
     * @Route("/addCredential/", name="addCredential")
     * @Template()
     */
    public function addCredentialAction(Request $request)
    {   
        if($this->get('session')->get('username')){
            if($request->request->get('service_name'))
            {            
                $fields=$request->request->get('fields');
                $service_name=$request->request->get('service_name');
                $DB = $this->get('connection');
                $status=$DB->saveCredentials($service_name,$fields);
                return $this->redirect($this->generateUrl('service'));
            }
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }

        
    }

    /**
     * @Route("/status/{id}/{status}", name="status")
     * 
     */
    public function statusAction($id,$status)
    {   
        if($this->get('session')->get('username')){
            $DB = $this->get('connection');
            $DB->changeStatus($id,$status);
            return $this->redirect($this->generateUrl('service'));
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }
       

    }

    /**
     * @Route("/login/", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {   
        if($request->request->get('username') && $request->request->get('password'))
        {
            try {
                $username=$request->request->get('username');
                $password=$request->request->get('password');
                $DB=$this->get('connection');
                $result=$DB->getUser(array($username,$password));
                if($result>1){
                    $session = new Session();
                    $session->start();

                    // set and get session attributes
                    $session->set('username', $username);
                    return $this->redirect($this->generateUrl('dashboard'));

                }else{
                    return array('error_msg' => 'Incorrect Username or Password','username'=>$username,'password'=>$password);
                }

            } catch (\Exception $e) {
                $e->getMessage();
            }
           
        }
    }

    /**
     * @Route("/logout/", name="logout")
     * 
     */
    public function logout()
    {   
        $this->get('session')->clear();
        return $this->redirect($this->generateUrl('login'));

    }


   
}
