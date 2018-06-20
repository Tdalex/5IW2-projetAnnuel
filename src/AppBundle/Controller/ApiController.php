<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Roadtrip;
use AppBundle\Entity\Waypoint;
use AppBundle\Entity\Stop;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/waypoint/{type}", name="api_waypoint")
     * @Method("GET")
     */
    public function apiWaypointAction(Request $request, WaypointManager $waypointManager, $type = null)
    {
        $datapush = $request->query->all();
        if ($type == 'all') {
            $data = $waypointManager->findAll();
        } else if ($type == 'by_distance') {
            $data = $waypointManager->findByDistance($datapush);
        } else if ($type == 'by_coordinates') {
            $data = $waypointManager->findByCoordinates($datapush);
        } else {
            throw new NotFoundHttpException('Sorry not existing!');
        }

        foreach($data as $key => $d){
            $data[$key] = $d->getData();
        }
        $result['count'] = count($data);
        $result['data']  = $data;

        return new JsonResponse($result);
    }

     /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/waypoint/nearest", name="api_waypoint")
     * @Method("GET")
     */
    public function apiWaypointNearesAction(Request $request, WaypointManager $waypointManager)
    {
        $data = $waypointManager->findNearest($request->query->all(), $request->query->get('limit', 1));

        foreach($data as $key => $d){
            $data[$key] = $d->getData();
        }
        $result['count'] = count($data);
        $result['data']  = $data;

        return new JsonResponse($result);
    }

}
