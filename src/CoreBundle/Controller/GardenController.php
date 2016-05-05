<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Garden;
use CoreBundle\Form\Type\GardenType;

class GardenController extends Controller
{
  public function getGardensAction()
  {
  } // "get_gardens"            [GET] /gardens

  public function postGardensAction(Request $request)
  {
    return $this->formGarden(new Garden(), $request, "post");

  } // "post_gardens"           [POST] /gardens

  public function getGardenAction($id)
    {} // "get_garden"            [GET] /gardens/:id

  private function formGarden(Garden $garden, Request $request, $method="post")
  {
    $form = $this->createForm(GardenType::class, $garden, array("method" => $method));
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid())
    {
      $em = $this->getDoctrine()->getManager();
      $em->persist($garden);
      $em->flush();
      return $garden;
    }
    return new JsonResponse("error", 400);
  }
}
