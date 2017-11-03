<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
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
}
