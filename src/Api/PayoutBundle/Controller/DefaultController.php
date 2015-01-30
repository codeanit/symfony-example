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
     * @Route("/credential/{id}", name="credential")
     * @Template()
     */
    public function credentialAction($id)
    {       
        $DB=$this->get('connection');
        $services=$DB->getServiceCredentials($id);        
        return array('name'=>$id,'services'=>json_decode(base64_decode($services[2])));     
    }

     /**
     * @Route("/save/", name="save")
     *
     */
    public function saveAction(Request $request)
    {  
        $username=$request->request->get('username');
        $password=$request->request->get('password');
        $domain=$request->request->get('domain');
        $credential=json_encode(array('username'=>$username,'password'=>$password,'domain'=>$domain)); 
        $service_name=$request->request->get('service_name');
        $DB = $this->get('connection');
        $status=$DB->saveCredentials($service_name,$credential);
        return $this->redirect($this->generateUrl('service'));
    }


}
