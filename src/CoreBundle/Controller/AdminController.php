<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Security\GardenVoter;
use CoreBundle\Form\Type\GardenType;

class AdminController extends CoreController
{
    // TODO add filter
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getGardensAction(Request $request)
    {
        /** @var \CoreBundle\Repository\GardenRepository $repo */
        $repo = $this->getRepository();

        $query = $repo->queryAdminGardens();

        $pagination = $this->getPagination($request, $query, 'garden');

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $pagination->getItemNumberPerPage(),
            'gardens' => $pagination->getItems(),
            'page' => $pagination->getCurrentPageNumber(),
        ];
    }

    public function getUsersAction(Request $request)
    {
        /** @var \UserBundle\Repository\UserRepository $repo */
        $repo = $this->getRepository('UserBundle:User');

        $query = $repo->queryAdminUsers();

        $pagination = $this->getPagination($request, $query, 'user');

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $pagination->getItemNumberPerPage(),
            'users' => $pagination->getItems(),
            'page' => $pagination->getCurrentPageNumber(),
        ];
    }

    protected function getRepositoryName()
    {
        return 'CoreBundle:Garden';
    }
}
