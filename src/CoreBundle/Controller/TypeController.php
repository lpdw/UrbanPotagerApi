<?php

namespace CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use CoreBundle\Entity\Type;
use CoreBundle\Security\TypeVoter;
use CoreBundle\Form\Type\TypeType;
use CoreBundle\Filter\TypeFilter;

class TypeController extends CoreController
{
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getTypesAction(Request $request)
    {
        /** @var \CoreBundle\Filter\TypeFilter $filter */
        $filter = $this->get('core.type_filter');
        $filter->setRequest($request);

        $itemPerPage = $this->getItemPerPage('type');

        $query = $filter->getQuery('queryBuilderAll');

        $pagination = $this->getPagination($request, $query, $itemPerPage);

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $itemPerPage,
            'types' => $pagination->getItems(),
            'page' => $pagination->getPage() + 1,
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

        return new JsonResponse($this->getAllErrors($form), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Type';
    }
}
