<?php

namespace CoreBundle\Filter;

use Symfony\Component\HttpFoundation\Request;

abstract class Filter
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $alias;

    const ITEM_PER_PAGE = 'item_per_page';
    const ORDER_BY = 'order_by';
    const ORDER = 'order';

    /**
     * @param Request $request
     *
     * @return Filter
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->request->query->has($key);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->request->query->get($key);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function like($key)
    {
        return '%' . $this->get($key) . '%';
    }

    protected function getAlias(\Doctrine\ORM\QueryBuilder $qb)
    {
        return $qb->getRootAliases()[0] . '.';
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function filter(\Doctrine\ORM\QueryBuilder $qb)
    {
        if ($this->has(self::ORDER_BY)) {
            $ordersBy = $this->get(self::ORDER_BY);
            $orders = $this->get(self::ORDER);

            foreach ($ordersBy as $key => $orderBy) {
                $qb->addOrderBy($this->alias . $orderBy, isset($orders[$key]) ? $orders[$key] : 'ASC');
            }
        }

        return $qb;
    }

    /**
     * @param string
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQuery($method)
    {
        if (!method_exists($this->repo, $method)) {
            throw new \LogicException('This code should not be reached!');
        }

        $qb = $this->repo->$method();
        $this->alias = $this->getAlias($qb);

        $query = $this->filter($qb)->getQuery();

        return $query;
    }
}
