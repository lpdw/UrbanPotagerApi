<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;

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

    /*
     * @return array
     */
    protected function getAllErrors(Form $form)
    {
        $errorsString = [];

        /** @var \Symfony\Component\Form\FormInterface $child */
        foreach ($form->all() as $child)
        {
            $errors = $child->getErrors(true, false);

            foreach($errors as $error) {
                $errorsString[$child->getName()] = $error->getMessage();
            }
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
    protected function t($message)
    {
        return $this->get('translator')->trans($message);
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
     * @return string
     */
    abstract protected function getRepositoryName();
}
