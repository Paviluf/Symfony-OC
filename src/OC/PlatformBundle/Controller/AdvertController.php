<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if($page < 1) {
            throw new NotFoundHttpException('Page '.$page.' inexistante');    
        }

        return $this->render('@OCPlatform/Advert/index.html.twig');
    }

    public function viewAction($id)
    {
        return $this->render('@OCPlatform/Advert/view.html.twig', array('id' => $id));
    }

    public function viewSlugAction($year, $slug, $_format)
    {
        return $this->render('@OCPlatform/Advert/view.html.twig');
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
