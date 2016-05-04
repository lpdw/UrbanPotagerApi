<?php

namespace CoreBundle\Controller;

use CoreBundle\Security\ConfigurationVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use CoreBundle\Entity\Configuration;
use CoreBundle\Form\Type\ConfigurationType;

class ConfigurationController extends CoreController
{
    // TODO add filter
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getConfigurationsAction()
    {
        /** @var \CoreBundle\Repository\ConfigurationRepository $repo */
        $repo = $this->getRepository();

        $configurations = $repo->findAll();

        return [
            'count' => count($configurations),
            'configurations' => $configurations,
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

        return $this->formType($configuration, $request, 'post');
    }

    /**
     * @View(serializerGroups={"Default", "detail-configuration"})
     * @ParamConverter("configuration", options={"mapping": {"configuration": "slug"}})
     */
    public function patchConfigurationAction(Configuration $configuration, Request $request)
    {
        $this->isGranted(ConfigurationVoter::EDIT, $configuration);

        return $this->formType($configuration, $request, 'patch');
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

    private function formType(Configuration $configuration, Request $request, $method = 'post')
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

        return new JsonResponse($this->getAllErrors($form), self::BAD_REQUEST);
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Configuration';
    }
}
