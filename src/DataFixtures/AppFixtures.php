<?php

namespace App\DataFixtures;

use App\Entity\AgendaSlot;
use App\Entity\User;
use App\Factory\AgendaFactory;
use App\Factory\AgendaSlotFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->promoteAdministrator()->create([
            'email' => 'admin@example.com',
            'fullName' => 'Hugo Hamon',
        ]);

        UserFactory::createOne([
            'email' => 'freemium_user@example.com',
            'roles' => [User::ROLE_FREEMIUM_USER],
        ]);

        UserFactory::createOne([
            'email' => 'premium_user@example.com',
            'roles' => [User::ROLE_PREMIUM_USER],
        ]);

        UserFactory::createMany(237);

        AgendaFactory::createOne([
            'isEnabled' => true,
            'name' => 'Vacations Planning',
        ]);

        $agenda2 = AgendaFactory::createOne([
            'isEnabled' => true,
            'name' => 'Work & Meetings',
        ]);

        AgendaSlotFactory::createOne([
            'agenda' => $agenda2,
            'opensAt' => (new \DateTimeImmutable('2023-02-22 10:00:00', new \DateTimeZone('America/Montreal')))
                ->setTimezone(new \DateTimeZone('UTC')),
            'closesAt' => (new \DateTimeImmutable('2023-02-22 10:30:00', new \DateTimeZone('America/Montreal')))
                ->setTimezone(new \DateTimeZone('UTC')),
            'status' => AgendaSlot::STATUS_OPEN,
        ]);

        AgendaSlotFactory::createOne([
            'agenda' => $agenda2,
            'opensAt' => (new \DateTimeImmutable('2023-02-22 10:30:00', new \DateTimeZone('America/Montreal')))
                ->setTimezone(new \DateTimeZone('UTC')),
            'closesAt' => (new \DateTimeImmutable('2023-02-22 11:00:00', new \DateTimeZone('America/Montreal')))
                ->setTimezone(new \DateTimeZone('UTC')),
            'status' => AgendaSlot::STATUS_BOOKED,
        ]);

        AgendaSlotFactory::createOne([
            'agenda' => $agenda2,
            'opensAt' => (new \DateTimeImmutable('2023-02-22 11:30:00', new \DateTimeZone('America/Montreal')))
                ->setTimezone(new \DateTimeZone('UTC')),
            'closesAt' => (new \DateTimeImmutable('2023-02-22 12:00:00', new \DateTimeZone('America/Montreal')))
                ->setTimezone(new \DateTimeZone('UTC')),
            'status' => AgendaSlot::STATUS_OPEN,
        ]);

        AgendaSlotFactory::createOne([
            'agenda' => $agenda2,
            'opensAt' => (new \DateTimeImmutable('2023-02-22 14:00:00', new \DateTimeZone('America/Montreal')))
                ->setTimezone(new \DateTimeZone('UTC')),
            'closesAt' => (new \DateTimeImmutable('2023-02-22 14:30:00', new \DateTimeZone('America/Montreal')))
                ->setTimezone(new \DateTimeZone('UTC')),
            'status' => AgendaSlot::STATUS_CLOSED,
        ]);

        AgendaSlotFactory::createMany(120, [
            'agenda' => $agenda2,
        ]);

        AgendaFactory::createOne([
            'isEnabled' => false,
            'name' => 'Disabled Agenda',
        ]);

        AgendaFactory::createMany(166);
    }
}
