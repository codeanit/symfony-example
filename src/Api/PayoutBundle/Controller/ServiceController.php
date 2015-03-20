<?php

namespace Api\PayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;



class ServiceController extends Controller
{
    /**
     * @Route("/fService/", name="fService")
     * @Template()
     */
    public function fServiceAction()
    {         
        if($this->get('session')->get('username')){
            $DB=$this->get('connection');
            $services=$DB->getfServices();
            return array('services'=>$services);
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }
    }

    /**
     * @Route("/xaddfService/", name="xaddfService")
     * @Template()
     */
    public function addfServiceAction(Request $request)
    {         
        if($this->get('session')->get('username')){
            print_r($request->request->all());
        }else{
            return $this->redirect($this->generateUrl('login'));            
        }
    }
   
}
