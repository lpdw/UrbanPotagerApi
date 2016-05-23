<?php

namespace CoreBundle\Filter;

use Symfony\Component\HttpFoundation\Request;

abstract class Filter
{
    /**
     * @var \Symfony\Component\Translation\DataCollectorTranslator
     */
    protected $translator;

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

    /**
     * @var string
     */
    protected $error;

    /**
     * @var array
     */
    private $ordersBy;

    /**
     * @var array
     */
    private $orders;

    const ITEM_PER_PAGE = 'item_per_page';
    const ORDER_BY = 'order_by';
    const ORDER = 'order';

    public function __construct(\Symfony\Component\Translation\DataCollectorTranslator $translator)
    {
        $this->translator = $translator;
        $this->orders = [];
        $this->ordersBy = [];
    }

    /**
     * @param Request $request
     *
     * @return Filter
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        if ($this->has(self::ORDER_BY)) {
            $orderBy = $this->get(self::ORDER_BY);

            if (!is_array($orderBy)) {
                $this->ordersBy[] = $orderBy;
            } else {
                $this->ordersBy = $orderBy;
            }
        }

        if ($this->has(self::ORDER)) {
            $order = $this->get(self::ORDER);

            if (!is_array($order)) {
                $this->orders[] = $order;
            } else {
                $this->orders = $order;
            }
        }

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

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @return string
     */
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
        foreach ($this->ordersBy as $key => $orderBy) {
            $qb->addOrderBy($this->alias . $orderBy, isset($this->orders[$key]) ? $this->orders[$key] : 'ASC');
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

    /**
     * @return bool
     */
    public function isValid()
    {
        $fields = $this->getFields();

        foreach ($this->ordersBy as $orderBy) {
            if (!in_array($orderBy, $fields)) {
                $this->error = $this->translator->trans('core.filter.field_not_found', ['%field%' => $orderBy]);
                return false;
            }
        }

        foreach ($this->orders as $order) {
            if (!in_array($order, ['ASC', 'DESC'])) {
                $this->error = $this->translator->trans('core.filter.bad_order', ['%order%' => $order]);
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    abstract protected function getFields();
}
