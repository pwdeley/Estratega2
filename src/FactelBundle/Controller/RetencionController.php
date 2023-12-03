<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Retencion;
use FactelBundle\Form\RetencionType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

require_once 'ProcesarComprobanteElectronico.php';

/**
 * Retencion controller.
 *
 * @Route("/comprobantes/retencion")
 */
class RetencionController extends Controller {

    /**
     * Lists all Retencion entities.
     *
     * @Route("/", name="retencion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        return array();
    }

    /**
     * Lists all Retenciones entities.
     *
     * @Route("/retenciones", name="all_retenciones")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     */
    public function retencionesAction() {
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
        $emisorId = null;
        $idPtoEmision = null;
        if ($this->get("security.context")->isGranted("ROLE_EMISOR_ADMIN")) {
            $emisorId = $em->getRepository('FactelBundle:User')->findEmisorId($this->get("security.context")->gettoken()->getuser()->getId());
        } else {
            $idPtoEmision = $em->getRepository('FactelBundle:PtoEmision')->findIdPtoEmisionByUsuario($this->get("security.context")->gettoken()->getuser()->getId());
        }
        $count = $em->getRepository('FactelBundle:Retencion')->cantidadRetenciones($idPtoEmision, $emisorId);
        $entities = $em->getRepository('FactelBundle:Retencion')->findRetenciones($sSearch, $iDisplayStart, $iDisplayLength, $idPtoEmision, $emisorId);
        $totalDisplayRecords = $count;

        if ($sSearch != "") {
            $totalDisplayRecords = count($entities);
        }
        $retencionArray = array();
        $i = 0;
        foreach ($entities as $entity) {
            $fechaAutorizacion = "";
            $fechaAutorizacion = $entity->getFechaAutorizacion() != null ? $entity->getFechaAutorizacion()->format("d/m/Y H:i:s") : "";
            $retencionArray[$i] = [$entity->getId(), $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial(), $entity->getCliente()->getNombre(), $entity->getFechaEmision()->format("d/m/Y"), $fechaAutorizacion, $entity->getPeriodoFiscal(), $entity->getEstado()];
            $i++;
        }

        $arr = array(
            "iTotalRecords" => (int) $count,
            "iTotalDisplayRecords" => (int) $totalDisplayRecords,
            'aaData' => $retencionArray
        );

        $post_data = json_encode($arr);

        return new Response($post_data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Creates a new Retencion entity.
     *
     * @Route("/", name="retencion_create")
     * @Method("POST")
     * @Template("FactelBundle:Retencion:new.html.twig")
     */
    public function createAction(Request $request) {
        $secuencial = $request->request->get("secuencial");
        $fechaEmision = $request->request->get("fechaEmision");
        $idCliente = $request->request->get("idCliente");
        $nombre = $request->request->get("nombre");
        $celular = $request->request->get("celular");
        $email = $request->request->get("email");
        $tipoIdentificacion = $request->request->get("tipoIdentificacion");
        $identificacion = $request->request->get("identificacion");
        $direccion = $request->request->get("direccion");
        $nuevoCliente = $request->request->get("nuevoCliente");

        $periodoFiscal = $request->request->get("periodoFiscal");
        $idRetencion = $request->request->get("idRetencion");
        $idFacturaCompra = $request->request->get("idFacturaCompra");

        $texto = "";
        $campos = "";

        $cantidadErrores = 0;
        if ($secuencial == '') {
            $campos .= "Secuencial, ";
            $cantidadErrores++;
        }
        if ($fechaEmision == '') {
            $campos .= "Fecha Emision, ";
            $cantidadErrores++;
        }
        if ($nombre == '') {
            $campos .= "Nombre Cliente, ";
            $cantidadErrores++;
        }
        if ($tipoIdentificacion == '') {
            $campos .= "Tipo Identificacion, ";
            $cantidadErrores++;
        }
        if ($identificacion == '') {
            $campos .= "Identificacion, ";
            $cantidadErrores++;
        }
        if ($periodoFiscal == '') {
            $campos .= "Periodo Fiscal, ";
            $cantidadErrores++;
        }

        if ($cantidadErrores > 0) {
            if ($cantidadErrores == 1) {
                $texto = "El campo <strong>" . $campos . "</strong> no puede estar vacios";
            } else {
                $texto = "Los campos " . $campos . " no pueden estar vacios";
            }
            $this->get('session')->getFlashBag()->add(
                    'notice', $texto
            );

            return $this->redirect($this->generateUrl('retencion_new', array()));
        }

        $em = $this->getDoctrine()->getManager();
        if ($idRetencion != null && $idRetencion != '') {
            $entity = new Retencion();
            $entity = $em->getRepository('FactelBundle:Retencion')->find($idRetencion);
            if (!is_null($entity)) {
                $mensajes = $entity->getMensajes();
                foreach ($mensajes as $mensaje) {
                    $em->remove($mensaje);
                }

                foreach ($entity->getImpuestos() as $impuesto) {
                    $em->remove($impuesto);
                }

                $em->flush();
            }
        } else {
            $entity = new Retencion();
        }

        $em = $this->getDoctrine()->getManager();
        $ptoEmision = $em->getRepository('FactelBundle:PtoEmision')->findPtoEmisionEstabEmisorByUsuario($this->get("security.context")->gettoken()->getuser()->getId());

        if ($ptoEmision != null && count($ptoEmision) > 0) {
            $establecimiento = $ptoEmision[0]->getEstablecimiento();
            $emisor = $establecimiento->getEmisor();

            $entity->setEstado("CREADA");
            $entity->setAmbiente($emisor->getAmbiente());
            $entity->setTipoEmision($emisor->getTipoEmision());
            $entity->setSecuencial($secuencial);
            $entity->setClaveAcceso($this->claveAcceso($entity, $emisor, $establecimiento, $ptoEmision[0], $fechaEmision));

            $fechaModificada = str_replace("/", "-", $fechaEmision);
            $fecha = new \DateTime($fechaModificada);

            $entity->setFechaEmision($fecha);
            $cliente = $em->getRepository('FactelBundle:Cliente')->find($idCliente);
            if ($nuevoCliente) {
                $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
                $cliente = $em->getRepository('FactelBundle:Cliente')->findOneBy(array("identificacion" => $identificacion, "emisor" => $emisorId));
                if ($cliente == null) {
                    $cliente = new \FactelBundle\Entity\Cliente();
                }
                $emisor = $em->getRepository('FactelBundle:Emisor')->find($emisorId);
                $cliente->setEmisor($emisor);
            }

            $cliente->setNombre($nombre);
            $cliente->setTipoIdentificacion($tipoIdentificacion);
            $cliente->setIdentificacion($identificacion);
            if ($celular != null && $celular != "") {
                $cliente->setCelular($celular);
            }
            if ($email != null && $email != "") {
                $cliente->setCorreoElectronico($email);
            }
            $cliente->setDireccion($direccion);
            $em->persist($cliente);
            $em->flush();


            $entity->setCliente($cliente);
            $entity->setEmisor($emisor);
            $entity->setEstablecimiento($establecimiento);
            $entity->setPtoEmision($ptoEmision[0]);
            $entity->setPeriodoFiscal($periodoFiscal);
            $codImpuesto = $request->request->get("tipoImpuesto");
            $codRetencion = $request->request->get("codRetencion");
            $porcentaje = $request->request->get("porcentaje");
            $baseImponible = $request->request->get("baseImponible");

            $documento = $request->request->get("documento");
            $tipoDoc = $request->request->get("tipoDoc");
            $fecha = $request->request->get("fecha");
            foreach ($codImpuesto as $clave => $valor) {
                $impuestoComprobanteRetencion = new \FactelBundle\Entity\ImpuestoComprobanteRetencion();
                $impuestoComprobanteRetencion->setCodigo($codImpuesto[$clave]);
                $impuestoComprobanteRetencion->setCodigoRetencion($codRetencion[$clave]);
                $impuestoComprobanteRetencion->setBaseImponible($baseImponible[$clave]);
                $impuestoComprobanteRetencion->setPorcentajeRetener($porcentaje[$clave]);

                $total = round(floatval($baseImponible[$clave]) * floatval($porcentaje[$clave]) / 100, 2);
                $impuestoComprobanteRetencion->setValorRetenido($total);
                $impuestoComprobanteRetencion->setNumDocSustento($documento[$clave]);

                $fechaDocSustento = str_replace("/", "-", $fecha[$clave]);
                $fechaFormateada = new \DateTime($fechaDocSustento);

                $impuestoComprobanteRetencion->setFechaEmisionDocSustento($fechaFormateada);
                $impuestoComprobanteRetencion->setCodDocSustento($tipoDoc[$clave]);
                $impuestoComprobanteRetencion->setRetencion($entity);
                $entity->addImpuesto($impuestoComprobanteRetencion);
            }


            $em->persist($entity);
            $em->flush();



            if ($idRetencion == null || $idRetencion == '') {
                if ($idFacturaCompra != null) {
                    $facturaCompra = $em->getRepository('FactelBundle:Compra')->find($idFacturaCompra);
                    if ($facturaCompra) {
                        $facturaCompra->setRetencionCreadaId($entity->getId());
                    }
                    $em->persist($facturaCompra);
                }


                $ptoEmision[0]->setSecuencialRetencion($ptoEmision[0]->getSecuencialRetencion() + 1);
                $em->persist($ptoEmision[0]);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
        } else {
            throw $this->createNotFoundException('El usuario del sistema no tiene asignado un Punto de Emision.');
        }
    }

    private function eliminarReferenciaEnFacturaCompra($retencionId) {

        $em = $this->getDoctrine()->getManager();
        $compra = $em->getRepository('FactelBundle:Compra')->findOneBy(array("retencionCreadaId" =>$retencionId));
        if ($compra != null) {
           $compra->setRetencionCreadaId(null);
           $em->persist($compra);
           $em->flush();
        }
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/anular/{id}", name="retencion_anular")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function anularAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Retencion')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la Retencion con ID = ' + $id);
        }
        $entity->setEstado("ANULADA");
        $em->persist($entity);
        $em->flush();
        $this->eliminarReferenciaEnFacturaCompra($id);
        return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/eliminar/{id}", name="retencion_eliminar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function eliminarAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Retencion')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la retencion con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "No se puede eliminar un documento autorizado"
            );
            return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
        }

        foreach ($entity->getMensajes() as $mensaje) {
            $em->remove($mensaje);
        }

        foreach ($entity->getImpuestos() as $impuestos) {
            $em->remove($impuestos);
        }
        $em->remove($entity);
        $em->flush();
        $this->eliminarReferenciaEnFacturaCompra($id);
        return $this->redirect($this->generateUrl('retencion'));
    }

    /**
     *
     * @Route("/procesar/{id}", name="retencion_procesar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function procesarAccion($id) {

        $entity = new Retencion();
        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $respuesta = null;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Retencion')->findRetencionById($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Retencion con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Este comprobante electronico ya fue autorizado"
            );
            return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
        }
        $emisor = $entity->getEmisor();
        $hoy = date("Y-m-d");
        if ($emisor->getPlan() != null && $emisor->getFechaFin()) {
            if ($hoy > $emisor->getFechaFin()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Su plan ha caducado por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
            }
            if ($emisor->getCantComprobante() > $emisor->getPlan()->getCantComprobante()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Ha superado el numero de comprobantes contratado en su plan, por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
            }
        }
        $configApp = new \configAplicacion();
        $configApp->dirFirma = $emisor->getDirFirma();
        $configApp->passFirma = $emisor->getPassFirma();
        $configApp->dirAutorizados = $emisor->getDirDocAutorizados();
        if ($entity->getEstablecimiento()->getDirLogo() != "") {
            $configApp->dirLogo = $entity->getEstablecimiento()->getDirLogo();
        } else {
            $configApp->dirLogo = $emisor->getDirLogo();
        }

        $configCorreo = new \configCorreo();
        $configCorreo->correoAsunto = "Nuevo Comprobante Electronico";
        $configCorreo->correoHost = $emisor->getServidorCorreo();
        $configCorreo->correoPass = $emisor->getPassCorreo();
        $configCorreo->correoPort = $emisor->getPuerto();
        $configCorreo->correoRemitente = $emisor->getCorreoRemitente();
        $configCorreo->sslHabilitado = $emisor->getSSL();
        $emailCopiaOculta = null;
        if ($this->get("security.context")->gettoken()->getuser()->getCopiarEmail()) {
            $emailCopiaOculta = $this->get("security.context")->gettoken()->getuser()->getEmail();
        }
        if ($entity->getEstablecimiento()->getEmailCopia() && $entity->getEstablecimiento()->getEmailCopia() != "") {
            if ($emailCopiaOculta != "") {
                $emailCopiaOculta = $emailCopiaOculta . "," . $entity->getEstablecimiento()->getEmailCopia();
            } else {
                $emailCopiaOculta = $entity->getEstablecimiento()->getEmailCopia();
            }
        }

        if ($emailCopiaOculta) {
            $configCorreo->BBC = $emailCopiaOculta;
        }
        if ($entity->getEstado() != "PROCESANDOSE") {
            $retencion = new \comprobanteRetencion();
            $retencion->configAplicacion = $configApp;
            $retencion->configCorreo = $configCorreo;

            $retencion->ambiente = $entity->getAmbiente();
            $retencion->tipoEmision = $entity->getTipoEmision();
            $retencion->razonSocial = $emisor->getRazonSocial();
            if ($entity->getEstablecimiento()->getNombreComercial() != "") {
                $retencion->nombreComercial = $entity->getEstablecimiento()->getNombreComercial();
            } else if ($emisor->getNombreComercial() != "") {
                $retencion->nombreComercial = $emisor->getNombreComercial();
            }
            $retencion->ruc = $emisor->getRuc(); //[Ruc]
            $retencion->codDoc = "07";
            $retencion->establecimiento = $entity->getEstablecimiento()->getCodigo();
            $retencion->ptoEmision = $entity->getPtoEmision()->getCodigo();
            $retencion->secuencial = $entity->getSecuencial();
            $retencion->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
            $retencion->dirMatriz = $emisor->getDireccionMatriz();
            $retencion->dirEstablecimiento = $entity->getEstablecimiento()->getDireccion();
            if ($emisor->getContribuyenteEspecial() != "") {
                $retencion->contribuyenteEspecial = $emisor->getContribuyenteEspecial();
            }
            $retencion->obligadoContabilidad = $emisor->getObligadoContabilidad();
            $retencion->tipoIdentificacionSujetoRetenido = $entity->getCliente()->getTipoIdentificacion();
            $retencion->razonSocialSujetoRetenido = $entity->getCliente()->getNombre();
            $retencion->identificacionSujetoRetenido = $entity->getCliente()->getIdentificacion();


            $impuestoArray = array();
            $impuestos = $entity->getImpuestos();
            foreach ($impuestos as $impuestoRetencion) {
                $impuesto = new \impuestoComprobanteRetencion(); // Impuesto del detalle
                $impuesto->codigo = $impuestoRetencion->getCodigo();
                $impuesto->codigoRetencion = $impuestoRetencion->getCodigoRetencion();
                $impuesto->baseImponible = $impuestoRetencion->getBaseImponible();
                $impuesto->porcentajeRetener = $impuestoRetencion->getPorcentajeRetener();
                $impuesto->valorRetenido = $impuestoRetencion->getValorRetenido();
                $impuesto->codDocSustento = $impuestoRetencion->getCodDocSustento();
                $impuesto->numDocSustento = $impuestoRetencion->getNumDocSustento();
                $impuesto->fechaEmisionDocSustento = $impuestoRetencion->getFechaEmisionDocSustento()->format("d/m/Y");

                $impuestoArray[] = $impuesto;
            }
            $retencion->periodoFiscal = $entity->getPeriodoFiscal();
            $retencion->impuestos = $impuestoArray;
            $camposAdicionales = array();
            foreach ($entity->getComposAdic() as $campoAdic) {
                $campoAdicional = new \campoAdicional();
                $campoAdicional->nombre = $campoAdic->getNombre();
                $campoAdicional->valor = $campoAdic->getValor();

                $camposAdicionales [] = $campoAdic;
            }
            if ($emisor->getRegimenRimpe()) {
                $retencion->regimenRimpes = "Contribuyente Negocio Popular - Régimen RIMPE";
            }
            if ($emisor->getRegimenRimpe1()) {
                $retencion->regimenReimpes1 = "Contribuyente Régimen RIMPE";
            }

            if ($emisor->getResolucionAgenteRetencion()) {
                $retencion->agenteRetencion = $emisor->getResolucionAgenteRetencion();
            }

            $cliente = $entity->getCliente();
            if ($cliente->getDireccion() != "") {
                $campoAdic = new \campoAdicional();
                $campoAdic->nombre = "Direccion";
                $campoAdic->valor = $cliente->getDireccion();

                $camposAdicionales [] = $campoAdic;
            }
            if ($cliente->getCelular() != "") {
                $campoAdic = new \campoAdicional();
                $campoAdic->nombre = "Telefono";
                $campoAdic->valor = $cliente->getCelular();

                $camposAdicionales [] = $campoAdic;
            }
            if ($cliente->getTipoIdentificacion() != "07" && $cliente->getCorreoElectronico() != "") {
                $campoAdic = new \campoAdicional();
                $campoAdic->nombre = "Email";
                $campoAdic->valor = $cliente->getCorreoElectronico();

                $camposAdicionales [] = $campoAdic;
            }
            if (count($camposAdicionales) > 0) {
                $retencion->infoAdicional = $camposAdicionales;
            }

            $procesarComprobante = new \procesarComprobante();
            $procesarComprobante->comprobante = $retencion;

            if (!$entity->getFirmado()) {
                $procesarComprobante->envioSRI = false;
                $respuesta = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                if ($respuesta->return->estadoComprobante == "FIRMADO") {
                    $entity->setFirmado(true);
                    $procesarComprobante->envioSRI = true;
                    $respuesta = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                    if ($respuesta->return->estadoComprobante == "DEVUELTA" || $respuesta->return->estadoComprobante == "NO AUTORIZADO") {
                        $entity->setEnviarSiAutorizado(true);
                    }
                }
            } else if ($entity->getEstado() == "ERROR") {
                $procesarComprobante->envioSRI = true;
                $respuesta = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                if ($respuesta->return->estadoComprobante == "DEVUELTA" || $respuesta->return->estadoComprobante == "NO AUTORIZADO") {
                    $entity->setEnviarSiAutorizado(true);
                }
            } else if ($entity->getEnviarSiAutorizado()) {
                $procesarComprobante->envioSRI = true;
                $respuesta = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                if ($respuesta->return->estadoComprobante == "AUTORIZADO") {
                    $procesarComprobante->envioSRI = false;
                    $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
                }
            }
        } else {
            $comprobantePendiente = new \comprobantePendiente();
            $comprobantePendiente->configAplicacion = $configApp;
            $comprobantePendiente->configCorreo = $configCorreo;

            $comprobantePendiente->ambiente = $entity->getAmbiente();
            $comprobantePendiente->codDoc = "07";
            $comprobantePendiente->establecimiento = $entity->getEstablecimiento()->getCodigo();
            $comprobantePendiente->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
            $comprobantePendiente->ptoEmision = $entity->getPtoEmision()->getCodigo();
            $comprobantePendiente->ruc = $emisor->getRuc();
            $comprobantePendiente->secuencial = $entity->getSecuencial();
            $comprobantePendiente->tipoEmision = $entity->getTipoEmision();

            $procesarComprobantePendiente = new \procesarComprobantePendiente();
            $procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;

            $respuesta = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
            if ($respuesta->return->estadoComprobante == "PROCESANDOSE") {
                $respuesta->return->estadoComprobante = "ERROR";
            }
        }
        if ($respuesta->return->mensajes != null) {
            $mensajesArray = array();
            if (is_array($respuesta->return->mensajes)) {
                $mensajesArray = $respuesta->return->mensajes;
            } else {
                $mensajesArray[] = $respuesta->return->mensajes;
            }
            foreach ($mensajesArray as $mensaje) {
                if ($mensaje->identificador == "43") {
                    $comprobantePendiente = new \comprobantePendiente();
                    $comprobantePendiente->configAplicacion = $configApp;
                    $comprobantePendiente->configCorreo = $configCorreo;

                    $comprobantePendiente->ambiente = $entity->getAmbiente();
                    $comprobantePendiente->codDoc = "07";
                    $comprobantePendiente->establecimiento = $entity->getEstablecimiento()->getCodigo();
                    $comprobantePendiente->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
                    $comprobantePendiente->ptoEmision = $entity->getPtoEmision()->getCodigo();
                    $comprobantePendiente->ruc = $emisor->getRuc();
                    $comprobantePendiente->secuencial = $entity->getSecuencial();
                    $comprobantePendiente->tipoEmision = $entity->getTipoEmision();

                    $procesarComprobantePendiente = new \procesarComprobantePendiente();
                    $procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;

                    $respuesta = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);
                }
            }
        }
        $entity->setNumeroAutorizacion($respuesta->return->numeroAutorizacion);

        if ($respuesta->return->fechaAutorizacion != "") {
            $fechaAutorizacion = str_replace("/", "-", $respuesta->return->fechaAutorizacion);
            $entity->setFechaAutorizacion(new \DateTime($fechaAutorizacion));
        }
        $entity->setEstado($respuesta->return->estadoComprobante);
        if ($entity->getEstado() == "AUTORIZADO") {
            $entity->setNombreArchivo("CR" . $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial());

            if ($emisor->getAmbiente() == "2") {
                $emisor->setCantComprobante($emisor->getCantComprobante() + 1);
                $em->persist($emisor);
            }
        }

        $mensajes = $entity->getMensajes();
        foreach ($mensajes as $mensaje) {
            $em->remove($mensaje);
        }
        if ($respuesta->return->mensajes != null) {
            $mensajesArray = array();
            if (is_array($respuesta->return->mensajes)) {
                $mensajesArray = $respuesta->return->mensajes;
            } else {
                $mensajesArray[] = $respuesta->return->mensajes;
            }
            foreach ($mensajesArray as $mensaje) {
                $mensajeGenerado = new \FactelBundle\Entity\Mensaje();
                $mensajeGenerado->setIdentificador($mensaje->identificador);
                $mensajeGenerado->setMensaje($mensaje->mensaje);
                $mensajeGenerado->setInformacionAdicional($mensaje->informacionAdicional);
                $mensajeGenerado->setTipo($mensaje->tipo);
                $mensajeGenerado->setRetencion($entity);
                $em->persist($mensajeGenerado);
            }
        }
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/enviarEmail/{id}", name="retencion_enviar_email")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function sendEmail(Request $request, $id) {
        $destinatario = $request->request->get("email");

        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Retencion')->findRetencionById($id);
        $emisor = $entity->getEmisor();

        $configCorreo = new \configCorreo();
        $configCorreo->correoAsunto = "Nuevo Comprobante Electronico";
        $configCorreo->correoHost = $emisor->getServidorCorreo();
        $configCorreo->correoPass = $emisor->getPassCorreo();
        $configCorreo->correoPort = $emisor->getPuerto();
        $configCorreo->correoRemitente = $emisor->getCorreoRemitente();
        $configCorreo->sslHabilitado = $emisor->getSSL();

        $reenvioEmailParam = new \reenvioEmailParam();

        $reenvioEmailParam->dirDocAutorizados = $emisor->getDirDocAutorizados();
        $reenvioEmailParam->configCorreo = $configCorreo;
        $reenvioEmailParam->identificacionComprador = $entity->getCliente()->getIdentificacion();
        $reenvioEmailParam->nombreArchivo = $entity->getNombreArchivo();
        if ($destinatario != null && $destinatario != '') {
            $reenvioEmailParam->otrosDestinatarios = $destinatario;
        }


        $reenviarEmail = new \reenviarEmail();
        $reenviarEmail->reenvioEmailParam = $reenvioEmailParam;

        $respuesta = $procesarComprobanteElectronico->reenviarEmail($reenviarEmail);

        if ($respuesta->return->mensajes != null) {
            $mensajesArray = array();
            if (is_array($respuesta->return->mensajes)) {
                $mensajesArray = $respuesta->return->mensajes;
            } else {
                $mensajesArray[] = $respuesta->return->mensajes;
            }

            foreach ($mensajesArray as $mensaje) {
                $this->get('session')->getFlashBag()->add(
                        'notice', $mensaje->mensaje . ". " . $mensaje->informacionAdicional
                );
            }
        } else {
            $this->get('session')->getFlashBag()->add(
                    'confirm', "Correo enviado con exito"
            );
        }
        return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/descargar/{id}/{type}", name="retencion_descargar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function descargarAction($id, $type = "zip") {
        $em = $this->getDoctrine()->getManager();
        $retencion = new Retencion();
        $retencion = $em->getRepository('FactelBundle:Retencion')->findRetencionById($id);
        if ($retencion->getEstado() != "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Para descargar los archivos generados el comprobantes debe haber sido AUTORIZADO"
            );
            return $this->redirect($this->generateUrl('retencion_show', array('id' => $retencion->getId())));
        }
        $archivoName = $retencion->getNombreArchivo();
        $pathXML = $retencion->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $retencion->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".xml";
        $pathPDF = $retencion->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $retencion->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".pdf";
        if ($type == "zip") {
            $zip = new \ZipArchive();
            $zipDir = "../web/zip/" . $archivoName . '.zip';
            $zip->open($zipDir, \ZipArchive::CREATE);
            if (file_exists($pathXML)) {
                $zip->addFromString(basename($pathXML), file_get_contents($pathXML));
            }
            if (file_exists($pathPDF)) {
                $zip->addFromString(basename($pathPDF), file_get_contents($pathPDF));
            }

            $zip->close();
            $response = new Response();
//then send the headers to foce download the zip file
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($zipDir) . '"');
            $response->headers->set('Pragma', "no-cache");
            $response->headers->set('Expires', "0");
            $response->headers->set('Content-Transfer-Encoding', "binary");
            $response->sendHeaders();
            $response->setContent(readfile($zipDir));
            return $response;
        } else if ($type == "pdf") {
            $response = new Response();
//then send the headers to foce download the zip file
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($pathPDF) . '"');
            $response->headers->set('Pragma', "no-cache");
            $response->headers->set('Expires', "0");
            $response->headers->set('Content-Transfer-Encoding', "binary");
            $response->sendHeaders();
            $response->setContent(readfile($pathPDF));
            return $response;
        }
    }

    /**
     * Creates a form to create a Retencion entity.
     *
     * @param Retencion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Retencion $entity) {
        $form = $this->createForm(new RetencionType(), $entity, array(
            'action' => $this->generateUrl('retencion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Retencion entity.
     *
     * @Route("/nueva", name="retencion_new")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function newAction() {
        $em = $this->getDoctrine()->getManager();
        $ptoEmision = $em->getRepository('FactelBundle:PtoEmision')->findPtoEmisionEstabEmisorByUsuario($this->get("security.context")->gettoken()->getuser()->getId());
        if ($ptoEmision != null && count($ptoEmision) > 0) {
            return array(
                'pto' => $ptoEmision,
            );
        } else {
            throw $this->createNotFoundException('El usuario del sistema no tiene asignado un Punto de Emision.');
        }
    }

    /**
     * Finds and displays a Retencion entity.
     *
     * @Route("/{id}", name="retencion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Retencion')->findRetencionById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retencion entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Retencion entity.
     *
     * @Route("/{id}/edit", name="retencion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Retencion')->findRetencionById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retencion entity.');
        }
        if ($entity->getEstado() == "AUTORIZADO" || $entity->getEstado() == "ERROR") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Solo pueden ser editadas las Retenciones en estado: NO AUTORIZADO, DEVUELTA y PROCESANDOSE"
            );
            return $this->redirect($this->generateUrl('retencion_show', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a form to edit a Retencion entity.
     *
     * @param Retencion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Retencion $entity) {
        $form = $this->createForm(new RetencionType(), $entity, array(
            'action' => $this->generateUrl('retencion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Retencion entity.
     *
     * @Route("/{id}", name="retencion_update")
     * @Method("PUT")
     * @Template("FactelBundle:Retencion:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Retencion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retencion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('retencion_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @return BinaryFileResponse
     * @Route("/descarga/ayuda", name="retencion_help")
     */
    public function downloadAction() {
        $path = $this->get('kernel')->getRootDir() . "/../web/upload/";
        $file = $path . 'Ayuda Codigos Retenciones.xlsx'; // Path to the file on the server
        $response = new BinaryFileResponse($file);

// Give the file a name:
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Ayuda Codigos Retenciones.xlsx');

        return $response;
    }

    /**
     * Deletes a Retencion entity.
     *
     * @Route("/{id}", name="retencion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:Retencion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Retencion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('retencion'));
    }

    /**
     * Creates a form to delete a Retencion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('retencion_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    private function claveAcceso($retencion, $emisor, $establecimiento, $ptoEmision, $fechaEmision) {
        $claveAcceso = str_replace("/", "", $fechaEmision);
        $claveAcceso .= "07";
        $claveAcceso .= $emisor->getRuc();
        $claveAcceso .= $retencion->getAmbiente();
        $serie = $establecimiento->getCodigo() . $ptoEmision->getCodigo();
        $claveAcceso .= $serie;
        $claveAcceso .= $retencion->getSecuencial();
        $claveAcceso .= "12345678";
        $claveAcceso .= $retencion->getTipoEmision();
        $claveAcceso .= $this->modulo11($claveAcceso);

        return $claveAcceso;
    }

    private function modulo11($claveAcceso) {
        $multiplos = [2, 3, 4, 5, 6, 7];
        $i = 0;
        $cantidad = strlen($claveAcceso);
        $total = 0;
        while ($cantidad > 0) {
            $total += intval(substr($claveAcceso, $cantidad - 1, 1)) * $multiplos[$i];
            $i++;
            $i = $i % 6;
            $cantidad--;
        }
        $modulo11 = 11 - $total % 11;
        if ($modulo11 == 11) {
            $modulo11 = 0;
        } else if ($modulo11 == 10) {
            $modulo11 = 1;
        }

        return strval($modulo11);
    }

}
