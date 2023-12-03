<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Guia;
use FactelBundle\Entity\GuiaHasProducto;
use FactelBundle\Entity\Impuesto;
use FactelBundle\Entity\CampoAdicional;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use FactelBundle\Util;

require_once 'ProcesarComprobanteElectronico.php';

/**
 * Guia controller.
 *
 * @Route("/comprobantes/guia")
 */
class GuiaController extends Controller {

    /**
     * Lists all entities.
     *
     * @Route("/", name="guia")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        return array();
    }

    /**
     * Lists all Guia entities.
     *
     * @Route("/guias", name="all_guia")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     */
    public function guiasAction() {
        if (isset($_GET['sEcho'])) {
            $sEcho = $_GET['sEcho'];
        }
        if (isset($_GET['iDisplayStart'])) {
            $iDisplayStart = intval($_GET['iDisplayStart']);
        }
        if (isset($_GET['iDisplayLength'])) {
            $iDisplayLength = intval($_GET['iDisplayLength']);
        }
        $sSearch = "";
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
        $count = $em->getRepository('FactelBundle:Guia')->cantidadGuias($idPtoEmision, $emisorId);
        $entities = $em->getRepository('FactelBundle:Guia')->findGuias($sSearch, $iDisplayStart, $iDisplayLength, $idPtoEmision, $emisorId);
        $totalDisplayRecords = $count;

        if ($sSearch != "") {
            $totalDisplayRecords = count($em->getRepository('FactelBundle:Guia')->findGuias($sSearch, $iDisplayStart, 1000000, $idPtoEmision, $emisorId));
        }
        $guiaArray = array();
        $i = 0;
        foreach ($entities as $entity) {
            $fechaAutorizacion = "";
            $fechaAutorizacion = $entity->getFechaAutorizacion() != null ? $entity->getFechaAutorizacion()->format("d/m/Y H:i:s") : "";
            $guiaArray[$i] = [$entity->getId(), $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial(), $entity->getCliente()->getNombre(), $entity->getFechaIniTransporte()->format("d/m/Y"), $fechaAutorizacion, $entity->getEstado()];
            $i++;
        }

        $arr = array(
            "iTotalRecords" => (int) $count,
            "iTotalDisplayRecords" => (int) $totalDisplayRecords,
            'aaData' => $guiaArray
        );

        $post_data = json_encode($arr);

        return new Response($post_data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Creates a new Guia entity.
     *
     * @Route("/procesar/{id}", name="guia_procesar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function procesarAccion($id) {
        $entity = new Guia();
        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $respuesta = null;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Guia')->findGuiaById($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe la guia con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Este comprobante electronico ya fue autorizado"
            );
            return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
        }
        $emisor = $entity->getEmisor();
        $hoy = date("Y-m-d");
        if ($emisor->getPlan() != null && $emisor->getFechaFin()) {
            if ($hoy > $emisor->getFechaFin()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Su plan ha caducado por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
            }
            if ($emisor->getCantComprobante() > $emisor->getPlan()->getCantComprobante()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Ha superado el numero de comprobantes contratado en su plan, por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
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
            $guia = new \guiaRemision();
            $guia->configAplicacion = $configApp;
            $guia->configCorreo = $configCorreo;

            $guia->ambiente = $entity->getAmbiente();
            $guia->tipoEmision = $entity->getTipoEmision();
            $guia->razonSocial = $emisor->getRazonSocial();
            if ($entity->getEstablecimiento()->getNombreComercial() != "") {
                $guia->nombreComercial = $entity->getEstablecimiento()->getNombreComercial();
            } else if ($emisor->getNombreComercial() != "") {
                $guia->nombreComercial = $emisor->getNombreComercial();
            }
            $guia->ruc = $emisor->getRuc(); //[Ruc]
            $guia->codDoc = "06";
            $guia->establecimiento = $entity->getEstablecimiento()->getCodigo();
            $guia->ptoEmision = $entity->getPtoEmision()->getCodigo();
            $guia->secuencial = $entity->getSecuencial();
            $guia->fechaIniTransporte = $entity->getFechaIniTransporte()->format("d/m/Y");
            $guia->fechaFinTransporte = $entity->getFechaFinTransporte()->format("d/m/Y");
            $guia->dirMatriz = $emisor->getDireccionMatriz();
            $guia->dirEstablecimiento = $entity->getEstablecimiento()->getDireccion();
            if ($emisor->getContribuyenteEspecial() != "") {
                $guia->contribuyenteEspecial = $emisor->getContribuyenteEspecial();
            }
            $guia->obligadoContabilidad = $emisor->getObligadoContabilidad();

            $guia->dirPartida = $entity->getDirPartida();
            $guia->razonSocialTransportista = $entity->getRazonSocialTransportista();
            $guia->tipoIdentificacionTransportista = "06";
            $guia->rucTransportista = $entity->getRucTransportista();
            $guia->placa = $entity->getPlaca();

            $destinatario = new \destinatario();
            $destinatario->identificacionDestinatario = $entity->getCliente()->getIdentificacion();
            $destinatario->razonSocialDestinatario = $entity->getCliente()->getNombre();
            $destinatario->dirDestinatario = $entity->getCliente()->getDireccion();
            $destinatario->motivoTraslado = $entity->getMotivoTraslado();

            $detalles = array();
            foreach ($entity->getGuiasHasProducto() as $producto) {
                $detalle = new \detalleGuiaRemision();
                $detalle->codigoInterno = $producto->getCodigoProducto();
                $detalle->descripcion = $producto->getNombre();
                $detalle->cantidad = $producto->getCantidad();
                $detalles[] = $detalle;
            }
            $destinatario->detalles = $detalles;
            $guia->destinatarios = $destinatario;

            $camposAdicionales = array();
            foreach ($entity->getComposAdic() as $campoAdic) {
                $campoAdicional = new \campoAdicional();
                $campoAdicional->nombre = $campoAdic->getNombre();
                $campoAdicional->valor = $campoAdic->getValor();

                $camposAdicionales [] = $campoAdic;
            }
            if ($emisor->getRegimenRimpe()) {
                $guia->regimenRempes = "Contribuyente Negocio Popular - Régimen RIMPE";
            }
            if ($emisor->getRegimenRimpe1()) {
                $guia->regimenRimpes1 = "Contribuyente Régimen RIMPE";
            }

            if ($emisor->getResolucionAgenteRetencion()) {
                $guia->agenteRetencion = $emisor->getResolucionAgenteRetencion();
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
                $guia->infoAdicional = $camposAdicionales;
            }
            $procesarComprobante = new \procesarComprobante();
            $procesarComprobante->comprobante = $guia;

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
            $comprobantePendiente->codDoc = "06";
            $comprobantePendiente->establecimiento = $entity->getEstablecimiento()->getCodigo();
            $comprobantePendiente->fechaEmision = $entity->getFechaIniTransporte()->format("d/m/Y");
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
                    $comprobantePendiente->codDoc = "06";
                    $comprobantePendiente->establecimiento = $entity->getEstablecimiento()->getCodigo();
                    $comprobantePendiente->fechaEmision = $entity->getFechaIniTransporte()->format("d/m/Y");
                    $comprobantePendiente->ptoEmision = $entity->getPtoEmision()->getCodigo();
                    $comprobantePendiente->ruc = $emisor->getRuc();
                    $comprobantePendiente->secuencial = $entity->getSecuencial();
                    $comprobantePendiente->tipoEmision = $entity->getTipoEmision();

                    $procesarComprobantePendiente = new \procesarComprobantePendiente();
                    $procesarComprobantePendiente->comprobantePendiente = $comprobantePendiente;

                    $respuesta = $procesarComprobanteElectronico->procesarComprobantePendiente($procesarComprobantePendiente);

                    break;
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
            $entity->setNombreArchivo("GR" . $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial());
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
                $mensajeGenerado->setGuia($entity);
                $em->persist($mensajeGenerado);
            }
        }
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/anular/{id}", name="guia_anular")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function anularAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Guia')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la Guia con ID = ' + $id);
        }
        $entity->setEstado("ANULADA");
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/eliminar/{id}", name="guia_eliminar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function eliminarAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Guia')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la guia con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "No se puede eliminar un documento autorizado"
            );
            return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
        }

        foreach ($entity->getMensajes() as $mensaje) {
            $em->remove($mensaje);
        }

        foreach ($entity->getGuiasHasProducto() as $guiaHasProducto) {
            $em->remove($guiaHasProducto);
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('guia'));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/enviarEmail/{id}", name="guia_enviar_email")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function sendEmail(Request $request, $id) {
        $destinatario = $request->request->get("email");

        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Guia')->findFacturaById($id);
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
        $reenvioEmailParam->identificacionComprador = $entity->getRucTransportista();
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
        return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Guia entity.
     *
     * @Route("/", name="guia_create")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     * @Template("FactelBundle:Guia:new.html.twig")
     */
    public function createAction(Request $request) {

        $secuencial = $request->request->get("secuencial");
        $fechaEmision = $request->request->get("fechaIniTransporte");
        $idCliente = $request->request->get("idCliente");
        $nombre = $request->request->get("nombre");
        $celular = $request->request->get("celular");
        $email = $request->request->get("email");
        $tipoIdentificacion = $request->request->get("tipoIdentificacion");
        $identificacion = $request->request->get("identificacion");
        $direccion = $request->request->get("direccion");
        $nuevoCliente = $request->request->get("nuevoCliente");
        $idGuia = $request->request->get("idGuia");

        $fechaFinTransporte = $request->request->get("fechaFinTransporte");
        $dirPartida = $request->request->get("dirPartida");
        $razonSocialTransportista = $request->request->get("razonSocialTransportista");
        $rucTransportista = $request->request->get("rucTransportista");
        $placa = $request->request->get("placa");
        $motivoTraslado = $request->request->get("motivoTraslado");

        $texto = "";
        $campos = "";
        $cantidadErrores = 0;
        if ($secuencial == '') {
            $campos .= "Secuencial, ";
            $cantidadErrores++;
        }
        if ($fechaEmision == '') {
            $campos .= "Fecha Inicio Transporte, ";
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

        if ($fechaFinTransporte == '') {
            $campos .= "Fecha Fin Transporte, ";
            $cantidadErrores++;
        }
        if ($dirPartida == '') {
            $campos .= "Direccion Partida, ";
            $cantidadErrores++;
        }
        if ($razonSocialTransportista == '') {
            $campos .= "Razon Social Transportista, ";
            $cantidadErrores++;
        }
        if ($rucTransportista == '') {
            $campos .= "RUC Transportista, ";
            $cantidadErrores++;
        }
        if ($placa == '') {
            $campos .= "Placa, ";
            $cantidadErrores++;
        }
        if ($motivoTraslado == '') {
            $campos .= "Motivo Traslado, ";
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

            return $this->redirect($this->generateUrl('guia_new', array()));
        }
        $em = $this->getDoctrine()->getManager();
        if ($idGuia != null && $idGuia != '') {
            $entity = new Guia();
            $entity = $em->getRepository('FactelBundle:Guia')->find($idGuia);
            if (!is_null($entity)) {
                $mensajes = $entity->getMensajes();
                foreach ($mensajes as $mensaje) {
                    $em->remove($mensaje);
                }
                $guiasHasProducto = $entity->getGuiasHasProducto();
                foreach ($guiasHasProducto as $guiaHasProducto) {
                    $em->remove($guiaHasProducto);
                }
                $em->flush();
            }
        } else {
            $entity = new Guia();
        }



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
            $entity->setFechaIniTransporte($fecha);

            $fechaModificada = str_replace("/", "-", $fechaFinTransporte);
            $fecha = new \DateTime($fechaModificada);
            $entity->setFechaFinTransporte($fecha);
            $entity->setDirPartida($dirPartida);
            $entity->setRazonSocialTransportista($razonSocialTransportista);
            $entity->setRucTransportista($rucTransportista);
            $entity->setPlaca($placa);
            $entity->setMotivoTraslado($motivoTraslado);

            $cliente = $em->getRepository('FactelBundle:Cliente')->find($idCliente);
            if ($nuevoCliente) {
                $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
                if ($em->getRepository('FactelBundle:Cliente')->findBy(array("identificacion" => $identificacion, "emisor" => $emisorId)) != null) {
                    $this->get('session')->getFlashBag()->add(
                            'notice', "La identificación del cliente ya se encuentra resgistrada. Utilice la opción de búsqueda"
                    );
                    return $this->redirect($this->generateUrl('guia_new', array()));
                }
                $cliente = new \FactelBundle\Entity\Cliente();

                $emisor = $em->getRepository('FactelBundle:Emisor')->find($emisorId);
                $cliente->setEmisor($emisor);
            }

            $cliente->setNombre($nombre);
            $cliente->setTipoIdentificacion($tipoIdentificacion);
            $cliente->setIdentificacion($identificacion);
            $cliente->setCelular($celular);
            $cliente->setCorreoElectronico($email);
            $cliente->setDireccion($direccion);
            $em->persist($cliente);
            $em->flush();


            $entity->setCliente($cliente);
            $entity->setEmisor($emisor);
            $entity->setEstablecimiento($establecimiento);
            $entity->setPtoEmision($ptoEmision[0]);



            $idProductoArray = $request->request->get("idProducto");
            if ($idProductoArray == null) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "La guia debe contener al menos un producto"
                );
                return $this->redirect($this->generateUrl('guia_new', array()));
            }
            $productos = $em->getRepository('FactelBundle:Producto')->findById($idProductoArray);
            if (count($productos) == 0) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Los productos solicitados para esta guia no se encuentran disponibles"
                );
                return $this->redirect($this->generateUrl('guia_new', array()));
            }
            foreach ($productos as $producto) {
                $guiasHasProducto = new GuiaHasProducto();
                $idProducto = $producto->getId();

                $guiasHasProducto->setProducto($producto);
                $cantidadArray = $request->request->get("cantidad");
                $nombreProducto = $request->request->get("nombreProducto");
                $codigoProducto = $request->request->get("codigoProducto");

                $guiasHasProducto->setCantidad($cantidadArray[$idProducto]);

                $guiasHasProducto->setNombre($nombreProducto[$idProducto]);
                $guiasHasProducto->setCodigoProducto($codigoProducto[$idProducto]);
                $guiasHasProducto->setGuia($entity);
                $entity->addGuiasHasProducto($guiasHasProducto);
            }

            $em->persist($entity);
            $em->flush();
            if ($idGuia == null || $idGuia == '') {
                $ptoEmision[0]->setSecuencialGuiaRemision($ptoEmision[0]->getSecuencialGuiaRemision() + 1);
                $em->persist($ptoEmision[0]);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
        } else {
            throw $this->createNotFoundException('El usuario del sistema no tiene asignado un Punto de Emision.');
        }
    }

    /**
     * Creates a new Guia entity.
     *
     * @Route("/descargar/{id}/{type}", name="guia_descargar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function descargarAction($id, $type = "zip") {
        $em = $this->getDoctrine()->getManager();
        $guia = new Guia();
        $guia = $em->getRepository('FactelBundle:Guia')->findGuiaById($id);
        if ($guia->getEstado() != "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Para descargar los archivos generados el comprobantes debe haber sido AUTORIZADO"
            );
            return $this->redirect($this->generateUrl('guia_show', array('id' => $guia->getId())));
        }
        $archivoName = $guia->getNombreArchivo();
        $pathXML = $guia->getEmisor()->getDirDocAutorizados() . "/" . $guia->getRucTransportista() . "/" . $archivoName . ".xml";
        $pathPDF = $guia->getEmisor()->getDirDocAutorizados() . "/" . $guia->getRucTransportista() . "/" . $archivoName . ".pdf";

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
     * Displays a form to create a new Guia entity.
     *
     * @Route("/nueva", name="guia_new")
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
     * Finds and displays a Guia entity.
     *
     * @Route("/{id}", name="guia_show")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Guia')->findGuiaById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Guia entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Guia entity.
     *
     * @Route("/{id}/editar", name="guia_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Guia')->findGuiaById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Guia entity.');
        }
        if ($entity->getEstado() == "AUTORIZADO" || $entity->getEstado() == "ERROR") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Solo puede ser editada la Guia en estado: NO AUTORIZADO, DEVUELTA y PROCESANDOSE"
            );
            return $this->redirect($this->generateUrl('guia_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
        );
    }

    private function claveAcceso($guia, $emisor, $establecimiento, $ptoEmision, $fechaEmision) {
        $claveAcceso = str_replace("/", "", $fechaEmision);
        $claveAcceso .= "06";
        $claveAcceso .= $emisor->getRuc();
        $claveAcceso .= $guia->getAmbiente();
        $serie = $establecimiento->getCodigo() . $ptoEmision->getCodigo();
        $claveAcceso .= $serie;
        $claveAcceso .= $guia->getSecuencial();
        $claveAcceso .= "12345678";
        $claveAcceso .= $guia->getTipoEmision();
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
