<?php

namespace CoreBundle\Filter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ConfigurationFilter extends Filter
{
    const NAME = 'name';
    const BEFORE = "created_before";
    const AFTER = "created_after";

    private $dateBefore;
    private $dateAfter;

    public function __construct(\Symfony\Component\Translation\DataCollectorTranslator $translator, EntityRepository $repo)
    {
        parent::__construct($translator);

        $this->repo = $repo;
        $this->dateBefore = null;
        $this->dateAfter = null;
    }

    protected function buildParams()
    {
        parent::buildParams();

        if ($this->has(self::BEFORE)) {
            $this->dateBefore = $this->dateFromTimestamp($this->get(self::BEFORE));
        }

        if ($this->has(self::AFTER)) {
            $this->dateAfter = $this->dateFromTimestamp($this->get(self::AFTER));
        }
    }

    public function isValid()
    {
        $valid = parent::isValid();

        if (!$valid) {
            return false;
        }

        if (!is_null($this->dateBefore) && !$this->dateBefore instanceof \DateTime) {
            $this->error = $this->translator->trans("core.filter.bad_date_before", ['%timestamp%' => $this->get(self::BEFORE)]);
            return false;
        }

        if (!is_null($this->dateAfter) && !$this->dateAfter instanceof \DateTime) {
            $this->error = $this->translator->trans("core.filter.bad_date_after", ['%timestamp%' => $this->get(self::BEFORE)]);
            return false;
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

        if ($this->has(self::BEFORE)) {
            $qb->andWhere($this->alias . 'createdAt <= :before')
                ->setParameter('before', $this->dateBefore);
        }

        if ($this->has(self::AFTER)) {
            $qb->andWhere($this->alias . 'createdAt >= :after')
                ->setParameter('after', $this->dateAfter);
        }

        return $qb;
    }

    protected function getFields()
    {
        return [self::NAME, 'createdAt'];
    }
}
