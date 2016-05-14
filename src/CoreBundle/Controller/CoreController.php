<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Interfaces\OwnableInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use UserBundle\Entity\User;

abstract class CoreController extends Controller
{
    const DEFAULT_ITEM_PER_PAGE = 10;

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

    /*
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
            $request->query->getInt('page', 1),
            $limit
        );

        return $pagination;
    }

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
    protected function getItemPerPage($item)
    {
        try {
            return $this->getParameter($item . '_per_page');
        } catch (\InvalidArgumentException $e) {
            return self::DEFAULT_ITEM_PER_PAGE;
        }
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
     * @return string
     */
    abstract protected function getRepositoryName();
}
