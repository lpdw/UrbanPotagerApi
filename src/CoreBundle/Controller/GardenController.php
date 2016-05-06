<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Configuration;
use CoreBundle\Security\GardenVoter;
use CoreBundle\Security\ConfigurationVoter;
use CoreBundle\Form\Type\GardenType;
use UserBundle\Entity\User;

class GardenController extends CoreController
{
    const GARDEN_PER_PAGE = 10; // TODO put into config.yml

    // TODO add filter
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getGardensAction(Request $request)
    {
        /** @var \CoreBundle\Repository\GardenRepository $repo */
        $repo = $this->getRepository();

        $query = $repo->queryPublicGardens();

        $pagination = $this->getPagination($request, $query, self::GARDEN_PER_PAGE);

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => self::GARDEN_PER_PAGE,
            'gardens' => $pagination->getItems(),
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
     * @View(serializerGroups={"Default", "detail-garden"}, statusCode=201)
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function postGardenAction(Request $request)
    {
        $this->isGranted(GardenVoter::CREATE, $garden = new Garden());

        $garden->setOwner($this->getUser());

        return $this->formGarden($garden, $request, "post");
    }

    /**
     * @View(serializerGroups={"Default", "detail-garden"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function patchGardenAction(Garden $garden, Request $request)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);

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

        return new JsonResponse($this->getAllErrors($form), self::BAD_REQUEST);
    }

    protected function getRepositoryName()
    {
        return 'CoreBundle:Garden';
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    protected function isOwner(Garden $garden, User $user)
    {
        $owner = $garden->getOwner();

        if (is_null($owner) || is_null($user)) {
            return false;
        }

        return $owner->getId() === $user->getId();
    }
}
