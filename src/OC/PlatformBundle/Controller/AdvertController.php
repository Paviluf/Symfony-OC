<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function indexAction(Request $request, $page)
    {
        if($page < 1) {
            throw new NotFoundHttpException('Page '.$page.' inexistante');    
        }

        // Workaround this limitation : https://stackoverflow.com/questions/46267523/symfony-3-parameters-default-value-not-returned
        $routeName = $request->get('_route');
        if($routeName == 'oc_platform_home_page' && $page == 1) {
            return $this->redirectToRoute('oc_platform_home');
        }

        return $this->render('@OCPlatform/Advert/index.html.twig', array('test' => $page));
    }

    public function viewAction($id)
    {
        return $this->render('@OCPlatform/Advert/view.html.twig', array('id' => $id));
    }

    public function addAction(Request $request)
    {
        if($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('info', 'annonce bien enregistrée');
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }

        return $this->render('@OCPlatform/Advert/edit.html.twig');
    }

    public function editAction($id)
    {
        if($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('info', 'annonce bien enregistrée');
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }
        
        return $this->render('@OCPlatform/Advert/edit.html.twig');
    }

    public function deleteAction($id)
    {
        return $this->render('@OCPlatform/Advert/edit.html.twig');
    }
}
