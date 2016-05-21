<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Garden;
use CoreBundle\Security\GardenVoter;
use CoreBundle\Security\AlertVoter;
use CoreBundle\Form\Type\AlertType;
use CoreBundle\Entity\Alert;

class AlertController extends CoreController
{
    /**
     * @FOSRest\View(serializerGroups={"Default"})
     * @FOSRest\Get("/gardens/{garden}/alerts")
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function getAlertsAction(Garden $garden)
    {
        $this->isGranted(GardenVoter::VIEW, $garden); // TODO increase voter

        return [
            'alert' => $garden->getAlerts(),
        ];
    }

    /**
     * @FOSRest\View(serializerGroups={"Default", "detail-alert"})
     * @ParamConverter("alert", options={"mapping": {"alert": "slug"}})
     */
    public function getAlertAction(Alert $alert)
    {
        $this->isGranted(AlertVoter::VIEW, $alert);

        return [
            'alert' => $alert,
        ];
    }

    /**
     * @FOSRest\View(serializerGroups={"Default"}, statusCode=201)
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function postGardensAlertAction(Garden $garden, Request $request)
    {
        $alert = new Alert();
        $this->isGranted(AlertVoter::CREATE, $alert);

        $alert->setGarden($garden);

        return $this->formAlert($alert, $request, 'post');
    }

    /**
     * @FOSRest\View(serializerGroups={"Default"})
     * @ParamConverter("alert", options={"mapping": {"alert": "slug"}})
     */
    public function patchAlertAction(Alert $alert, Request $request)
    {
        $this->isGranted(AlertVoter::EDIT, $alert);

        return $this->formAlert($alert, $request, 'patch');
    }

    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("alert", options={"mapping": {"alert": "slug"}})
     */
    public function deleteAlertAction(Alert $alert)
    {
        $this->isGranted(AlertVoter::DELETE, $alert);

        $this->getManager()->remove($alert);
        $this->getManager()->flush();
    }

    private function formAlert(Alert $alert, Request $request, $method = 'post')
    {
        $form = $this->createForm(AlertType::class, $alert, ['method' => $method]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getManager();
            $em->persist($alert);
            $em->flush();

            return [
                'alert' => $alert,
            ];
        }

        return new JsonResponse($this->getAllErrors($form), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Alert';
    }
}
