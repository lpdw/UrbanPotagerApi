<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class MeController extends CoreController
{
    /**
     * @View(serializerGroups={"Default", "details-user", "me"})
     */
    public function getMeAction()
    {
        return [
            'user' => $this->getUser(),
        ];
    }

    /**
     * @View(serializerGroups={"Default", "me-garden"})
     */
    public function getMeGardensAction(Request $request)
    {
        /** @var \CoreBundle\Repository\GardenRepository $repo */
        $repo = $this->getRepository('CoreBundle:Garden');

        $query = $repo->queryMeGardens($this->getUser());

        $pagination = $this->getPagination($request, $query, 'garden');

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => $pagination->getItemNumberPerPage(),
            'gardens' => $pagination->getItems(),
            'page' => $pagination->getCurrentPageNumber(),
        ];
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return '';
    }
}
