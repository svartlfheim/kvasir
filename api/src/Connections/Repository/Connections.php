<?php

namespace App\Connections\Repository;

use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Connections extends ServiceEntityRepository implements ConnectionsInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Connection::class);
    }

    public function save(Connection $conn): Connection
    {
        $em = $this->getEntityManager();

        $em->persist($conn);
        $em->flush($conn);

        return $conn;
    }

    public function all(): ConnectionList
    {
        $qb = $this->createQueryBuilder('c');
        $q = $qb->select('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery();

        return ConnectionList::fromArray($q->getResult());
    }
}
