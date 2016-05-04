<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class CoreController extends Controller
{
    const OK            = 200;
    const CREATED       = 201;
    const NO_CONTENT    = 204;
    const BAD_REQUEST   = 400;

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
                $errorsString[$child->getName()] = $error->getMessage(); // TODO translate ?
            }
        }

        return $errorsString;
    }

    protected function isGranted($attributes, $object = null)
    {
        if (!parent::isGranted($attributes, $object)) {
            throw new AccessDeniedException();
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
     * @return string
     */
    abstract protected function getRepositoryName();
}
