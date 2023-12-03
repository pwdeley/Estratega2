<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Cliente;
use FactelBundle\Form\ClienteType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

require_once 'reader.php';

/**
 * Cliente controller.
 *
 * @Route("/cliente")
 */
class ClienteController extends Controller {

    /**
     * Lists all Cliente entities.
     *
     * @Route("/", name="cliente")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
       return array();
    }
    

    /**
     * Lists all Cliente entities.
     *
     * @Route("/clientes", name="all_client")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     */
    public function clientesAction() {
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
        $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
        $count = $em->getRepository('FactelBundle:Cliente')->cantidadClientes($emisorId);
        $entities = $em->getRepository('FactelBundle:Cliente')->findClientes($sSearch, $iDisplayStart, $iDisplayLength, $emisorId);
        $totalDisplayRecords = $count;

        if ($sSearch != "") {
            $totalDisplayRecords = count($entities);
        }
        $clienteArray = array();
        $i = 0;
        foreach ($entities as $entity) {
            $clienteArray[$i] = [$entity->getId(), $entity->getTipoIdentificacion(), $entity->getIdentificacion(), $entity->getNombre(), $entity->getCelular(), $entity->getCorreoElectronico(), $entity->getDireccion()];
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
     * Creates a new Cliente entity.
     *
     * @Route("/", name="cliente_create")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     * @Template("FactelBundle:Cliente:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Cliente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if (!$this->get("security.context")->isGranted("ROLE_ADMIN")) {
            $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();

            $emisor = $em->getRepository('FactelBundle:Emisor')->find($emisorId);
            $entity->setEmisor($emisor);
        }
        
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cliente'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cliente $entity) {
        $form = $this->createForm(new ClienteType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('cliente_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Cliente entity.
     *
     * @Route("/nuevo", name="cliente_new")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Cliente();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/cargar", name="cliente_load")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     * @Template()
     */
    public function cargarClienteAction() {
        $form = $this->createClienteForm();

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     *
     * @Route("/cargar", name="cliente_create_masiva")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR_ADMIN")
     */
    public function createClienteAction(Request $request) {
        $form = $this->createClienteForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $emisorId = $em->getRepository('FactelBundle:User')->findEmisorId($this->get("security.context")->gettoken()->getuser()->getId());
            $emisor = $em->getRepository('FactelBundle:Emisor')->find($emisorId);

            $newFile = $form['Clientes']->getData();
            date_default_timezone_set("America/Guayaquil");
            $fecha = date("dmYHis");
            $fileName = "ProductoAutomatico-" . $fecha . ".xls";
            $newFile->move($this->getUploadRootDir(), $fileName);
            $data = new Spreadsheet_Excel_Reader();
            $data->Spreadsheet_Excel_Reader();
            $data->setOutputEncoding('CP1251');
            $clienteCreado =0;
            $clienteActualizado =0;

            $data->read($this->getUploadRootDir() . '/' . $fileName);
            for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                if (isset($data->sheets[0]['cells'][$i][1]) && isset($data->sheets[0]['cells'][$i][2]) && isset($data->sheets[0]['cells'][$i][3])) {
                    $identificacion = utf8_encode($data->sheets[0]['cells'][$i][3]);
                    $cliente = $em->getRepository('FactelBundle:Cliente')->findOneBy(array("identificacion" => $identificacion, "emisor" => $emisorId));
                    
                    if (!$cliente) {
                        $cliente = new \FactelBundle\Entity\Cliente();
                        $cliente->setEmisor($emisor);
                        $clienteCreado++;
                    }else{
                       $clienteActualizado++; 
                    }
                    $cliente->setNombre(utf8_encode($data->sheets[0]['cells'][$i][1]));
                    $cliente->setTipoIdentificacion($data->sheets[0]['cells'][$i][2]);
                    $cliente->setIdentificacion($identificacion);
                    if (isset($data->sheets[0]['cells'][$i][4])) {
                        $cliente->setDireccion(utf8_encode($data->sheets[0]['cells'][$i][4]));
                    }
                    if (isset($data->sheets[0]['cells'][$i][5])) {
                        $cliente->setCelular($data->sheets[0]['cells'][$i][5]);
                    }
                    if (isset($data->sheets[0]['cells'][$i][6])) {
                        $cliente->setCorreoElectronico(utf8_encode($data->sheets[0]['cells'][$i][6]));
                    }
                    $em->persist($cliente);
                }
            }

            $em->flush();
        }
         $this->get('session')->getFlashBag()->add(
                    'notice', "Clientes Creados: " . $clienteCreado . ". Clientes Actualizados: " . $clienteActualizado
            );
 
        return $this->redirect($this->generateUrl('cliente'));
    }

    /**
     * Finds and displays a Cliente entity.
     *
     * @Route("/{id}", name="cliente_show")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }
        if (!$this->get("security.context")->isGranted("ROLE_ADMIN")) {
            $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
            if ($emisorId != $entity->getEmisor()->getId()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Solo puede ver los clientes de su empresa"
                );
                return $this->redirect($this->generateUrl('home', array()));
            }
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Cliente entity.
     *
     * @Route("/{id}/editar", name="cliente_edit")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Cliente')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }
        if (!$this->get("security.context")->isGranted("ROLE_ADMIN")) {
            $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
            if ($emisorId != $entity->getEmisor()->getId()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Solo puede editar los clientes de su empresa"
                );
                return $this->redirect($this->generateUrl('home', array()));
            }
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
     * Creates a form to edit a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Cliente $entity) {
        $form = $this->createForm(new ClienteType($this->get("security.context")), $entity, array(
            'action' => $this->generateUrl('cliente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Cliente entity.
     *
     * @Route("/{id}", name="cliente_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_EMISOR")
     * @Template("FactelBundle:Cliente:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }
        if (!$this->get("security.context")->isGranted("ROLE_ADMIN")) {
            $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
            if ($emisorId != $entity->getEmisor()->getId()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Solo puede actualizar los clientes de su empresa"
                );
                return $this->redirect($this->generateUrl('home', array()));
            }
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cliente_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Cliente entity.
     *
     * @Route("/{id}", name="cliente_delete")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:Cliente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cliente entity.');
            }
            if (!$this->get("security.context")->isGranted("ROLE_ADMIN")) {
                $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
                if ($emisorId != $entity->getEmisor()->getId()) {
                    $this->get('session')->getFlashBag()->add(
                            'notice', "Solo puede borrar los clientes de su empresa"
                    );
                    return $this->redirect($this->generateUrl('home', array()));
                }
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cliente'));
    }

    /**
     * Creates a form to delete a Cliente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl('cliente_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    public function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/upload';
    }

    public function createClienteForm() {

        $builder = $this->createFormBuilder();
        $builder->setAction($this->generateUrl('cliente_create_masiva'));
        $builder->setMethod('POST');

        $builder->add('Clientes', 'file');

        $builder->add('import', 'submit', array(
            'label' => 'Cargar',
            'attr' => array('class' => 'import'),
        ));
        return $builder->getForm();
    }

}
