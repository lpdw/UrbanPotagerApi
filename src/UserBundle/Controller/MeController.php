<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class MeController extends CoreController
{
    const GARDEN_PER_PAGE = 10; // TODO put into config.yml

    /**
     * @View(serializerGroups={"Default", "me-garden"})
     */
    public function getMeGardensAction(Request $request)
    {
        /** @var \CoreBundle\Repository\GardenRepository $repo */
        $repo = $this->getRepository('CoreBundle:Garden');

        $query = $repo->queryMeGardens($this->getUser());

        $pagination = $this->getPagination($request, $query, self::GARDEN_PER_PAGE);

        return [
            'total_items' => $pagination->getTotalItemCount(),
            'item_per_page' => self::GARDEN_PER_PAGE,
            'gardens' => $pagination->getItems(),
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
