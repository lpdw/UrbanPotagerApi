<?php

namespace CoreBundle\Filter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class MeasureFilter extends DateFilter
{
    public function __construct(\Symfony\Component\Translation\DataCollectorTranslator $translator, EntityRepository $repo)
    {
        parent::__construct($translator);

        $this->repo = $repo;
    }

    public function getResult($method, $params = [])
    {
        $query = parent::getQuery($method, $params);

        return $query->getArrayResult();
    }
}
