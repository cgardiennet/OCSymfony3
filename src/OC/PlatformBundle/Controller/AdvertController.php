<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdvertController extends Controller
{

    public function indexAction($page, Request $request)
    {
        if ($page < 1) {
            throw new NotFoundHttpException(sprintf(
                'Page #%s inexistante',
                $page
            ));
        }

        $listAdverts = $this
            ->get('oc_platform.adverts')
            ->getAdvertCollection()
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

    public function viewAction($id, Request $request)
    {
        $advert = $this->get('oc_platform.adverts')->getDefaultAdvert($id);

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'advert' => $advert,
            ]
        );

        return $response;
    }

    public function viewSlugAction($slug, $year, $_format, Request $request)
    {
        $id = 5;
        $tag = $request->query->get('tag');

        // Récupération de la session
        $session = $request->getSession();
//         $userId = $session->get('user_id');
        $session->set('user_id', 91);

        $url = $this->generateUrl(
            'oc_platform_view',
            array('id' => $id)
        );

        $url2 = $this->generateUrl(
            'oc_platform_view',
            array('id' => $id),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'id' => $id,
                'url' => $url,
                'url2' => $url2,
                'tag' => $tag,
                'isPost' => $request->isMethod('GET'),
                'slug' => $slug,
                'year' => $year,
                'format' => $_format,
//                 'sessionUserId' => $userId
            ]
        );

        return $response;
    }

    public function addAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Annonce bien enregistrée.'
            );
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName()
        );

        return $response;
    }

    public function editAction($id, Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Annonce bien modifiée.'
            );
            return $this->redirectToRoute('oc_platform_view', array('id' => $id));
        }

        $advert = $this->get('oc_platform.adverts')->getDefaultAdvert($id);

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'advert' => $advert
            ]
        );

        return $response;
    }

    public function deleteAction($id, Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Annonce bien supprimée.'
            );
            return $this->redirectToRoute('oc_platform_home');
        }

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName()
        );

        return $response;
    }

}