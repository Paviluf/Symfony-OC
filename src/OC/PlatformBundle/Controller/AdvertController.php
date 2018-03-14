<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
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

    public function addAction()
    {
        return $this->render('@OCPlatform/Advert/edit.html.twig');
    }

    public function editAction($id)
    {
        return $this->render('@OCPlatform/Advert/edit.html.twig');
    }

    public function deleteAction($id)
    {
        return $this->render('@OCPlatform/Advert/edit.html.twig');
    }
}
