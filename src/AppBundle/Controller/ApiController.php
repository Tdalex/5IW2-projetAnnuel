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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/waypoint/by_distance", name="api_waypoint_distance")
     * @Method("GET")
     */
    public function waypointByDistanceAction(Request $request, WaypointManager $waypointManager)
    {
        $datapush = $request->query->all();
        $data = $waypointManager->findByDistance($datapush);

        foreach($data as $key => $d){
            $data[$key] = $d->getData();
        }

        return new JsonResponse($data);
    }

     /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/waypoint/all", name="app_api_waypoint_all")
     * @Method("GET")
     */
    public function allWaypointsAction(Request $request, WaypointManager $waypointManager)
    {
        $data = $waypointManager->findAll();

        foreach($data as $key => $d){
            $data[$key] = $d->getData();
        }

        return new JsonResponse($data);
    }

}
