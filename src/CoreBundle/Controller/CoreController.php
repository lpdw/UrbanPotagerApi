<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\ORM\Query;
use FOS\RestBundle\Util\Codes;
use CoreBundle\Entity\Interfaces\OwnableInterface;
use UserBundle\Entity\User;

abstract class CoreController extends Controller
{
    /**
     * @param string $name
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($name = null)
    {
        return $this->getDoctrine()->getRepository($name ?: $this->getRepositoryName());
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param Form $form
     * @return array
     */
    protected function getAllErrors(Form $form)
    {
        $errorsString = [];

        foreach ($form->getErrors() as $error) {
            $errorsString['form_error'] = $error->getMessage();
        }

        foreach ($form->all() as $name => $child) {
            $errorsString[$name] = $this->getAllErrors($child);
        }

        return $errorsString;
    }

    protected function isGranted($attributes, $object = null)
    {
        if (!parent::isGranted($attributes, $object)) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @param string $message
     * @return string
     */
    protected function t($message, $domain = 'messages')
    {
        return $this->get('translator')->trans($message, [], $domain);
    }

    /**
     * @param Request $request
     * @param Query $query
     * @param int $limit
     *
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    protected function getPagination(Request $request, Query $query, $limit = 10)
    {
        /** @var \Knp\Component\Pager\Paginator $paginator */
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $this->getPage($request),
            $limit
        );

        return $pagination;
    }

    /**
     * @param string $groupToAdd
     * @param Request $request
     */
    protected function addSerializerGroup($groupToAdd, Request $request)
    {
        $groups = $request->attributes->get('_view')->getSerializerGroups();
        $groups[] = $groupToAdd;

        $request->attributes->get('_view')->setSerializerGroups($groups);
    }

    /**
     * @param $item
     *
     * @return int
     */
    protected function getItemPerPage($item, Request $request = null)
    {
        $itemPerPage = 0;

        if (!is_null($request)) {
            $itemPerPage = $request->query->getInt('item_per_page', 0);
        }

        if ($itemPerPage < 1) {
            try {
                $itemPerPage = $this->getParameter($item . '_per_page');
            } catch (\InvalidArgumentException $e) {
                throw new \LogicException('This code should not be reached!', 0, $e);
            }
        }

        return $itemPerPage;
    }

    /**
     * @param Request $request
     * @return int
     */
    protected function getPage(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        // avoid page negative
        if ($page < 1) {
            $page = 1;
        }

        return $page;
    }

    /**
     * @param OwnableInterface $ownable
     * @param User|null $user
     *
     * @return bool
     */
    protected function isOwner(OwnableInterface $ownable, User $user = null)
    {
        $owner = $ownable->getOwner();
        $user = $user ?: $this->getUser();

        if (is_null($owner) || is_null($user)) {
            return false;
        }

        return $owner->getId() === $user->getId();
    }

    /**
     * @param string $name
     * @param Event $event
     */
    protected function dispatch($name, Event $event)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $dispatcher->dispatch($name, $event);
    }

    /**
     * @param $name
     * @param Request $request
     *
     * @return \CoreBundle\Filter\Filter
     */
    protected function getFilter($name, Request $request)
    {
        /** @var \CoreBundle\Filter\Filter $filter */
        $filter = $this->get($name);

        $filter->setRequest($request);

        if (!$filter->isValid()) {
            $error = $filter->getError();

            if (empty($error)) {
                $error = $this->t('core.error.bad_filter');
            }

            throw new HttpException(Codes::HTTP_BAD_REQUEST, $error); // TODO add more info
        }

        return $filter;
    }

    /**
     * @return string
     */
    abstract protected function getRepositoryName();
}
