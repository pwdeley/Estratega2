<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\NotaCredito;
use FactelBundle\Entity\NotaCreditoHasProducto;
use FactelBundle\Form\NotaCreditoType;
use FactelBundle\Entity\Impuesto;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

require_once 'ProcesarComprobanteElectronico.php';

/**
 * NotaCredito controller.
 *
 * @Route("/comprobantes/nota-credito")
 */
class NotaCreditoController extends Controller {

    /**
     * Lists all NotaCredito entities.
     *
     * @Route("/", name="notacredito")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function indexAction() {

        return array(
        );
    }

    /**
     * Lists all Nota Credito entities.
     *
     * @Route("/notasCredito", name="all_notasCredito")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     */
    public function notasCreditoAction() {
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
        $count = $em->getRepository('FactelBundle:NotaCredito')->cantidadNotasCredito($idPtoEmision, $emisorId);
        $entities = $em->getRepository('FactelBundle:NotaCredito')->findNotasCredito($sSearch, $iDisplayStart, $iDisplayLength, $idPtoEmision, $emisorId);
        $totalDisplayRecords = $count;

        if ($sSearch != "") {
            $totalDisplayRecords = count($entities);
        }
        $notaCreditoArray = array();
        $i = 0;
        foreach ($entities as $entity) {
            $fechaAutorizacion = "";
            $fechaAutorizacion = $entity->getFechaAutorizacion() != null ? $entity->getFechaAutorizacion()->format("d/m/Y H:i:s") : "";
            $notaCreditoArray[$i] = [$entity->getId(), $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial(), $entity->getCliente()->getNombre(), $entity->getFechaEmision()->format("d/m/Y"), $fechaAutorizacion, $entity->getValorTotal(), $entity->getEstado()];
            $i++;
        }

        $arr = array(
            "iTotalRecords" => (int) $count,
            "iTotalDisplayRecords" => (int) $totalDisplayRecords,
            'aaData' => $notaCreditoArray
        );

        $post_data = json_encode($arr);

        return new Response($post_data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     *
     * @Route("/procesar/{id}", name="notacredito_procesar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function procesarAccion($id) {

        $entity = new NotaCredito();
        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $respuesta = null;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:NotaCredito')->findNotaCreditoById($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Nota Crédito con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Este comprobante electronico ya fue autorizado"
            );
            return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
        }
        $emisor = $entity->getEmisor();
        $hoy = date("Y-m-d");
        if ($emisor->getPlan() != null && $emisor->getFechaFin()) {
            if ($hoy > $emisor->getFechaFin()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Su plan ha caducado por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
            }
            if ($emisor->getCantComprobante() > $emisor->getPlan()->getCantComprobante()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Ha superado el numero de comprobantes contratado en su plan, por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
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
            $notaCredito = new \notaCredito();
            $notaCredito->configAplicacion = $configApp;
            $notaCredito->configCorreo = $configCorreo;


            $notaCredito->ambiente = $entity->getAmbiente();
            $notaCredito->tipoEmision = $entity->getTipoEmision();
            $notaCredito->razonSocial = $emisor->getRazonSocial();
            if ($entity->getEstablecimiento()->getNombreComercial() != "") {
                $notaCredito->nombreComercial = $entity->getEstablecimiento()->getNombreComercial();
            } else if ($emisor->getNombreComercial() != "") {
                $notaCredito->nombreComercial = $emisor->getNombreComercial();
            }
            $notaCredito->ruc = $emisor->getRuc(); //[Ruc]
            $notaCredito->codDoc = "04";
            $notaCredito->establecimiento = $entity->getEstablecimiento()->getCodigo();
            $notaCredito->ptoEmision = $entity->getPtoEmision()->getCodigo();
            $notaCredito->secuencial = $entity->getSecuencial();
            $notaCredito->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
            $notaCredito->dirMatriz = $emisor->getDireccionMatriz();
            $notaCredito->dirEstablecimiento = $entity->getEstablecimiento()->getDireccion();
            if ($emisor->getContribuyenteEspecial() != "") {
                $notaCredito->contribuyenteEspecial = $emisor->getContribuyenteEspecial();
            }
            $notaCredito->obligadoContabilidad = $emisor->getObligadoContabilidad();
            $notaCredito->tipoIdentificacionComprador = $entity->getCliente()->getTipoIdentificacion();
            $notaCredito->razonSocialComprador = $entity->getCliente()->getNombre();
            $notaCredito->identificacionComprador = $entity->getCliente()->getIdentificacion();
            $notaCredito->totalSinImpuestos = $entity->getTotalSinImpuestos();



            $notaCredito->valorModificacion = $entity->getValorTotal();
            $notaCredito->moneda = "DOLAR"; //DOLAR
            $codigoPorcentajeIVA = "";
            $detalles = array();
            $notaCreditoHasProducto = $entity->getNotaCreditoHasProducto();
            $impuestosTotalICE = array();
            $baseImponibleICE = array();
            $impuestosTotalIRBPNR = array();
            $baseImponibleIRBPNR = array();
            foreach ($notaCreditoHasProducto as $notaCreditoHasProducto) {
                $producto = new \FactelBundle\Entity\Producto();
                $producto = $notaCreditoHasProducto->getProducto();
                $detalleNotaCredito = new \detalleNotaCredito();
                $detalleNotaCredito->codigoInterno = $notaCreditoHasProducto->getCodigoProducto();
                if ($producto->getCodigoAuxiliar() != "") {
                    $detalleNotaCredito->codigoAdicional = $producto->getCodigoAuxiliar();
                }
                $detalleNotaCredito->descripcion = $notaCreditoHasProducto->getNombre();
                $detalleNotaCredito->cantidad = $notaCreditoHasProducto->getCantidad();
                $detalleNotaCredito->precioUnitario = $notaCreditoHasProducto->getPrecioUnitario();
                $detalleNotaCredito->descuento = $notaCreditoHasProducto->getDescuento();
                $detalleNotaCredito->precioTotalSinImpuesto = $notaCreditoHasProducto->getValorTotal();

                $impuestos = array();
                $impuestosProducto = $notaCreditoHasProducto->getImpuestos();
                foreach ($impuestosProducto as $impuestoProducto) {

                    $impuesto = new \impuesto(); // Impuesto del detalle
                    $impuesto->codigo = $impuestoProducto->getCodigo();
                    if ($impuestoProducto->getCodigo() == "2" && $impuestoProducto->getValor() > 0) {
                        $codigoPorcentajeIVA = $impuestoProducto->getCodigoPorcentaje();
                    }
                    $impuesto->codigoPorcentaje = $impuestoProducto->getCodigoPorcentaje();
                    $impuesto->tarifa = $impuestoProducto->getTarifa();
                    $impuesto->baseImponible = $impuestoProducto->getBaseImponible();
                    $impuesto->valor = $impuestoProducto->getValor();
                    $impuestos[] = $impuesto;

                    if ($impuestoProducto->getCodigo() == "3") {
                        if (isset($impuestosTotalICE[$impuestoProducto->getCodigoPorcentaje()])) {
                            $impuestosTotalICE[$impuestoProducto->getCodigoPorcentaje()] += $impuestoProducto->getValor();
                            $baseImponibleICE[$impuestoProducto->getCodigoPorcentaje()] += $impuestoProducto->getBaseImponible();
                        } else {
                            $impuestosTotalICE[$impuestoProducto->getCodigoPorcentaje()] = $impuestoProducto->getValor();
                            $baseImponibleICE[$impuestoProducto->getCodigoPorcentaje()] = $impuestoProducto->getBaseImponible();
                        }
                    }
                    if ($impuestoProducto->getCodigo() == "5") {
                        if (isset($impuestosTotalIRBPNR[$impuestoProducto->getCodigoPorcentaje()])) {
                            $impuestosTotalIRBPNR[$impuestoProducto->getCodigoPorcentaje()] += $impuestoProducto->getValor();
                            $baseImponibleIRBPNR[$impuestoProducto->getCodigoPorcentaje()] += $impuestoProducto->getBaseImponible();
                        } else {
                            $impuestosTotalIRBPNR[$impuestoProducto->getCodigoPorcentaje()] = $impuestoProducto->getValor();
                            $baseImponibleIRBPNR[$impuestoProducto->getCodigoPorcentaje()] = $impuestoProducto->getBaseImponible();
                        }
                    }
                }
                $detalleNotaCredito->impuestos = $impuestos;
                $detalles[] = $detalleNotaCredito;
            }

            $totalImpuestoArray = array();
            if ($entity->getSubtotal12() > 0) {
                $totalImpuesto = new \totalImpuesto();
                $totalImpuesto->codigo = "2";
                $totalImpuesto->codigoPorcentaje = $codigoPorcentajeIVA;
                $totalImpuesto->baseImponible = $entity->getSubtotal12();
                $totalImpuesto->valor = $entity->getIva12();

                $totalImpuestoArray[] = $totalImpuesto;
            }
            if ($entity->getSubtotal0() > 0) {
                $totalImpuesto = new \totalImpuesto();
                $totalImpuesto->codigo = "2";
                $totalImpuesto->codigoPorcentaje = "0";
                $totalImpuesto->baseImponible = $entity->getSubtotal0();
                $totalImpuesto->valor = "0.00";

                $totalImpuestoArray[] = $totalImpuesto;
            }
            if ($entity->getSubtotalExentoIVA() > 0) {
                $totalImpuesto = new \totalImpuesto();
                $totalImpuesto->codigo = "2";
                $totalImpuesto->codigoPorcentaje = "7";
                $totalImpuesto->baseImponible = $entity->getSubtotalExentoIVA();
                $totalImpuesto->valor = "0.00";

                $totalImpuestoArray[] = $totalImpuesto;
            }
            if ($entity->getSubtotalNoIVA() > 0) {
                $totalImpuesto = new \totalImpuesto();
                $totalImpuesto->codigo = "2";
                $totalImpuesto->codigoPorcentaje = "6";
                $totalImpuesto->baseImponible = $entity->getSubtotalNoIVA();
                $totalImpuesto->valor = "0.00";

                $totalImpuestoArray[] = $totalImpuesto;
            }
            foreach ($impuestosTotalICE as $clave => $valor) {
                $totalImpuesto = new \totalImpuesto();
                $totalImpuesto->codigo = "3";
                $totalImpuesto->codigoPorcentaje = (string) $clave;
                $totalImpuesto->baseImponible = sprintf("%01.2f", $baseImponibleICE[$clave]);
                $totalImpuesto->valor = sprintf("%01.2f", $valor);

                $totalImpuestoArray[] = $totalImpuesto;
            }

            foreach ($impuestosTotalIRBPNR as $clave => $valor) {
                $totalImpuesto = new \totalImpuesto();
                $totalImpuesto->codigo = "5";
                $totalImpuesto->codigoPorcentaje = (string) $clave;
                $totalImpuesto->baseImponible = sprintf("%01.2f", $baseImponibleIRBPNR[$clave]);
                $totalImpuesto->valor = sprintf("%01.2f", $valor);

                $totalImpuestoArray[] = $totalImpuesto;
            }

            $notaCredito->detalles = $detalles;
            $notaCredito->totalConImpuesto = $totalImpuestoArray;

            $camposAdicionales = array();
            foreach ($entity->getComposAdic() as $campoAdic) {
                $campoAdicional = new \campoAdicional();
                $campoAdicional->nombre = $campoAdic->getNombre();
                $campoAdicional->valor = $campoAdic->getValor();

                $camposAdicionales [] = $campoAdic;
            }
            if ($emisor->getRegimenRimpe()) {
                $notaCredito->regimenRimpe = "Contribuyente Negocio Popular - Régimen RIMPE";
            }
            if ($emisor->getRegimenRimpe1()) {
                $notaCredito->regimenRimpe1 = "Contribuyente Régimen RIMPE";
            }

            if ($emisor->getResolucionAgenteRetencion()) {
                $notaCredito->agenteRetencion = $emisor->getResolucionAgenteRetencion();
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
            $notaCredito->codDocModificado = $entity->getTipoDocMod();
            $notaCredito->numDocModificado = $entity->getNroDocMod();
            $notaCredito->fechaEmisionDocSustento = $entity->getFechaEmisionDocMod()->format("d/m/Y");
            $notaCredito->motivo = $entity->getMotivo();
            if (count($camposAdicionales) > 0) {
                $notaCredito->infoAdicional = $camposAdicionales;
            }

            $procesarComprobante = new \procesarComprobante();
            $procesarComprobante->comprobante = $notaCredito;

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
            $comprobantePendiente->codDoc = "04";
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
                    $comprobantePendiente->codDoc = "04";
                    $comprobantePendiente->establecimiento = $entity->getEstablecimiento()->getCodigo();
                    $comprobantePendiente->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
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
            $entity->setNombreArchivo("NC" . $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial());

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
                $mensajeGenerado->setNotaCredito($entity);
                $em->persist($mensajeGenerado);
            }
        }
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/anular/{id}", name="notacredito_anular")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function anularAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:NotaCredito')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la Nota Credito con ID = ' + $id);
        }
        $entity->setEstado("ANULADA");
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/eliminar/{id}", name="notacredito_eliminar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function eliminarAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:NotaCredito')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la nota credito con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "No se puede eliminar un documento autorizado"
            );
            return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
        }

        foreach ($entity->getMensajes() as $mensaje) {
            $em->remove($mensaje);
        }

        foreach ($entity->getNotaCreditoHasProducto() as $notaCreditoHasProducto) {
            foreach ($notaCreditoHasProducto->getImpuestos() as $impuesto) {
                $em->remove($impuesto);
            }
            foreach ($notaCreditoHasProducto->getDetallesAdicionales() as $detalle) {
                $em->remove($detalle);
            }
            $em->remove($notaCreditoHasProducto);
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('notacredito', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/enviarEmail/{id}", name="notacredito_enviar_email")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function sendEmail(Request $request, $id) {
        $destinatario = $request->request->get("email");

        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:NotaCredito')->findFacturaById($id);
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
        return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new NotaCredito entity.
     *
     * @Route("/", name="notacredito_create")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     * @Template("FactelBundle:NotaCredito:new.html.twig")
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

        $idNotaCredito = $request->request->get("idNotaCredito");

        $tipoDocMod = $request->request->get("tipoDocumento");
        $fechaEmisionDocMod = $request->request->get("fechaDocModificado");
        $numeroDocMod = $request->request->get("numeroDocMod");
        $motivo = $request->request->get("motivo");

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
        if ($fechaEmisionDocMod == '') {
            $campos .= "Fecha Emision Documento Modificado, ";
            $cantidadErrores++;
        }
        if ($numeroDocMod == '') {
            $campos .= "Numero Documento Modificado, ";
            $cantidadErrores++;
        }
        if ($motivo == '') {
            $campos .= "Motivo, ";
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

            return $this->redirect($this->generateUrl('notacredito_new', array()));
        }
        $em = $this->getDoctrine()->getManager();
        if ($idNotaCredito != null && $idNotaCredito != '') {
            $entity = new NotaCredito();
            $entity = $em->getRepository('FactelBundle:NotaCredito')->find($idNotaCredito);
            if (!is_null($entity)) {
                $mensajes = $entity->getMensajes();
                foreach ($mensajes as $mensaje) {
                    $em->remove($mensaje);
                }
                $notasCreditoHasProducto = $entity->getNotaCreditoHasProducto();
                foreach ($notasCreditoHasProducto as $notaCreditoHasProducto) {
                    foreach ($notaCreditoHasProducto->getImpuestos() as $impuesto) {
                        $em->remove($impuesto);
                    }
                    $em->remove($notaCreditoHasProducto);
                }
                $em->flush();
            }
        } else {
            $entity = new NotaCredito();
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

            $entity->setFechaEmision($fecha);
            $cliente = $em->getRepository('FactelBundle:Cliente')->find($idCliente);
            if ($nuevoCliente) {
                $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
                if ($em->getRepository('FactelBundle:Cliente')->findBy(array("identificacion" => $identificacion, "emisor" => $emisorId)) != null) {
                    $this->get('session')->getFlashBag()->add(
                            'notice', "La identificación del cliente ya se encuentra resgistrada. Utilice la opción de búsqueda"
                    );
                    return $this->redirect($this->generateUrl('notacredito_new', array()));
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

            $subTotalSinImpuesto = 0;
            $subTotal12 = 0;
            $subTotal0 = 0;
            $subTotaNoObjeto = 0;
            $subTotaExento = 0;
            $descuento = 0;
            $ice = 0;
            $irbpnr = 0;
            $iva12 = 0;
            $valorTotal = 0;

            $idProductoArray = $request->request->get("idProducto");
            if ($idProductoArray == null) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "La Nota de Credito debe contener al menos un producto"
                );
                return $this->redirect($this->generateUrl('notacredito_new', array()));
            }

            $productos = $em->getRepository('FactelBundle:Producto')->findById($idProductoArray);
            if (count($productos) == 0) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Los productos solicitados para esta Nota de Credito no se encuentran disponibles"
                );
                return $this->redirect($this->generateUrl('notacredito_new', array()));
            }

            foreach ($productos as $producto) {
                $notaCreditoHasProducto = new NotaCreditoHasProducto();
                $idProducto = $producto->getId();

                $notaCreditoHasProducto->setProducto($producto);
                $impuestoIva = $producto->getImpuestoIVA();
                $impuestoICE = $producto->getImpuestoICE();
                $impuestoIRBPNR = $producto->getImpuestoIRBPNR();

                $cantidadArray = $request->request->get("cantidad");
                $descuentoArray = $request->request->get("descuento");

                $precioUnitario = $request->request->get("precio");
                $nombreProducto = $request->request->get("nombreProducto");
                $codigoProducto = $request->request->get("codigoProducto");

                $iceArray = $request->request->get("ice");
                $irbpnrArray = $request->request->get("irbpnr");
                $baseImponible = 0;

                if ($impuestoIva != null) {
                    $impuesto = new Impuesto();
                    $impuesto->setCodigo("2");
                    $impuesto->setCodigoPorcentaje($impuestoIva->getCodigoPorcentaje());
                    $baseImponible = floatval($cantidadArray[$idProducto]) * floatval($precioUnitario[$idProducto]) - floatval($descuentoArray[$idProducto]);
                    $impuesto->setBaseImponible($baseImponible);
                    $impuesto->setTarifa("0");
                    $impuesto->setValor(0.00);

                    if ($impuestoIva->getCodigoPorcentaje() == "0") {
                        $subTotal0 += $baseImponible;
                    } else if ($impuestoIva->getCodigoPorcentaje() == "6") {
                        $subTotaNoObjeto += $baseImponible;
                    } else if ($impuestoIva->getCodigoPorcentaje() == "7") {
                        $subTotaExento += $baseImponible;
                    } else {
                        $impuesto->setTarifa($impuestoIva->getTarifa());
                        $impuesto->setValor(round($baseImponible * $impuestoIva->getTarifa() / 100, 2));

                        $subTotal12 += $baseImponible;
                        $iva12 += round($baseImponible * $impuestoIva->getTarifa() / 100, 2);
                    }
                    $impuesto->setNotaCreditoHasProducto($notaCreditoHasProducto);

                    $notaCreditoHasProducto->addImpuesto($impuesto);
                    $subTotalSinImpuesto += $baseImponible;
                }

                if ($impuestoICE != null) {
                    $impuesto = new Impuesto();
                    $impuesto->setCodigo("3");
                    $impuesto->setCodigoPorcentaje($impuestoICE->getCodigoPorcentaje());
                    $impuesto->setTarifa("0");
                    $impuesto->setBaseImponible($baseImponible);
                    $impuesto->setValor($iceArray[$idProducto]);

                    $impuesto->setNotaCreditoHasProducto($notaCreditoHasProducto);

                    $notaCreditoHasProducto->addImpuesto($impuesto);
                    $ice += floatval($iceArray[$idProducto]);
                }

                if ($impuestoIRBPNR != null) {
                    $impuesto = new Impuesto();
                    $impuesto->setCodigo("5");
                    $impuesto->setCodigoPorcentaje($impuestoIRBPNR->getCodigoPorcentaje());
                    $impuesto->setTarifa("0");
                    $impuesto->setBaseImponible($baseImponible);
                    $impuesto->setValor($irbpnrArray[$idProducto]);

                    $impuesto->setNotaCreditoHasProducto($notaCreditoHasProducto);

                    $notaCreditoHasProducto->addImpuesto($impuesto);
                    $irbpnr += floatval($irbpnrArray[$idProducto]);
                }
                $descuento += floatval($descuentoArray[$idProducto]);

                $notaCreditoHasProducto->setCantidad($cantidadArray[$idProducto]);
                $notaCreditoHasProducto->setPrecioUnitario($precioUnitario[$idProducto]);
                $notaCreditoHasProducto->setCodigoProducto($codigoProducto[$idProducto]);
                $notaCreditoHasProducto->setNombre($nombreProducto[$idProducto]);
                $notaCreditoHasProducto->setDescuento($descuentoArray[$idProducto]);
                $notaCreditoHasProducto->setValorTotal($baseImponible);
                $notaCreditoHasProducto->setNotaCredito($entity);
                $entity->addNotaCreditoHasProducto($notaCreditoHasProducto);
            }

            $entity->setTipoDocMod($tipoDocMod);
            $fechaEmisionDocMod = str_replace("/", "-", $fechaEmisionDocMod);
            $fechaMod = new \DateTime($fechaEmisionDocMod);
            $entity->setFechaEmisionDocMod($fechaMod);
            $entity->setNroDocMod($numeroDocMod);
            $entity->setMotivo($motivo);

            $entity->setTotalSinImpuestos($subTotalSinImpuesto);
            $entity->setSubtotal12($subTotal12);
            $entity->setSubtotal0($subTotal0);
            $entity->setSubtotalNoIVA($subTotaNoObjeto);
            $entity->setSubtotalExentoIVA($subTotaExento);
            $entity->setValorICE($ice);
            $entity->setValorIRBPNR($irbpnr);
            $entity->setIva12($iva12);
            $entity->setTotalDescuento($descuento);
            $importeTotal = floatval($subTotalSinImpuesto) + floatval($ice) + floatval($irbpnr) + $iva12;
            $entity->setValorTotal($importeTotal);
            $em->persist($entity);
            $em->flush();

            if ($idNotaCredito == null || $idNotaCredito == '') {
                $ptoEmision[0]->setSecuencialNotaCredito($ptoEmision[0]->getSecuencialNotaCredito() + 1);
                $em->persist($ptoEmision[0]);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
        } else {
            throw $this->createNotFoundException('El usuario del sistema no tiene asignado un Punto de Emision.');
        }
    }

    /**
     * Displays a form to create a new NotaCredito entity.
     *
     * @Route("/nueva", name="notacredito_new")
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
     * Creates a new Factura entity.
     *
     * @Route("/descargar/{id}/{type}", name="notacredito_descargar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function descargarAction($id, $type = "zip") {
        $em = $this->getDoctrine()->getManager();
        $notaCredito = new NotaCredito();
        $notaCredito = $em->getRepository('FactelBundle:NotaCredito')->findNotaCreditoById($id);
        if ($notaCredito->getEstado() != "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Para descargar los archivos generados el comprobantes debe haber sido AUTORIZADO"
            );
            return $this->redirect($this->generateUrl('notacredito_show', array('id' => $notaCredito->getId())));
        }
        $archivoName = $notaCredito->getNombreArchivo();
        $pathXML = $notaCredito->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $notaCredito->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".xml";
        $pathPDF = $notaCredito->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $notaCredito->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".pdf";
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
     * Finds and displays a NotaCredito entity.
     *
     * @Route("/{id}", name="notacredito_show")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:NotaCredito')->findNotaCreditoById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NotaCredito entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to edit an existing NotaCredito entity.
     *
     * @Route("/{id}/edit", name="notacredito_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:NotaCredito')->findNotaCreditoById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find notacredito entity.');
        }
        if ($entity->getEstado() == "AUTORIZADO" || $entity->getEstado() == "ERROR") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Solo pueden ser editadas las Nota Credito en estado: NO AUTORIZADO, DEVUELTA y PROCESANDOSE"
            );
            return $this->redirect($this->generateUrl('notacredito_show', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a form to edit a NotaCredito entity.
     *
     * @param NotaCredito $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(NotaCredito $entity) {
        $form = $this->createForm(new NotaCreditoType(), $entity, array(
            'action' => $this->generateUrl('notacredito_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing NotaCredito entity.
     *
     * @Route("/{id}", name="notacredito_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_EMISOR")
     * @Template("FactelBundle:NotaCredito:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:NotaCredito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NotaCredito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('notacredito_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a NotaCredito entity.
     *
     * @Route("/{id}", name="notacredito_delete")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:NotaCredito')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NotaCredito entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('notacredito'));
    }

    /**
     * Creates a form to delete a NotaCredito entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('notacredito_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    private function claveAcceso($notaCredito, $emisor, $establecimiento, $ptoEmision, $fechaEmision) {
        $claveAcceso = str_replace("/", "", $fechaEmision);
        $claveAcceso .= "04";
        $claveAcceso .= $emisor->getRuc();
        $claveAcceso .= $notaCredito->getAmbiente();
        $serie = $establecimiento->getCodigo() . $ptoEmision->getCodigo();
        $claveAcceso .= $serie;
        $claveAcceso .= $notaCredito->getSecuencial();
        $claveAcceso .= "12345678";
        $claveAcceso .= $notaCredito->getTipoEmision();
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
