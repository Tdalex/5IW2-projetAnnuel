<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Route("user")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('AppBundle:user:index.html.twig', array(
            'users' => $users,
        ));
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
                $view = $this->container->get('templating')->render('AppBundle:Mails:mail_activate_account.html.twig', [
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

                return $this->redirectToRoute('user_index');
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
     * @Route("/connect", name="user_connect")
     * @Method("POST")
     */
    public function connectAction(Request $request)
    {
        $email    = trim($request->request->get('email'));
        $password = trim($request->request->get('plainPassword'));

        $em       = $this->get('doctrine')->getManager();
        $query    = $em->createQuery("SELECT u FROM AppBundle\Entity\User u WHERE u.email =:email AND u.enabled = true")
                       ->setParameter('email', $email);

        $user   = $query->getOneOrNullResult();
        $errors = array();

        if ($user) {
            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);

            if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt()) || $password == $user->getPassword()) {
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
                return $this->redirectToRoute('my_account');
            } else {
                $errors[] = "Email ou mot de passe incorrect";
            }
        } else {
            $errors[] = "Email ou mot de passe incorrect";
        }

        return $this->redirect('/');
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/mon-compte", name="my_account")
     * @Method({"GET", "POST"})
     */
    public function myAccountAction(Request $request)
    {
        $session = $this->get('session');
        $currentUser = $session->get('currentUser');

        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOne();



        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('AppBundle:user:edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
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
            'callback' => 'roadtrip.loc/' . $this->generateUrl('front_user_login_fb'),
            'keys'     => [
                'id'     => $this->container->getParameter('facebook_app_id'),
                'secret' => $this->container->getParameter('facebook_app_secret')
            ]
        ];

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

            $dataPush  = array(
                'facebookId' => $userProfile->identifier,
                'user'       => array(
                    'firstName' => $userProfile->firstName,
                    'lastName'  => $userProfile->lastName,
                    'gender'    => $gender,
                    'email'     => $userProfile->email,
                    'birthdate' => $birthdate,
                    'plainPassword' => array(
                        'first'  => $userProfile->identifier,
                        'second' => $userProfile->identifier,
                    )
                )
            );
            $adapter->disconnect();
        }catch(\Exception $e){
            return $this->redirect('/');
        }

        $facebookId = trim($dataPush['facebookId']);
        $em         = $this->get('doctrine')->getManager();
        $query      = $em->createQuery("SELECT u FROM AppBundle\Entity\User u WHERE u.facebookId =:facebookId")
                       ->setParameter('facebookId', $facebookId);

        $user = $query->getOneOrNullResult();

        //register
        if(!$user){
            $user = new user();
            if($dataPush['user']['plainPassword']['first']){
                $encoder_service = $this->get('security.encoder_factory');
                $encoder = $encoder_service->getEncoder($user);
                $encoded = $encoder->encodePassword($user, $dataPush['user']['plainPassword']['first']);
                $user->setPassword($encoded);
            }

            // Bind value with form
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($dataPush);
            if ($form->isValid()) {
                if($dataPush['user']['plainPassword']['first']){
                    $encoder_service = $this->get('security.encoder_factory');
                    $encoder = $encoder_service->getEncoder($user);
                    $encoded = $encoder->encodePassword($user, $dataPush['user']['plainPassword']['first']);
                    $user->setPassword($encoded);
                }
                $user->setEnabled(false);
                $user->setFacebookId($facebookId);

                $token = bin2hex(random_bytes(20));
                $user->setToken($token);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $register = true;
            }
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

                return $this->redirect('/');

            //account not activated
            }else{
                $token = $user->getToken();
                $register = true;
            }
        }

        if($register){
            //send email
            $view = $this->container->get('templating')->render('AppBundle:Mails:mail_activate_account.html.twig', [
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
        return $this->redirect('/');
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
            $view = $this->container->get('templating')->render('AppBundle:Mails:mail_register.html.twig', [
                'user'     => $currentUser
            ]);

            $message = \Swift_Message::newInstance()
                ->setSubject('Roadtrip: Compte activé')
                ->setFrom('noreply@roadtrip.loc')
                ->setBody($view, 'text/html');

            // Adds mail send
            $message->setTo($currentUser['email']);
            $response = $this->container->get('mailer')->send($message);

            return $this->redirectToRoute('my_account');
        }
        return $this->redirect('/');
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
                    $encoded = $encoder->encodePassword($user, $passwords[0]);
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
                    $view = $this->container->get('templating')->render('AppBundle:Mails:mail_password_changed.html.twig', [
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
                    return $this->redirectToRoute('my_account');
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
