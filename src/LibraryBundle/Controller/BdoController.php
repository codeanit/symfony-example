<?php

namespace LibraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


class BdoController extends Controller
{
    /**
     * @Route("/abdo/", name="abdo")
     * @Template()
     */
    public function myBdoAction(Request $request)
    {
        // if ($request->request->has('username') && $request->request->has('password')) {
        //     try {
        //         $username = $request->request->get('username');
        //         $password = $request->request->get('password');
        //         $DB = $this->get('connection');
        //         $result = $DB->getUser(array($username, $password));
        //         if ($result>1) {
        //             $session = new Session();
        //             $session->start();

        //             // set and get session attributes
        //             $session->set('username', $username);

        //             return $this->redirect($this->generateUrl('dashboard'));
        //         } else {
        //             return array('error_msg' => 'Incorrect Username or Password','username' => $username,'password' => $password);
        //         }
        //     } catch (\Exception $e) {
        //         $e->getMessage();
        //     }
        // }

        echo 2222;
        die;
    }
}
