<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\RouteResource;
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
 * @RouteResource("Access")
 */
class GardenAccessController extends CoreController
{
    /**
     * @View(serializerGroups={"Default"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @Get("/gardens/{garden}/access")
     */
    public function cgetAction(Garden $garden)
    {
        $this->isGranted(GardenVoter::VIEW, $garden);

        return [
            'access' => $garden->getAccess(),
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-access"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     * @Get("/gardens/{garden}/access/{type}")
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
     * @View(serializerGroups={"Default"}, statusCode=201)
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @Post("/gardens/{garden}/access")
     */
    public function postAction(Garden $garden, Request $request)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);

        $access = new Access();
        $access->setGarden($garden);

        return $this->formAccess($access, $request, 'post');
    }

    /**
     * @View(serializerGroups={"Default"})
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     * @Patch("/gardens/{garden}/access/{type}")
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
     * @Delete("/gardens/{garden}/access/{type}")
     */
    public function deleteAction(Garden $garden, Type $type)
    {
        $this->isGranted(GardenVoter::EDIT, $garden);

        $access = $this->findAccess($garden, $type);

        $this->getManager()->remove($access);
        $this->getManager()->flush();
    }

    private function formAccess(Access $access, Request $request, $method = 'post')
    {
        $formType = 'post' === $method ? AccessType::class : AccessEditType::class;

        $form = $this->createForm($formType, $access, ['method' => $method]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getManager();
            $em->persist($access);
            $em->flush();

            return [
                'access' => $access,
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
