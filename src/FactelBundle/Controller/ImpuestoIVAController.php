<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\ImpuestoIVA;
use FactelBundle\Form\ImpuestoIVAType;
use JMS\SecurityExtraBundle\Annotation\Secure;
/**
 * ImpuestoIVA controller.
 *
 * @Route("/impuesto/iva")
 */
class ImpuestoIVAController extends Controller {

    /**
     * Lists all ImpuestoIVA entities.
     *
     * @Route("/", name="impuesto_iva")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FactelBundle:ImpuestoIVA')->findAll();
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
     * Creates a new ImpuestoIVA entity.
     *
     * @Route("/", name="impuesto_iva_create")
     * @Method("POST")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("FactelBundle:ImpuestoIVA:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ImpuestoIVA();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('impuesto_iva'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ImpuestoIVA entity.
     *
     * @param ImpuestoIVA $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ImpuestoIVA $entity) {
        $form = $this->createForm(new ImpuestoIVAType(), $entity, array(
            'action' => $this->generateUrl('impuesto_iva_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ImpuestoIVA entity.
     *
     * @Route("/nuevo", name="impuesto_iva_new")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function newAction() {
        $entity = new ImpuestoIVA();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }



    /**
     * Displays a form to edit an existing ImpuestoIVA entity.
     *
     * @Route("/{id}/editar", name="impuesto_iva_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:ImpuestoIVA')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ImpuestoIVA entity.');
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
     * Creates a form to edit a ImpuestoIVA entity.
     *
     * @param ImpuestoIVA $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ImpuestoIVA $entity) {
        $form = $this->createForm(new ImpuestoIVAType(), $entity, array(
            'action' => $this->generateUrl('impuesto_iva_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing ImpuestoIVA entity.
     *
     * @Route("/{id}", name="impuesto_iva_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("FactelBundle:ImpuestoIVA:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:ImpuestoIVA')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ImpuestoIVA entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('impuesto_iva'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ImpuestoIVA entity.
     *
     * @Route("/{id}", name="impuesto_iva_delete")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:ImpuestoIVA')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ImpuestoIVA entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('impuesto_iva'));
    }

    /**
     * Creates a form to delete a ImpuestoIVA entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl('impuesto_iva_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
