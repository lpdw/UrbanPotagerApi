<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Event\MeasureSentEvent;
use CoreBundle\Form\Type\MeasureType;
use CoreBundle\Entity\Measure;

class MeasureController extends CoreController
{
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
    * @return string
    */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Measure';
    }
}
