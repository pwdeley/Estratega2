<?php

ini_set('default_socket_timeout', 600);

class factura extends comprobanteGeneral {

    public $detalles; // detalleFactura
    public $guiaRemision; // string
    public $identificacionComprador; // string
    public $importeTotal; // string
    public $infoAdicional; // campoAdicional
    public $moneda; // string
    public $pagos; // pago
    public $propina; // string
    public $razonSocialComprador; // string
    public $tipoIdentificacionComprador; // string
    public $totalConImpuesto; // totalImpuesto
    public $totalDescuento; // string
    public $totalSinImpuestos; // string
    public $totalSubsidio; // string
    public $codDocReemb; // string
    public $totalComprobantesReembolso; // string
    public $totalBaseImponibleReembolso; // string
    public $totalImpuestoReembolso; // string
    public $reembolsos; // reembolsoFactura

}

class proforma {

    public $configCorreo;
    public $dirLogo;
    public $dirProformas;
    public $tipoEmision;
    public $razonSocial;
    public $nombreComercial;
    public $ruc;
    public $numero;
    public $dirMatriz;
    public $dirEstablecimiento;
    public $fechaEmision;
    public $razonSocialComprador;
    public $identificacionComprador;
    public $direccionComprador;
    public $subTotal12;
    public $subTotal0;
    public $subTotalSinImpuesto;
    public $iva;
    public $totalDescuento;
    public $importeTotal;
    public $detalles; // detalleProforma
    public $infoAdicional;

}

class detalleProforma {

    public $codigo;
    public $descripcion;
    public $cantidad;
    public $precioUnitario;
    public $descuento;
    public $precioTotalSinImpuesto;

}

class liquidacionCompra extends comprobanteGeneral {

    public $detalles; // detalleLiquidacionCompra
    public $direccionProveedor; // string
    public $identificacionProveedor; // string
    public $importeTotal; // string
    public $infoAdicional; // campoAdicional
    public $moneda; // string
    public $razonSocialProveedor; // string
    public $tipoIdentificacionProveedor; // string
    public $totalConImpuesto; // totalImpuesto
    public $totalDescuento; // string
    public $totalSinImpuestos; // string
    public $pagos;
    public $codDocReemb; // string
    public $totalComprobantesReembolso; // string
    public $totalBaseImponibleReembolso; // string
    public $totalImpuestoReembolso; // string
    public $reembolsos; // reembolsoLiquidacionCompra

}

class comprobanteGeneral {

    public $ambiente; // string
    public $claveAcc; // string
    public $codDoc; // string
    public $configAplicacion; // configAplicacion
    public $configCorreo; // configCorreo
    public $contribuyenteEspecial; // string
    public $dirEstablecimiento; // string
    public $dirMatriz; // string
    public $regimenRimpes; // string
    public $regimenRimpes1; // string
    public $agenteRetencion; // string
    public $establecimiento; // string
    public $fechaEmision; // string
    public $nombreComercial; // string
    public $obligadoContabilidad; // string
    public $ptoEmision; // string
    public $razonSocial; // string
    public $ruc; // string
    public $secuencial; // string
    public $tipoDoc; // string
    public $tipoEmision; // string

}

class detalleFactura {

    public $cantidad; // string
    public $codigoAuxiliar; // string
    public $codigoPrincipal; // string
    public $descripcion; // string
    public $descuento; // string
    public $precioSinSubsidio; // string
    public $detalleAdicional; // detalleAdicional
    public $impuestos; // impuesto
    public $precioTotalSinImpuesto; // string
    public $precioUnitario; // string

}

class reembolsoFactura {

    public $tipoIdentificacionProveedorReembolso;
    public $identificacionProveedorReembolso;
    public $codPaisPagoProveedorReembolso;
    public $tipoProveedorReembolso;
    public $codDocReembolso;
    public $estabDocReembolso;
    public $ptoEmiDocReembolso;
    public $secuencialDocReembolso;
    public $fechaEmisionDocReembolso;
    public $numeroautorizacionDocReemb;
    public $impuestos;

}

class reembolsoLiquidacionCompra {

    public $tipoIdentificacionProveedorReembolso;
    public $identificacionProveedorReembolso;
    public $codPaisPagoProveedorReembolso;
    public $tipoProveedorReembolso;
    public $codDocReembolso;
    public $estabDocReembolso;
    public $ptoEmiDocReembolso;
    public $secuencialDocReembolso;
    public $fechaEmisionDocReembolso;
    public $numeroautorizacionDocReemb;
    public $impuestos;

}

class detalleLiquidacionCompra {

    public $cantidad; // string
    public $codigoAuxiliar; // string
    public $codigoPrincipal; // string
    public $descripcion; // string
    public $descuento; // string
    public $detalleAdicional; // detalleAdicional
    public $impuestos; // impuesto
    public $precioTotalSinImpuesto; // string
    public $precioUnitario; // string

}

class detalleAdicional {

    public $nombre; // string
    public $valor; // string

}

class pago {

    public $formaPago; // string
    public $total; // string
    public $plazo; // string
    public $unidadTiempo; // string

}

class impuesto {

    public $baseImponible; // string
    public $codigo; // string
    public $codigoPorcentaje; // string
    public $tarifa; // string
    public $valor; // string

}

class campoAdicional {

    public $nombre; // string
    public $valor; // string

}

class totalImpuesto {

    public $baseImponible; // string
    public $codigo; // string
    public $codigoPorcentaje; // string
    public $descuentoAdicional; // string
    public $tarifa; // string
    public $valor; // string

}

class configAplicacion {

    public $dirAutorizados; // string
    public $dirFirma; // string
    public $dirLogo; // string
    public $passFirma; // string

}

class configCorreo {

    public $correoAsunto; // string
    public $correoHost; // string
    public $correoPass; // string
    public $correoPort; // string
    public $correoRemitente; // string
    public $sslHabilitado; // boolean
    public $BBC; // string
    public $CC; // string

}

class guiaRemision {

    public $destinatarios; // destinatario
    public $dirPartida; // string
    public $fechaFinTransporte; // string
    public $fechaIniTransporte; // string
    public $infoAdicional; // campoAdicional
    public $placa; // string
    public $razonSocialTransportista; // string
    public $rise; // string
    public $rucTransportista; // string
    public $tipoIdentificacionTransportista; // string

}

class destinatario {

    public $codDocSustento; // string
    public $codEstabDestino; // string
    public $detalles; // detalleGuiaRemision
    public $dirDestinatario; // string
    public $docAduaneroUnico; // string
    public $fechaEmisionDocSustento; // string
    public $identificacionDestinatario; // string
    public $motivoTraslado; // string
    public $numAutDocSustento; // string
    public $numDocSustento; // string
    public $razonSocialDestinatario; // string
    public $ruta; // string

}

class detalleGuiaRemision {

    public $cantidad; // string
    public $codigoAdicional; // string
    public $codigoInterno; // string
    public $descripcion; // string
    public $detallesAdicionales; // detalleAdicional

}

class comprobanteRetencion extends comprobanteGeneral {

    public $identificacionSujetoRetenido; // string
    public $impuestos; // impuestoComprobanteRetencion
    public $infoAdicional; // campoAdicional
    public $periodoFiscal; // string
    public $razonSocialSujetoRetenido; // string
    public $tipoIdentificacionSujetoRetenido; // string

}

class impuestoComprobanteRetencion {

    public $baseImponible; // string
    public $codDocSustento; // string
    public $codigo; // string
    public $codigoRetencion; // string
    public $fechaEmisionDocSustento; // string
    public $numDocSustento; // string
    public $porcentajeRetener; // string
    public $valorRetenido; // string

}

class notaDebito extends comprobanteGeneral {

    public $codDocModificado; // string
    public $fechaEmisionDocSustento; // string
    public $identificacionComprador; // string
    public $impuestos; // impuesto
    public $infoAdicional; // campoAdicional
    public $motivos; // motivo
    public $numDocModificado; // string
    public $razonSocialComprador; // string
    public $rise; // string
    public $tipoIdentificacionComprador; // string
    public $totalSinImpuestos; // string
    public $valorTotal; // string

}

class motivo {

    public $razon; // string
    public $valor; // string

}

class notaCredito extends comprobanteGeneral {

    public $codDocModificado; // string
    public $detalles; // detalleNotaCredito
    public $fechaEmisionDocSustento; // string
    public $identificacionComprador; // string
    public $infoAdicional; // campoAdicional
    public $moneda; // string
    public $motivo; // string
    public $numDocModificado; // string
    public $razonSocialComprador; // string
    public $rise; // string
    public $tipoIdentificacionComprador; // string
    public $totalConImpuesto; // totalImpuesto
    public $totalSinImpuestos; // string
    public $valorModificacion; // string

}

class detalleNotaCredito {

    public $cantidad; // string
    public $codigoAdicional; // string
    public $codigoInterno; // string
    public $descripcion; // string
    public $descuento; // string
    public $detallesAdicionales; // detalleAdicional
    public $impuestos; // impuesto
    public $precioTotalSinImpuesto; // string
    public $precioUnitario; // string

}

class comprobantePendiente {

    public $ambiente; // string
    public $codDoc; // string
    public $configAplicacion; // configAplicacion
    public $configCorreo; // configCorreo
    public $establecimiento; // string
    public $fechaEmision; // string
    public $ptoEmision; // string
    public $ruc; // string
    public $secuencial; // string
    public $tipoEmision; // string
    public $clavAcc;
    public $enviarEmail; // string
    public $otrosDestinatarios;

}

class procesarComprobanteLote {

    public $comprobanteLote; // comprobanteLote

}

class comprobanteLote {

    public $ambiente; // string
    public $claveAcceso; // string
    public $codDoc; // string
    public $comprobantes; // comprobanteGeneral
    public $configAplicacion; // configAplicacion
    public $configCorreo; // configCorreo
    public $establecimiento; // string
    public $fechaEmision; // string
    public $idUnico; // string
    public $ptoEmision; // string
    public $ruc; // string
    public $secuencial; // string
    public $tipoEmision; // string

}

class procesarComprobanteLoteResponse {

    public $return; // respuestaComprobanteLote

}

class respuestaComprobanteLote {

    public $claveAccesoConsultada; // string
    public $error; // boolean
    public $mensajeGeneral; // mensajeGenerado
    public $respuestas; // respuesta

}

class mensajeGenerado {

    public $identificador; // string
    public $informacionAdicional; // string
    public $mensaje; // string
    public $tipo; // string

}

class respuesta {

    public $claveAcceso; // string
    public $comprobanteID; // string
    public $estadoComprobante; // string
    public $mensajes; // mensajeGenerado
    public $numeroAutorizacion; // string
    public $fechaAutorizacion;

}

class reenvioEmailParam {

    public $configCorreo; // configCorreo
    public $dirDocAutorizados; // string
    public $identificacionComprador; // string
    public $nombreArchivo; // string
    public $otrosDestinatarios; // string

}

class reenviarEmail {

    public $reenvioEmailParam; // comprobantePendiente

}

class reenviarEmailResponse {

    public $return; // respuesta

}

class procesarComprobantePendiente {

    public $comprobantePendiente; // comprobantePendiente

}

class procesarComprobantePendienteResponse {

    public $return; // respuesta

}

class procesarComprobante {

    public $comprobante; // comprobanteGeneral
    public $envioSRI;

}

class procesarComprobanteResponse {

    public $return; // respuesta

}

class procesarProforma {

    public $proforma; // proforma

}

class procesarProformaResponse {

    public $return; // respuesta

}

/**
 * ProcesarComprobanteElectronico class
 *
 *
 *
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class ProcesarComprobanteElectronico extends SoapClient {

    private static $classmap = array(
        'factura' => 'factura',
        'proforma' => 'proforma',
        'liquidacionCompra' => 'liquidacionCompra',
        'comprobanteGeneral' => 'comprobanteGeneral',
        'detalleFactura' => 'detalleFactura',
        'detalleProforma' => 'detalleProforma',
        'reembolsoFactura' => 'reembolsoFactura',
        'reembolsoLiquidacionCompra' => 'reembolsoLiquidacionCompra',
        'detalleLiquidacionCompra' => 'detalleLiquidacionCompra',
        'detalleAdicional' => 'detalleAdicional',
        'impuesto' => 'impuesto',
        'pagos' => 'pagos',
        'campoAdicional' => 'campoAdicional',
        'totalImpuesto' => 'totalImpuesto',
        'configAplicacion' => 'configAplicacion',
        'configCorreo' => 'configCorreo',
        'guiaRemision' => 'guiaRemision',
        'destinatario' => 'destinatario',
        'detalleGuiaRemision' => 'detalleGuiaRemision',
        'comprobanteRetencion' => 'comprobanteRetencion',
        'impuestoComprobanteRetencion' => 'impuestoComprobanteRetencion',
        'notaDebito' => 'notaDebito',
        'motivo' => 'motivo',
        'notaCredito' => 'notaCredito',
        'detalleNotaCredito' => 'detalleNotaCredito',
        'comprobantePendiente' => 'comprobantePendiente',
        'procesarComprobanteLote' => 'procesarComprobanteLote',
        'comprobanteLote' => 'comprobanteLote',
        'procesarComprobanteLoteResponse' => 'procesarComprobanteLoteResponse',
        'respuestaComprobanteLote' => 'respuestaComprobanteLote',
        'mensajeGenerado' => 'mensajeGenerado',
        'respuesta' => 'respuesta',
        'procesarComprobantePendiente' => 'procesarComprobantePendiente',
        'procesarComprobantePendienteResponse' => 'procesarComprobantePendienteResponse',
        'procesarComprobante' => 'procesarComprobante',
        'procesarComprobanteResponse' => 'procesarComprobanteResponse',
        'reenvioEmailParam' => 'reenvioEmailParam',
        'reenviarEmail' => 'reenviarEmail',
        'reenviarEmailResponse' => 'reenviarEmailResponse'
    );

    public function ProcesarComprobanteElectronico($wsdl = "http://localhost:8080/MasterOffline/ProcesarComprobanteElectronico?wsdl", $options = array()) {
        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }
        parent::__construct($wsdl, $options);
    }

    /**
     *
     *
     * @param procesarComprobantePendiente $parameters
     * @return procesarComprobantePendienteResponse
     */
    public function procesarComprobantePendiente(procesarComprobantePendiente $parameters) {
        return $this->__soapCall('procesarComprobantePendiente', array($parameters), array(
                    'uri' => 'http://Servicio/',
                    'soapaction' => ''
                        )
        );
    }

    /**
     *
     *
     * @param procesarComprobanteLote $parameters
     * @return procesarComprobanteLoteResponse
     */
    public function procesarComprobanteLote(procesarComprobanteLote $parameters) {
        return $this->__soapCall('procesarComprobanteLote', array($parameters), array(
                    'uri' => 'http://Servicio/',
                    'soapaction' => ''
                        )
        );
    }

    /**
     *
     *
     * @param procesarComprobante $parameters
     * @return procesarComprobanteResponse
     */
    public function procesarComprobante(procesarComprobante $parameters) {
        return $this->__soapCall('procesarComprobante', array($parameters), array(
                    'uri' => 'http://Servicio/',
                    'soapaction' => ''
                        )
        );
    }

        /**
     *
     *
     * @param procesarXML $parameters
     * @return procesarXMLResponse
     */
    public function procesarProforma(procesarProforma $parameters) {
        return $this->__soapCall('procesarProforma', array($parameters), array(
                    'uri' => 'http://Servicio/',
                    'soapaction' => ''
                        )
        );
    }

    /**
     *
     *
     * @param procesarComprobante $parameters
     * @return procesarComprobanteResponse
     */
    public function reenviarEmail(reenviarEmail $parameters) {
        return $this->__soapCall('reenviarEmail', array($parameters), array(
                    'uri' => 'http://Servicio/',
                    'soapaction' => ''
                        )
        );
    }

}

?>
