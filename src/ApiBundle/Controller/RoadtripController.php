<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
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

    /**
     * @Rest\View()
     * @Rest\Get("/user/{id}/roadtrip")
     */
    public function getRoadtripsByUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneById($request->get('id'));
        $roadtrips = $em->getRepository('AppBundle:Roadtrip')->findBy(array('owner' => $request->get('id')));

        if (empty($roadtrips)) {
            return \FOS\RestBundle\View\View::create(['message' => 'no roadtrips'], Response::HTTP_NOT_FOUND);
        }

        return $roadtrips;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/user/register")
     */
    public function postUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, array(
            'csrf_protection' => false,
//            'allow_extra_fields' => true
        ));
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $user->setEnabled(true);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }


}
