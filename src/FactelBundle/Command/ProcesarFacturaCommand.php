<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProcesarFacturaCommand
 *
 * @author yoelvys
 */

namespace FactelBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use FactelBundle\Entity\Factura;
use FactelBundle\Entity\FacturaHasProducto;
use FactelBundle\Entity\Impuesto;
use FactelBundle\Entity\CampoAdicional;
use FactelBundle\Entity\CargaError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once(realpath(dirname(__FILE__) . '/../Controller/ProcesarComprobanteElectronico.php'));
require_once 'reader.php';

class ProcesarFacturaCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('factel:command:facturas')
                ->setDescription('Procesa las facturas cargadas desde el archivo excel')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $enProceso = $em->getRepository('FactelBundle:CargaArchivo')->findby(array("estado" => "EN PROCESO", "type" => "FACTURA"));
        if (count($enProceso) == 0) {
            $archivos = $em->getRepository('FactelBundle:CargaArchivo')->findby(array("procesoAutomatico" => true, "estado" => "CARGADO", "type" => "FACTURA"));
            foreach ($archivos as $archivo) {
                try {
                    $this->procesarFacturaMasivaAction($archivo->getId());
                    $archivo->setfinProcesamiento(new \DateTime());
                    $archivo->setEstado("PROCESADO");
                    $em->persist($archivo);
                    $em->flush();
                    break;
                } catch (\Exception $e) {
                    $error = new CargaError();
                    $error->setMessage("Error procesando el archivo. Error: " . $e->getMessage());
                    $error->setCargaArchivo($archivo);
                    $em->persist($error);

                    $archivo->setfinProcesamiento(new \DateTime());
                    $archivo->setEstado("ERROR PROCESANDO");
                    $em->persist($archivo);
                    $em->flush();
                }
            }
        }
        $facturas = $em->getRepository('FactelBundle:Factura')->findFacturasCargadasArchivo();
        foreach ($facturas as $factura) {
            try {
                $this->procesarFactura($factura["id"]);
            } catch (\Exception $e) {
                $facturaObj = $em->getRepository('FactelBundle:Factura')->find($factura["id"]);
                $mensajes = $facturaObj->getMensajes();
                foreach ($mensajes as $mensaje) {
                    $em->remove($mensaje);
                }
                var_dump("Error procesando la factura con id: " . $factura["id"] . " Error: " . $e->getMessage());
                $mensajeGenerado = new \FactelBundle\Entity\Mensaje();
                $mensajeGenerado->setIdentificador("2000");
                $mensajeGenerado->setMensaje("ERROR EN EL PROCESO AUTOMATICO");
                $mensajeGenerado->setInformacionAdicional($e->getMessage());
                $mensajeGenerado->setTipo("ERROR");

                $mensajeGenerado->setFactura($facturaObj);
                $em->persist($mensajeGenerado);
                $em->flush();
            }
        }
    }

    public function procesarFacturaMasivaAction($id) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $emisor = new \FactelBundle\Entity\Emisor();
        $archivo = $em->getRepository('FactelBundle:CargaArchivo')->find($id);
        if ($archivo->getEstado() == "CARGADO") {
            $ptoEmision = $em->getRepository('FactelBundle:PtoEmision')->findPtoEmisionEstabEmisorByUsuario($archivo->getCreatedBy()->getId());
            $establecimiento = $ptoEmision[0]->getEstablecimiento();
            $emisor = $establecimiento->getEmisor();

            $data = new Spreadsheet_Excel_Reader();
            $data->setOutputEncoding('UTF-8');
            $data->Spreadsheet_Excel_Reader();

            $productoCreado = 0;
            $productoActualizado = 0;

            $data->read($this->getUploadRootDir() . '/' . $archivo->getDirArchivo());
            date_default_timezone_set("America/Guayaquil");
            $archivo->setInicioProcesamiento(new \DateTime());
            $archivo->setEstado("EN PROCESO");
            $em->persist($archivo);
            $em->flush();
            $existError = false;
            for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                if (isset($data->sheets[0]['cells'][$i][1]) && isset($data->sheets[0]['cells'][$i][2]) && isset($data->sheets[0]['cells'][$i][3]) && isset($data->sheets[0]['cells'][$i][4]) && isset($data->sheets[0]['cells'][$i][5]) && isset($data->sheets[0]['cells'][$i][6]) && isset($data->sheets[0]['cells'][$i][7]) && isset($data->sheets[0]['cells'][$i][8])) {
                    $idFactura = "";
                    try {
                        $idFactura = $data->sheets[0]['cells'][$i][1];
                        $codigoPrincipal = $data->sheets[0]['cells'][$i][3];
                        $producto = $em->getRepository('FactelBundle:Producto')->findOneBy(array("codigoPrincipal" => $codigoPrincipal, "emisor" => $emisor->getId()));

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
                        $identificacion = utf8_encode($data->sheets[0]['cells'][$i][7]);
                        $cliente = $em->getRepository('FactelBundle:Cliente')->findOneBy(array("identificacion" => $identificacion, "emisor" => $emisor->getId()));
                        if ($cliente == null) {
                            $cliente = new \FactelBundle\Entity\Cliente();
                            $cliente->setEmisor($emisor);
                        }

                        $cliente->setNombre(utf8_encode($data->sheets[0]['cells'][$i][8]));
                        $cliente->setTipoIdentificacion(utf8_encode($data->sheets[0]['cells'][$i][6]));
                        $cliente->setIdentificacion($identificacion);
                        if (isset($data->sheets[0]['cells'][$i][9])) {
                            $cliente->setCorreoElectronico(utf8_encode($data->sheets[0]['cells'][$i][9]));
                        }
                        $em->persist($cliente);
                        $em->flush();

                        $entity->setCliente($cliente);
                        $entity->setEmisor($emisor);
                        $entity->setEstablecimiento($establecimiento);
                        $entity->setPtoEmision($ptoEmision[0]);

                        if (isset($data->sheets[0]['cells'][$i][11])) {
                            $entity->setObservacion(utf8_encode($data->sheets[0]['cells'][$i][11]));
                        }

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
                        $entity->setFormaPago($data->sheets[0]['cells'][$i][2]);
                        if (isset($data->sheets[0]['cells'][$i][10])) {
                            $entity->setPlazo($data->sheets[0]['cells'][$i][10]);
                        }
                        $pos = 0;
                        $productosId = array();
                        $cantidadArray = array();
                        $descuentoArray = array();
                        $error = false;
                        while (true && isset($data->sheets[0]['cells'][$i][1])) {
                            if (isset($data->sheets[0]['cells'][$i][3]) && isset($data->sheets[0]['cells'][$i][4]) && isset($data->sheets[0]['cells'][$i][5])) {
                                if ($idFactura == $data->sheets[0]['cells'][$i][1]) {
                                    $codPorducto = utf8_encode($data->sheets[0]['cells'][$i][3]);
                                    $productosId[$pos++] = $codPorducto;
                                    $cantidadArray[$codPorducto] = $data->sheets[0]['cells'][$i][4];
                                    $descuentoArray[$codPorducto] = $data->sheets[0]['cells'][$i][5];
                                    $i++;
                                } else {
                                    break;
                                }
                            } else {
                                $error = true;
                                break;
                            }
                        }
                        if ($error) {
                            break;
                        } else {
                            $i--;
                        }

                        $productos = array();

                        foreach ($productosId as $productoId) {
                            $producto = $em->getRepository('FactelBundle:Producto')->findBy(array("codigoPrincipal" => $productoId, "emisor" => $emisor));
                            if (count($producto) == 0) {
                                throw new NotFoundHttpException("El codigo principal " . $productoId . "  no se encuentra en el listado de productos, primeramente debe crear los productos en el sistema");
                            }
                            $productos[] = $producto[0];
                        }
                        $valorTotalSubsidio = 0.0;
                        $valorTotalSubsidioSinIva = 0.0;
                        foreach ($productos as $producto) {
                            $subsidio = 0.0;
                            $facturaHasProducto = new FacturaHasProducto();
                            $idProducto = $producto->getCodigoPrincipal();

                            $facturaHasProducto->setProducto($producto);
                            $impuestoIva = $producto->getImpuestoIVA();
                            $baseImponible = 0;
                            if ($producto->getTieneSubsidio()) {
                                $subsidio = ($producto->getPrecioSinSubsidio() - floatval($producto->getPrecioUnitario())) * floatval($cantidadArray[$idProducto]);
                                $valorTotalSubsidioSinIva += $subsidio;
                            }
                            if ($impuestoIva != null) {
                                $impuesto = new Impuesto();
                                $impuesto->setCodigo("2");
                                $impuesto->setCodigoPorcentaje($impuestoIva->getCodigoPorcentaje());
                                $baseImponible = floatval($cantidadArray[$idProducto]) * floatval($producto->getPrecioUnitario()) - floatval($descuentoArray[$idProducto]);
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
                                    $tarifaIva = $impuestoIva->getTarifa();
                                    if ($subsidio > 0) {
                                        $subsidio = ($subsidio * $impuestoIva->getTarifa() / 100) + $subsidio;
                                    }
                                }

                                $impuesto->setFacturaHasProducto($facturaHasProducto);

                                $facturaHasProducto->addImpuesto($impuesto);
                                $subTotalSinImpuesto += $baseImponible;
                                $valorTotalSubsidio += $subsidio;
                            }

                            $descuento += floatval($descuentoArray[$idProducto]);

                            $facturaHasProducto->setCantidad($cantidadArray[$idProducto]);
                            $facturaHasProducto->setPrecioUnitario($producto->getPrecioUnitario());
                            $facturaHasProducto->setDescuento($descuentoArray[$idProducto]);
                            $facturaHasProducto->setValorTotal($baseImponible);
                            $facturaHasProducto->setNombre($producto->getNombre());
                            $facturaHasProducto->setCodigoProducto($producto->getCodigoPrincipal());
                            $facturaHasProducto->setFactura($entity);
                            if ($subsidio > 0) {
                                $facturaHasProducto->setPrecioSinSubsidio($producto->getPrecioSinSubsidio());
                            }
                            $entity->addFacturasHasProducto($facturaHasProducto);
                        }

                        if (isset($tarifaIva)) {
                            $iva12 = round($subTotal12 * $tarifaIva / 100, 2);
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
                        if ($valorTotalSubsidio > 0) {
                            $valorTotalSubsidio = round($valorTotalSubsidio, 2);
                            $valorTotalSinSubsidio = round($importeTotal + $valorTotalSubsidio, 2);
                            $entity->setTotalSubsidio($valorTotalSubsidio);
                            $entity->setTotalSinSubsidio($valorTotalSinSubsidio);
                            $entity->setTotalSubsidioSinIva($valorTotalSubsidioSinIva);
                        } else {
                            $entity->setTotalSubsidio(0.00);
                            $entity->setTotalSinSubsidio(0.00);
                            $entity->setTotalSubsidioSinIva(0.00);
                        }

                        $entity->setCargaAutomatica(true);
                        $entity->setIdFacturaCarga($idFactura);
                        $em->persist($entity);
                        $em->flush();

                        $ptoEmision[0]->setSecuencialFactura($ptoEmision[0]->getSecuencialFactura() + 1);
                        $em->persist($ptoEmision[0]);
                        $em->flush();
                    } catch (\Exception $e) {
                        $error = new CargaError();
                        $error->setMessage("ID Factura: " . $idFactura . " Error: " . $e->getMessage());
                        $error->setCargaArchivo($archivo);
                        $em->persist($error);
                        $em->flush();
                        $existError = true;
                    }
//$this->funtionCrearXmlPDF($entity->getId());
                }
            }
        }
    }

    private function procesarFactura($id) {
        $entity = new Factura();
        $procesarComprobanteElectronico = new \ProcesarComprobanteElectronico();
        $respuesta = null;
        $em = $this->getContainer()->get('doctrine')->getManager();
        $entity = $em->getRepository('FactelBundle:Factura')->findFacturaById($id);
        $emisor = $entity->getEmisor();
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

        if ($entity->getEstado() != "PROCESANDOSE") {
            $factura = new \factura();
            $factura->configAplicacion = $configApp;
            $factura->configCorreo = $configCorreo;

            $factura->ambiente = $entity->getAmbiente();
            $factura->tipoEmision = $entity->getTipoEmision();
            $factura->razonSocial = $emisor->getRazonSocial();
            if ($entity->getEstablecimiento()->getNombreComercial() != "") {
                $factura->nombreComercial = $entity->getEstablecimiento()->getNombreComercial();
            } else if ($emisor->getNombreComercial() != "") {
                $factura->nombreComercial = $emisor->getNombreComercial();
            }
            $factura->ruc = $emisor->getRuc(); //[Ruc]
            $factura->codDoc = "01";
            $factura->establecimiento = $entity->getEstablecimiento()->getCodigo();
            $factura->ptoEmision = $entity->getPtoEmision()->getCodigo();
            $factura->secuencial = $entity->getSecuencial();
            $factura->fechaEmision = $entity->getFechaEmision()->format("d/m/Y");
            $factura->dirMatriz = $emisor->getDireccionMatriz();
            $factura->dirEstablecimiento = $entity->getEstablecimiento()->getDireccion();
            if ($emisor->getContribuyenteEspecial() != "") {
                $factura->contribuyenteEspecial = $emisor->getContribuyenteEspecial();
            }
            $factura->obligadoContabilidad = $emisor->getObligadoContabilidad();
            $factura->tipoIdentificacionComprador = $entity->getCliente()->getTipoIdentificacion();
            $factura->razonSocialComprador = $entity->getCliente()->getNombre();
            $factura->identificacionComprador = $entity->getCliente()->getIdentificacion();
            $factura->totalSinImpuestos = $entity->getTotalSinImpuestos();
            $factura->totalDescuento = $entity->getTotalDescuento();



            $factura->propina = $entity->getPropina();
            $factura->importeTotal = $entity->getValorTotal();
            $factura->moneda = "DOLAR"; //DOLAR
            $pagos = array();

            $pago = new \pago();
            $pago->formaPago = $entity->getFormaPago();
            if ($entity->getPlazo()) {
                $pago->plazo = $entity->getPlazo();
                $pago->unidadTiempo = "Dias";
            }
            $pago->total = $entity->getValorTotal();
            $pagos [] = $pago;

            $factura->pagos = $pagos;
            $codigoPorcentajeIVA = "";
            $detalles = array();
            $facturasHasProducto = $entity->getFacturasHasProducto();
            $impuestosTotalICE = array();
            $baseImponibleICE = array();
            $impuestosTotalIRBPNR = array();
            $baseImponibleIRBPNR = array();
            foreach ($facturasHasProducto as $facturasHasProducto) {
                $producto = new \FactelBundle\Entity\Producto();
                $producto = $facturasHasProducto->getProducto();
                $detalleFactura = new \detalleFactura();
                $detalleFactura->codigoPrincipal = $facturasHasProducto->getCodigoProducto();
                if ($producto->getCodigoAuxiliar() != "") {
                    $detalleFactura->codigoAuxiliar = $producto->getCodigoAuxiliar();
                }
                $detalleFactura->descripcion = $facturasHasProducto->getNombre();
                $detalleFactura->cantidad = $facturasHasProducto->getCantidad();
                $detalleFactura->precioUnitario = $facturasHasProducto->getPrecioUnitario();
                $detalleFactura->descuento = $facturasHasProducto->getDescuento();
                $detalleFactura->precioTotalSinImpuesto = $facturasHasProducto->getValorTotal();

                $impuestos = array();
                $impuestosProducto = $facturasHasProducto->getImpuestos();
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
                $detalleFactura->impuestos = $impuestos;
                $detalles[] = $detalleFactura;
            }
            $totalImpuestoArray = array();
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

            $factura->detalles = $detalles;
            $factura->totalConImpuesto = $totalImpuestoArray;

            $camposAdicionales = array();
            if (is_array($entity->getComposAdic())) {
                foreach ($entity->getComposAdic() as $campoAdic) {
                    $campoAdicional = new \campoAdicional();
                    $campoAdicional->nombre = $campoAdic->getNombre();
                    $campoAdicional->valor = $campoAdic->getValor();

                    $camposAdicionales [] = $campoAdic;
                }
            }
             if ($emisor->getRegimenRimpe()) {
            $factura->regimenRimpes = "“Contribuyente Negocio Popular - Régimen RIMPE";
        }
        if ($emisor->getRegimenRimpe1()) {
       $factura->regimenRimpes1 = "Contribuyente Régimen RIMPE";
   }

        if ($emisor->getResolucionAgenteRetencion()) {
            $factura->agenteRetencion = $emisor->getResolucionAgenteRetencion();
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
            if ($entity->getObservacion() != "") {
                $campoAdic = new \campoAdicional();
                $campoAdic->nombre = "Observacion";
                $campoAdic->valor = $entity->getObservacion();

                $camposAdicionales [] = $campoAdic;
            }
            if (count($camposAdicionales) > 0) {
                $factura->infoAdicional = $camposAdicionales;
            }
            if ($entity->getEstablecimiento()->getEmailCopia() && $entity->getEstablecimiento()->getEmailCopia() != "") {
                $configCorreo->BBC = $entity->getEstablecimiento()->getEmailCopia();
            }


            $procesarComprobante = new \procesarComprobante();
            $procesarComprobante->comprobante = $factura;

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
            $comprobantePendiente->codDoc = "01";
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
                    $comprobantePendiente->codDoc = "01";
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
            $entity->setNombreArchivo("FAC" . $entity->getEstablecimiento()->getCodigo() . "-" . $entity->getPtoEmision()->getCodigo() . "-" . $entity->getSecuencial());
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
                $mensajeGenerado->setFactura($entity);
                $em->persist($mensajeGenerado);
            }
        }
        $em->persist($entity);
        $em->flush();
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

    public function getUploadRootDir() {
        return __DIR__ . '/../../../web/upload';
    }

}
