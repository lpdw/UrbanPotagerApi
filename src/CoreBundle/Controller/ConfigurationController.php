<?php

namespace CoreBundle\Controller;

use CoreBundle\Security\ConfigurationVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use CoreBundle\Entity\Configuration;
use CoreBundle\Form\Type\ConfigurationType;

class ConfigurationController extends CoreController
{
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getConfigurationsAction(Request $request)
    {
        /** @var \CoreBundle\Filter\ConfigurationFilter $filter */
        $filter = $this->getFilter('core.configuration_filter', $request);

        $query = $filter->getQuery('queryBuilderMeConfiguration', [$this->getUser()]);

        $pagination = $this->getPagination($request, $query, 'configuration');

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $pagination->getItemNumberPerPage(),
            'configurations' => $pagination->getItems(),
            'page' => $pagination->getCurrentPageNumber(),
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-configuration"})
     * @ParamConverter("configuration", options={"mapping": {"configuration": "slug"}})
     */
    public function getConfigurationAction(Configuration $configuration)
    {
        $this->isGranted(ConfigurationVoter::VIEW, $configuration);

        return [
            'configuration' => $configuration,
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-configuration"}, statusCode=201)
     */
    public function postConfigurationAction(Request $request)
    {
        $this->isGranted(ConfigurationVoter::CREATE, $configuration = new Configuration());

        $configuration->setOwner($this->getUser());

        return $this->formConfiguration($configuration, $request, 'post');
    }

    /**
     * @View(serializerGroups={"Default", "detail-configuration"})
     * @ParamConverter("configuration", options={"mapping": {"configuration": "slug"}})
     */
    public function patchConfigurationAction(Configuration $configuration, Request $request)
    {
        $this->isGranted(ConfigurationVoter::EDIT, $configuration);

        $configuration->setIsWateringActive(false); // TODO increase system

        return $this->formConfiguration($configuration, $request, 'patch');
    }

    /**
     * @ParamConverter("configuration", options={"mapping": {"configuration": "slug"}})
     */
    public function deleteConfigurationAction(Configuration $configuration)
    {
        $this->isGranted(ConfigurationVoter::DELETE, $configuration);

        $this->getManager()->remove($configuration);
        $this->getManager()->flush();
    }

    private function formConfiguration(Configuration $configuration, Request $request, $method = 'post')
    {
        $form = $this->createForm(ConfigurationType::class, $configuration, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getManager();
            $em->persist($configuration);
            $em->flush();

            return [
                'configuration' => $configuration,
            ];
        }

        return new JsonResponse($this->getAllErrors($form), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Configuration';
    }
}
