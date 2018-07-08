<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Waypoint;
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
        $waypoint = new Waypoint();
        $form = $this->createForm('AppBundle\Form\WaypointType',$waypoint,array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('partner'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isValid()){
//                dump($request->request->get('data'));
//                dump($request->query->get('data'));
//                dump($_POST);die;
                $waypoint->setPhone($waypoint->getPhone()->getnationalNumber());
                $waypoint->setLat(48.9902807);
                $waypoint->setLon(2.055868700000019);
                $waypoint->setStatus('disabled');
                //$waypoint->setSponsor(true);
                //generate token
                $token = bin2hex(random_bytes(20));
                $waypoint->setToken($token);
                $em = $this->getDoctrine()->getManager();
                $em->persist($waypoint);
                $em->flush();

                // Send mail
                $view = $this->container->get('templating')->render('AppBundle:mails:mail_partner_subscribe.html.twig', [
                    'token'    => $token
                ]);

                if($this->sendEmailToAdmin($form->getData()) && $this->sendEmailToUser($form->getData(), $view)){
                    $this->addFlash("success", "Votre demande a bien été envoyée :)");
                    return $this->redirectToRoute('partner');
                }else{
                    $this->addFlash("error", "Une erreure est survenue. Veuillez ressayer plus tard");
                }
            }
        }

        return $this->render('AppBundle:default:partner.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function sendEmailToAdmin($data){
        $view = $this->container->get('templating')->render('AppBundle:mails:mail_partner_information.html.twig', [
            'data' => $data
        ]);

        $myappContactMail = $this->container->getParameter('mailer_user');
        $myappContactPassword = $this->container->getParameter('mailer_password');

        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465,'ssl')
            ->setUsername($myappContactMail)
            ->setPassword($myappContactPassword);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance("Nouvelle demande de sponsor")
            ->setFrom(array($myappContactMail => "Message by ".$data->getEmail()))
            ->setTo( $myappContactMail)
            ->setBody($view, 'text/html');


        return $mailer->send($message);
    }

    private function sendEmailToUser($data, $view){
        $myappContactMail = $this->container->getParameter('mailer_user');
        $myappContactPassword = $this->container->getParameter('mailer_password');

        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465,'ssl')
            ->setUsername($myappContactMail)
            ->setPassword($myappContactPassword);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance("RoadMonTrip | Demande de partenariat")
            ->setFrom("noreply@roadmontrip.loc")
            ->setTo($data->getEmail())
            ->setBody($view, 'text/html');

        return $mailer->send($message);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("partner/activate/{token}", name="partner_activate")
     * @Method("GET")
     */
    public function activatePartnerAction(Waypoint $waypoint, $token,  Request $request)
    {
        if ($waypoint->getToken() == $token && $waypoint->getStatus() != 'enabled') {
            $waypoint->setStatus('enabled');
            $em = $this->getDoctrine()->getManager();
            $em->persist($waypoint);
            $em->flush();

            //send email
            $view = $this->container->get('templating')->render('AppBundle:mails:mail_partner_register.html.twig', [
                'waypoint'     => $waypoint
            ]);

            $myappContactMail = $this->container->getParameter('mailer_user');
            $myappContactPassword = $this->container->getParameter('mailer_password');

            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465,'ssl')
                ->setUsername($myappContactMail)
                ->setPassword($myappContactPassword);

            $mailer = \Swift_Mailer::newInstance($transport);

            $message = \Swift_Message::newInstance("RoadMonTrip | Partenariat validé")
                ->setFrom("noreply@roadmontrip.loc")
                ->setTo($waypoint->getEmail())
                ->setBody($view, 'text/html');

            $mailer->send($message);


            return $this->redirectToRoute('homepage');
        }
        $this->addFlash("error", "Une erreure est survenue. Veuillez ressayer plus tard");
        return $this->redirectToRoute('partner');
    }
}
