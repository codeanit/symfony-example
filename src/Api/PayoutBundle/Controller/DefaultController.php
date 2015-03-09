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
        print_r($Q->executeQueuedOperation());        
        die;      
    }

    /**
     * @Route("/fqueue/", name="fqueue")
     * 
     */
    public function fileAction()
    {         
        $Q=$this->get('queue'); 
        print_r($Q->executeFileOperation());       
        die; 
    }

    /**
     * @Route("/xls/", name="xls")
     * 
     */
    public function xlsAction()
    {   

        $path= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/upload/green.xlsx';      
        $inputFileType = \PHPExcel_IOFactory::identify($path);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($path);  
        $sheet = $objPHPExcel->getSheet(0); 
        var_dump($sheet->ToArray());
       echo "flaskdf";
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
                return $this->redirect($this->generateUrl('service'));

            }else{
                $field=$DB->getFields($id);
                return array('service_name'=>$name,'fields'=>$field[0],'service_id'=>$field[1],'is_ftp'=>$field[2]);
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
        if($request->request->has('username') && $request->request->has('password'))
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
    
    /**
     * @Route("/upload/{name}/{id}", name="upload")
     * @Template()
     */
    public function uploadAction($name,$id)
    {
        if($this->get('session')->get('username')){
            $DB=$this->get('connection');
            return array('service_name'=>$name,'service_id'=>$id);     
            
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }
             
    }

    /**
     * @Route("/uploadFile/", name="uploadFile")
     * 
     */
    public function uploadFileAction(Request $request)
    {
            $path=$this->get('request')->server->get('DOCUMENT_ROOT').'/upload/';
            $DB=$this->get('connection');
            if($request->files){
                foreach ($request->files as $uploadedFile) {                
                   $file = $uploadedFile->move($path,$uploadedFile->getClientOriginalName());                                 
                   if(file_exists($path.$uploadedFile->getClientOriginalName()))
                   {
                    $DB->saveUploadData($uploadedFile->getClientOriginalName(),
                                        $request->request->get('service_id'),
                                        $request->request->get('service_name')
                                       );
                    return $this->redirect($this->generateUrl('service')); 
                   }
                }
            }
            
    }

    /**
     * @Route("/hack/", name="hack")
     * @Template()
     */
    public function hackAction()
    {
        return array();
    }

   
}
