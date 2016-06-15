<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Security\AlertVoter;
use CoreBundle\Form\Type\AlertType;
use CoreBundle\Entity\Alert;

class AlertController extends CoreController
{
    /**
     * @FOSRest\View(serializerGroups={"Default"})
     */
    public function getAlertsAction(Request $request)
    {
        /** @var \CoreBundle\Filter\AlertFilter $filter */
        $filter = $this->getFilter('core.alert_filter', $request);

        $query = $filter->getQuery('queryBuilderMeAlerts', [$this->getUser()]);

        $pagination = $this->getPagination($request, $query, 'alert');

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $pagination->getItemNumberPerPage(),
            'alerts' => $pagination->getItems(),
            'page' => $pagination->getCurrentPageNumber(),
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
     * @FOSRest\View(serializerGroups={"Default", "detail-alert"}, statusCode=201)
     */
    public function postAlertAction(Request $request)
    {
        $this->isGranted(AlertVoter::CREATE, $alert = new Alert());

        $alert->setOwner($this->getUser());

        return $this->formAlert($alert, $request, 'post');
    }

    /**
     * @FOSRest\View(serializerGroups={"Default", "detail-alert"})
     * @ParamConverter("alert", options={"mapping": {"alert": "slug"}})
     */
    public function patchAlertAction(Alert $alert, Request $request)
    {
        $this->isGranted(AlertVoter::EDIT, $alert);

        return $this->formAlert($alert, $request, 'patch');
    }

    /**
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
