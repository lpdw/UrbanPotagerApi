<?php

namespace CoreBundle\Controller;

use CoreBundle\Security\TypeVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use CoreBundle\Entity\Type;
use CoreBundle\Form\Type\TypeType;

class TypeController extends CoreController
{
    // TODO add filter
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getTypesAction()
    {
        /** @var \CoreBundle\Repository\TypeRepository $repo */
        $repo = $this->getRepository();

        $types = $repo->findAll();

        return [
            'total_items' => count($types),
            'types' => $types,
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-type"})
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     */
    public function getTypeAction(Type $type)
    {
        $this->isGranted(TypeVoter::VIEW, $type);

        return [
            'type' => $type,
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-type"}, statusCode=201)
     */
    public function postTypeAction(Request $request)
    {
        $this->isGranted(TypeVoter::CREATE, $type = new Type());

        return $this->formType($type, $request, 'post');
    }

    /**
     * @View(serializerGroups={"Default", "detail-type"})
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     */
    public function patchTypeAction(Type $type, Request $request)
    {
        $this->isGranted(TypeVoter::EDIT, $type);

        return $this->formType($type, $request, 'patch'); // TODO form for edit (can not change name ?)
    }

    /**
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     */
    public function deleteTypeAction(Type $type)
    {
        $this->isGranted(TypeVoter::DELETE, $type);

        $this->getManager()->remove($type);
        $this->getManager()->flush();
    }

    private function formType(Type $type, Request $request, $method = 'post')
    {
        $form = $this->createForm(TypeType::class, $type, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getManager();
            $em->persist($type);
            $em->flush();

            return [
                'type' => $type,
            ];
        }

        return new JsonResponse($this->getAllErrors($form), self::BAD_REQUEST);
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Type';
    }
}
