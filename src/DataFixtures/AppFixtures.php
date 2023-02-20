<?php

namespace App\DataFixtures;

use App\Factory\AgendaFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        AgendaFactory::createOne([
            'isEnabled' => true,
            'name' => 'Vacations Planning',
        ]);

        AgendaFactory::createOne([
            'isEnabled' => true,
            'name' => 'Work & Meetings',
        ]);

        AgendaFactory::createOne([
            'isEnabled' => false,
            'name' => 'Disabled Agenda',
        ]);

        AgendaFactory::createMany(166);
    }
}
