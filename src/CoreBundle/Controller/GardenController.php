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
      if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($garden);
        $em->flush();

        return [
            'garden' => $garden,
        ];
      }

      return new JsonResponse("error", self::BAD_REQUEST);
    }

    protected function getRepositoryName()
    {
        return 'CoreBundle:Garden';
    }
}
