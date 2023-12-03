<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\ImpuestoICE;
use FactelBundle\Form\ImpuestoICEType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * ImpuestoICE controller.
 *
 * @Route("/impuesto/ice")
 */
class ImpuestoICEController extends Controller {

    /**
     * Lists all ImpuestoICE entities.
     *
     * @Route("/", name="impuesto_ice")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FactelBundle:ImpuestoICE')->findAll();
        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }
        return array(
            'entities' => $entities,
            'deleteForms' => $deleteForms,
        );
    }

    /**
     * Creates a new ImpuestoICE entity.
     *
     * @Route("/", name="impuesto_ice_create")
     * @Method("POST")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("FactelBundle:ImpuestoICE:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ImpuestoICE();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('impuesto_ice'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ImpuestoICE entity.
     *
     * @param ImpuestoICE $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ImpuestoICE $entity) {
        $form = $this->createForm(new ImpuestoICEType(), $entity, array(
            'action' => $this->generateUrl('impuesto_ice_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new ImpuestoICE entity.
     *
     * @Route("/nuevo", name="impuesto_ice_new")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function newAction() {
        $entity = new ImpuestoICE();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ImpuestoICE entity.
     *
     * @Route("/{id}/editar", name="impuesto_ice_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:ImpuestoICE')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ImpuestoICE entity.');
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
     * Creates a form to edit a ImpuestoICE entity.
     *
     * @param ImpuestoICE $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ImpuestoICE $entity) {
        $form = $this->createForm(new ImpuestoICEType(), $entity, array(
            'action' => $this->generateUrl('impuesto_ice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing ImpuestoICE entity.
     *
     * @Route("/{id}", name="impuesto_ice_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("FactelBundle:ImpuestoICE:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:ImpuestoICE')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ImpuestoICE entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('impuesto_ice'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ImpuestoICE entity.
     *
     * @Route("/{id}", name="impuesto_ice_delete")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:ImpuestoICE')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ImpuestoICE entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('impuesto_ice'));
    }

    /**
     * Creates a form to delete a ImpuestoICE entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl('impuesto_ice_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
