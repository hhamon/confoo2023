<?php

namespace App\Factory;

use App\Entity\AgendaSlot;
use App\Repository\AgendaSlotRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<AgendaSlot>
 *
 * @method        AgendaSlot|Proxy create(array|callable $attributes = [])
 * @method static AgendaSlot|Proxy createOne(array $attributes = [])
 * @method static AgendaSlot|Proxy find(object|array|mixed $criteria)
 * @method static AgendaSlot|Proxy findOrCreate(array $attributes)
 * @method static AgendaSlot|Proxy first(string $sortedField = 'id')
 * @method static AgendaSlot|Proxy last(string $sortedField = 'id')
 * @method static AgendaSlot|Proxy random(array $attributes = [])
 * @method static AgendaSlot|Proxy randomOrCreate(array $attributes = [])
 * @method static AgendaSlotRepository|RepositoryProxy repository()
 * @method static AgendaSlot[]|Proxy[] all()
 * @method static AgendaSlot[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static AgendaSlot[]|Proxy[] createSequence(array|callable $sequence)
 * @method static AgendaSlot[]|Proxy[] findBy(array $attributes)
 * @method static AgendaSlot[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static AgendaSlot[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class AgendaSlotFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        $allowedOpensAt = [
            '08:00',
            '08:30',
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '12:00',
            '12:30',
            '13:00',
            '13:30',
            '14:00',
            '14:30',
            '15:00',
            '15:30',
            '16:00',
            '16:30',
        ];

        $opensAtDate = self::faker()->date();
        $opensAtTime = self::faker()->randomElement($allowedOpensAt);

        $opensAt = new \DateTimeImmutable($opensAtDate . ' ' . $opensAtTime, new \DateTimeZone('America/Montreal'));
        $closesAt = $opensAt->modify('+30 minutes');

        $opensAtUtc = $opensAt->setTimezone(new \DateTimeZone('UTC'));
        $closesAt = $closesAt->setTimezone(new \DateTimeZone('UTC'));

        return [
            'agenda' => AgendaFactory::random(),
            'opensAt' => $opensAtUtc,
            'closesAt' => $closesAt,
            'status' => self::faker()->randomElement([
                AgendaSlot::STATUS_OPEN,
                AgendaSlot::STATUS_CLOSED,
                AgendaSlot::STATUS_BOOKED,
            ]),
        ];
    }

    protected static function getClass(): string
    {
        return AgendaSlot::class;
    }
}
