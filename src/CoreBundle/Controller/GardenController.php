<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Garden;
use CoreBundle\Form\Type\GardenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GardenController extends CoreController
{
    public function getGardensAction()
    {
        $repo = $this->getRepository();

        $gardens = $repo->findAll();

        return [
            'count' => count($gardens),
            'gardens' => $gardens,
        ];
    }

    public function postGardensAction(Request $request)
    {
        return $this->formGarden(new Garden(), $request, "post");
    }

    public function getGardenAction($id)
    {

    }

    private function formGarden(Garden $garden, Request $request, $method = 'post')
    {
        $form = $this->createForm(GardenType::class, $garden, array('method' => $method));
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $em->persist($garden);
        $em->flush();

        return $garden;
    }

    protected function getRepositoryName()
    {
        return 'CoreBundle:Garden';
    }
}
