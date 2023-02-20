<?php

namespace App\Factory;

use App\Entity\Agenda;
use App\Repository\AgendaRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Agenda>
 *
 * @method        Agenda|Proxy create(array|callable $attributes = [])
 * @method static Agenda|Proxy createOne(array $attributes = [])
 * @method static Agenda|Proxy find(object|array|mixed $criteria)
 * @method static Agenda|Proxy findOrCreate(array $attributes)
 * @method static Agenda|Proxy first(string $sortedField = 'id')
 * @method static Agenda|Proxy last(string $sortedField = 'id')
 * @method static Agenda|Proxy random(array $attributes = [])
 * @method static Agenda|Proxy randomOrCreate(array $attributes = [])
 * @method static AgendaRepository|RepositoryProxy repository()
 * @method static Agenda[]|Proxy[] all()
 * @method static Agenda[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Agenda[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Agenda[]|Proxy[] findBy(array $attributes)
 * @method static Agenda[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Agenda[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class AgendaFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'id' => self::faker()->unique()->uuid(),
            'isEnabled' => self::faker()->boolean(72),
            'name' => self::faker()->unique()->text(30),
        ];
    }

    protected static function getClass(): string
    {
        return Agenda::class;
    }
}
