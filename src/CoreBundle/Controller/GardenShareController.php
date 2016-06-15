<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Security\UserShareVoter;
use CoreBundle\Form\UserShareRepository;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Share;
use CoreBundle\Entity\UserShare;

class GardenShareController extends CoreController
{

    /**
     * @View(serializerGroups={"Default"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function getGardenShareAction(Request $request)
    {
        $shares = $garden->getShares();

        $this->isGranted(UserShareVoter::VIEW, $garden);

        return [
            'shares' => $shares,
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-share"}, statusCode=201)
     */
    public function postConfigurationAction(Request $request)
    {
        $this->isGranted(UserShareVoter::CREATE, $share = new Share());

        $share->setOwner($this->getUser());

        return $this->formConfiguration($share, $request, 'post');
    }

    private function formUserShare(Share $share, Request $request, $method = 'post')
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
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     */
    public function deleteGardenSharesAction(Share $share)
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
