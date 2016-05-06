<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Configuration;
use CoreBundle\Security\GardenVoter;
use CoreBundle\Security\ConfigurationVoter;

class GardenConfigurationController extends GardenController
{
    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function getConfigurationAction(Garden $garden)
    {
        $configuration = $garden->getConfiguration();

        if (is_null($configuration)) {
            return $this->createNotFoundException();
        }

        $this->isGranted(ConfigurationVoter::VIEW, $configuration);

        return [
            'configuration' => $configuration,
        ];
    }

    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("configuration", options={"mapping": {"configuration": "slug"}})
     */
    public function postConfigurationAction(Garden $garden, Configuration $configuration)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);
        $this->isGranted(ConfigurationVoter::VIEW, $configuration);

        if (!is_null($garden->getConfiguration())) {
            return new JsonResponse([], self::CONFLICT);
        }

        $garden->setConfiguration($configuration);

        $this->getManager()->flush();

        return new JsonResponse([], self::OK);
    }

    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function deleteConfigurationsAction(Garden $garden)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);

        $garden->setConfiguration(null);

        $this->getManager()->flush();
    }
}
