<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Establecimiento;
use FactelBundle\Form\EstablecimientoType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Establecimiento controller.
 *
 * @Route("/establecimiento")
 */
class EstablecimientoController extends Controller {

    /**
     * Lists all Establecimiento entities.
     *
     * @Route("/", name="establecimiento")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $deleteForms = array();
        if ($this->get("security.context")->isGranted("ROLE_ADMIN")) {
            $entities = $em->getRepository('FactelBundle:Establecimiento')->findEstablecimientos();
        } else {
            $user = $this->get("security.context")->getToken()->getUser();
            $entities = $em->getRepository('FactelBundle:Establecimiento')->findEstablecimientosEmisor($user->getEmisor()->getId());
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
     * Creates a new Establecimiento entity.
     *
     * @Route("/", name="establecimiento_create")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Method("POST")
     * @Template("FactelBundle:Establecimiento:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Establecimiento();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $fullDirArchivo = $entity->getEmisor()->getDirDocAutorizados();

            $newLogo = $form['logo']->getData();
            if ($newLogo != null) {
                $newLogo->move($fullDirArchivo, $newLogo->getClientOriginalName());
                $entity->setDirLogo($fullDirArchivo . "/" . $newLogo->getClientOriginalName());
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('establecimiento'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Establecimiento entity.
     *
     * @param Establecimiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Establecimiento $entity) {
        $form = $this->createForm(new EstablecimientoType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('establecimiento_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Establecimiento entity.
     *
     * @Route("/nuevo", name="establecimiento_new")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Establecimiento();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Establecimiento entity.
     *
     * @Route("/{id}", name="establecimiento_show")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Establecimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Establecimiento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Establecimiento entity.
     *
     * @Route("/{id}/editar", name="establecimiento_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Establecimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Establecimiento entity.');
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
     * Creates a form to edit a Establecimiento entity.
     *
     * @param Establecimiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Establecimiento $entity) {
        $form = $this->createForm(new EstablecimientoType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('establecimiento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('id' => "update")
        ));

        return $form;
    }

    /**
     * Edits an existing Establecimiento entity.
     *
     * @Route("/{id}", name="establecimiento_update")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Method("PUT")
     * @Template("FactelBundle:Establecimiento:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Establecimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Establecimiento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $fullDirArchivo = $entity->getEmisor()->getDirDocAutorizados();
            $newLogo = $editForm['logo']->getData();
            if ($newLogo != null) {
                $newLogo->move($fullDirArchivo, $newLogo->getClientOriginalName());
                $entity->setDirLogo($fullDirArchivo . "/" . $newLogo->getClientOriginalName());
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('establecimiento_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Establecimiento entity.
     *
     * @Route("/{id}", name="establecimiento_delete")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:Establecimiento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Establecimiento entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('establecimiento'));
    }

    /**
     * Creates a form to delete a Establecimiento entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl('establecimiento_delete', array('id' => $id)))
                        ->setAttribute('id', 'delete')
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
