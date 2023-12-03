<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\PtoEmision;
use FactelBundle\Form\PtoEmisionType;

/**
 * PtoEmision controller.
 *
 * @Route("/ptoemision")
 */
class PtoEmisionController extends Controller {

    /**
     * Lists all PtoEmision entities.
     *
     * @Route("/", name="ptoemision")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager();
        $deleteForms = array();
        if ($this->get("security.context")->isGranted("ROLE_ADMIN")) {
            $entities = $em->getRepository('FactelBundle:PtoEmision')->findPtosEmision();
        } else {
            $user = $this->get("security.context")->getToken()->getUser();
            $entities = $em->getRepository('FactelBundle:PtoEmision')->findPtosEmisionEmisor($user->getEmisor()->getId());
        }


        foreach ($entities as $entity) {
            $deleteForms[$entity[0]->getId()] = $this->createDeleteForm($entity[0]->getId())->createView();
        }
        return array(
            'entities' => $entities,
            'deleteForms' => $deleteForms,
        );
    }

    /**
     * Creates a new PtoEmision entity.
     *
     * @Route("/", name="ptoemision_create")
     * @Method("POST")
     * @Template("FactelBundle:PtoEmision:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new PtoEmision();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ptoemision'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a PtoEmision entity.
     *
     * @param PtoEmision $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PtoEmision $entity) {
        $form = $this->createForm(new PtoEmisionType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('ptoemision_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new PtoEmision entity.
     *
     * @Route("/nuevo", name="ptoemision_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new PtoEmision();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a PtoEmision entity.
     *
     * @Route("/{id}", name="ptoemision_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:PtoEmision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PtoEmision entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PtoEmision entity.
     *
     * @Route("/{id}/edit", name="ptoemision_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:PtoEmision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PtoEmision entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a PtoEmision entity.
     *
     * @param PtoEmision $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PtoEmision $entity) {
        $form = $this->createForm(new PtoEmisionType($this->get("security.context"), $entity->getId()), $entity, array(
            'action' => $this->generateUrl('ptoemision_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing PtoEmision entity.
     *
     * @Route("/{id}", name="ptoemision_update")
     * @Method("PUT")
     * @Template("FactelBundle:PtoEmision:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:PtoEmision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PtoEmision entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ptoemision'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a PtoEmision entity.
     *
     * @Route("/{id}", name="ptoemision_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:PtoEmision')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PtoEmision entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ptoemision'));
    }

    /**
     * Creates a form to delete a PtoEmision entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl('ptoemision_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
