<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\OCPlatformBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
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

        $nbPerPage = $this->container->getParameter('nb_per_page');

        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em
            ->getRepository('OCPlatformBundle:Advert')
            ->getAdverts($page, $nbPerPage)
        ;

        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'listAdverts' => $listAdverts,
                'page' => $page,
                'nbPages' => $nbPages
            ]
        );

        return $response;
    }

    public function viewAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advertRepository = $em->getRepository('OCPlatformBundle:Advert');

        $advert = $advertRepository
            ->loadApplicationsFromAdvert()
            ->loadCategoriesFromAdvert()
            ->findIdFromQueryBuilder($id)
        ;

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $advert) {
            throw new NotFoundHttpException(sprintf(
                "L'annonce d'id %s n'existe pas.",
                $id
            ));
        }

        $advertSkillRepository =$em->getRepository('OCPlatformBundle:AdvertSkill');

        $listAdvertSkills = $advertSkillRepository
            ->loadAdvertSkillWithSkill()
            ->findByFromQueryBuilder(
                array(
                    sprintf('%s.advert = :advert', $advertSkillRepository->getDefaultAlias()) => 'AND'
                ),
                array('advert' => $advert)
            )
        ;

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'advert' => $advert,
                'listAdvertSkills' => $listAdvertSkills
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
        $userId = $session->get('user_id');

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
                'sessionUserId' => $userId
            ]
        );

        return $response;
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = new Advert();

        $form = $this
            ->createForm(AdvertType::class, $advert)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'Annonce bien enregistrée.'
            );
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'advert' => $advert,
                'form' => $form->createView()
            ]
        );

        return $response;
    }

    public function editAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $advert = $em
            ->getRepository('OCPlatformBundle:Advert')
            ->find($id)
        ;

        $form = $this
            ->createForm(AdvertEditType::class, $advert)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'Annonce bien modifiée.'
            );
            return $this->redirectToRoute('oc_platform_view', array('id' => $id));
        }

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $advert) {
            throw new NotFoundHttpException(sprintf(
                "L'annonce d'id %s n'existe pas.",
                $id
            ));
        }

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            [
                'advert' => $advert,
                'form' => $form->createView()
            ]
        );

        return $response;
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em
            ->getRepository('OCPlatformBundle:Advert')
            ->find($id)
        ;

        if (null === $advert) {
            throw new NotFoundHttpException(sprintf(
                "L'annonce d'id %s n'existe pas.",
                $id
            ));
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this
            ->get('form.factory')
            ->create()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'Annonce bien supprimée.'
            );
            return $this->redirectToRoute('oc_platform_home');
        }

        $response = $this->render(
            $this->get('to_basics.autotemplate')->getTemplateFileName(),
            array(
              'advert' => $advert,
              'form'   => $form->createView()
            )
        );

        return $response;
    }

    public function testAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $adverts = $em
            ->getRepository('OCPlatformBundle:Advert')
            ->findAll()
        ;

        foreach ($adverts as $advert) {
            $advert->setTitle($advert->getTitle() . '!');
            $em->persist($advert);
        }
        $em->flush();

        $request->getSession()->getFlashBag()->add(
            'notice',
            'Tentative slugs.'
        );

        return $this->redirectToRoute('oc_platform_home');
    }

    public function purgeAction($days, Request $request)
    {
        $purgeService = $this->get('oc_platform.purger.advert');
        $result = $purgeService->purge($days);

        $request->getSession()->getFlashBag()->add(
            'notice',
            sprintf(
                '%s annonces de plus de %s jours sans candidatures supprimées',
                $result,
                $days
            )
        );

        return $this->redirectToRoute('oc_platform_home');
    }

}