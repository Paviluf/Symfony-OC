<?php

namespace OC\PlatformBundle\Service;

use Doctrine\ORM\EntityManager;

class PurgerAdvert
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;    
    }

    public function purge($days)
    {
        $advertRepo = $this->em->getRepository('OCPlatformBundle:Advert');
        $advertsToRemove = $advertRepo->getAdvertsWithoutApplication($days);

        foreach ($advertsToRemove as $advert) {
            $this->em->remove($advert);
        }

        $this->em->flush();
    }
}