<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em
            ->getRepository('OCPlatformBundle:Advert')
            ->getAdverts(1, 3)
        ;

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'listAdverts' => $listAdverts,
                'page' => 1,
                'nbPages' => 1
            ]
        );

        return $response;
    }

    public function contactAction(Request $request)
    {
        $request->getSession()->getFlashBag()->add(
            'notice',
            "La page de contact n'est pas encore disponible, merci de revenir plus tard"
        );

        return $this->redirectToRoute('oc_core_home');
    }
}
