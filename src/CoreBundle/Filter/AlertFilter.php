<?php

namespace CoreBundle\Filter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use CoreBundle\Entity\Alert;

class AlertFilter extends DateFilter
{
    const NAME = 'name';
    const COMPARISON = 'comparisons';
    const GARDEN = 'gardens';
    const TYPE = 'types';

    private $comparisons;
    private $gardens;
    private $types;

    public function __construct(\Symfony\Component\Translation\DataCollectorTranslator $translator, EntityRepository $repo)
    {
        parent::__construct($translator);

        $this->repo = $repo;
        $this->comparisons = [];
        $this->gardens = [];
        $this->types = [];
    }

    protected function buildParams()
    {
        parent::buildParams();

        if ($this->has(self::GARDEN)) {
            $garden = $this->get(self::GARDEN);

            if (!is_array($garden)) {
                $this->gardens[] = $garden;
            } else {
                $this->gardens = $garden;
            }
        }

        if ($this->has(self::COMPARISON)) {
            $comparison = $this->get(self::COMPARISON);

            if (!is_array($comparison)) {
                $this->comparisons[] = $comparison;
            } else {
                $this->comparisons = $comparison;
            }
        }

        if ($this->has(self::TYPE)) {
            $type = $this->get(self::TYPE);

            if (!is_array($type)) {
                $this->types[] = $type;
            } else {
                $this->types = $type;
            }
        }
    }

    public function isValid()
    {
        $isValid = parent::isValid();

        if (!$isValid) {
            return false;
        }

        foreach ($this->comparisons as $comparison) {
            if (!in_array($comparison, Alert::$OPERATOR)) {
                $this->error = $this->translator->trans('core.filter.comparison_not_found', ['%field%' => $comparison]);
                return false;
            }
        }

        return true;
    }

    public function filter(QueryBuilder $qb)
    {
        $qb = parent::filter($qb);

        if ($this->has(self::NAME)) {
            $name = $this->like(self::NAME);

            $qb->andWhere($this->alias . 'name LIKE :name')
                ->setParameter('name', $name);
        }

        if ($this->has(self::COMPARISON)) {
            $qb->andWhere($this->alias . 'comparison IN (:comparisons)')
                ->setParameter('comparisons', $this->comparisons);
        }

        if ($this->has(self::GARDEN)) {
            $qb->leftJoin($this->alias . 'gardens', 'g')
                ->andWhere('g.slug IN (:gardens)')
                ->setParameter('gardens', $this->gardens);
        }

        if ($this->has(self::TYPE)) {
            $qb->leftJoin($this->alias . 'type', 't')
                ->andWhere('t.slug IN (:types)')
                ->setParameter('types', $this->types);
        }

        return $qb;
    }

    protected function getFields()
    {
        $fields = parent::getFields();

        return array_merge([self::NAME, self::TYPE, 'comparison', self::GARDEN], $fields);
    }
}
