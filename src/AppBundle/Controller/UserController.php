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
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()){
                $user->setEnabled(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('user_show', array('id' => $user->getId()));
            }
        }

        return $this->render('AppBundle:user:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('AppBundle:user:show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
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
            'callback' => $this->container->getParameter('sitemapDomain') . '' . $this->generateUrl('front_user_login_fb'),
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

                $this->get('by.user')->save($user);
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
            // $view = $this->container->get('templating')->render('AppBundle:Mails:mail_activate_account.html.twig', [
            //     'token'    => $token,
            //     'user'     => $user,
            //     'id'       => $user->getId()
            // ]);

            // $message = \Swift_Message::newInstance()
            //     ->setSubject($this->get('translator')->trans('front.mail.user.activate.object', array(), 'front'))
            //     ->setFrom('noreply@roadtrip.loc')
            //     ->setBody($view, 'text/html');

            // // Adds mail send
            // $message->setTo($dataPush['user']['email']);
            // $response = $this->container->get('mailer')->send($message);
        }
        return $this->redirect('/');
    }

}
