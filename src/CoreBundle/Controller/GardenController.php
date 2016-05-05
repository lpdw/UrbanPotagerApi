<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Garden;
use CoreBundle\Form\Type\GardenType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
  } // "get_gardens"            [GET] /gardens

  public function postGardensAction(Request $request)
  {
    return $this->formGarden(new Garden(), $request, "post");

  } // "post_gardens"           [POST] /gardens

  public function getGardenAction($id)
    {

    } // "get_garden"            [GET] /gardens/:id

  private function formGarden(Garden $garden, Request $request, $method='post')
  {
    $form = $this->createForm(GardenType::class, $garden, array('method' => $method));
    $form->handleRequest($request);


      $em = $this->getDoctrine()->getManager();
      $em->persist($garden);
      $em->flush();

      return $garden;

  }

  /**
   * @return string
   */
  protected function getRepositoryName()
  {
      return 'CoreBundle:Garden';
  }
}
