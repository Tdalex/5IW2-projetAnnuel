<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use AppBundle\Service\RoadtripManager;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, RoadtripManager $roadtripManager)
    {
        $em = $this->getDoctrine()->getManager();
        $allFilters   = $roadtripManager->getFilters($em);

        // replace this example code with whatever you need
        return $this->render('AppBundle:default:index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'filters'  => $allFilters
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
        foreach ($roadtrips as $key => $value){
            $roadtrips[$key]['url'] = $this->generateUrl('roadtrip_show', array('slug' => $roadtrips[$key]['slug']));
        }
        $roadtrips = $this->get('serializer')->serialize($roadtrips, 'json');

        return new JsonResponse(array('roadtrips' => $roadtrips));
    }

    /**
     * @Route ("/contact_us", name="contact_us")
     * @Method ({"GET", "POST"})
     */
    public function contactUsAction(Request $request) {
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $email = $request->request->get('email');
        $sujet = $request->request->get('sujet');
        $message = $request->request->get('message');

        $msend = (new \Swift_Message('De '.$nom.' '.$prenom.' : '.$sujet))
            ->setFrom($email)
            ->setTo('noreply@roadtrip.loc')
            ->setBody($message, 'text/html');

        $response = $this->container->get('mailer')->send($msend);

        $this->addFlash(
            'notice',
            'Votre message a bien été envoyé !'
        );

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route ("/partner", name="partner")
     * @Method ({"GET", "POST"})
     */
    public function partnerAction(Request $request) {
        $form = $this->createForm('AppBundle\Form\ContactType',null,array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('partner'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isValid()){
                // Send mail
                if($this->sendEmail($form->getData())){

                    // Everything OK, redirect to wherever you want ! :

                    $this->addFlash("success", "Votre demande a bien été prise en compte :)");
                    return $this->redirectToRoute('partner');
                }else{
                    // An error ocurred, handle
                    $this->addFlash("error", "Une erreure est survenue. Veuillez ressayer plus tard");
                }
            }
        }

        return $this->render('AppBundle:default:partner.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function sendEmail($data){
        $myappContactMail = $this->container->getParameter('mailer_user');
        $myappContactPassword = $this->container->getParameter('mailer_password');

        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465,'ssl')
            ->setUsername($myappContactMail)
            ->setPassword($myappContactPassword);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance("Nouvelle demande de souscription d'établissement ")
            ->setFrom(array($myappContactMail => "Message by ".$data["phone"]))
            ->setTo(array(
                $myappContactMail => $myappContactMail
            ))
            ->setBody("ContactPhone: ".$data["phone"]."<br>ContactMail :".$data["email"]);

        return $mailer->send($message);
    }
}
