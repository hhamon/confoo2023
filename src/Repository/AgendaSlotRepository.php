<?php

namespace App\Repository;

use App\Entity\Agenda;
use App\Entity\AgendaSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AgendaSlot>
 *
 * @method AgendaSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgendaSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgendaSlot[]    findAll()
 * @method AgendaSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgendaSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgendaSlot::class);
    }

    public function save(AgendaSlot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AgendaSlot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return AgendaSlot[]
     */
    public function findUpcomingSlots(Agenda $agenda): array
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $qb = $this->createQueryBuilder('slot');

        $qb->andWhere($qb->expr()->eq('slot.agenda', ':agenda'))
            ->andWhere($qb->expr()->gt('slot.opensAt', ':now'))
            ->orderBy('slot.opensAt', 'ASC')
            ->setParameter('agenda', $agenda)
            ->setParameter('now', $now);

        /** @var AgendaSlot[] */
        return $qb->getQuery()->getResult();
    }
}
