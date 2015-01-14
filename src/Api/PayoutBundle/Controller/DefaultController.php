<?php

namespace Api\PayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}", name="default_index")
     */
    public function indexAction($name)
    {
        // return Response('ok');
        return array('adfd' => 'adfadf');
    }
}
