<?php

namespace TO\BasicsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TOBasicsBundle:Default:index.html.twig');
    }
}
