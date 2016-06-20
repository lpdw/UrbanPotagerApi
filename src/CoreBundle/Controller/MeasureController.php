<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Security\GardenVoter;
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

        /** @var \CoreBundle\Filter\MeasureFilter $filter */
        $filter = $this->getFilter('core.measure_filter', $request);

        /** @var \CoreBundle\Repository\MeasureRepository $repo */
        $repo = $this->getRepository();

        $totalItems = $repo->countPerGardenAndType($garden, $type);

        $page = $this->getPage($request);
        $itemPerPage = $this->getItemPerPage('measure', $request);

        $measures = $filter->getResult('queryBuilderMeasureByGardenAndType', [$garden, $type, $page, $itemPerPage]);

        return [
            'measures' => $measures,
            'total_items' => $totalItems,
            'item_per_page' => $itemPerPage,
            'page' => $page,
        ];
    }

    /**
     * @View(serializerGroups={"Default", "detail-configuration"}, statusCode=201)
     */
    public function postMeasureAction(Request $request)
    {
        $measure = new Measure();

        $apiKey = $request->query->get('api_key');
        $garden = $this->getGardenByApiKey($apiKey);
        $measure->setGarden($garden);

        $response = $this->formMeasure($measure, $request, "post");

        return $response;
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
                'configuration' => $measure->getGarden()->getConfiguration(),
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
            throw $this->createNotFoundException($this->t('core.error.garden_not_found'));
        }

        return $garden;
    }

    /**
     * @param Garden $garden
     * @param Type $type
     */
    private function grantedViewMeasure(Garden $garden, Type $type)
    {
        $this->isGranted(GardenVoter::VIEW, $garden);

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
        if ($this->isOwner($garden) || $this->isAdmin()) {
            return true;
        } else {
            /** @var \CoreBundle\Repository\AccessRepository $repo */
            $repo = $this->getRepository('CoreBundle:Access');

            return $repo->measureIsPublic($garden, $type);
        }
    }

    /**
    * @return string
    */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Measure';
    }
}
