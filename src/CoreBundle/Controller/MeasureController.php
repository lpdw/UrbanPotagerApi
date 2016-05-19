<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Measure;
use CoreBundle\Entity\Garden;
use CoreBundle\Form\Type\MeasureType;

class MeasureController extends Controller
{

  /**
   * Get entity instance
   * @var integer $id Id of the entity
   * @return Organisation
   */
  private function getMeasures($id)
  {
      $em = $this->getDoctrine()->getManager();
      $garden = $em
        ->getRepository('CoreBundle:Garden')
        ->find($id)
      ;
      $measures = $em
        ->getRepository('CoreBundle:Measure')
        ->findBy(array('garden' => $garden))
      ;

      return $measures;
  }

  private function getGarden($id)
  {
        $em = $this->getDoctrine()->getManager();
        $garden = $em
            ->getRepository('CoreBundle:Garden')
            ->findBy(array('id' => $id))
        ;


        return $garden;
  }

  public function getGardenDatasAction($id)
  {
      $measures = $this->getMeasures($id);

      return array(
              'measures' => $measures,
              );
  }

  public function postGardenDatasAction(Request $request, $id)
  {
        $garden = $this->getGarden($id);

        return $this->formMeasure(new Measure(), $request, "post");
  }

  private function formMeasure(Measure $measure, Request $request, $method='post')
  {
        $form = $this->createForm(MeasureType::class, $measure, array('method' => $method));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($measure);
            $em->flush();

            return [
                'measure' => $measure,
            ];
        }

        return new JsonResponse("error", Codes::HTTP_BAD_REQUEST);

    }

  /**
   * @return string
   */
  protected function getRepositoryName()
  {
      return 'CoreBundle:Measure';
  }
}
