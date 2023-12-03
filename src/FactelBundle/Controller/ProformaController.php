<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Proforma;
use FactelBundle\Entity\Factura;
use FactelBundle\Entity\FacturaHasProducto;
use FactelBundle\Entity\ProformaHasProducto;
use FactelBundle\Entity\Impuesto;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

require_once 'ProcesarComprobanteElectronico.php';
require_once 'reader.php';

/**
 * Factura controller.
 *
 * @Route("/comprobantes/proforma")
 */
class ProformaController extends Controller {

    /**
     * Lists all Emisor entities.
     *
     * @Route("/", name="proforma")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        return array();
    }

    /**
     * Lists all Factura entities.
     *
     * @Route("/proformas", name="all_proforma")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     */
    public function proformasAction() {
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
        $idEstablecimiento = null;
        $userId = $this->get("security.context")->gettoken()->getuser()->getId();
        $user = $em->getRepository('FactelBundle:User')->find($userId);
        if ($this->get("security.context")->isGranted("ROLE_EMISOR_ADMIN")) {
            $emisorId = $user->getEmisor()->getId();
        } else {
            $idEstablecimiento = $user->getPtoEmision()->getEstablecimiento()->getId();
        }

        $count = $em->getRepository('FactelBundle:Proforma')->cantidadProformas($idEstablecimiento, $emisorId);
        $entities = $em->getRepository('FactelBundle:Proforma')->findProformas($sSearch, $iDisplayStart, $iDisplayLength, $idEstablecimiento, $emisorId);
        $totalDisplayRecords = $count;

        if ($sSearch != "") {
            $totalDisplayRecords = count($em->getRepository('FactelBundle:Proforma')->findProformas($sSearch, $iDisplayStart, 1000000, $idEstablecimiento, $emisorId));
        }
        $proformaArray = array();
        $i = 0;
        foreach ($entities as $entity) {
            $proformaArray[$i] = [$entity->getId(), $entity->getNumero(), $entity->getCliente()->getNombre(), $entity->getCliente()->getIdentificacion(), $entity->getFechaEmision()->format("d/m/Y"), $entity->getValorTotal(), $entity->getEstado()];
            $i++;
        }

        $arr = array(
            "iTotalRecords" => (int) $count,
            "iTotalDisplayRecords" => (int) $totalDisplayRecords,
            'aaData' => $proformaArray
        );

        $post_data = json_encode($arr);

        return new Response($post_data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     *
     * @Route("/eliminar/{id}", name="proforma_eliminar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function eliminarAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Proforma')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la proforma con ID = ' + $id);
        }

        foreach ($entity->getMensajes() as $mensaje) {
            $em->remove($mensaje);
        }

        foreach ($entity->getProformasHasProducto() as $proformaHasProducto) {
            foreach ($proformaHasProducto->getImpuestos() as $impuesto) {
                $em->remove($impuesto);
            }
            $em->remove($proformaHasProducto);
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('proforma'));
    }

    /**
     *
     * @Route("/proforma-factura/{id}", name="proforma_migrar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function migrarProformaAccion($id) {
        $proforma = new Proforma();
        $em = $this->getDoctrine()->getManager();
        $proforma = $em->getRepository('FactelBundle:Proforma')->findProformaById($id);

        if (!$proforma) {
            throw $this->createNotFoundException('No existe la proforma con ID = ' + $id);
        }
        if ($proforma->getEstado() == "MIGRADA" || $proforma->getEstado() == "ANULADA") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "En el estado actual de la proforma no puede ser migrada"
            );
            return $this->redirect($this->generateUrl('proforma_show', array('id' => $proforma->getId())));
        }

        $entity = new \FactelBundle\Entity\Factura();
        $ptoEmision = $em->getRepository('FactelBundle:PtoEmision')->findPtoEmisionEstabEmisorByUsuario($this->get("security.context")->gettoken()->getuser()->getId());
        $establecimiento = $ptoEmision[0]->getEstablecimiento();
        $emisor = $establecimiento->getEmisor();

        $entity = new Factura();
        date_default_timezone_set("America/Guayaquil");
        $fechaEmision = date("d/m/Y");
        $entity->setEstado("CREADA");
        $entity->setAmbiente($emisor->getAmbiente());
        $entity->setTipoEmision($emisor->getTipoEmision());
        $secuencial = $ptoEmision[0]->getSecuencialFactura();
        while (strlen($secuencial) < 9) {
            $secuencial = "0" . $secuencial;
        }
        $entity->setSecuencial($secuencial);
        $entity->setClaveAcceso($this->claveAcceso($entity, $emisor, $establecimiento, $ptoEmision[0], $fechaEmision));
        $fechaModificada = str_replace("/", "-", $fechaEmision);
        $fecha = new \DateTime($fechaModificada);
        $entity->setFechaEmision($fecha);


        $entity->setCliente($proforma->getCliente());
        $entity->setEmisor($emisor);
        $entity->setEstablecimiento($establecimiento);
        $entity->setPtoEmision($ptoEmision[0]);

        foreach ($proforma->getProformasHasProducto() as $proformaHasProducto) {
            $facturaHasProducto = new FacturaHasProducto();
          
            foreach ($proformaHasProducto->getImpuestos() as $impuesto) {
                  
                $impuestoFactura = new Impuesto();
                $impuestoFactura->setCodigo($impuesto->getCodigo());
                $impuestoFactura->setCodigoPorcentaje($impuesto->getCodigoPorcentaje());

                $impuestoFactura->setBaseImponible($impuesto->getBaseImponible());

                $impuestoFactura->setTarifa($impuesto->getTarifa());
                $impuestoFactura->setValor($impuesto->getValor());
                $impuestoFactura->setFacturaHasProducto($facturaHasProducto);
                $facturaHasProducto->addImpuesto($impuestoFactura);
            }
            $facturaHasProducto->setProducto($proformaHasProducto->getProducto());
            $facturaHasProducto->setCantidad($proformaHasProducto->getCantidad());
            $facturaHasProducto->setPrecioUnitario($proformaHasProducto->getPrecioUnitario());
            $facturaHasProducto->setDescuento($proformaHasProducto->getDescuento());
            $facturaHasProducto->setValorTotal($proformaHasProducto->getValorTotal());
            $facturaHasProducto->setNombre($proformaHasProducto->getNombre());
            $facturaHasProducto->setCodigoProducto($proformaHasProducto->getCodigoProducto());
            $facturaHasProducto->setFactura($entity);
            $entity->addFacturasHasProducto($facturaHasProducto);
        }

        $entity->setFormaPago("01");
        $entity->setTotalSinImpuestos($proforma->getTotalSinImpuestos());
        $entity->setSubtotal12($proforma->getSubtotal12());
        $entity->setSubtotal0($proforma->getSubtotal0());
        $entity->setSubtotalNoIVA(0.00);
        $entity->setSubtotalExentoIVA(0.00);
        $entity->setValorICE(0.00);
        $entity->setValorIRBPNR(0.00);
        $entity->setIva12($proforma->getIva12());
        $entity->setTotalDescuento($proforma->getTotalDescuento());
        $entity->setPropina(0);
        $entity->setValorTotal($proforma->getValorTotal());
        $em->persist($entity);
        $em->flush();

        $proforma->setEstado("MIGRADA");
        $em->persist($proforma);
        $ptoEmision[0]->setSecuencialFactura($ptoEmision[0]->getSecuencialFactura() + 1);
        $em->persist($ptoEmision[0]);
        $em->flush();
        return $this->redirect($this->generateUrl('factura_show', array('id' => $entity->getId())));
    }

    /**
     *
     * @Route("/enviar/{id}", name="proforma_enviar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function procesarAccion($id) {
        $entity = new Proforma();
        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $respuesta = null;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Proforma')->findProformaById($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe la proforma con ID = ' + $id);
        }
        
        $emisor = $entity->getEmisor();
        $proforma = new \proforma();
        $proforma->dirProformas = $emisor->getDirDocAutorizados();
        if ($entity->getEstablecimiento()->getDirLogo() != "") {
            $proforma->dirLogo = $entity->getEstablecimiento()->getDirLogo();
        } else {
            $proforma->dirLogo = $emisor->getDirLogo();
        }

        $configCorreo = new \configCorreo();
        $configCorreo->correoAsunto = "Proforma No. " . $entity->getNumero();
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


        $proforma->configCorreo = $configCorreo;
        $proforma->numero = $entity->getNumero();
        $proforma->razonSocial = $emisor->getRazonSocial();
        if ($entity->getEstablecimiento()->getNombreComercial() != "") {
            $proforma->nombreComercial = $entity->getEstablecimiento()->getNombreComercial();
        } else if ($emisor->getNombreComercial() != "") {
            $proforma->nombreComercial = $emisor->getNombreComercial();
        }
        $proforma->ruc = $emisor->getRuc(); //[Ruc]
        $proforma->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
        $proforma->dirMatriz = $emisor->getDireccionMatriz();
        $proforma->dirEstablecimiento = $entity->getEstablecimiento()->getDireccion();
        $proforma->razonSocialComprador = $entity->getCliente()->getNombre();
        $proforma->identificacionComprador = $entity->getCliente()->getIdentificacion();

        $proforma->subTotal0 = $entity->getSubtotal0();
        $proforma->subTotal12 = $entity->getSubtotal12();
        $proforma->subTotalSinImpuesto = $entity->getTotalSinImpuestos();
        $proforma->iva = $entity->getIva12();
        $proforma->totalDescuento = $entity->getTotalDescuento();
        $proforma->importeTotal = $entity->getValorTotal();


        $codigoPorcentajeIVA = "";
        $detalles = array();
        $proformasHasProducto = $entity->getProformasHasProducto();

        foreach ($proformasHasProducto as $proformaHasProducto) {
            $producto = new \FactelBundle\Entity\Producto();
            $producto = $proformaHasProducto->getProducto();
            $detalleProforma = new \detalleProforma();
            $detalleProforma->codigo = $proformaHasProducto->getCodigoProducto();
            $detalleProforma->descripcion = $proformaHasProducto->getNombre();
            $detalleProforma->cantidad = $proformaHasProducto->getCantidad();
            $detalleProforma->precioUnitario = $proformaHasProducto->getPrecioUnitario();
            $detalleProforma->descuento = $proformaHasProducto->getDescuento();
            $detalleProforma->precioTotalSinImpuesto = $proformaHasProducto->getValorTotal();

            $detalles[] = $detalleProforma;
        }


        $proforma->detalles = $detalles;

        $camposAdicionales = array();

        foreach ($entity->getComposAdic() as $campoAdic) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = $campoAdic->getNombre();
            $campoAdicional->valor = $campoAdic->getValor();

            $camposAdicionales [] = $campoAdic;
        }

        $cliente = $entity->getCliente();
        if ($cliente->getDireccion() != "") {
            $proforma->direccionComprador = $cliente->getDireccion();
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
        if ($entity->getObservacion() != "") {
            $campoAdic = new \campoAdicional();
            $campoAdic->nombre = "Observacion";
            $campoAdic->valor = $entity->getObservacion();

            $camposAdicionales [] = $campoAdic;
        }
        if (count($camposAdicionales) > 0) {
            $proforma->infoAdicional = $camposAdicionales;
        }


        $procesarProforma = new \procesarProforma();
        $procesarProforma->proforma = $proforma;
        $respuesta = $procesarComprobanteElectronico->procesarProforma($procesarProforma);

        $entity->setEstado($respuesta->return->estadoComprobante);
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
                $mensajeGenerado->setProforma($entity);
                $em->persist($mensajeGenerado);
            }
        }
        $entity->setNombreArchivo("PROFORMA-" . $entity->getNumero());
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('proforma_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/anular/{id}", name="proforma_anular")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function anularAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:Proforma')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la proforma con ID = ' + $id);
        }
        $entity->setEstado("ANULADA");
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('proforma_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Proforma entity.
     *
     * @Route("/", name="proforma_create")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     * @Template("FactelBundle:Proforma:new.html.twig")
     */
    public function createAction(Request $request) {

        $fechaEmision = $request->request->get("fechaEmision");
        $idCliente = $request->request->get("idCliente");
        $nombre = $request->request->get("nombre");
        $celular = $request->request->get("celular");
        $email = $request->request->get("email");
        $tipoIdentificacion = $request->request->get("tipoIdentificacion");
        $identificacion = $request->request->get("identificacion");
        $direccion = $request->request->get("direccion");
        $nuevoCliente = $request->request->get("nuevoCliente");
        $idProforma = $request->request->get("idProforma");
        $observacion = $request->request->get("observacion");
        $numero = $request->request->get("numero");

        $texto = "";
        $campos = "";
        $cantidadErrores = 0;

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

        if ($cantidadErrores > 0) {
            if ($cantidadErrores == 1) {
                $texto = "El campo <strong>" . $campos . "</strong> no puede estar vacios";
            } else {
                $texto = "Los campos " . $campos . " no pueden estar vacios";
            }
            $this->get('session')->getFlashBag()->add(
                    'notice', $texto
            );

            return $this->redirect($this->generateUrl('proforma_new', array()));
        }
        $em = $this->getDoctrine()->getManager();
        if ($idProforma != null && $idProforma != '') {
            $entity = $em->getRepository('FactelBundle:Proforma')->find($idProforma);
            if (!is_null($entity)) {
                $mensajes = $entity->getMensajes();
                foreach ($mensajes as $mensaje) {
                    $em->remove($mensaje);
                }
                $proformasHasProducto = $entity->getProformasHasProducto();
                foreach ($proformasHasProducto as $proformaHasProducto) {
                    foreach ($proformaHasProducto->getImpuestos() as $impuesto) {
                        $em->remove($impuesto);
                    }
                    $em->remove($proformaHasProducto);
                }

                $em->flush();
            }
        } else {
            $entity = new Proforma();
        }



        $ptoEmision = $em->getRepository('FactelBundle:PtoEmision')->findPtoEmisionEstabEmisorByUsuario($this->get("security.context")->gettoken()->getuser()->getId());

        if ($ptoEmision != null && count($ptoEmision) > 0) {
            $establecimiento = $ptoEmision[0]->getEstablecimiento();
            $emisor = $establecimiento->getEmisor();
            $entity->setEstado("CREADA");
            $entity->setNumero($numero);
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
                    return $this->redirect($this->generateUrl('proforma_new', array()));
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

            $subTotalSinImpuesto = 0;
            $subTotal12 = 0;
            $subTotal0 = 0;
            $descuento = 0;
            $iva12 = 0;
            $valorTotal = 0;

            $idProductoArray = $request->request->get("idProducto");
            if ($idProductoArray == null) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "La proforma debe contener al menos un producto"
                );
                return $this->redirect($this->generateUrl('proforma_new', array()));
            }
            $productos = $em->getRepository('FactelBundle:Producto')->findById($idProductoArray);
            if (count($productos) == 0) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Los productos solicitados para esta proforma no se encuentran disponibles"
                );
                return $this->redirect($this->generateUrl('proforma_new', array()));
            }
            foreach ($productos as $producto) {
                $subsidio = 0.0;
                $proformaHasProducto = new ProformaHasProducto();
                $idProducto = $producto->getId();

                $proformaHasProducto->setProducto($producto);
                $impuestoIva = $producto->getImpuestoIVA();

                $cantidadArray = $request->request->get("cantidad");
                $descuentoArray = $request->request->get("descuento");
                $precioUnitario = $request->request->get("precio");
                $nombreProducto = $request->request->get("nombreProducto");
                $codigoProducto = $request->request->get("codigoProducto");

                $baseImponible = 0;
                if ($impuestoIva != null) {
                    $impuesto = new Impuesto();
                    $impuesto->setCodigo("2");
                    $impuesto->setCodigoPorcentaje($impuestoIva->getCodigoPorcentaje());
                    $baseImponible = floatval($cantidadArray[$idProducto]) * floatval($precioUnitario[$idProducto]) - floatval($descuentoArray[$idProducto]);
                    $impuesto->setBaseImponible($baseImponible);
                    $impuesto->setTarifa("0");
                    $impuesto->setValor(0.00);

                    if ($impuestoIva->getTarifa() == 0) {
                        $subTotal0 += $baseImponible;
                    } else {
                        $impuesto->setTarifa($impuestoIva->getTarifa());
                        $impuesto->setValor(round($baseImponible * $impuestoIva->getTarifa() / 100, 2));
                        $subTotal12 += $baseImponible;
                        $tarifaIva = $impuestoIva->getTarifa();
                    }

                    $impuesto->setProformaHasProducto($proformaHasProducto);

                    $proformaHasProducto->addImpuesto($impuesto);
                    $subTotalSinImpuesto += $baseImponible;
                }

                $descuento += floatval($descuentoArray[$idProducto]);
                $proformaHasProducto->setCantidad($cantidadArray[$idProducto]);
                $proformaHasProducto->setPrecioUnitario($precioUnitario[$idProducto]);
                $proformaHasProducto->setDescuento($descuentoArray[$idProducto]);
                $proformaHasProducto->setValorTotal($baseImponible);
                $proformaHasProducto->setNombre($nombreProducto[$idProducto]);
                $proformaHasProducto->setCodigoProducto($codigoProducto[$idProducto]);
                $proformaHasProducto->setProforma($entity);
                $entity->addProformasHasProducto($proformaHasProducto);
            }

            if (isset($tarifaIva)) {
                $iva12 = round($subTotal12 * $tarifaIva / 100, 2);
            }
            $entity->setTotalSinImpuestos($subTotalSinImpuesto);
            $entity->setSubtotal12($subTotal12);
            $entity->setSubtotal0($subTotal0);
            $entity->setIva12($iva12);
            $entity->setTotalDescuento($descuento);
            $importeTotal = floatval($subTotalSinImpuesto) + $iva12;
            $entity->setValorTotal($importeTotal);
            $em->persist($entity);
            $em->flush();

            if ($idProforma == null || $idProforma == '') {
                $numero = $establecimiento->getSecuencialProforma() == 0 ? 1 : $establecimiento->getSecuencialProforma();
                $establecimiento->setSecuencialProforma($numero + 1);
                $em->persist($establecimiento);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('proforma_show', array('id' => $entity->getId())));
        } else {
            throw $this->createNotFoundException('El usuario del sistema no tiene asignado un Punto de Emision.');
        }
    }

    /**
     * Creates a new Proforma entity.
     *
     * @Route("/descargar/{id}/{type}", name="proforma_descargar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function descargarAction($id) {
        $em = $this->getDoctrine()->getManager();
        $proforma = new Proforma();
        $proforma = $em->getRepository('FactelBundle:Proforma')->findProformaById($id);

        $archivoName = $proforma->getNombreArchivo();
        $pathPDF = $proforma->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $proforma->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".pdf";

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

    /**
     * Displays a form to create a new Factura entity.
     *
     * @Route("/nueva", name="proforma_new")
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
     * Finds and displays a Factura entity.
     *
     * @Route("/{id}", name="proforma_show")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Proforma')->findProformaById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Factura entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Factura entity.
     *
     * @Route("/{id}/editar", name="proforma_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:Proforma')->findProformaById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proforma entity.');
        }
        if ($entity->getEstado() != "CREADA" && $entity->getEstado() != "ENVIADA") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Solo puede ser editada la proforma en estado: CREADA, ENVIADA"
            );
            return $this->redirect($this->generateUrl('proforma_show', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Deletes a Factura entity.
     *
     * @Route("/{id}", name="factura_delete")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:Factura')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Factura entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('factura'));
    }

    /**
     * Creates a form to delete a Factura entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('factura_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    public function getUploadRootDir() {
// the absolute directory path where uploaded
// documents should be saved
        return __DIR__ . '/../../../web/upload';
    }

    private function claveAcceso($factura, $emisor, $establecimiento, $ptoEmision, $fechaEmision) {
        $claveAcceso = str_replace("/", "", $fechaEmision);
        $claveAcceso .= "01";
        $claveAcceso .= $emisor->getRuc();
        $claveAcceso .= $factura->getAmbiente();
        $serie = $establecimiento->getCodigo() . $ptoEmision->getCodigo();
        $claveAcceso .= $serie;
        $claveAcceso .= $factura->getSecuencial();
        $claveAcceso .= "12345678";
        $claveAcceso .= $factura->getTipoEmision();
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
