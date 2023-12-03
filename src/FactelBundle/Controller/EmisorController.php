<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Emisor;
use FactelBundle\Form\EmisorType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Emisor controller.
 *
 * @Route("/emisor")
 */
class EmisorController extends Controller {

    /**
     * Lists all Emisor entities.
     *
     * @Route("/", name="emisor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        if ($this->get("security.context")->isGranted("ROLE_ADMIN")) {
            $em = $this->getDoctrine()->getManager();
            $planes = $em->getRepository('FactelBundle:Plan')->findBy(array("activo" => true));
            $entities = $em->getRepository('FactelBundle:Emisor')->findAll();
            $deleteForms = array();
            foreach ($entities as $entity) {
                $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
            }
            return array(
                'entities' => $entities,
                'deleteForms' => $deleteForms,
                'planes' => $planes
            );
        } else {
            $user = $this->get("security.context")->getToken()->getUser();
            return $this->render(
                            'FactelBundle:Emisor:show.html.twig', array('entity' => $user->getEmisor())
            );
        }
    }

    /**
     * Creates a new Emisor entity.
     *
     * @Route("/", name="emisor_create")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("POST")
     * @Template("FactelBundle:Emisor:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Emisor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $fullDirArchivo = $form['dirDocAutorizados']->getData();
            if (!is_dir($fullDirArchivo)) {
                mkdir($fullDirArchivo, 0777, true);
            }
            $dirAutorizado = $fullDirArchivo;
            if (!is_dir($dirAutorizado)) {
                mkdir($dirAutorizado, 0777, true);
            }
            $entity->setDirDocAutorizados($dirAutorizado);
            $newLogo = $form['logo']->getData();
            $newLogo->move($fullDirArchivo, $newLogo->getClientOriginalName());
            $newFirma = $form['firma']->getData();
            $newFirma->move($fullDirArchivo, $newFirma->getClientOriginalName());

            $entity->setDirLogo($fullDirArchivo . "/" . $newLogo->getClientOriginalName());
            $entity->setDirFirma($fullDirArchivo . "/" . $newFirma->getClientOriginalName());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('emisor'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Emisor entity.
     *
     * @Route("/configurar-plan", name="config-plan")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("POST")
     */
    public function configAction(Request $request) {
        $fechaInicio = $request->request->get("fechaInicial");
        $fechaFin = $request->request->get("fechaFinal");
        $idPlan = $request->request->get("plan");
        $idEmisor = $request->request->get("idEmisor");
        $em = $this->getDoctrine()->getManager();
        $emisor = new Emisor();
        $emisor = $em->getRepository('FactelBundle:Emisor')->find($idEmisor);
        $plan = $em->getRepository('FactelBundle:Plan')->find($idPlan);
        $fechaModificada = str_replace("/", "-", $fechaInicio);
        $fecha = new \DateTime($fechaModificada);

        $emisor->setFechaInicio($fecha);
        $fechaModificada = str_replace("/", "-", $fechaFin);
        $fecha = new \DateTime($fechaModificada);
        $emisor->setFechaFin($fecha);
        $emisor->setPlan($plan);
        $emisor->setCantComprobante(0);
        $em->persist($emisor);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
                'confirm', "Se ha configurado el plan " . $plan->getNombre() . " al emisor " . $emisor->getRazonSocial()
        );
        return $this->redirect($this->generateUrl('emisor'));
    }

    /**
     * Creates a form to create a Emisor entity.
     *
     * @param Emisor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Emisor $entity) {
        $form = $this->createForm(new EmisorType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('emisor_create'),
            'method' => 'POST',
            'attr' => array('id' => "create")
        ));


        return $form;
    }

    /**
     * Displays a form to edit an existing Emisor entity.
     *
     * @Route("/borrar-doc-prueba", name="borrar_doc")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Method("GET")
     */
    public function deleteDocumentoPrueba() {
        $em = $this->getDoctrine()->getManager();
        $emisorId = $em->getRepository('FactelBundle:User')->findEmisorId($this->get("security.context")->gettoken()->getuser()->getId());
        $emisor = new Emisor();
        $emisor = $em->getRepository('FactelBundle:Emisor')->find($emisorId);
        foreach ($emisor->getFacturas() as $factura) {
            if ($factura->getAmbiente() == "1") {
                foreach ($factura->getMensajes() as $mensaje) {
                    $em->remove($mensaje);
                }

                foreach ($factura->getFacturasHasProducto() as $facturaHasProducto) {
                    foreach ($facturaHasProducto->getImpuestos() as $impuesto) {
                        $em->remove($impuesto);
                    }
                    foreach ($facturaHasProducto->getDetallesAdicionales() as $detalle) {
                        $em->remove($detalle);
                    }
                    $em->remove($facturaHasProducto);
                }
                $reembolsos = $factura->getReembolsos();
                foreach ($reembolsos as $reembolso) {
                    $em->remove($reembolso);
                }
                
                $em->remove($factura);
            }
        }

        foreach ($emisor->getNotasCredito() as $notaCredito) {
            if ($notaCredito->getAmbiente() == "1") {
                foreach ($notaCredito->getMensajes() as $mensaje) {
                    $em->remove($mensaje);
                }

                foreach ($notaCredito->getNotaCreditoHasProducto() as $notaCreditoHasProducto) {
                    foreach ($notaCreditoHasProducto->getImpuestos() as $impuesto) {
                        $em->remove($impuesto);
                    }
                    foreach ($notaCreditoHasProducto->getDetallesAdicionales() as $detalle) {
                        $em->remove($detalle);
                    }
                    $em->remove($notaCreditoHasProducto);
                }
                $em->remove($notaCredito);
            }
        }
        foreach ($emisor->getNotasDebito() as $notaDebito) {
            if ($notaDebito->getAmbiente() == "1") {
                foreach ($notaDebito->getMensajes() as $mensaje) {
                    $em->remove($mensaje);
                }

                foreach ($notaDebito->getMotivos() as $motivo) {
                    $em->remove($motivo);
                }
                foreach ($notaDebito->getImpuestos() as $impuesto) {
                    $em->remove($impuesto);
                }
                $em->remove($notaDebito);
            }
        }

        foreach ($emisor->getRetencion() as $retencion) {
            if ($retencion->getAmbiente() == "1") {
                foreach ($retencion->getMensajes() as $mensaje) {
                    $em->remove($mensaje);
                }

                foreach ($retencion->getImpuestos() as $impuestos) {
                    $em->remove($impuestos);
                }
                $em->remove($retencion);
            }
        }

        foreach ($emisor->getGuias() as $guia) {
            if ($guia->getAmbiente() == "1") {
                foreach ($guia->getMensajes() as $mensaje) {
                    $em->remove($mensaje);
                }

                foreach ($guia->getGuiasHasProducto() as $guiaHasProducto) {
                    $em->remove($guiaHasProducto);
                }
                $em->remove($guia);
            }
        }
        
        foreach ($emisor->getLiquidacionescompra() as $liquidacion) {
            if ($liquidacion->getAmbiente() == "1") {
                foreach ($liquidacion->getMensajes() as $mensaje) {
                    $em->remove($mensaje);
                }
                foreach ($liquidacion->getLiquidacionesCompraHasProducto() as $liquidacionCompraHasProducto) {
                     foreach ($liquidacionCompraHasProducto->getImpuestos() as $impuesto) {
                        $em->remove($impuesto);
                    }
                    $em->remove($liquidacionCompraHasProducto);
                }
                
                $reembolsos = $liquidacion->getReembolsos();
                foreach ($reembolsos as $reembolso) {
                    $em->remove($reembolso);
                }
                
                $em->remove($liquidacion);
            }
        }
        $em->flush();
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Displays a form to create a new Emisor entity.
     *
     * @Route("/nuevo", name="emisor_new")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Emisor();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Emisor entity.
     *
     * @Route("/{id}", name="emisor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Emisor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Emisor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Emisor entity.
     *
     * @Route("/{id}/editar", name="emisor_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Emisor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Emisor entity.');
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
     * Creates a form to edit a Emisor entity.
     *
     * @param Emisor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Emisor $entity) {
        $form = $this->createForm(new EmisorType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('emisor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('id' => "update")
        ));

        return $form;
    }

    /**
     * Edits an existing Emisor entity.
     *
     * @Route("/{id}", name="emisor_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_ADMIN, ROLE_EMISOR_ADMIN")
     * @Template("FactelBundle:Emisor:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Emisor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Emisor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if (isset($editForm['dirDocAutorizados'])) {
                $fullDirArchivo = $editForm['dirDocAutorizados']->getData();
                if (!is_dir($fullDirArchivo)) {
                    mkdir($fullDirArchivo, 0777, true);
                }
            } else {
                $fullDirArchivo = $entity->getDirDocAutorizados();
            }
            $dirAutorizado = $fullDirArchivo;
            $entity->setDirDocAutorizados($dirAutorizado);
            $newLogo = $editForm['logo']->getData();
            $newLogo->move($fullDirArchivo, $newLogo->getClientOriginalName());
            $newFirma = $editForm['firma']->getData();
            $newFirma->move($fullDirArchivo, $newFirma->getClientOriginalName());

            $entity->setDirLogo($fullDirArchivo . "/" . $newLogo->getClientOriginalName());
            $entity->setDirFirma($fullDirArchivo . "/" . $newFirma->getClientOriginalName());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('emisor_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Emisor entity.
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/{id}", name="emisor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:Emisor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Emisor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('emisor'));
    }

    /**
     * Creates a form to delete a Emisor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl('emisor_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
