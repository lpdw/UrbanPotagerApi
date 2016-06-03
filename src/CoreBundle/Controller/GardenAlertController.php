<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Alert;
use CoreBundle\Security\GardenVoter;
use CoreBundle\Security\AlertVoter;

class GardenAlertController extends CoreController
{
    /**
     * @View(serializerGroups={"Default"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function getAlertsAction(Garden $garden)
    {
        $alerts = $garden->getAlerts();

        $this->isGranted(GardenVoter::EDIT, $garden); // TODO increase

        return [
            'alerts' => $alerts,
        ];
    }

    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("alert", options={"mapping": {"alert": "slug"}})
     */
    public function postAlertsAction(Garden $garden, Alert $alert)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);
        $this->isGranted(AlertVoter::VIEW, $alert);

        $alert->addGarden($garden);

        $this->getManager()->flush();

        return new JsonResponse([], Codes::HTTP_OK);
    }

    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("alert", options={"mapping": {"alert": "slug"}})
     */
    public function deleteAlertsAction(Garden $garden, Alert $alert)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);
        $this->isGranted(AlertVoter::VIEW, $alert);

        $alert->removeGarden($garden);

        $this->getManager()->flush();
    }

    protected function getRepositoryName()
    {
        return 'CoreBundle:Alert';
    }
}
