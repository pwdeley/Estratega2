<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\NotaDebito;
use FactelBundle\Entity\Motivo;
use FactelBundle\Entity\Impuesto;
use FactelBundle\Form\NotaDebitoType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

require_once 'ProcesarComprobanteElectronico.php';

/**
 * NotaDebito controller.
 *
 * @Route("/comprobantes/nota-debito")
 */
class NotaDebitoController extends Controller {

    /**
     * Lists all NotaDebito entities.
     *
     * @Route("/", name="notadebito")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        return array(
        );
    }

    /**
     * Lists all Nota Debito entities.
     *
     * @Route("/notasDebito", name="all_notasDebito")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     */
    public function notasDebitoAction() {
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
        $count = $em->getRepository('FactelBundle:NotaDebito')->cantidadNotasDebito($idPtoEmision, $emisorId);
        $entities = $em->getRepository('FactelBundle:NotaDebito')->findNotasDebito($sSearch, $iDisplayStart, $iDisplayLength, $idPtoEmision, $emisorId);
        $totalDisplayRecords = $count;

        if ($sSearch != "") {
            $totalDisplayRecords = count($entities);
        }
        $notaDebitoArray = array();
        $i = 0;
        foreach ($entities as $entity) {
            $fechaAutorizacion = "";
            $fechaAutorizacion = $entity->getFechaAutorizacion() != null ? $entity->getFechaAutorizacion()->format("d/m/Y H:i:s") : "";
            $notaDebitoArray[$i] = [$entity->getId(), $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial(), $entity->getCliente()->getNombre(), $entity->getFechaEmision()->format("d/m/Y"), $fechaAutorizacion, $entity->getValorTotal(), $entity->getEstado()];
            $i++;
        }

        $arr = array(
            "iTotalRecords" => (int) $count,
            "iTotalDisplayRecords" => (int) $totalDisplayRecords,
            'aaData' => $notaDebitoArray
        );

        $post_data = json_encode($arr);

        return new Response($post_data, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Creates a new NotaDebito entity.
     *
     * @Route("/", name="notadebito_create")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("POST")
     * @Template("FactelBundle:NotaDebito:new.html.twig")
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

        $tipoDocMod = $request->request->get("tipoDocumento");
        $fechaEmisionDocMod = $request->request->get("fechaDocModificado");
        $numeroDocMod = $request->request->get("numeroDocMod");

        $idNotaDebito = $request->request->get("idNotaDebito");
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

        if ($cantidadErrores > 0) {
            if ($cantidadErrores == 1) {
                $texto = "El campo <strong>" . $campos . "</strong> no puede estar vacios";
            } else {
                $texto = "Los campos " . $campos . " no pueden estar vacios";
            }
            $this->get('session')->getFlashBag()->add(
                    'notice', $texto
            );

            return $this->redirect($this->generateUrl('notadebito_new', array()));
        }

        $em = $this->getDoctrine()->getManager();
        if ($idNotaDebito != null && $idNotaDebito != '') {
            $entity = new NotaDebito();
            $entity = $em->getRepository('FactelBundle:NotaDebito')->find($idNotaDebito);
            if (!is_null($entity)) {
                $mensajes = $entity->getMensajes();
                foreach ($mensajes as $mensaje) {
                    $em->remove($mensaje);
                }

                foreach ($entity->getImpuestos() as $impuesto) {
                    $em->remove($impuesto);
                }
                foreach ($entity->getMotivos() as $motivo) {
                    $em->remove($motivo);
                }

                $em->flush();
            }
        } else {
            $entity = new NotaDebito();
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
                if ($em->getRepository('FactelBundle:Cliente')->findBy(array("identificacion" => $identificacion, "emisor" => $emisorId)) != null) {
                    $emisorId = $this->get("security.context")->gettoken()->getuser()->getEmisor()->getId();
                    $this->get('session')->getFlashBag()->add(
                            'notice', "La identificación del cliente ya se encuentra resgistrada. Utilice la opción de búsqueda"
                    );
                    return $this->redirect($this->generateUrl('notadebito_new', array()));
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

            $entity->setTipoDocMod($tipoDocMod);
            $fechaEmisionDocMod = str_replace("/", "-", $fechaEmisionDocMod);
            $fechaMod = new \DateTime($fechaEmisionDocMod);
            $entity->setFechaEmisionDocMod($fechaMod);
            $entity->setNroDocMod($numeroDocMod);

            $motivos = $request->request->get("motivo");
            $valores = $request->request->get("valorMod");
            $count = count($motivos);
            if ($count == 0) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "La nota de debito debe tener al menos un detalle"
                );
                return $this->redirect($this->generateUrl('notadebito_new', array()));
            }
            foreach ($motivos as $clave => $valor) {
                $subTotalSinImpuesto += floatval($valores[$clave]);
                $motivo = new Motivo();
                $motivo->setRazon($motivos[$clave]);
                $motivo->setValor($valores[$clave]);

                $motivo->setNotaDebito($entity);
                $entity->addMotivo($motivo);
            }
            $ice = floatval($request->request->get("valorICE"));
            $tipoImpuesto = $request->request->get("impuesto");
            $impuesto = new Impuesto();
            $impuesto->setCodigo("2");
            $impuesto->setCodigoPorcentaje($tipoImpuesto);
            $impuesto->setBaseImponible($subTotalSinImpuesto);

            if ($tipoImpuesto == "2") {
                $subTotal12 = $subTotalSinImpuesto;
                $iva12 = round($subTotal12 * 0.12, 2);
                $impuesto->setTarifa("12");
                $impuesto->setValor($iva12);
            } else {
                $impuesto->setCodigoPorcentaje("0");
                $impuesto->setTarifa("0");
                $impuesto->setValor("0");
            }
            $impuesto->setNotaDebito($entity);
            $entity->addImpuesto($impuesto);

            if ($tipoImpuesto == "0") {
                $subTotal0 = $subTotalSinImpuesto;
            } else if ($tipoImpuesto == "6") {
                $subTotaNoObjeto = $subTotalSinImpuesto;
            } else if ($tipoImpuesto == "7") {
                $subTotaExento = $subTotalSinImpuesto;
            }
            if ($ice > 0) {
                $impuesto = new Impuesto();
                $impuesto->setCodigo("3");
                $impuesto->setCodigoPorcentaje($request->request->get("codICE"));
                $impuesto->setTarifa("0");
                $impuesto->setBaseImponible($subTotalSinImpuesto);
                $impuesto->setValor($ice);

                $impuesto->setNotaDebito($entity);
                $entity->addImpuesto($impuesto);
            }
            $entity->setTotalSinImpuestos($subTotalSinImpuesto);
            $entity->setSubtotal12($subTotal12);
            $entity->setSubtotal0($subTotal0);
            $entity->setSubtotalNoIVA($subTotaNoObjeto);
            $entity->setSubtotalExentoIVA($subTotaExento);
            $entity->setValorICE($ice);
            $entity->setIva12($iva12);
            $importeTotal = floatval($subTotalSinImpuesto) + floatval($ice) + $iva12;
            $entity->setValorTotal($importeTotal);
            $em->persist($entity);
            $em->flush();

            if ($idNotaDebito == null || $idNotaDebito == '') {
                $ptoEmision[0]->setSecuencialNotaDebito($ptoEmision[0]->getSecuencialNotaDebito() + 1);
                $em->persist($ptoEmision[0]);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
        } else {
            throw $this->createNotFoundException('El usuario del sistema no tiene asignado un Punto de Emision.');
        }
    }

    /**
     *
     * @Route("/anular/{id}", name="notadebito_anular")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function anularAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:NotaDebito')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la Nota Debito con ID = ' + $id);
        }
        $entity->setEstado("ANULADA");
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/eliminar/{id}", name="notadebito_eliminar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function eliminarAccion($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:NotaDebito')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe la nota debito con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "No se puede eliminar un documento autorizado"
            );
            return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
        }

        foreach ($entity->getMensajes() as $mensaje) {
            $em->remove($mensaje);
        }

        foreach ($entity->getMotivos() as $motivo) {
            $em->remove($motivo);
        }
        foreach ($entity->getImpuestos() as $impuesto) {
            $em->remove($impuesto);
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('notadebito'));
    }

    /**
     * Creates a new Factura entity.
     *
     * @Route("/enviarEmail/{id}", name="notadebito_enviar_email")
     * @Method("POST")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function sendEmail(Request $request, $id) {
        $destinatario = $request->request->get("email");

        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:NotaDebito')->findFacturaById($id);
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
        return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
    }

    /**
     *
     * @Route("/procesar/{id}", name="notadebito_procesar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function procesarAccion($id) {

        $entity = new NotaDebito();
        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $respuesta = null;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FactelBundle:NotaDebito')->findNotaDebitoById($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe la Nota Débito con ID = ' + $id);
        }
        if ($entity->getEstado() == "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Este comprobante electronico ya fue autorizado"
            );
            return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
        }
        $emisor = $entity->getEmisor();
        $emisor = $entity->getEmisor();
        $hoy = date("Y-m-d");
        if ($emisor->getPlan() != null && $emisor->getFechaFin()) {
            if ($hoy > $emisor->getFechaFin()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Su plan ha caducado por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
            }
            if ($emisor->getCantComprobante() > $emisor->getPlan()->getCantComprobante()) {
                $this->get('session')->getFlashBag()->add(
                        'notice', "Ha superado el numero de comprobantes contratado en su plan, por fovor contacte con nuestro equipo para su renovacion"
                );
                return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
            }
        }
        $configApp = new \configAplicacion();
        $configApp->dirFirma = $emisor->getDirFirma();
        $configApp->passFirma = $emisor->getPassFirma();
        $configApp->dirAutorizados = $emisor->getDirDocAutorizados();
        $configApp->dirLogo = $emisor->getDirLogo();

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
            $notaDebito = new \notaDebito();
            $notaDebito->configAplicacion = $configApp;
            $notaDebito->configCorreo = $configCorreo;

            $notaDebito->ambiente = $entity->getAmbiente();
            $notaDebito->tipoEmision = $entity->getTipoEmision();
            $notaDebito->razonSocial = $emisor->getRazonSocial();

            if ($entity->getEstablecimiento()->getNombreComercial() != "") {
                $notaDebito->nombreComercial = $entity->getEstablecimiento()->getNombreComercial();
            } else if ($emisor->getNombreComercial() != "") {
                $notaDebito->nombreComercial = $emisor->getNombreComercial();
            }
            $notaDebito->ruc = $emisor->getRuc(); //[Ruc]
            $notaDebito->codDoc = "05";
            $notaDebito->establecimiento = $entity->getEstablecimiento()->getCodigo();
            $notaDebito->ptoEmision = $entity->getPtoEmision()->getCodigo();
            $notaDebito->secuencial = $entity->getSecuencial();
            $notaDebito->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
            $notaDebito->dirMatriz = $emisor->getDireccionMatriz();
            $notaDebito->dirEstablecimiento = $entity->getEstablecimiento()->getDireccion();
            if ($emisor->getContribuyenteEspecial() != "") {
                $notaDebito->contribuyenteEspecial = $emisor->getContribuyenteEspecial();
            }
            $notaDebito->obligadoContabilidad = $emisor->getObligadoContabilidad();
            $notaDebito->tipoIdentificacionComprador = $entity->getCliente()->getTipoIdentificacion();
            $notaDebito->razonSocialComprador = $entity->getCliente()->getNombre();
            $notaDebito->identificacionComprador = $entity->getCliente()->getIdentificacion();
            $notaDebito->totalSinImpuestos = $entity->getTotalSinImpuestos();

            $impuestoArray = array();
            $impuestos = $entity->getImpuestos();
            foreach ($impuestos as $impuestoNotaDebito) {
                $impuesto = new \impuesto(); // Impuesto del detalle
                $impuesto->codigo = $impuestoNotaDebito->getCodigo();
                $impuesto->codigoPorcentaje = $impuestoNotaDebito->getCodigoPorcentaje();
                $impuesto->tarifa = $impuestoNotaDebito->getTarifa();
                $impuesto->baseImponible = $impuestoNotaDebito->getBaseImponible();
                $impuesto->valor = $impuestoNotaDebito->getValor();
                $impuestoArray[] = $impuesto;
            }
            $notaDebito->impuestos = $impuestoArray;
            $camposAdicionales = array();
            foreach ($entity->getComposAdic() as $campoAdic) {
                $campoAdicional = new \campoAdicional();
                $campoAdicional->nombre = $campoAdic->getNombre();
                $campoAdicional->valor = $campoAdic->getValor();

                $camposAdicionales [] = $campoAdic;
            }
            if ($emisor->getRegimenRimpe()) {
                $notaDebito->regimenRimpes = "Contribuyente Negocio Popular - Régimen RIMPE";
            }
            if ($emisor->getRegimenRimpe1()) {
                $notaDebito->regimenRimpes1 = "Contribuyente Régimen RIMPE";
            }

            if ($emisor->getResolucionAgenteRetencion()) {
                $notaDebito->agenteRetencion = $emisor->getResolucionAgenteRetencion();
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
            $notaDebito->codDocModificado = $entity->getTipoDocMod();
            $notaDebito->numDocModificado = $entity->getNroDocMod();
            $notaDebito->fechaEmisionDocSustento = $entity->getFechaEmisionDocMod()->format("d/m/Y");

            $motivosArray = array();
            $motivos = $entity->getMotivos();
            foreach ($motivos as $motivoNotaDebito) {
                $motivo = new \motivo();
                $motivo->razon = $motivoNotaDebito->getRazon();
                $motivo->valor = $motivoNotaDebito->getValor();
                $motivoArray [] = $motivo;
            }
            $notaDebito->valorTotal = $entity->getValorTotal();
            $notaDebito->motivos = $motivoArray;

            if (count($camposAdicionales) > 0) {
                $notaDebito->infoAdicional = $camposAdicionales;
            }

            $procesarComprobante = new \procesarComprobante();
            $procesarComprobante->comprobante = $notaDebito;

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
            $comprobantePendiente->codDoc = "05";
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
                    $comprobantePendiente->codDoc = "05";
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
            $entity->setNombreArchivo("ND" . $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial());
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
                $mensajeGenerado->setNotaDebito($entity);
                $em->persist($mensajeGenerado);
            }
        }
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
    }

    /**
     *
     * @Route("/descargar/{id}/{type}", name="notadebito_descargar")
     * @Method("GET")
     * @Secure(roles="ROLE_EMISOR")
     */
    public function descargarAction($id, $type = "zip") {
        $em = $this->getDoctrine()->getManager();
        $notaDebito = new NotaDebito();
        $notaDebito = $em->getRepository('FactelBundle:NotaDebito')->findNotaDebitoById($id);
        if ($notaDebito->getEstado() != "AUTORIZADO") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Para descargar los archivos generados el comprobantes debe haber sido AUTORIZADO"
            );
            return $this->redirect($this->generateUrl('notadebito_show', array('id' => $notaDebito->getId())));
        }
        $archivoName = $notaDebito->getNombreArchivo();
        $pathXML = $notaDebito->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $notaDebito->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".xml";
        $pathPDF = $notaDebito->getEmisor()->getDirDocAutorizados() . DIRECTORY_SEPARATOR . $notaDebito->getCliente()->getIdentificacion() . DIRECTORY_SEPARATOR . $archivoName . ".pdf";
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
     * Displays a form to create a new NotaDebito entity.
     *
     * @Route("/nueva", name="notadebito_new")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
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
     * Finds and displays a NotaDebito entity.
     *
     * @Route("/{id}", name="notadebito_show")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:NotaDebito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NotaDebito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing NotaDebito entity.
     *
     * @Route("/{id}/edit", name="notadebito_edit")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:NotaDebito')->findNotaDebitoById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Nota Debito entity.');
        }
        if ($entity->getEstado() == "AUTORIZADO" || $entity->getEstado() == "ERROR") {
            $this->get('session')->getFlashBag()->add(
                    'notice', "Solo pueden ser editadas las Nota Debito en estado: NO AUTORIZADO , DEVUELTA y PROCESANDOSE"
            );
            return $this->redirect($this->generateUrl('notadebito_show', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a form to edit a NotaDebito entity.
     *
     * @param NotaDebito $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(NotaDebito $entity) {
        $form = $this->createForm(new NotaDebitoType(), $entity, array(
            'action' => $this->generateUrl('notadebito_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing NotaDebito entity.
     *
     * @Route("/{id}", name="notadebito_update")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("PUT")
     * @Template("FactelBundle:NotaDebito:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FactelBundle:NotaDebito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NotaDebito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('notadebito_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a NotaDebito entity.
     *
     * @Route("/{id}", name="notadebito_delete")
     * @Secure(roles="ROLE_EMISOR")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FactelBundle:NotaDebito')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NotaDebito entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('notadebito'));
    }

    /**
     * Creates a form to delete a NotaDebito entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('notadebito_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    private function claveAcceso($notaDebito, $emisor, $establecimiento, $ptoEmision, $fechaEmision) {
        $claveAcceso = str_replace("/", "", $fechaEmision);
        $claveAcceso .= "05";
        $claveAcceso .= $emisor->getRuc();
        $claveAcceso .= $notaDebito->getAmbiente();
        $serie = $establecimiento->getCodigo() . $ptoEmision->getCodigo();
        $claveAcceso .= $serie;
        $claveAcceso .= $notaDebito->getSecuencial();
        $claveAcceso .= "12345678";
        $claveAcceso .= $notaDebito->getTipoEmision();
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
