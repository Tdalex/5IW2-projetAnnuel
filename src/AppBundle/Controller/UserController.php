<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User controller.
 *
 * @Route("mon-compte")
 */
class UserController extends Controller
{
    /**
     * my profile
     *
     * @Route("/profil", name="user_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $session = $this->get('session');
        $currentUser = $session->get('currentUser');
        if(!empty($currentUser)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->findOneById($currentUser['id']);
            $roadtrips = $em->getRepository('AppBundle:Roadtrip')->findBy(array('owner' => $currentUser['id']));

            return $this->render('AppBundle:user:my_account.html.twig', array(
                'user' => $user,
                'roadtrips' => $roadtrips
                ));
        }
        return $this->redirectToRoute('roadtrip_index');
    }

    /**
     * my profile
     *
     * @Route("/modification", name="modify_my_account.html.twig")
     * @Method({"GET","POST"})
     */
    public function modifyMyAccountAction(Request $request)
    {
        $session = $this->get('session');
        $currentUser = $session->get('currentUser');
        if(!empty($currentUser)){
            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneById($currentUser['id']);
            if(isset($user[0]) && $user[0]){
                $user = $user[0];
                $editForm = $this->createForm('AppBundle\Form\UserType', $user);
                $editForm->handleRequest($request);

                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirectToRoute('user_index');
                }

                return $this->render('AppBundle:user:modify_my_account.html.twig', array(
                    'user' => $user,
                    'edit_form' => $editForm->createView(),
                ));
            }
        }
        return $this->redirectToRoute('roadtrip_index');
    }

    /**
     * my profile
     *
     * @Route("/disconnect", name="user_disconnect")
     * @Method("GET")
     */
    public function disconnectAction(Request $request)
    {
        $session = $this->get('session');
        $currentUser = $session->set('currentUser', array());

        return $this->redirectToRoute('homepage');
    }


     /**
     * connect
     *
     * @Route("/se-connecter", name="user_connectForm")
     * @Method("GET")
     */
    public function connectFormAction()
    {
        return $this->render('AppBundle:user:login.html.twig');
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/inscription", name="user_register")
     * @Method({"GET", "POST"})
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()){

                $user->setEnabled(false);
                $token = bin2hex(random_bytes(20));
                $user->setToken($token);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                //send email
                $view = $this->container->get('templating')->render('AppBundle:mails:mail_activate_account.html.twig', [
                    'token'    => $token,
                    'user'     => $user,
                    'id'       => $user->getId()
                ]);

                $message = (new \Swift_Message('Roadtrip: activation de compte'))
                    ->setFrom('noreply@roadmontrip.loc')
                    ->setTo($user->getEmail())
                    ->setBody($view, 'text/html');

                $response = $this->container->get('mailer')->send($message);

                return $this->redirectToRoute('roadtrip_index');
            }
        }

        return $this->render('AppBundle:user:register.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/connexion", name="user_connect")
     * @Method("POST")
     */
    public function connectAction(Request $request)
    {
        $email    = trim($request->request->get('email'));
        $password = trim($request->request->get('password'));

        $em       = $this->get('doctrine')->getManager();
        $query    = $em->createQuery("SELECT u FROM AppBundle\Entity\User u WHERE u.email =:email AND u.enabled = true")
                       ->setParameter('email', $email);

        $user   = $query->getOneOrNullResult();

        if ($user) {
            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);

            if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
                $birthdate = null;
                if($user->getBirthDate() !== null)
                    $birthdate = $user->getBirthDate()->format('d-m-Y');

                $currentUser = array(
                    'id'        => $user->getId(),
                    'firstname' => $user->getFirstName(),
                    'lastname'  => $user->getLastName(),
                    'email'     => $user->getEmail(),
                    'gender'    => $user->getGender(),
                    'birthdate' => $birthdate,
                    'role'      => $user->getRoles()[0]
                );

                $session = $this->get('session');
                $session->set('currentUser', $currentUser);
                return $this->redirectToRoute('user_index');
            } else {
                $this->addFlash('error', 'Email ou mot de passe incorrect');
            }
        } else {
            $this->addFlash('error', 'Email ou mot de passe incorrect');
        }

        return $this->redirectToRoute('user_connectForm');
    }

     /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/loginFb", name="user_login_fb")
     * @Method("GET")
     */
    public function loginFacebookAction(Request $request)
    {
        $config = [
            'callback' => 'http://roadtrip.loc' . $this->generateUrl('user_login_fb'),
            'keys'     => [
                'id'     => $this->container->getParameter('facebook_app_id'),
                'secret' => $this->container->getParameter('facebook_app_secret')
            ]
        ];

        $register = false;

        try {
            $adapter = new \Hybridauth\Provider\Facebook($config);
            $adapter->authenticate();
            $isConnected = $adapter->isConnected();
            $userProfile = $adapter->getUserProfile();

            $birthdate = "";
            if($userProfile->birthYear){
                $month = $userProfile->birthMonth < 10 ? '0'.$userProfile->birthMonth : $userProfile->birthMonth;
                $day   = $userProfile->birthDay < 10 ? '0'.$userProfile->birthDay : $userProfile->birthDay;
                $birthdate = $userProfile->birthYear . '-' . $month . '-' . $day;
            }

            if($userProfile->gender == 'male' || $userProfile->gender == 'female'){
                $gender = strtoupper($userProfile->gender);
            }else{
                $gender = 'MALE';
            }
            $dataPush = array(
                'facebookId' => $userProfile->identifier,
                'user'       => array(
                    'firstName'     => $userProfile->firstName,
                    'lastName'      => $userProfile->lastName,
                    'gender'        => $gender,
                    'email'         => $userProfile->email,
                    'birthdate'     => $birthdate,
                    'plainPassword' => $userProfile->identifier
                )
            );
            $adapter->disconnect();
        }catch(\Exception $e){
            return $this->redirectToRoute('roadtrip_index');
        }

        $facebookId = trim($dataPush['facebookId']);
        $em         = $this->get('doctrine')->getManager();
        $query      = $em->createQuery("SELECT u FROM AppBundle\Entity\User u WHERE u.facebookId =:facebookId")
                       ->setParameter('facebookId', $facebookId);

        $user = $query->getOneOrNullResult();

        //register
        if(!$user){
            $user = new user();

            $user->setFirstname($dataPush['user']['firstName']);
            $user->setLastname($dataPush['user']['lastName']);
            $user->setgender($dataPush['user']['gender']);
            $user->setEmail($dataPush['user']['email']);

            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);
            $encoded = $encoder->encodePassword($dataPush['user']['plainPassword'], $user->getSalt());
            $user->setPassword($encoded);

            $user->setEnabled(true);
            $user->setFacebookId($facebookId);

            $token = bin2hex(random_bytes(20));
            $user->setToken($token);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            if($user->getBirthDate() !== null)
                $birthdate = $user->getBirthDate()->format('d-m-Y');

            $currentUser = array(
                'id'        => $user->getId(),
                'firstname' => $user->getFirstName(),
                'lastname'  => $user->getLastName(),
                'email'     => $user->getEmail(),
                'gender'    => $user->getGender(),
                'birthdate' => $birthdate
            );

            $session = $this->get('session');
            $session->set('currentUser', $currentUser);

        }else{
            //account activated
            if($user->isEnabled()){
                $birthdate = null;
                if($user->getBirthDate() !== null)
                    $birthdate = $user->getBirthDate()->format('d-m-Y');

                $currentUser = array(
                    'id'        => $user->getId(),
                    'firstname' => $user->getFirstName(),
                    'lastname'  => $user->getLastName(),
                    'email'     => $user->getEmail(),
                    'gender'    => $user->getGender(),
                    'birthdate' => $birthdate
                );


                $session = $this->get('session');
                $session->set('currentUser', $currentUser);

                return $this->redirectToRoute('roadtrip_index');

            //account not activated
            }else{
                $token = $user->getToken();
                $register = true;
            }
        }

        if($register){
            //send email
            $view = $this->container->get('templating')->render('AppBundle:mails:mail_activate_account.html.twig', [
                'token'    => $token,
                'user'     => $user,
                'id'       => $user->getId()
            ]);

            $message = \Swift_Message::newInstance()
                ->setSubject('Roadtrip: activation de compte')
                ->setFrom('noreply@roadtrip.loc')
                ->setBody($view, 'text/html');

            // Adds mail send
            $message->setTo($user->getEmail());
            $response = $this->container->get('mailer')->send($message);
        }
        return $this->redirectToRoute('roadtrip_index');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/activate/{id}/{token}", name="user_activate")
     * @Method("GET")
     */
    public function activateAction(User $user, $token, Request $request)
    {
        if ($user && $user->getToken() == $token && !$user->isEnabled()) {
            $user->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $birthdate = null;
            if($user->getBirthDate() !== null)
                $birthdate = $user->getBirthDate()->format('d-m-Y');

            $currentUser = array(
                'id'        => $user->getId(),
                'firstname' => $user->getFirstName(),
                'lastname'  => $user->getLastName(),
                'email'     => $user->getEmail(),
                'gender'    => $user->getGender(),
                'birthdate' => $birthdate,
                'role'      => $user->getRoles()[0]
            );

            $session = $this->get('session');
            $session->set('currentUser', $currentUser);

            //send email
            $view = $this->container->get('templating')->render('AppBundle:mails:mail_register.html.twig', [
                'user'     => $currentUser
            ]);

            $message = \Swift_Message::newInstance()
                ->setSubject('Roadtrip: Compte activé')
                ->setFrom('noreply@roadtrip.loc')
                ->setBody($view, 'text/html');

            // Adds mail send
            $message->setTo($currentUser['email']);
            $response = $this->container->get('mailer')->send($message);

            return $this->redirectToRoute('user_index');
        }
        return $this->redirectToRoute('roadtrip_index');
    }

     /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/reinitialisation/{id}/{token}", name="user_reset_password")
     * @Method({"GET","POST"})
     */
    public function resetPasswordAction(Request $request,user $user, $token)
    {
        $errors = array();
        if($user->getForgotToken() == $token){
            if($this->getRequest()->isMethod('POST')){
                $passwords = $request->request->all()['password'];
                if($passwords[0] == $passwords[1]){
                    $encoder_service = $this->get('security.encoder_factory');
                    $encoder = $encoder_service->getEncoder($user);
                    $encoded = $encoder->encodePassword($passwords[0], $user->getSalt());
                    $user->setPassword($encoded);

                    $user->setForgotToken(bin2hex(random_bytes(20)));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $currentUser = array(
                        'firstname' => $user->getFirstName(),
                        'lastname'  => $user->getLastName(),
                        'gender'    => $user->getGender(),
                        'email'     => $user->getEmail(),
                    );

                    //send email
                    $view = $this->container->get('templating')->render('AppBundle:mails:mail_password_changed.html.twig', [
                        'user' => $currentUser
                    ]);

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Roadtrip: Mot de passe modifié')
                        ->setFrom('noreply@roadtrip.loc')
                        ->setBody($view, 'text/html');

                    // Adds mail send
                    $message->setTo($user->getEmail());
                    $response = $this->container->get('mailer')->send($message);

                    $session = $this->get('session');
                    $session->set('currentUser', $currentUser);

                    return $this->redirectToRoute('user_index');
                }
                $errors[] = 'les mots de passes sont différents';
            }
        }else{
            $errors[] = 'token invalide';
        }

        return $this->render('AppBundle:user:reset_password.html.twig', array(
            'user' => $user,
            'errors' => $errors
        ));
    }

}
