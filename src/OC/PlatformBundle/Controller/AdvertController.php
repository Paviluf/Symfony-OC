<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;

class AdvertController extends Controller
{
    public function indexAction(Request $request, $page)
    {
        $listAdverts = array(
            array(
              'title'   => 'Recherche développpeur Symfony',
              'id'      => 1,
              'author'  => 'Alexandre',
              'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
              'date'    => new \Datetime()),
            array(
              'title'   => 'Mission de webmaster',
              'id'      => 2,
              'author'  => 'Hugo',
              'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
              'date'    => new \Datetime()),
            array(
              'title'   => 'Offre de stage webdesigner',
              'id'      => 3,
              'author'  => 'Mathieu',
              'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
              'date'    => new \Datetime())
        );

        if($page < 1) {
            throw new NotFoundHttpException('Page '.$page.' inexistante');    
        }

        // Workaround this limitation : https://stackoverflow.com/questions/46267523/symfony-3-parameters-default-value-not-returned
        $routeName = $request->get('_route');
        if($routeName == 'oc_platform_home_page' && $page == 1) {
            return $this->redirectToRoute('oc_platform_home');
        }

        return $this->render('@OCPlatform/Advert/index.html.twig', array('listAdverts' => $listAdverts));
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $advertRepo = $em->getRepository("OCPlatformBundle:Advert");

        $advert = $advertRepo->find($id);

        if(!$advert) {
            throw new NotFoundHttException('L\'annonce '.$id.' n\'existe pas');   
        }

        return $this->render('@OCPlatform/Advert/view.html.twig', array('advert' => $advert));
    }

    public function addAction(Request $request)
    {
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        $advert->setImage($image);

        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->flush();

        if($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('info', 'annonce bien enregistrée');
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        return $this->render('@OCPlatform/Advert/add.html.twig', array('advert' => $advert));
    }

    public function editAction(Request $request, $id)
    {
        if($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('info', 'annonce bien enregistrée');
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }
        
        return $this->render('@OCPlatform/Advert/edit.html.twig', array('advert'=>$advert));
    }

    public function deleteAction($id)
    {
        return $this->render('@OCPlatform/Advert/edit.html.twig', array('advert'=>$advert));
    }

    public function menuAction($limit)
    {
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner')
          );

        return $this->render('@OCPlatform/Advert/menu.html.twig', array('listAdverts' => $listAdverts)); 
    }
}
