<?php

namespace CoreBundle\Controller;

use CoreBundle\Form\Type\CollectionAccessType;
use CoreBundle\Model\CollectionAccess;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Garden;
use CoreBundle\Security\GardenVoter;
use CoreBundle\Form\Type\AccessType;
use CoreBundle\Form\Type\AccessEditType;
use CoreBundle\Entity\Access;
use CoreBundle\Entity\Type;


/**
 * @FOSRest\RouteResource("Access")
 */
class GardenAccessController extends CoreController
{
    /**
     * @FOSRest\View(serializerGroups={"Default"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @FOSRest\Get("/gardens/{garden}/access")
     */
    public function cgetAction(Garden $garden)
    {
        $this->isGranted(GardenVoter::VIEW, $garden);

        return [
            'access' => $garden->getAccess(),
        ];
    }

    /**
     * @FOSRest\View(serializerGroups={"Default", "detail-access"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     * @FOSRest\Get("/gardens/{garden}/access/{type}")
     */
    public function getAction(Garden $garden, Type $type)
    {
        $this->isGranted(GardenVoter::VIEW, $garden);

        $access = $this->findAccess($garden, $type);

        return [
            'access' => $access
        ];
    }

    /**
     * @FOSRest\View(serializerGroups={"Default"}, statusCode=201)
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @FOSRest\Post("/gardens/{garden}/access")
     */
    public function postAction(Garden $garden, Request $request)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);

        $collectionAccess = new CollectionAccess();
        $collectionAccess->setAccess($garden->getAccess());

        return $this->formAccess($garden, $collectionAccess, $request, 'post');
    }

    /**
     * @FOSRest\View(serializerGroups={"Default"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     * @FOSRest\Patch("/gardens/{garden}/access/{type}")
     */
    public function patchAction(Garden $garden, Type $type, Request $request)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);

        $access = $this->findAccess($garden, $type);
        $access->setIsPublic(false); // TODO increase system

        return $this->formAccess($access, $request, 'patch');
    }

    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     * @FOSRest\Delete("/gardens/{garden}/access/{type}")
     */
    public function deleteAction(Garden $garden, Type $type)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);

        $access = $this->findAccess($garden, $type);

        $this->getManager()->remove($access);
        $this->getManager()->flush();
    }

    private function formAccess(Garden $garden, CollectionAccess $collectionAccess, Request $request, $method = 'post')
    {
        $formType = 'post' === $method ? CollectionAccessType::class : AccessEditType::class;

        $form = $this->createForm($formType, $collectionAccess, ['method' => $method]);
        $form->handleRequest($request);

        $cAccess = $collectionAccess->getAccess();

        /** @var \CoreBundle\Entity\Access $access */
        foreach($cAccess as $access) {
            $access->setGarden($garden);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getManager();
            $em->persist($garden);
            $em->flush();

            return [
                'access' => $garden->getAccess(),
            ];
        }

        return new JsonResponse($this->getAllErrors($form), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @param Garden $garden
     * @param Type $type
     *
     * @return Access
     */
    private function findAccess(Garden $garden, Type $type)
    {
        /** @var \CoreBundle\Repository\AccessRepository $repo */
        $repo = $this->getRepository();

        $access = $repo->findByGardenAndType($garden, $type);

        if (is_null($access)) {
            throw $this->createNotFoundException();
        }

        return $access;
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Access';
    }
}
