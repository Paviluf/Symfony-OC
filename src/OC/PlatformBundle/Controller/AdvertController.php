<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;

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
        $em = $this->getDoctrine()->getManager();

        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

        $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();

        foreach($listSkills as $skill) {
            $advertSkill = new AdvertSkill();

            $advertSkill->setAdvert($advert);
            $advertSkill->setSkill($skill);
            $advertSkill->setLevel('Expert');
            $em->persist($advertSkill);
        }

        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        $advert->setImage($image);

        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("J'ai toutes les qualités requises");
        
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent("Je suis très motivé");

        $application1->setAdvert($advert);
        $application2->setAdvert($advert);

        $em->persist($advert);
        $em->persist($application1);
        $em->persist($application2);
        $em->flush();

        if($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('info', 'annonce bien enregistrée');
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        return $this->render('@OCPlatform/Advert/add.html.twig', array('advert' => $advert));
    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce ".$id." n'existe pas.");
        }

        $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        $em->flush();

        if($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('info', 'annonce bien enregistrée');
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }
        
        return $this->render('@OCPlatform/Advert/edit.html.twig', array('advert'=>$advert));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce ".$id." n'existe pas.");
        }

        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        $em->flush();

        return $this->render('@OCPlatform/Advert/edit.html.twig', array('advert'=>$advert));
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
}
