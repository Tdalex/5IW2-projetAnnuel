<?php

namespace AppBundle\Controller;

use AppBundle\Entity\IsLiked;
use AppBundle\Entity\Roadtrip;
use AppBundle\Entity\User;
use AppBundle\Service\IsLikedManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * IsLiked controller.
 *
 * @Route("favoris")
 */
class IsLikedController extends Controller
{
    /**
     * Lists all isliked entities.
     *
     * @Route("/", name="isliked_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $userId = $session->get('currentUser')['id'];
        //$user = $em->getRepository('AppBundle:User')->findOneBy(array('id'=> $userId));
        $favorites = [];

        $limit = 6;
        $offset = 0;

        $isliked = $em->getRepository('AppBundle:IsLiked')->showFavoriteLimited($userId, $limit, $offset);
        if(!empty($isliked)) {
            foreach($isliked as $like){
                $favorites[] = $like->getRoadtripId();
            }
        }

        return $this->render('AppBundle:isliked:index.html.twig', array(
            'favorites'   => $favorites,
        ));
    }

    /**
     * Lists all isliked entities.
     *
     * @Route("/tousMesFavoris/{page}", name="isliked_all", defaults={"page": 1})
     * @Method({"GET", "POST"})
     */
    public function allFavoritesAction(Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $userId = $session->get('currentUser')['id'];
        //$user = $em->getRepository('AppBundle:User')->findOneBy(array('id'=> $userId));
        $favorites = [];

        $limit = 24;
        $offset = $limit * ($page - 1);

        $isliked = $em->getRepository('AppBundle:IsLiked')->showFavoriteLimited($userId, $limit, $offset);
        if(!empty($isliked)) {
            foreach($isliked as $like){
                $favorites[] = $like->getRoadtripId();
            }
        }

        return $this->render('AppBundle:isliked:allFavorites.html.twig', array(
            'favorites'   => $favorites,
            'page' => $page
        ));
    }

    /**
     * Lists all isliked entities.
     *
     * @Route("/add/{roadtripId}/{userId}", name="isliked_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request, $roadtripId, $userId, IsLikedManager $isLikedManager)
    {
        $em = $this->getDoctrine()->getManager();
        $like = new IsLiked();
        $roadtrip = $em->getRepository('AppBundle:Roadtrip')->findOneBy(array('id' => $roadtripId));
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

        $like->setRoadtripId($roadtrip);
        $like->setUserId($user);

        $em->persist($like);
        $em->flush();

        $getLike = 1;

        return new JsonResponse(
            array(
                'view' => $this->renderView(
                    'AppBundle:partials:buttons_liked.html.twig', array(
                        'like' => $getLike
                    )
            ))
        );
    }

    /**
     * Lists all isliked entities.
     *
     * @Route("/remove/{roadtripId}/{userId}", name="isliked_remove")
     * @Method({"GET", "POST"})
     */
    public function removeAction(Request $request, $roadtripId, $userId, IsLikedManager $isLikedManager)
    {
        $em = $this->getDoctrine()->getManager();
        //$roadtrip = $em->getRepository('AppBundle:Roadtrip')->findOneBy(array('id' => $roadtripId));
        //$user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
        $like = $em->getRepository('AppBundle:IsLiked')->findOneBy(array('roadtripId' => $roadtripId, 'userId' => $userId));
        $em->remove($like);
        $em->flush();

        $getLike = 0;

        return new JsonResponse(
            array(
                'view' => $this->renderView(
                    'AppBundle:partials:buttons_liked.html.twig', array(
                        'like' => $getLike                    )
                ))
        );
    }
}