<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\ImpuestoIRBPNR;
use FactelBundle\Form\ImpuestoIRBPNRType;
use JMS\SecurityExtraBundle\Annotation\Secure;
/**
 * ImpuestoIRBPNR controller.
 *
 * @Route("/impuesto/irbpnr")
 */
class ImpuestoIRBPNRController extends Controller {

    /**
     * Lists all ImpuestoIRBPNR entities.
     *
     * @Route("/", name="impuesto_irbpnr")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FactelBundle:ImpuestoIRBPNR')->findAll();
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
     * Creates a new ImpuestoIRBPNR entity.
     *
     * @Route("/", name="impuesto_irbpnr_create")
     * @Method("POST")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("FactelBundle:ImpuestoIRBPNR:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ImpuestoIRBPNR();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('impuesto_irbpnr'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ImpuestoIRBPNR entity.
     *
     * @param ImpuestoIRBPNR $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ImpuestoIRBPNR $entity) {
        $form = $this->createForm(new ImpuestoIRBPNRType(), $entity, array(
            'action' => $this->generateUrl('impuesto_irbpnr_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new ImpuestoIRBPNR entity.
     *
     * @Route("/nuevo", name="impuesto_irbpnr_new")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function newAction() {
        $entity = new ImpuestoIRBPNR();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ImpuestoIRBPNR entity.
     *
     * @Route("/{id}/editar", name="impuesto_irbpnr_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:ImpuestoIRBPNR')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ImpuestoIRBPNR entity.');
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
     * Creates a form to edit a ImpuestoIRBPNR entity.
     *
     * @param ImpuestoIRBPNR $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ImpuestoIRBPNR $entity) {
        $form = $this->createForm(new ImpuestoIRBPNRType(), $entity, array(
            'action' => $this->generateUrl('impuesto_irbpnr_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }

    /**
     * Edits an existing ImpuestoIRBPNR entity.
     *
     * @Route("/{id}", name="impuesto_irbpnr_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("FactelBundle:ImpuestoIRBPNR:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:ImpuestoIRBPNR')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ImpuestoIRBPNR entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('impuesto_irbpnr'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ImpuestoIRBPNR entity.
     *
     * @Route("/{id}", name="impuesto_irbpnr_delete")
     * @Method("DELETE")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:ImpuestoIRBPNR')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ImpuestoIRBPNR entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('impuesto_irbpnr'));
    }

    /**
     * Creates a form to delete a ImpuestoIRBPNR entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl('impuesto_irbpnr_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
