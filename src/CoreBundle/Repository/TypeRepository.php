<?php

namespace CoreBundle\Repository;

/**
 * TypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function findBySlug($slug)
    {
        $qb = $this->createQueryBuilder('t')
                    ->where('t.slug = :slug')
                    ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function queryAll()
    {
        return $this->createQueryBuilder('t')->getQuery();
    }
}
