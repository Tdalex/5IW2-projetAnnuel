<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('AppBundle:default:index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

	/**
     * @Route("/hello", name="front_content_block_page_hello")
	 * @Method({"GET"})
	 * @param ContentBlock $page
	 * @return render
	 */
    public function helloDefaultAction(Request $request)
    {
        return $this->render('default/hello.html.twig', [
			'firstname' => "toto"
        ]);
    }

    /**
     * @Route("/hello/{firstname}", name="front_content_block_page")
	 * @Method({"GET"})
	 * @param ContentBlock $page
	 * @return render
	 */
    public function helloAction(Request $request, $firstname)
    {
		dump($request);
        return $this->render('default/hello.html.twig', [
			'firstname' => $firstname
        ]);
    }

    /**
     * @Route ("/search_address", name="search_address")
     * @Method ({"GET", "POST"})
     *
     */
    public function searchAddressAction(Request $request) {
        $term = $request->request->get('address');

        $em = $this->getDoctrine()->getManager();
        $addresses = $em->getRepository('AppBundle:Stop')->searchByTerm($term);

        return new JsonResponse($addresses);
    }

    /**
     * @Route ("/search_roadtrip", name="search_roadtrip")
     * @Method ({"GET", "POST"})
     */
    public function searchRoadtripAction(Request $request) {
        $dep = $request->request->get('dep');
        $dest = $request->request->get('dest');
        $em = $this->getDoctrine()->getManager();

        $roadtrips = $em->getRepository('AppBundle:Roadtrip')->searchRtDepDest($dep, $dest);
        $roadtrips = $this->get('serializer')->serialize($roadtrips, 'json');

        return new JsonResponse(array('roadtrips' => $roadtrips));
    }
}
