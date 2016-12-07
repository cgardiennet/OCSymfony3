<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $listAdverts = $this
            ->get('oc_platform.adverts')
            ->getAdvertCollection()
            ->getCollectionSlice(-3, 3)
            ->sortCollection('desc')
            ->displayCollection()
        ;

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'listAdverts' => $listAdverts
            ]
        );

        return $response;
    }

    public function contactAction(Request $request)
    {
        $request->getSession()->getFlashBag()->add(
            'info',
            "La page de contact n'est pas encore disponible, merci de revenir plus tard"
        );

        return $this->redirectToRoute('oc_core_home');
    }
}
