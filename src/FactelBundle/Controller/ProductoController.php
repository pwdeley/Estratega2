<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Producto;
use FactelBundle\Form\ProductoType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

require_once 'reader.php';

/**
 * Producto controller.
 *
 * @Route("/producto")
 */
class ProductoController extends Controller {

    /**
     * Lists all Producto entities.
     *
     * @Route("/", name="producto")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $deleteForms = array();
        if ($this->get("security.context")->isGranted("ROLE_ADMIN")) {
            $entities = $em->getRepository('FactelBundle:Producto')->findProductos();
        } else {
            $user = $this->get("security.context")->getToken()->getUser();
            $entities = $em->getRepository('FactelBundle:Producto')->findProductosEmisor($user->getEmisor()->getId());
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
     * Lists all Cliente entities.
     *
     * @Route("/productos", name="all_product")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     */
    public function productosAction() {

        if (isset($_GET['sEcho'])) {
            $sEcho = $_GET['sEcho'];
        }
        if (isset($_GET['iDisplayStart'])) {
            $iDisplayStart = intval($_GET['iDisplayStart']);
        }
        if (isset($_GET['iDisplayLength'])) {
            $iDisplayLength = intval($_GET['iDisplayLength']);
        }
        if (isset($_GET['sSearch'])) {
            $sSearch = $_GET['sSearch'];
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->get("security.context")->getToken()->getUser();
        $idEmisor = $user->getEmisor()->getId();
        $count = $em->getRepository('FactelBundle:Producto')->cantidadProductos($idEmisor);
        $entities = $em->getRepository('FactelBundle:Producto')->findTodosProductos($sSearch, $iDisplayStart, $iDisplayLength, $idEmisor);
        $totalDisplayRecords = $count;

        if ($sSearch != "") {
            $totalDisplayRecords = count($entities);
        }
        $clienteArray = array();
        $i = 0;
        foreach ($entities as $entity) {
            $iva = $entity->getImpuestoIVA()->getcodigoPorcentaje();
            $tarifa = $entity->getImpuestoIVA()->getTarifa();
            $ice = $entity->getImpuestoICE();
            if ($ice != null) {
                $ice = true;
            } else {
                $ice = false;
            }

            $IRBPNR = $entity->getImpuestoIRBPNR();
            if ($IRBPNR != null) {
                $IRBPNR = true;
            } else {
                $IRBPNR = false;
            }
            $clienteArray[$i] = [$entity->getId(), $entity->getCodigoPrincipal(), $entity->getCodigoAuxiliar(), $entity->getNombre(), $entity->getPrecioUnitario(), $iva, $ice, $IRBPNR, $tarifa, $entity->getTieneSubsidio(), $entity->getPrecioSinSubsidio()];
            $i++;
        }

        $arr = array(
            "iTotalRecords" => (int) $count,
            "iTotalDisplayRecords" => (int) $totalDisplayRecords,
            'aaData' => $clienteArray
        );

        $post_data = json_encode($arr);

        return new Response($post_data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Creates a new Producto entity.
     *
     * @Route("/", name="producto_create")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Template("FactelBundle:Producto:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Producto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->get("security.context")->getToken()->getUser();
            $producto = $em->getRepository("FactelBundle:Producto")->findBy(array("codigoPrincipal" => $entity->getCodigoPrincipal(), "emisor" => $user->getEmisor()));
            if ($producto) {
                $this->get('session')->getFlashBag()->add('error', "Ya existe un producto con el codigo principal " . $entity->getCodigoPrincipal());
            } else {
                $tieneSubsidio = $request->request->get("tieneSubsidio");
                $precioSinSubsidio = $request->request->get("precioSinSubsidio");
                if ($tieneSubsidio && boolval($tieneSubsidio)) {
                    $entity->setTieneSubsidio(true);
                    $entity->setPrecioSinSubsidio($precioSinSubsidio);
                } else {
                    $entity->setTieneSubsidio(false);
                    $entity->setPrecioSinSubsidio(0);
                }
                $em->persist($entity);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('producto'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Producto entity.
     *
     * @param Producto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Producto $entity) {
        $form = $this->createForm(new ProductoType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('producto_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Producto entity.
     *
     * @Route("/nuevo", name="producto_new")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Template()
     */
    public function newAction() {
        $entity = new Producto();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/cargar", name="producto_load")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Template()
     */
    public function cargarProductoAction() {
        $form = $this->createProductoForm();

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     *
     * @Route("/cargar", name="producto_create_masivo")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     */
    public function createProductoAction(Request $request) {
        $form = $this->createProductoForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $emisorId = $em->getRepository('FactelBundle:User')->findEmisorId($this->get("security.context")->gettoken()->getuser()->getId());
            $emisor = $em->getRepository('FactelBundle:Emisor')->find($emisorId);

            $newFile = $form['Productos']->getData();
            date_default_timezone_set("America/Guayaquil");
            $fecha = date("dmYHis");
            $fileName = "ProductoAutomatico-" . $fecha . ".xls";
            $newFile->move($this->getUploadRootDir(), $fileName);
            $data = new Spreadsheet_Excel_Reader();
            $data->Spreadsheet_Excel_Reader();
            $data->setOutputEncoding('UTF-8');
            $productoCreado = 0;
            $productoActualizado = 0;

            $data->read($this->getUploadRootDir() . '/' . $fileName);
            for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

                if (isset($data->sheets[0]['cells'][$i][1]) && isset($data->sheets[0]['cells'][$i][2]) && isset($data->sheets[0]['cells'][$i][4]) && isset($data->sheets[0]['cells'][$i][5])) {
                    $codigoPrincipal = utf8_encode($data->sheets[0]['cells'][$i][2]);
                    $producto = $em->getRepository('FactelBundle:Producto')->findOneBy(array("codigoPrincipal" => $codigoPrincipal, "emisor" => $emisorId));
                    if (!$producto) {
                        $producto = new \FactelBundle\Entity\Producto();
                        $producto->setEmisor($emisor);
                        $productoCreado++;
                    } else {
                        $productoActualizado ++;
                    }

                    $producto->setNombre(utf8_encode($data->sheets[0]['cells'][$i][1]));
                    $producto->setCodigoPrincipal(utf8_encode($data->sheets[0]['cells'][$i][2]));
                    if (isset($data->sheets[0]['cells'][$i][3])) {
                        $producto->setCodigoAuxiliar(utf8_encode($data->sheets[0]['cells'][$i][3]));
                    }
                    $producto->setPrecioUnitario($data->sheets[0]['cells'][$i][4]);


                    $codigoIVA = $data->sheets[0]['cells'][$i][5];
                    $impuestoIVA = $em->getRepository('FactelBundle:ImpuestoIVA')->findOneBy(array("codigoPorcentaje" => $codigoIVA));

                    if ($impuestoIVA) {
                        $producto->setImpuestoIVA($impuestoIVA);
                    }

                    if (isset($data->sheets[0]['cells'][$i][6])) {
                        $codigoICE = $data->sheets[0]['cells'][$i][6];
                        $impuestoICE = $em->getRepository('FactelBundle:ImpuestoICE')->findOneBy(array("codigoPorcentaje" => $codigoICE));
                        if ($impuestoICE) {
                            $producto->setImpuestoICE($impuestoICE);
                        }
                    }
                    if (isset($data->sheets[0]['cells'][$i][7])) {
                        $codigoIRBPNR = $data->sheets[0]['cells'][$i][7];
                        $impuestoIRBPNR = $em->getRepository('FactelBundle:ImpuestoIRBPNR')->findOneBy(array("codigoPorcentaje" => $codigoIRBPNR));
                        if ($codigoIRBPNR) {
                            $producto->setImpuestoIRBPNR($impuestoIRBPNR);
                        }
                    }
                    $em->persist($producto);
                }
            }

            $em->flush();
        }
        $this->get('session')->getFlashBag()->add(
                'notice', "Productos Creados: " . $productoCreado . ". Productos Actualizados: " . $productoActualizado
        );
        return $this->redirect($this->generateUrl('producto'));
    }

    /**
     * Finds and displays a Producto entity.
     *
     * @Route("/{id}", name="producto_show")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Producto')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Producto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Producto entity.
     *
     * @Route("/{id}/editar", name="producto_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Producto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Producto entity.');
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
     * Creates a form to edit a Producto entity.
     *
     * @param Producto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Producto $entity) {
        $form = $this->createForm(new ProductoType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('producto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Producto entity.
     *
     * @Route("/{id}", name="producto_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Template("FactelBundle:Producto:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Producto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Producto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $tieneSubsidio = $request->request->get("tieneSubsidio");
            $precioSinSubsidio = $request->request->get("precioSinSubsidio");
            if ($tieneSubsidio && boolval($tieneSubsidio)) {
                $entity->setTieneSubsidio(true);
                $entity->setPrecioSinSubsidio($precioSinSubsidio);
            } else {
                $entity->setTieneSubsidio(false);
                $entity->setPrecioSinSubsidio(0);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('producto_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Producto entity.
     *
     * @Route("/{id}", name="producto_delete")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:Producto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Producto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('producto'));
    }

    /**
     * Creates a form to delete a Producto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl('producto_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    public function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/upload';
    }

    public function createProductoForm() {

        $builder = $this->createFormBuilder();
        $builder->setAction($this->generateUrl('producto_create_masivo'));
        $builder->setMethod('POST');

        $builder->add('Productos', 'file');

        $builder->add('import', 'submit', array(
            'label' => 'Cargar',
            'attr' => array('class' => 'import'),
        ));
        return $builder->getForm();
    }

}
