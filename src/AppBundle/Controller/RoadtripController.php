<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Roadtrip;
use AppBundle\Entity\Stop;
use AppBundle\Service\IsLikedManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\RoadtripManager;

/**
 * Roadtrip controller.
 *
 * @Route("roadtrip")
 */
class RoadtripController extends Controller
{
    /**
     * Lists all roadtrip entities.
     *
     * @Route("/", name="roadtrip_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, RoadtripManager $roadtripManager)
    {
        $em = $this->getDoctrine()->getManager();
        $datapush = $request->request->all();
        $filters = array();

        if(isset($datapush['filters'])){
            $filters = $datapush['filters'];
            $durations = explode(';',$datapush['durations']);
            $nbStops = explode(';',$datapush['nbStops']);
            $filters['durations']['min'] = $durations[0];
            $filters['durations']['max'] = $durations[1];
            $filters['nbStops']['min']   = $nbStops[0];
            $filters['nbStops']['max']   = $nbStops[1];
        }

        $roadtrips = $em->getRepository('AppBundle:Roadtrip')->search($filters);
        $allFilters   = $roadtripManager->getFilters($em);
        return $this->render('AppBundle:roadtrip:index.html.twig', array(
            'choices'   => $filters,
            'roadtrips' => $roadtrips,
            'filters'   => $allFilters
        ));
    }

    /**
     * Template temporaire.
     *
     * @Route("/accueil", name="roadtrip_accueil")
     * @Method("GET")
     */
    public function accueilAction()
    {

        return $this->render('AppBundle:roadtrip:accueil.html.twig');
    }

    /**
     * Creates a new roadtrip entity.
     *
     * @Route("/new", name="roadtrip_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $roadtrip = new Roadtrip();
        $user = $request->getSession()->get('currentUser');
        $owner = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $user));
        $form = $this->createForm('AppBundle\Form\RoadtripType', $roadtrip, array('action' => $this->generateUrl('roadtrip_new')));
        $form->handleRequest($request);

        if ($user) {
            if ($form->isSubmitted()) {
                if(!$form->isValid()) {
                    $errors = [];
                    if ($form->count() > 0) {
                        foreach ($form->all() as $child) {
                            /**
                             * @var \Symfony\Component\Form\Form $child
                             */
                            if (!$child->isValid()) {
                                $errors[$child->getName()] = $this->getErrorMessages($child);
                            }
                        }
                    }
                    /**
                     * @var \Symfony\Component\Form\FormError $error
                     */
                    foreach ($form->getErrors() as $key => $error) {
                        $errors[] = $error->getMessage();
                    }
                    dump($errors);
                }
                else {
                    $roadtrip->setIsRemoved(false);
                    $roadtrip->setOwner($owner);
                    $roadtrip->setNbStops(0);
                    $em->persist($roadtrip);
                    $em->flush();

                    //mise à jour de la colonne "roadtripStop" de chaque stop avec l'identifiant du roadtrip en cours de création
                    $idRoadtrip = $roadtrip->getId();
                    $rt = $em->getRepository('AppBundle:Roadtrip')->findOneBy(array('id' => $idRoadtrip));
                    $roadtrip->getStopStart()->setRoadTripStop($rt);
                    $roadtrip->getStopEnd()->setRoadTripStop($rt);
                    $stops = $roadtrip->getStops();
                    $roadtrip->setNbStops(0);
                    if (!empty($stops)){
                        $roadtrip->setNbStops(count($stops));
                        foreach ($stops as $stop) {
                            $stop->setRoadTripStop($rt);
                        }
                    }
                    $owner->addOwned($roadtrip);
                    $em->flush();

                    return $this->redirectToRoute('roadtrip_show', array('slug' => $roadtrip->getSlug()));
                }
            }
        } else {
            return $this->redirectToRoute('roadtrip_index');
        }

        return $this->render('AppBundle:roadtrip:new.html.twig', array(
            'roadtrip' => $roadtrip,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a roadtrip entity.
     *
     * @Route("/{slug}", name="roadtrip_show")
     * @Method("GET")
     */
    public function showAction(Roadtrip $roadtrip, IsLikedManager $isLikedManager)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $likes = [];
        if($session->get('currentUser')) {
            $currentUser = $session->get('currentUser');
            $likes = $em->getRepository('AppBundle:IsLiked')->isLike($roadtrip->getId(), $currentUser['id']);
        }


        $deleteForm = $this->createDeleteForm($roadtrip);
        $review = $roadtrip->getReview();
        $note = 0;
        $compteur = 0;
        $commentaires = [];
        foreach ($review as $a) {
            $note += $a->getNote();
            $commentaires [$compteur]['date'] = $a->getCreatedAt();
            $commentaires [$compteur]['user'] = $a->getUserId()->getFirstName().' '.$a->getUserId()->getLastName();
            $commentaires [$compteur]['commentaire'] = $a->getCommentaire();
            $compteur ++;

        }

        if($compteur !== 0) {
            $moyenne = round($note / $compteur, 1);
        } else {
            $moyenne = "Aucune note";
        }
        $like = empty($likes) ? 0 : 1;

        return $this->render('AppBundle:roadtrip:show.html.twig', array(
            'roadtrip' => $roadtrip,
            'delete_form' => $deleteForm->createView(),
            'moyenne' => $moyenne,
            'commentaires' => $commentaires,
            'like' => $like
        ));
    }

    /**
     * Displays a form to edit an existing roadtrip entity.
     *
     * @Route("/{slug}/edit", name="roadtrip_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Roadtrip $roadtrip)
    {
        //récupération de la liste de tous les stops en enlevant le départ et la destination pour pouvoir les afficher dans le form
        $startStop = $roadtrip->getStopStart()->getId();
        $endStop = $roadtrip->getStopEnd()->getId();
        $allStops = $roadtrip->getStops()->toArray();
        $stops = array();
        foreach($allStops as $stop) {
            if($stop->getId() == $startStop || $stop->getId() == $endStop) {
                continue;
            }
            else {
                $stops[] = $stop;
            }
        }

        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($roadtrip);
        $editForm = $this->createForm('AppBundle\Form\RoadtripType', $roadtrip);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if(!$editForm->isValid()) {
                $errors = [];
                if ($editForm->count() > 0) {
                    foreach ($editForm->all() as $child) {
                        /**
                         * @var \Symfony\Component\Form\Form $child
                         */
                        if (!$child->isValid()) {
                            $errors[$child->getName()] = $this->getErrorMessages($child);
                        }
                    }
                }
                /**
                 * @var \Symfony\Component\Form\FormError $error
                 */
                foreach ($editForm->getErrors() as $key => $error) {
                    $errors[] = $error->getMessage();
                }
                dump($errors);
            }
            else {
                $rtForm = $request->request->get('roadtrip');
                $idRoadtrip = $roadtrip->getId();
                $rt = $em->getRepository('AppBundle:Roadtrip')->findOneBy(array('id' => $idRoadtrip));
                $stopRt = $em->getRepository('AppBundle:Stop')->findBy(array('roadTripStop' => $idRoadtrip));
                foreach ($stopRt as $s) {
                    $em->remove($s);
                }
                //Attribution de chaque valeur récupérées depuis le form à chaque colonne correspondante
                $sStart = $rtForm['stopStart'];
                $sEnd = $rtForm['stopEnd'];
                $sts = $rtForm['stops'];
                unset($sts['__name__']);
                $rtSStop = new Stop();
                $rtEStop = new Stop();
                $rtStops = new ArrayCollection();
                //startStop
                $rtSStop->setAddress($sStart['address']);
                $rtSStop->setTitle($sStart['title']);
                $rtSStop->setDescription($sStart['description']);
                $rtSStop->setLat($sStart['lat']);
                $rtSStop->setlon($sStart['lon']);
                $rtSStop->setRoadTripStop($rt);
                $roadtrip->setStopStart($rtSStop);
                //endStop
                $rtEStop->setAddress($sEnd['address']);
                $rtEStop->setTitle($sEnd['title']);
                $rtEStop->setDescription($sEnd['description']);
                $rtEStop->setLat($sEnd['lat']);
                $rtEStop->setlon($sEnd['lon']);
                $rtEStop->setRoadTripStop($rt);
                $roadtrip->setStopEnd($rtEStop);
                //stops
                $roadtrip->setNbStops(0);
                if($sts){
                    $roadtrip->setNbStops(count($sts));
                }
                foreach ($sts as $s) {
                    $st = new Stop();
                    $st->setAddress($s['address']);
                    $st->setTitle($s['title']);
                    $st->setDescription($s['description']);
                    $st->setLat($s['lat']);
                    $st->setlon($s['lon']);
                    $st->setRoadTripStop($rt);
                    $roadtrip->addStop($st);
                }
                $em->flush();

                return $this->redirectToRoute('roadtrip_show', array('slug' => $roadtrip->getSlug()));
            }
        }

        return $this->render('AppBundle:roadtrip:edit.html.twig', array(
            'roadtrip' => $roadtrip,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'stops' => $stops,
        ));
    }

    /**
     * Deletes a roadtrip entity.
     *
     * @Route("/{slug}", name="roadtrip_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Roadtrip $roadtrip)
    {
        $form = $this->createDeleteForm($roadtrip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($roadtrip);
            $em->flush();
        }

        return $this->redirectToRoute('roadtrip_index');
    }


    /**
     * Creates a form to delete a roadtrip entity.
     *
     * @param Roadtrip $roadtrip The roadtrip entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Roadtrip $roadtrip)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('roadtrip_delete', array('slug' => $roadtrip->getSlug())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * search roadtrip.
     *
     * @param Roadtrip $roadtrip The roadtrip entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function serchAction(Roadtrip $roadtrip)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('roadtrip_delete', array('slug' => $roadtrip->getSlug())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
