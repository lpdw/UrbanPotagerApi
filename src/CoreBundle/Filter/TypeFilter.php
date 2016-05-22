<?php

namespace CoreBundle\Filter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class TypeFilter extends Filter
{
    const TYPE = 'type';
    const NAME = 'name';

    public function __construct(EntityRepository $repo)
    {
        $this->repo = $repo;
    }

    public function filter(QueryBuilder $qb)
    {
        $qb = parent::filter($qb);

        if ($this->has(self::TYPE)) {
            $type = $this->get(self::TYPE);

            $qb->andWhere('t.type = :type')
                ->setParameter('type', $type);
        }

        if ($this->has(self::NAME)) {
            $name = $this->like(self::NAME);

            $qb->andWhere('t.name LIKE :name')
                ->setParameter('name', $name);
        }

        return $qb;
    }
}
