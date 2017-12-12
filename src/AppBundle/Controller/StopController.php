<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Stop controller.
 *
 * @Route("stop")
 */
class StopController extends Controller
{
    /**
     * Lists all stop entities.
     *
     * @Route("/", name="stop_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stops = $em->getRepository('AppBundle:Stop')->findAll();

        return $this->render('stop/index.html.twig', array(
            'stops' => $stops,
        ));
    }

    /**
     * Creates a new stop entity.
     *
     * @Route("/new", name="stop_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $stop = new Stop();
        $form = $this->createForm('AppBundle\Form\StopType', $stop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stop);
            $em->flush();

            return $this->redirectToRoute('stop_show', array('id' => $stop->getId()));
        }

        return $this->render('stop/new.html.twig', array(
            'stop' => $stop,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a stop entity.
     *
     * @Route("/{id}", name="stop_show")
     * @Method("GET")
     */
    public function showAction(Stop $stop)
    {
        $deleteForm = $this->createDeleteForm($stop);

        return $this->render('stop/show.html.twig', array(
            'stop' => $stop,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing stop entity.
     *
     * @Route("/{id}/edit", name="stop_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Stop $stop)
    {
        $deleteForm = $this->createDeleteForm($stop);
        $editForm = $this->createForm('AppBundle\Form\StopType', $stop);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('stop_edit', array('id' => $stop->getId()));
        }

        return $this->render('stop/edit.html.twig', array(
            'stop' => $stop,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a stop entity.
     *
     * @Route("/{id}", name="stop_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Stop $stop)
    {
        $form = $this->createDeleteForm($stop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stop);
            $em->flush();
        }

        return $this->redirectToRoute('stop_index');
    }

    /**
     * Creates a form to delete a stop entity.
     *
     * @param Stop $stop The stop entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Stop $stop)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stop_delete', array('id' => $stop->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
