<?php

namespace CoreBundle\Filter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class GardenFilter extends DateFilter
{
    const NAME = 'name';
    const ZIP_CODE = 'zip_code';
    const OWNER = 'owners';

    private $owners;
    private $zipCodes;

    public function __construct(\Symfony\Component\Translation\DataCollectorTranslator $translator, EntityRepository $repo)
    {
        parent::__construct($translator);

        $this->repo = $repo;
        $this->owners = [];
        $this->zipCodes = [];
    }

    protected function buildParams()
    {
        parent::buildParams();

        if ($this->has(self::OWNER)) {
            $owner = $this->get(self::OWNER);

            if (!is_array($owner)) {
                $this->owners[] = $owner;
            } else {
                $this->owners = $owner;
            }
        }

        if ($this->has(self::ZIP_CODE)) {
            $zipcode = $this->get(self::ZIP_CODE);

            if (!is_array($zipcode)) {
                $this->zipCodes[] = $zipcode;
            } else {
                $this->zipCodes = $zipcode;
            }
        }
    }

    public function filter(QueryBuilder $qb)
    {
        $qb = parent::filter($qb);

        if ($this->has(self::NAME)) {
            $name = $this->like(self::NAME);

            $qb->andWhere($this->alias . 'name LIKE :name')
                ->setParameter('name', $name);
        }

        if ($this->has(self::OWNER)) {
            $qb->leftJoin($this->alias . 'owner', 'o')
                ->andWhere('o.username IN (:owners)')
                ->setParameter('owners', $this->owners);
        }

        if ($this->has(self::ZIP_CODE)) {
            $qb->andWhere($this->alias . 'zipCode IN (:zipcodes)')
                ->setParameter('zipcodes', $this->zipCodes);
        }

        return $qb;
    }

    protected function getFields()
    {
        return [self::NAME];
    }
}
