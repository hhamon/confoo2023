<?php

namespace App\DataFixtures;

use App\Entity\Agenda;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $agenda1 = new Agenda('Vacations Planning');
        $agenda1->setIsEnabled(true);

        $agenda2 = new Agenda('Work & Meetings');
        $agenda2->setIsEnabled(true);

        $agenda3 = new Agenda('Disabled Agenda');
        $agenda3->setIsEnabled(false);

        $manager->persist($agenda1);
        $manager->persist($agenda2);
        $manager->persist($agenda3);

        $manager->flush();
    }
}
