<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use CoreBundle\Security\Voter;

abstract class CoreController extends Controller
{
    /**
     * @param int|string $id
     * @param string $access
     * @param array $options
     *
     * @return object
     */
    protected function getEntity($id, $access = Voter::VIEW, array $options = [])
    {
        $options = $this->getOptions($options);

        return $this->get("core.get_entity")->get($id, $access, $options);
    }

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

    /**
     * @param string $message
     * @return string
     */
    protected function t($message)
    {
        return $this->get('translator')->trans($message);
    }

    /**
     * @param array $options
     * @return array
     */
    private function getOptions(array $options)
    {
        $defaultOptions = [
            "repository" => $this->getRepositoryName(),
            "method" => "find"
        ];

        return array_merge($defaultOptions, $options);
    }

    /**
     * @return string
     */
    abstract protected function getRepositoryName();
}
