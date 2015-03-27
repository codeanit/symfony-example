<?php

namespace BackendBundle\Controller;

use ApiBundle\Model\DB as connection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/demo/secured")
 */
class SecuredController extends Controller
{

    private $_connection;
    
    public function __construct() { 

    }
    
    /**
     * @Route("/login", name="_demo_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login_check", name="_demo_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_demo_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/hello", defaults={"name"="World"}),
     * @Route("/hello/{name}", name="_demo_secured_hello")
     * @Template()
     */
    public function helloAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/hello/admin/{name}", name="_demo_secured_hello_admin")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template()
     */
    public function helloadminAction($name)
    {
        return array('name' => $name);
    }

    /**
     * List Transactions
     * 
     * @Route("/transactions", name="_demo_secured_transactions")
     * @Template()
     */
    public function listTransactionsAction()
    { 
        $DB = $this->get('connection');

        $transactions = $DB->getTransactions();      

        return array('transactions'=>$transactions);  
    }

    /**
     * @Route("/services/", name="_demo_secured_services")
     * @Template()
     */
    public function listServicesAction()
    { 
        $DB=$this->get('connection');
        $services=$DB->getServices();

        return array('services' => $services);
    }

     /**
     * @Route("/service/add/{name}/{id}", name="_demo_secured_service_add")
     * @Template()
     */
    public function addServiceFieldsAction(Request $request,$name=null,$id=null)
    {   
        $DB = $this->get('connection');

        if($request->request->get('service_name'))
        {             
            $service_name=$request->request->get('service_name');
            $ftp=($request->request->get('ftp_service'))?$request->request->get('ftp_service'):'0';
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
                $status=$DB->editService($service_name,$fields,$service_id,$ftp);
            }else{
                $status=$DB->saveService($service_name,$fields,$ftp);
            }

            return $this->redirect($this->generateUrl('_demo_secured_services'));

        }else{
            $field=$DB->getFields($id);
            return array('service_name'=>$name,'fields'=>$field[0],'service_id'=>$field[1],'is_ftp'=>$field[2]);
        }
    }

    /**
     * @Route("/service/{id}/credentials", name="_demo_secured_credentials")
     * @Template()
     */
    public function viewCredentialsAction($id)
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
     * @Route("/credential/add", name="_demo_secured_add_credential")
     * @Template()
     */
    public function addCredentialAction(Request $request)
    {
        if($request->request->get('service_name')) {            
            $fields=$request->request->get('fields');
            $service_name=$request->request->get('service_name');
            $DB = $this->get('connection');
            $status=$DB->saveCredentials($service_name,$fields);

            return $this->redirect($this->generateUrl('_demo_secured_services'));
        }
    }

    /**
     * @Route("/service/{id}/status/{status}", name="_demo_secured_service_status")
     * 
     */
    public function statusAction($id, $status)
    {   
            $DB = $this->get('connection');
            $DB->changeStatus($id,$status);

            return $this->redirect($this->generateUrl('_demo_secured_services'));
    }

    /**
     * @Route("/logs/{name}/{id}", name="_demo_secured_service_logs")
     * @Template()
     */
    public function serviceLogsAction($name,$id)
    {
            $DB=$this->get('connection');
            $data=$DB->getLogList($name,$id);

            return array('logs'=>$data,'service_name'=>$name);
            
    }
}

