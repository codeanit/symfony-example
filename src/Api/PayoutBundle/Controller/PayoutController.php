<?php

namespace Api\PayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations\View;
use Acme\BlogBundle\Entity\Page;
use  Api\PayoutBundle\Model\TBConnectionModel as TBConnection;


class PayoutController extends Controller
{

    private $TBConnection;

    public function __construct() 
    {
        $this->TBConnection = new TBConnection(); 
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
    
    public function getTransactionsAction()
    { 
         $request = $this->getRequest();
        // $request->query->get()

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
    public function postTransactionAction()
    {       
         
        $post = $_POST;
        return $post;    
    }

}
