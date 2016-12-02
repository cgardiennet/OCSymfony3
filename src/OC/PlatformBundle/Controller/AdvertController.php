<?php

namespace OC\PlatformBundle\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdvertController extends Controller
{

    public function menuAction(Request $request)
    {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
        $listAdverts = $this->getListAdverts();

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }

    public function indexAction($page, Request $request)
    {
        echo "<pre>";
        print_r($this->get('router.request_context'));
        echo "</pre>";
        die;

        if ($page < 1) {
            throw new NotFoundHttpException(sprintf(
                'Page #%s inexistante',
                $page
            ));
        }

        $listAdverts = $this->getListAdverts();


        $response = $this->render(
            $this->getTemplateFileName($request),
            [
                'listAdverts' => $listAdverts
            ]
        );

        return $response;
    }

    public function viewAction($id, Request $request)
    {


        $advert = $this->getDefaultAdvert($id);

        $response = $this->render(
            $this->getTemplateFileName($request),
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
            $this->getTemplateFileName($request),
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
            $this->getTemplateFileName($request)
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

        $advert = $this->getDefaultAdvert($id);

        $response = $this->render(
            $this->getTemplateFileName($request),
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
                'Annonce bien modifiée.'
            );
            return $this->redirectToRoute('oc_platform_view', array('id' => $id));
        }

        $response = $this->render(
            $this->getTemplateFileName($request)
        );

        return $response;
    }

    protected function getTemplateFileName(Request $request)
    {
        preg_match(
            '/^(.*)\\\(.*)\\\Controller\\\(.*)Controller::(.*)Action$/',
            $request->attributes->get('_controller'),
            $matches
        );

        $bundleName = $matches[1] . $matches[2];
        $controllerName = $matches[3];
        $actionName = $matches[4];

        $defaultExtension = 'html.twig';

        $templateFileName = sprintf(
            '%s:%s:%s.%s',
            $bundleName,
            $controllerName,
            $actionName,
            $defaultExtension
        );

        return $templateFileName;
    }

    protected function getDefaultAdvert($id)
    {
        $advert = array(
            'title'   => 'Recherche développpeur Symfony2',
            'id'      => $id,
            'author'  => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
            'date'    => new DateTime()
        );

        return $advert;
    }

    protected function getListAdverts()
    {
        // Notre liste d'annonce en dur
        $listAdverts = array(
            array(
                'title'   => 'Recherche développpeur Symfony',
                'id'      => 1,
                'author'  => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
                'date'    => new DateTime()
            ),
            array(
                'title'   => 'Mission de webmaster',
                'id'      => 2,
                'author'  => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'    => new DateTime()
            ),
            array(
                'title'   => 'Offre de stage webdesigner',
                'id'      => 3,
                'author'  => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'    => new DateTime()
            )
        );

        return $listAdverts;
    }
}