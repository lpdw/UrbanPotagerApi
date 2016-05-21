<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Event\MeasureSentEvent;
use CoreBundle\Form\Type\MeasureType;
use CoreBundle\Entity\Measure;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Type;

class MeasureController extends CoreController
{
    /**
     * @ParamConverter("garden", options={"mapping": {"garden": "slug"}})
     * @ParamConverter("type", options={"mapping": {"type": "slug"}})
     */
    public function getGardensMeasuresAction(Garden $garden, Type $type, Request $request)
    {
        $this->grantedViewMeasure($garden, $type);

        /** @var \CoreBundle\Repository\MeasureRepository $repo */
        $repo = $this->getRepository();

        $totalItems = $repo->countPerGardenAndType($garden, $type);

        list($page, $itemPerPage) = $this->getPage($request);

        $measures = $repo->getMeasureByGardenAndType($garden, $type, $page, $itemPerPage);

        return [
            'measures' => $measures,
            'total_items' => $totalItems,
            'item_per_page' => $itemPerPage,
            'page' => $page,
        ];
    }

    /**
     * @View(serializerGroups={"Default"}, statusCode=201)
     */
    public function postMeasureAction(Request $request)
    {
        $measure = new Measure();

        $apiKey = $request->query->get('api_key');
        $garden = $this->getGardenByApiKey($apiKey);
        $measure->setGarden($garden);

        return $this->formMeasure($measure, $request, "post");
    }

    private function formMeasure(Measure $measure, Request $request, $method = 'post')
    {
        $form = $this->createForm(MeasureType::class, $measure, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($measure);
            $em->flush();

            $this->dispatch(MeasureSentEvent::NAME, new MeasureSentEvent($measure));

            return [
                'measure' => $measure,
            ];
        }

        return new JsonResponse($this->getAllErrors($form), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @param $apiKey
     *
     * @return \CoreBundle\Entity\Garden
     */
    private function getGardenByApiKey($apiKey)
    {
        /** @var \CoreBundle\Repository\GardenRepository $repo */
        $repo = $this->getRepository('CoreBundle:Garden');
        $garden = $repo->findByApiKey($apiKey);

        if (is_null($garden)) {
            throw $this->createNotFoundException();
        }

        return $garden;
    }

    /**
     * @param Garden $garden
     * @param Type $type
     */
    private function grantedViewMeasure(Garden $garden, Type $type)
    {
        if (!$this->canViewMeasure($garden, $type)) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @param Garden $garden
     * @param Type $type
     *
     * @return bool
     */
    private function canViewMeasure(Garden $garden, Type $type)
    {
        if ($this->isOwner($garden)) {
            return true;
        } else {
            /** @var \CoreBundle\Repository\AccessRepository $repo */
            $repo = $this->getRepository('CoreBundle:Access');

            return $repo->measureIsPublic($garden, $type);
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getPage(Request $request)
    {
        $itemPerPage = $request->query->getInt('itemPerPage', 0);

        // avoid itemPerPage negative
        if ($itemPerPage < 1) {
            $itemPerPage = $this->getItemPerPage('measure');
        }

        $page = $request->query->getInt('page', 1);

        // avoid page negative
        if ($page < 1) {
            $page = 1;
        }

        return [$page, $itemPerPage];
    }

    /**
    * @return string
    */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Measure';
    }
}
