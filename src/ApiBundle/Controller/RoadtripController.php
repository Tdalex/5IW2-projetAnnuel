<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;


class RoadtripController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/roadtrip")
     */
    public function getRoadtripsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Roadtrip');
        $roadtrips = $repository->findAll();

        return $roadtrips;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/roadtrip/{id}")
     */
    public function getRoadtripAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Roadtrip');
        $roadtrip = $repository->find($request->get('id'));

        if (empty($roadtrip)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Roadtrip not found'], Response::HTTP_NOT_FOUND);
        }

        return $roadtrip;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/user")
     */
    public function getUsersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:User");
        $users = $repository->findAll();

        return $users;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/user/{id}")
     */
    public function getUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:User");
        $user = $repository->find($request->get('id'));

        if (empty($user)) {
            return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

}
