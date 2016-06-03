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
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getAdminGardensAction(Request $request)
    {
        /** @var \CoreBundle\Filter\GardenFilter $filter */
        $filter = $this->getFilter('core.garden_filter', $request);

        $query = $filter->getQuery('queryBuilderAdminGardens');

        $pagination = $this->getPagination($request, $query, 'garden');

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $pagination->getItemNumberPerPage(),
            'gardens' => $pagination->getItems(),
            'page' => $pagination->getCurrentPageNumber(),
        ];
    }

    public function getAdminUsersAction(Request $request)
    {
        /** @var \CoreBundle\Filter\UserFilter $filter */
        $filter = $this->getFilter('core.user_filter', $request);

        $query = $filter->getQuery('queryBuilderAdminUsers');

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
