<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Form\Type\UserShareType;
use CoreBundle\Security\UserShareVoter;
use CoreBundle\Security\GardenVoter;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\UserShare;

class GardenShareController extends CoreController
{
    /**
     * @View(serializerGroups={"Default"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function cgetSharesAction(Garden $garden, Request $request)
    {
        $this->isGranted(GardenVoter::VIEW, $garden);

        /** @var \CoreBundle\Repository\UserShareRepository $repo */
        $repo = $this->getRepository();

        $query = $repo->queryShareByGarden($garden);

        $pagination = $this->getPagination($request, $query, 'share');

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $pagination->getItemNumberPerPage(),
            'shares' => $pagination->getItems(),
            'page' => $pagination->getCurrentPageNumber(),
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-share"})
     * @FOSRest\Get("/shares/{id}")
     */
    public function getShareAction(UserShare $share)
    {
        $this->isGranted(UserShareVoter::VIEW, $share);

        return [
            'share' => $share,
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-share"}, statusCode=201)
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function postShareAction(Garden $garden, Request $request)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);
        $this->isGranted(UserShareVoter::CREATE, $share = new UserShare());

        $share->setOwner($this->getUser());
        $share->setGarden($garden);

        return $this->formUserShare($share, $request, 'post');
    }

    /**
     * @View(serializerGroups={"Default", "detail-share"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @FOSRest\Patch("/shares/{id}")
     */
    public function patchShareAction(UserShare $share, Request $request)
    {
        $this->isGranted(UserShareVoter::EDIT, $share);

        return $this->formUserShare($share, $request, 'patch');
    }

    private function formUserShare(UserShare $share, Request $request, $method = 'post')
    {
        $form = $this->createForm(UserShareType::class, $share, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getManager();
            $em->persist($share);
            $em->flush();

            return [
                'share' => $share,
            ];
        }

        return new JsonResponse($this->getAllErrors($form), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @FOSRest\Delete("/shares/{id}")
     */
    public function deleteShareAction(UserShare $share)
    {
        $this->isGranted(UserShareVoter::DELETE, $share);

        $this->getManager()->remove($share);
        $this->getManager()->flush();
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:UserShare';
    }
}
