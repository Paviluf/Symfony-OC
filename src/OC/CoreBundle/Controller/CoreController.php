<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function homeAction()
    {
        return $this->render("@OCCore/Core/home.html.twig");
    }

    public function contactAction()
    {
        $this->get('session')->getFlashBag()->add('info', 'La page de contact n’est pas encore disponible”');

        return $this->redirectToRoute('oc_core_home');
    }
}