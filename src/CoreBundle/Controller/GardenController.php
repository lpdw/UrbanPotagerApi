<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Garden;
use CoreBundle\Security\GardenVoter;
use CoreBundle\Form\Type\GardenType;

class GardenController extends CoreController
{
    // TODO add filter
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getGardensAction(Request $request)
    {
        /** @var \CoreBundle\Repository\GardenRepository $repo */
        $repo = $this->getRepository();

        $itemPerPage = $this->getItemPerPage('garden');

        $query = $repo->queryPublicGardens();

        $pagination = $this->getPagination($request, $query, $itemPerPage);

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $itemPerPage,
            'gardens' => $pagination->getItems(),
            'page' => $pagination->getPage() + 1,
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-garden"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function getGardenAction(Garden $garden, Request $request)
    {
        $this->isGranted(GardenVoter::VIEW, $garden);

        if ($this->isOwner($garden, $this->getUser())) {
            $this->addSerializerGroup('me-garden', $request);
        }

        return [
            'garden' => $garden,
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-garden", "me-garden"}, statusCode=201)
     */
    public function postGardenAction(Request $request)
    {
        $this->isGranted(GardenVoter::CREATE, $garden = new Garden());

        $garden->setOwner($this->getUser());

        return $this->formGarden($garden, $request, "post");
    }

    /**
     * @View(serializerGroups={"Default", "detail-garden", "me-garden"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function patchGardenAction(Garden $garden, Request $request)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);

        $garden->setIsPublic(false); // TODO increase system

        return $this->formGarden($garden, $request, 'patch');
    }

    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function deleteGardenAction(Garden $garden)
    {
        $this->isGranted(GardenVoter::DELETE, $garden);

        $this->getManager()->remove($garden);
        $this->getManager()->flush();
    }

    private function formGarden(Garden $garden, Request $request, $method = 'post')
    {
        $form = $this->createForm(GardenType::class, $garden, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getManager();
            $em->persist($garden);
            $em->flush();

            return [
                'garden' => $garden,
            ];
        }

        return new JsonResponse($this->getAllErrors($form), Codes::HTTP_BAD_REQUEST);
    }

    protected function getRepositoryName()
    {
        return 'CoreBundle:Garden';
    }
}
