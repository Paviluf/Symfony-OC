<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;

class AdvertController extends Controller
{
    public function indexAction(Request $request, $page)
    {
        $nbPerPage = 1;
        $em = $this->getDoctrine()->getManager();
        $advertRepo = $em->getRepository("OCPlatformBundle:Advert");
        
        if($page < 1) {
            throw new NotFoundHttpException('Page '.$page.' inexistante');    
        }
        
        $listAdverts = $advertRepo->getAdverts($page, $nbPerPage);
        $nbPages = ceil(count($listAdverts) / $nbPerPage);
        if($page > $nbPages) {
            throw new NotFoundHttpException('Page '.$page.' inexistante');    
        }

        // Workaround this limitation : https://stackoverflow.com/questions/46267523/symfony-3-parameters-default-value-not-returned
        $routeName = $request->get('_route');
        if($routeName == 'oc_platform_home_page' && $page == 1) {
            return $this->redirectToRoute('oc_platform_home');
        }

        return $this->render('@OCPlatform/Advert/index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages' => $nbPages,
            'page' => $page
        ));
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $advertRepo = $em->getRepository("OCPlatformBundle:Advert");

        $advert = $advertRepo->find($id);

        if(!$advert) {
            throw new NotFoundHttpException('L\'annonce '.$id.' n\'existe pas');   
        }
        
        $applicationRepo = $em->getRepository("OCPlatformBundle:Application");

        $listApplications = $applicationRepo->findBy(array('advert' => $advert));

        $listAdvertSkills = $em
            ->getRepository('OCPlatformBundle:AdvertSkill')
            ->findBy(array('advert' => $advert))
        ;

        return $this->render('@OCPlatform/Advert/view.html.twig', 
            array('advert' => $advert,
                  'listApplications' => $listApplications,
                  'listAdvertSkills' => $listAdvertSkills
                ));
    }

    public function addAction(Request $request)
    { 
        $advert = new Advert();
        
        $form = $this->createForm(AdvertType::class, $advert);
        
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {          
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();
            
            $this->addFlash('notice', 'Annonce bien enregistrée');

            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        return $this->render('@OCPlatform/Advert/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce ".$id." n'existe pas.");
        }

        $form = $this->createForm(AdvertEditType::class, $advert);

        
        if($request->isMethod('POST')) {
            $em->flush();
            
            $this->addFlash('notice', 'annonce bien enregistrée');

            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }
        
        return $this->render('@OCPlatform/Advert/edit.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView()
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce ".$id." n'existe pas.");
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($advert);
            $em->flush();

            $this->addFlash('notice', "L'annonce a bien été supprimée");

            return $this->redirectToRoute('oc_core_home');
        }

        return $this->render('@OCPlatform/Advert/delete.html.twig', array(
            'advert'=>$advert,
            'form' => $form->createView()
        ));
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $advertRepo = $em->getRepository("OCPlatformBundle:Advert");
        
        $listAdverts = $advertRepo->findBy(
            array(),
            array('date' => 'desc'),
            $limit,
            0
        );

        return $this->render('@OCPlatform/Advert/menu.html.twig', array('listAdverts' => $listAdverts)); 
    }

    public function purgeAction(Request $request, $days)
    {
        if ($days < 1) {
            throw new NotFoundHttpException('Le nombre de jours pour la purge des annonces ne doit pas être inférieur à 1');
        }
  
        $purgerAdvert = $this->get('oc_platform.purger.advert');
        $purgerAdvert->purge($days);
    
        $this->addFlash('info', 'Annonces purgées');
  
        return $this->redirectToRoute('oc_core_home');
    }
}
