<?php

namespace App\Connections\Repository;

use App\Common\Database\ColumnSortOrder;
use App\Common\Database\ListOptions;
use App\Common\Database\Pagination;
use App\Common\Database\SortOrders;
use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

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

    public function byId(UuidInterface $id): ?Connection
    {
        return $this->find((string) $id);
    }

    protected function getCount(): int
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(c.id)');
        $q = $qb->getQuery();

        return $q->getSingleScalarResult();
    }

    protected function getPage(SortOrders $sort, Pagination $pagination): ConnectionList
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c');

        if (count($sort) == 0) {
            // Natural sorting for alphanumeric
            $sort = SortOrders::new(ColumnSortOrder::new('name', ColumnSortOrder::DIRECTION_ASC));
        }

        foreach ($sort as $order) {
            $f = $order->getField();
            $d = $order->getDirection();

            if (in_array($order->getField(), Connection::NATURALLY_SORTED_PROPERTIES)) {
                $qb->addOrderBy("LENGTH(c.$f)", $d);
            }

            $qb->addOrderBy("c.$f", $d);
        }

        $qb->setMaxResults($pagination->getPageSize());
        $qb->setFirstResult($pagination->calculateOffset());

        $q = $qb->getQuery();

        return ConnectionList::fromArray($q->getResult());
    }

    public function all(ListOptions $options): ConnectionList
    {
        if ($options->getPagination() !== null) {
            return $this->getPage($options->getSortOrders(), $options->getPagination());
        }

        $res = ConnectionList::empty();
        $page = 1;
        $pageSize = 100;
        $totalResults = $this->getCount();

        $pagination = new Pagination($page, $pageSize);

        while ($pagination->calculateOffset() < $totalResults) {
            $res->add($this->getPage($options->getSortOrders(), $pagination));

            $page++;
            $pagination = new Pagination($page, $pageSize);
        }

        return $res;
    }
}
