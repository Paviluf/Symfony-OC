<?php
// src/OC/PlatformBundle/DataFixtures/ORM/LoadAdvert.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;

class LoadAdvert implements FixtureInterface, DependentFixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime('now');
        $date->sub(new \DateInterval('P5D'));

        for ($i = 1; $i < 7; $i++) {
            $advert = new Advert();
            $advert->setTitle($i . ' Recherche développeur Symfony.');
            $advert->setAuthor('Alexandre');
            $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

            $listSkills = $manager->getRepository('OCPlatformBundle:Skill')->findAll();

            foreach ($listSkills as $skill) {
                $advertSkill = new AdvertSkill();

                $advertSkill->setAdvert($advert);
                $advertSkill->setSkill($skill);
                $advertSkill->setLevel('Expert');
                $manager->persist($advertSkill);
            }

            $image = new Image();
            $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
            $image->setAlt('Job de rêve');

            $advert->setImage($image);

            if ($i <= 3) {
                $application1 = new Application();
                $application1->setAuthor('Marine');
                $application1->setContent("J'ai toutes les qualités requises");

                $application2 = new Application();
                $application2->setAuthor('Pierre');
                $application2->setContent("Je suis très motivé");

                $application1->setAdvert($advert);
                $application2->setAdvert($advert);

                $manager->persist($application1);
                $manager->persist($application2);
            } else {
                $advert->setDate($date);
            }

            $manager->persist($advert);
        }
    
        // On déclenche l'enregistrement
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadSkill::class,
        );
    }
}
