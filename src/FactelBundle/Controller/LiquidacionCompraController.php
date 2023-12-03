<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\LiquidacionCompra;
use FactelBundle\Entity\LiquidacionCompraHasProducto;
use FactelBundle\Entity\Impuesto;
use FactelBundle\Entity\CampoAdicional;
use FactelBundle\Entity\LiquidacionCompraReembolso;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use FactelBundle\Util;

require_once 'ProcesarComprobanteElectronico.php';
require_once 'reader.php';

/**
 * Liquidacion controller.
 *
 * @Route("/comprobantes/liquidacion-compra")
 */
class LiquidacionCompraController extends Controller {

    /**
     * Lists all Emisor entities.
     *
     * @Route("/", name="liquidacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        return array();
    }

    /**
     * Lists all Liquidacion entities.
     *
     * @Route("/liquidaciones", name="all_liquidaciones")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     */
    public function liquidacionesAction() {
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
        $count = $em->getRepository('FactelBundle:LiquidacionCompra')->cantidadLiquidaciones($idPtoEmision, $emisorId);
        $entities = $em->getRepository('FactelBundle:LiquidacionCompra')->findLiquidaciones($sSearch, $iDisplayStart, $iDisplayLength, $idPtoEmision, $emisorId);
        $totalDisplayRecords = $count;

        if ($sSearch != "") {
            $totalDisplayRecords = count($em->getRepository('FactelBundle:LiquidacionCompra')->findLiquidaciones($sSearch, $iDisplayStart, 1000000, $idPtoEmision, $emisorId));
        }
        $liquidacionesArray = array();
        $i = 0;
        foreach ($entities as $entity) {
            $fechaAutorizacion = "";
            $fechaAutorizacion = $entity->getFechaAutorizacion() != null ? $entity->getFechaAutorizacion()->format("d/m/Y H:i:s") : "";
            $liquidacionesArray[$i] = [$entity->getId(), $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial(), $entity->getCliente()->getNombre(), $entity->getFechaEmision()->format("d/m/Y"), $fechaAutorizacion, $entity->getValorTotal(), $entity->getEstado()];
            $i++;
        }

        $arr = array(
            "iTotalRecords" => (int) $count,
            "iTotalDisplayRecords" => (int) $totalDisplayRecords,
            'aaData' => $liquidacionesArray
        );

        $post_data = json_encode($arr);

        return new Response($post_data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Creates a new Liquidacion entity.
     *
     * @Route("/procesar/{id}", name="liquidacion_procesar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function procesarAccion($id) {
        $entity = new LiquidacionCompra();
        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $respuesta = null;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:LiquidacionCompra')->findLiquidacionById($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Liquidacion con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Este comprobante electronico ya fue autorizado"
            );
            return $this->redirect($this->generateUrl('liquidacion_show', array('id' => $entity->getId())));
        }
        $emisor = $entity->getEmisor();
        $hoy = date("Y-m-d");
        if ($emisor->getPlan() != null && $emisor->getFechaFin()) {
            if ($hoy > $emisor->getFechaFin()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Su plan ha caducado por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('liquidacion_show', array('id' => $entity->getId())));
            }
            if ($emisor->getCantComprobante() > $emisor->getPlan()->getCantComprobante()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Ha superado el numero de comprobantes contratado en su plan, por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('liquidacion_show', array('id' => $entity->getId())));
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
            $liquidacion = new \liquidacionCompra();
            $liquidacion->configAplicacion = $configApp;
            $liquidacion->configCorreo = $configCorreo;

            $liquidacion->ambiente = $entity->getAmbiente();
            $liquidacion->tipoEmision = $entity->getTipoEmision();
            $liquidacion->razonSocial = $emisor->getRazonSocial();
            if ($entity->getEstablecimiento()->getNombreComercial() != "") {
                $liquidacion->nombreComercial = $entity->getEstablecimiento()->getNombreComercial();
            } else if ($emisor->getNombreComercial() != "") {
                $liquidacion->nombreComercial = $emisor->getNombreComercial();
            }
            $liquidacion->ruc = $emisor->getRuc(); //[Ruc]
            $liquidacion->codDoc = "03";
            $liquidacion->establecimiento = $entity->getEstablecimiento()->getCodigo();
            $liquidacion->ptoEmision = $entity->getPtoEmision()->getCodigo();
            $liquidacion->secuencial = $entity->getSecuencial();
            $liquidacion->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
            $liquidacion->dirMatriz = $emisor->getDireccionMatriz();
            $liquidacion->dirEstablecimiento = $entity->getEstablecimiento()->getDireccion();
            if ($emisor->getContribuyenteEspecial() != "") {
                $liquidacion->contribuyenteEspecial = $emisor->getContribuyenteEspecial();
            }
            $liquidacion->obligadoContabilidad = $emisor->getObligadoContabilidad();

            $liquidacion->tipoIdentificacionProveedor = $entity->getCliente()->getTipoIdentificacion();
            $liquidacion->razonSocialProveedor = $entity->getCliente()->getNombre();
            $liquidacion->identificacionProveedor = $entity->getCliente()->getIdentificacion();
            if ($entity->getCliente()->getDireccion() != "") {
                $liquidacion->direccionProveedor = $entity->getCliente()->getDireccion();
            }
            $liquidacion->totalSinImpuestos = $entity->getTotalSinImpuestos();
            $liquidacion->totalDescuento = $entity->getTotalDescuento();

            $liquidacion->importeTotal = $entity->getValorTotal();
            $liquidacion->moneda = "DOLAR"; //DOLAR
            $pagos = array();

            $pago = new \pago();
            $pago->formaPago = $entity->getFormaPago();
            if ($entity->getPlazo()) {
                $pago->plazo = $entity->getPlazo();
                $pago->unidadTiempo = "Dias";
            }
            $pago->total = $entity->getValorTotal();
            $pagos [] = $pago;

            $liquidacion->pagos = $pagos;

            $reembolsos = array();
            $liquidacionCompraReembolsos = $entity->getReembolsos();
            if (count($liquidacionCompraReembolsos) > 0) {
                $liquidacion->codDocReemb = "41";
                $baseTotal = 0.00;
                $ivaTotal = 0.00;
                foreach ($liquidacionCompraReembolsos as $liquidacionCompraReembolso) {
                    $reembolso = new \reembolsoFactura();
                    $reembolso->tipoIdentificacionProveedorReembolso = $liquidacionCompraReembolso->getTipoIdentificacionProveedorReembolso();
                    $reembolso->identificacionProveedorReembolso = $liquidacionCompraReembolso->getIdentificacionProveedorReembolso();
                    $reembolso->codPaisPagoProveedorReembolso = "593";
                    $reembolso->tipoProveedorReembolso = $liquidacionCompraReembolso->getTipoProveedorReembolso();
                    $reembolso->codDocReembolso = "01";
                    $reembolso->estabDocReembolso = $liquidacionCompraReembolso->getEstabDocReembolso();
                    $reembolso->ptoEmiDocReembolso = $liquidacionCompraReembolso->getPtoEmiDocReembolso();
                    $reembolso->secuencialDocReembolso = $liquidacionCompraReembolso->getSecuencialDocReembolso();
                    $reembolso->fechaEmisionDocReembolso = $liquidacionCompraReembolso->getFechaEmisionDocReembolso()->format("d/m/Y");
                    $reembolso->numeroautorizacionDocReemb = $liquidacionCompraReembolso->getNumeroautorizacionDocReemb();

                    $impuestosReembolso = array();
                    if ($liquidacionCompraReembolso->getBaseImponibleSinIvaReembolso() > 0) {
                        $impuesto = new \impuesto();
                        $impuesto->codigo = "2";
                        $impuesto->codigoPorcentaje = "0";
                        $impuesto->tarifa = "0";
                        $impuesto->valor = "0.00";
                        $impuesto->baseImponible = sprintf("%01.2f", $liquidacionCompraReembolso->getBaseImponibleSinIvaReembolso());
                        $impuestosReembolso[] = $impuesto;
                    }
                    if ($liquidacionCompraReembolso->getBaseImponibleReembolso() > 0) {
                        $impuesto = new \impuesto();
                        $impuesto->codigo = "2";
                        $impuesto->codigoPorcentaje = "2";
                        $impuesto->tarifa = "12";
                        $impuesto->baseImponible = sprintf("%01.2f", $liquidacionCompraReembolso->getBaseImponibleReembolso());
                        $iva = $liquidacionCompraReembolso->getBaseImponibleReembolso() * 0.12;
                        $impuesto->valor = sprintf("%01.2f", $iva);
                        $ivaTotal = $ivaTotal + $iva;
                        $impuestosReembolso[] = $impuesto;
                    }
                    $baseTotal = $baseTotal + $liquidacionCompraReembolso->getBaseImponibleReembolso() + $liquidacionCompraReembolso->getBaseImponibleSinIvaReembolso();
                    $reembolso->impuestos = $impuestosReembolso;
                    $reembolsos[] = $reembolso;
                }

                $total = $baseTotal + $ivaTotal;
                $liquidacion->totalComprobantesReembolso = sprintf("%01.2f", $total);
                $liquidacion->totalBaseImponibleReembolso = sprintf("%01.2f", $baseTotal);
                $liquidacion->totalImpuestoReembolso = sprintf("%01.2f", $ivaTotal);
                $liquidacion->reembolsos = $reembolsos;
            }

            $codigoPorcentajeIVA = "";
            $detalles = array();
            $liquidacionesCompraHasProducto = $entity->getLiquidacionesCompraHasProducto();
            $impuestosTotalICE = array();
            $baseImponibleICE = array();
            $impuestosTotalIRBPNR = array();
            $baseImponibleIRBPNR = array();
            foreach ($liquidacionesCompraHasProducto as $liquidacionHasProducto) {
                $producto = new \FactelBundle\Entity\Producto();
                $producto = $liquidacionHasProducto->getProducto();
                $detalleLiquidacion = new \detalleLiquidacionCompra();
                $detalleLiquidacion->codigoPrincipal = $liquidacionHasProducto->getCodigoProducto();
                if ($producto->getCodigoAuxiliar() != "") {
                    $detalleLiquidacion->codigoAuxiliar = $producto->getCodigoAuxiliar();
                }
                $detalleLiquidacion->descripcion = $liquidacionHasProducto->getNombre();
                $detalleLiquidacion->cantidad = $liquidacionHasProducto->getCantidad();
                $detalleLiquidacion->precioUnitario = $liquidacionHasProducto->getPrecioUnitario();
                $detalleLiquidacion->descuento = $liquidacionHasProducto->getDescuento();
                $detalleLiquidacion->precioTotalSinImpuesto = $liquidacionHasProducto->getValorTotal();

                $impuestos = array();
                $impuestosProducto = $liquidacionHasProducto->getImpuestos();
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
                $detalleLiquidacion->impuestos = $impuestos;
                $detalles[] = $detalleLiquidacion;
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

            $liquidacion->detalles = $detalles;
            $liquidacion->totalConImpuesto = $totalImpuestoArray;

            $camposAdicionales = array();

            foreach ($entity->getComposAdic() as $campoAdic) {
                $campoAdicional = new \campoAdicional();
                $campoAdicional->nombre = $campoAdic->getNombre();
                $campoAdicional->valor = $campoAdic->getValor();

                $camposAdicionales [] = $campoAdic;
            }
            if ($emisor->getRegimenRimpe()) {
                $liquidacion->regimenRimpes = "Contribuyente Negocio Popular - Régimen RIMPE";
            }
            if ($emisor->getRegimenRimpe1()) {
                $liquidacion->regimenRimpes1 = "Contribuyente Régimen RIMPE";
            }

            if ($emisor->getResolucionAgenteRetencion()) {
                $liquidacion->agenteRetencion = $emisor->getResolucionAgenteRetencion();
            }

            $cliente = $entity->getCliente();
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
            if ($entity->getObservacion() != "") {
                $campoAdic = new \campoAdicional();
                $campoAdic->nombre = "Observacion";
                $campoAdic->valor = $entity->getObservacion();

                $camposAdicionales [] = $campoAdic;
            }
            if (count($camposAdicionales) > 0) {
                $liquidacion->infoAdicional = $camposAdicionales;
            }

            $procesarComprobante = new \procesarComprobante();
            $procesarComprobante->comprobante = $liquidacion;

            if (!$entity->getFirmado() || $entity->getEstado() == "CREADA") {
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
            $comprobantePendiente->codDoc = "03";
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
                    $comprobantePendiente->codDoc = "03";
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
            $entity->setNombreArchivo("LIQ" . $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial());
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
                $mensajeGenerado->setLiquidacionCompra($entity);
                $em->persist($mensajeGenerado);
            }
        }
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('liquidacion_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Liquidacion entity.
     *
     * @Route("/", name="liquidacion_create")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     * @Template("FactelBundle:LiquidacionCompra:new.html.twig")
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
        $idLiquidacion = $request->request->get("idLiquidacion");
        $formaPago = $request->request->get("formaPago");
        $plazo = $request->request->get("plazo");
        $observacion = $request->request->get("observacion");

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
        if ($formaPago == '') {
            $campos .= "Forma Pago, ";
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

            return $this->redirect($this->generateUrl('liquidacion_new', array()));
        }
        $em = $this->getDoctrine()->getManager();
        if ($idLiquidacion != null && $idLiquidacion != '') {
            $entity = new LiquidacionCompra();
            $entity = $em->getRepository('FactelBundle:LiquidacionCompra')->find($idLiquidacion);
            if (!is_null($entity)) {
                $mensajes = $entity->getMensajes();
                foreach ($mensajes as $mensaje) {
                    $em->remove($mensaje);
                }
                $liquidacionesCompraHasProducto = $entity->getLiquidacionesCompraHasProducto();
                foreach ($liquidacionesCompraHasProducto as $liquidacionHasProducto) {
                    foreach ($liquidacionHasProducto->getImpuestos() as $impuesto) {
                        $em->remove($impuesto);
                    }
                    $em->remove($liquidacionHasProducto);
                }

                $reembolsos = $entity->getReembolsos();
                foreach ($reembolsos as $reembolso) {
                    $em->remove($reembolso);
                }

                $em->flush();
            }
        } else {
            $entity = new LiquidacionCompra();
        }


        $ptoEmision = $em->getRepository('FactelBundle:PtoEmision')->findPtoEmisionEstabEmisorByUsuario($this->get("security.context")->gettoken()->getuser()->getId());

        if ($ptoEmision != null && count($ptoEmision) > 0) {

            $liquidacionCompraConReembolso = $request->request->get("conReembolso");
            if ($liquidacionCompraConReembolso) {
                $tipoProveedorArray = $request->request->get("tipoProveedorReembolso");
                $baseImponibleArray = $request->request->get("baseImponibleReembolso");
                $baseImponibleSinIvaArray = $request->request->get("baseImponibleSinIvaReembolso");
                $lineaConIvaArray = $request->request->get("lineaConIva");

                $identificacionReembolsoArray = $request->request->get("identificacionReembolso");
                $estbleciemientoReembolsoArray = $request->request->get("estbleciemientoReembolso");
                $ptoEmisionReembolsoArray = $request->request->get("ptoEmisionReembolso");
                $secuencialReembolsoArray = $request->request->get("secuencialReembolso");
                $fechaReembolsoArray = $request->request->get("fechaReembolso");
                $autorizacionReembolsoArray = $request->request->get("autorizacionReembolso");


                if ($tipoProveedorArray == null) {
                    $this->get('session')->getFlashBag()->add(
                            'notice', "Una factura de reembolso tiene que tener al menos un detalle de reembolso"
                    );
                    return $this->redirect($this->generateUrl('factura_new', array()));
                }
                foreach ($tipoProveedorArray as $key => $tipoProveedor) {
                    $reembolso = new LiquidacionCompraReembolso();
                    $reembolso->setTipoProveedorReembolso($tipoProveedorArray[$key]);
                    $reembolso->setCodDocReembolso("01");
                    $tipoIdentificacionReemblso = "08";
                    if ($identificacionReembolsoArray[$key] == "9999999999999") {
                        $tipoIdentificacionReemblso = "07";
                    } else if (strlen($identificacionReembolsoArray[$key]) == 13) {
                        $tipoIdentificacionReemblso = "04";
                    } else if (strlen($identificacionReembolsoArray[$key]) == 10) {
                        $tipoIdentificacionReemblso = "05";
                    }

                    $reembolso->setTipoIdentificacionProveedorReembolso($tipoIdentificacionReemblso);
                    $reembolso->setIdentificacionProveedorReembolso($identificacionReembolsoArray[$key]);
                    $reembolso->setEstabDocReembolso($estbleciemientoReembolsoArray[$key]);
                    $reembolso->setPtoEmiDocReembolso($ptoEmisionReembolsoArray[$key]);
                    $reembolso->setSecuencialDocReembolso($secuencialReembolsoArray[$key]);
                    $fechaModificada = str_replace("/", "-", $fechaReembolsoArray[$key]);
                    $fecha = new \DateTime($fechaModificada);
                    $reembolso->setFechaEmisionDocReembolso($fecha);
                    $reembolso->setNumeroautorizacionDocReemb($autorizacionReembolsoArray[$key]);

                    $baseImponibleReembolso = floatval($baseImponibleArray[$key]);
                    $baseImponibleSinIva = floatval($baseImponibleSinIvaArray[$key]);
                    $ivaReembolso = 0.00;
                    if ($baseImponibleReembolso > 0) {
                        $ivaReembolso = round($baseImponibleReembolso * 0.12, 2);
                    }
                    $reembolso->setBaseImponibleSinIvaReembolso($baseImponibleSinIva);
                    $reembolso->setBaseImponibleReembolso($baseImponibleReembolso);
                    $reembolso->setImpuestoReembolso($ivaReembolso);
                    $reembolso->setLiquidacionCompra($entity);
                    $entity->addReembolso($reembolso);
                }
            }

            $establecimiento = $ptoEmision[0]->getEstablecimiento();
            $emisor = $establecimiento->getEmisor();

            $entity->setEstado("CREADA");
            $entity->setAmbiente($emisor->getAmbiente());
            $entity->setTipoEmision($emisor->getTipoEmision());
            $entity->setSecuencial($secuencial);
            $entity->setClaveAcceso($this->claveAcceso($entity, $emisor, $establecimiento, $ptoEmision[0], $fechaEmision));
            $entity->setObservacion($observacion);
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
                    return $this->redirect($this->generateUrl('liquidacion_new', array()));
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
            $propina = 0;
            $valorTotal = 0;

            $idProductoArray = $request->request->get("idProducto");
            if ($idProductoArray == null) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "La Liquidacion debe contener al menos un producto"
                );
                return $this->redirect($this->generateUrl('liquidacion_new', array()));
            }
            $productos = $em->getRepository('FactelBundle:Producto')->findById($idProductoArray);
            if (count($productos) == 0) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Los productos solicitados para esta Liquidacion no se encuentran disponibles"
                );
                return $this->redirect($this->generateUrl('liquidacion_new', array()));
            }
            foreach ($productos as $producto) {
                $liquidacionHasProducto = new LiquidacionCompraHasProducto();
                $idProducto = $producto->getId();

                $liquidacionHasProducto->setProducto($producto);
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

                    $impuesto->setLiquidacionCompraHasProducto($liquidacionHasProducto);

                    $liquidacionHasProducto->addImpuesto($impuesto);
                    $subTotalSinImpuesto += $baseImponible;
                }
                if ($impuestoICE != null) {
                    $impuesto = new Impuesto();
                    $impuesto->setCodigo("3");
                    $impuesto->setCodigoPorcentaje($impuestoICE->getCodigoPorcentaje());
                    $impuesto->setTarifa("0");
                    $impuesto->setBaseImponible($baseImponible);
                    $impuesto->setValor($iceArray[$idProducto]);

                    $impuesto->setLiquidacionCompraHasProducto($liquidacionHasProducto);

                    $liquidacionHasProducto->addImpuesto($impuesto);
                    $ice += floatval($iceArray[$idProducto]);
                }

                if ($impuestoIRBPNR != null) {
                    $impuesto = new Impuesto();
                    $impuesto->setCodigo("5");
                    $impuesto->setCodigoPorcentaje($impuestoIRBPNR->getCodigoPorcentaje());
                    $impuesto->setTarifa("0");
                    $impuesto->setBaseImponible($baseImponible);
                    $impuesto->setValor($irbpnrArray[$idProducto]);

                    $impuesto->setLiquidacionCompraHasProducto($liquidacionHasProducto);

                    $liquidacionHasProducto->addImpuesto($impuesto);
                    $irbpnr += floatval($irbpnrArray[$idProducto]);
                }
                $descuento += floatval($descuentoArray[$idProducto]);

                $liquidacionHasProducto->setCantidad($cantidadArray[$idProducto]);
                $liquidacionHasProducto->setPrecioUnitario($precioUnitario[$idProducto]);
                $liquidacionHasProducto->setDescuento($descuentoArray[$idProducto]);
                $liquidacionHasProducto->setValorTotal($baseImponible);
                $liquidacionHasProducto->setNombre($nombreProducto[$idProducto]);
                $liquidacionHasProducto->setCodigoProducto($codigoProducto[$idProducto]);
                $liquidacionHasProducto->setLiquidacionCompra($entity);
                $entity->addLiquidacionesCompraHasProducto($liquidacionHasProducto);
            }
            $entity->setFormaPago($formaPago);
            if ($plazo) {
                $entity->setPlazo($plazo);
            }


            $entity->setTotalSinImpuestos($subTotalSinImpuesto);
            $entity->setSubtotal12($subTotal12);
            $entity->setSubtotal0($subTotal0);
            $entity->setSubtotalNoIVA($subTotaNoObjeto);
            $entity->setSubtotalExentoIVA($subTotaExento);
            $entity->setValorICE($ice);
            $entity->setValorIRBPNR($irbpnr);
            $entity->setIva12($iva12);
            $entity->setTotalDescuento($descuento);
            $entity->setPropina(0);
            $importeTotal = floatval($subTotalSinImpuesto) + floatval($ice) + floatval($irbpnr) + $iva12;

            $entity->setValorTotal($importeTotal);
            $em->persist($entity);
            $em->flush();
            if ($idLiquidacion == null || $idLiquidacion == '') {
                $ptoEmision[0]->setSecuencialLiquidacionCompra($ptoEmision[0]->getSecuencialLiquidacionCompra() + 1);
                $em->persist($ptoEmision[0]);
                $em->flush();
            }
//$this->funtionCrearXmlPDF($entity->getId());
            return $this->redirect($this->generateUrl('liquidacion_show', array('id' => $entity->getId())));
        } else {
            throw $this->createNotFoundException('El usuario del sistema no tiene asignado un Punto de Emision.');
        }
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/anular/{id}", name="liquidacion_anular")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function anularAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:LiquidacionCompra')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la liquidacion en compra con ID = ' + $id);
        }
        $entity->setEstado("ANULADA");
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('liquidacion_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/eliminar/{id}", name="liquidacion_eliminar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function eliminarAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:LiquidacionCompra')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la liquidacion en compra con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "No se puede eliminar un documento autorizado"
            );
            return $this->redirect($this->generateUrl('liquidacion_show', array('id' => $entity->getId())));
        }

        foreach ($entity->getMensajes() as $mensaje) {
            $em->remove($mensaje);
        }
        foreach ($entity->getLiquidacionesCompraHasProducto() as $liquidacionCompraHasProducto) {
            foreach ($liquidacionCompraHasProducto->getImpuestos() as $impuesto) {
                $em->remove($impuesto);
            }
            $em->remove($liquidacionCompraHasProducto);
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('liquidacion'));
    }

    /**
     * Creates a new Liquidacion entity.
     *
     * @Route("/descargar/{id}/{type}", name="liquidacion_descargar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function descargarAction($id, $type = "zip") {
        $em = $this->getDoctrine()->getManager();
        $liquidacion = new LiquidacionCompra();
        $liquidacion = $em->getRepository('FactelBundle:LiquidacionCompra')->findLiquidacionById($id);

        $archivoName = $liquidacion->getNombreArchivo();
        $pathXML = $liquidacion->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $liquidacion->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".xml";
        $pathPDF = $liquidacion->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $liquidacion->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".pdf";
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
     * Displays a form to create a new Liquidacion entity.
     *
     * @Route("/nueva", name="liquidacion_new")
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
     * Finds and displays a Liquidacion entity.
     *
     * @Route("/{id}", name="liquidacion_show")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:LiquidacionCompra')->findLiquidacionById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidacion entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Liquidacion entity.
     *
     * @Route("/{id}/editar", name="liquidacion_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:LiquidacionCompra')->findLiquidacionById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidacion entity.');
        }
        if ($entity->getEstado() == "AUTORIZADO" || $entity->getEstado() == "ERROR") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Solo puede ser editada las liquidaciones en estado: NO AUTORIZADO, DEVUELTA y PROCESANDOSE"
            );
            return $this->redirect($this->generateUrl('liquidacion_show', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Deletes a Liquidacion entity.
     *
     * @Route("/{id}", name="liquidacion_delete")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:Liquidacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Liquidacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('liquidacion'));
    }

    /**
     * Creates a form to delete a Liquidacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('liquidacion_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    private function claveAcceso($liquidacion, $emisor, $establecimiento, $ptoEmision, $fechaEmision) {
        $claveAcceso = str_replace("/", "", $fechaEmision);
        $claveAcceso .= "03";
        $claveAcceso .= $emisor->getRuc();
        $claveAcceso .= $liquidacion->getAmbiente();
        $serie = $establecimiento->getCodigo() . $ptoEmision->getCodigo();
        $claveAcceso .= $serie;
        $claveAcceso .= $liquidacion->getSecuencial();
        $claveAcceso .= "12345678";
        $claveAcceso .= $liquidacion->getTipoEmision();
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

    public function getUploadRootDir() {
// the absolute directory path where uploaded
// documents should be saved
        return __DIR__ . '/../../../web/upload';
    }

}
