<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Roadtrip;
use AppBundle\Entity\Waypoint;
use AppBundle\Entity\Stop;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\RoadtripManager;
use AppBundle\Service\WaypointManager;

/**
 * Api controller.
 *
 * @Route("api")
 */
class ApiController extends Controller
{

    /**
     * @param Request $request
     * @param $ref
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/waypoint/by_distance", name="front_ajax_waypoint")
     * @Method("POST")
     */
    public function fundingAction(Request $request, WaypointManager $waypointManager)
    {
        $datapush = $request->request->all();
        $data = $waypointManager->findAll();
        // $data = $waypointManager->findByDistance($datapush);

        return new JsonResponse($data);
    }

}
