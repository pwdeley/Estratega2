<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Plan;
use FactelBundle\Form\PlanType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Plan controller.
 *
 * @Route("/facturacion/plan")
 */
class PlanController extends Controller
{

    /**
     * Lists all Plan entities.
     *
     * @Route("/", name="plan")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FactelBundle:Plan')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Plan entity.
     *
     * @Route("/", name="plan_create")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("POST")
     * @Template("FactelBundle:Plan:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Plan();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('plan'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Plan entity.
     *
     * @param Plan $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Plan $entity)
    {
        $form = $this->createForm(new PlanType(), $entity, array(
            'action' => $this->generateUrl('plan_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Plan entity.
     *
     * @Route("/new", name="plan_new")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Plan();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Plan entity.
     *
     * @Route("/{id}/edit", name="plan_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Plan')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Plan entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Plan entity.
    *
    * @param Plan $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Plan $entity)
    {
        $form = $this->createForm(new PlanType(), $entity, array(
            'action' => $this->generateUrl('plan_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing Plan entity.
     *
     * @Route("/{id}", name="plan_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("FactelBundle:Plan:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Plan')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Plan entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('plan'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Plan entity.
     *
     * @Route("/{id}", name="plan_delete")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:Plan')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Plan entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('plan'));
    }

    /**
     * Creates a form to delete a Plan entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plan_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
