@extends('Layout')
{% block css %}
    <!-- DataTables CSS -->
    <link href="{{asset('recursos/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')}}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{asset('recursos/bower_components/datatables-responsive/css/dataTables.responsive.css')}}" rel="stylesheet">
{% endblock %}

{% block panel_title %}
    <i class="fa fa-bar-chart-o fa-fw"></i> Nueva Factura Compra
    <div class="pull-right">

    </div>
{% endblock %}

{% block content %}
    {% for flashMessage in app.session.flashbag.get('notice') %}
        <div class="col-sm-12 alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4><strong>Ha ocurrido un error!</strong></h4>
            <p>{{ flashMessage }}</p>
        </div>
    {% endfor %}
    {% for flashMessage in app.session.flashbag.get('confirm') %}
        <div class="col-sm-12 alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{{ flashMessage }}</p>
        </div>
    {% endfor %}
    <form method="POST" action="{{path('guardar-compra-fisica')}}" id="formCargarCompra">
        <input type="text" id="idCompra" value="{{entity.id}}" class="form-control hidden" name="idCompra">
        <div class="col-md-12">
            <fieldset class="border col-sm-12">
                <legend class="border">Datos Factura</legend>
                <div class="form-group">
                    <div class="col-sm-12 col-md-3"> 
                        <label class="control-label">Fecha Emisi&oacute;n: *</label> 
                        <input type="text" class="form-control" id="fecha" size="3" name="fechaEmision" required="required" value="{{entity.fechaEmision |date('d/m/Y')}}"/> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <label class="control-label">Numero Factura*</label>
                        <input type="text" id="numeroFactura" class="form-control" name="numeroFactura" required="required" value="{{entity.numeroFactura}}">
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <label class="control-label">Numero Autorizacion*</label>
                        <input type="text" id="numeroAutorizacion"  class="form-control" name="numeroAutorizacion" required="required" value="{{entity.numeroAutorizacion}}"> 
                    </div>
                </div>
            </fieldset>
            <fieldset class="border col-sm-12">
                <legend class="border">Proveedor</legend>

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label">Nombre*</label>
                        <input type="text" id="nombre" class="form-control" name="nombre" required="required" value="{{entity.razonSocialProveedor}}">
                    </div>
                    <div class="col-md-3">
                        <label class="control-label">Identificaci&oacute;n*</label>
                        <input type="text" id="identificacion"  class="form-control" name="identificacion" required="required" value="{{entity.identificacionProveedor}}"> 
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">Direcci&oacute;n *</label>
                        <input type="text" id="direccion" class="form-control" name="direccion" required="required" value="{{entity.direccionMatrizProveedor}}">
                    </div>
                </div>
            </fieldset>
            <legend class="border">Productos</legend>
            <div id="mensajeAdvertencia">

            </div>
            <div class="col-sm-12 text-right">
                <button class="btn btn-info link-boton accion" id="nuevoProducto" type="button">
                    <i class="glyphicon glyphicon-plus-sign icon-white"> Nuevo</i>
                </button>
            </div>
            <div class="dataTable_wrapper table-responsive col-lg-12">  
                <table class="table table-striped table-bordered table-hover" id="productos">
                    <thead>
                    <thead>
                        <tr>
                            <th >Cantidad *</th> 
                            <th>Descripci&oacute;n</th>
                            <th>Precio</th>
                            <th>Descuento</th>
                            <th style="width: 80px">Con IVA?</th>
                            <th>Total</th>
                            <th>ICE</th>
                            <th>Accion</th>

                        </tr>
                    </thead>
                    <tbody>
                        {% set count = 0 %}
                        {% for item in entity.detallesCompra %}
                            {% set count = count +1 %}
                            <tr class = "filaProducto">
                                <td>
                                    <input type = "text" class= "hidden count" value = "{{count}}">
                                    <input type = "text" size= "4" name = "cantidad[{{count}}]" id="cantidad{{count}}" value="{{item.cantidad}}" onchange = "calcularaValorTotalXProducto({{count}})">
                                </td>
                                <td><input type = "text" id="nombreProducto{{count}}" name = "nombreProducto[{{count}}]" value="{{item.nombre}}"></td>
                                <td><input type = "text"  size= "4" name = "precio[{{count}}]" id="precio{{count}}" value="{{item.precioUnitario}}" required onchange = "calcularaValorTotalXProducto({{count}})"></td>
                                <td><input type = "text"  size= "4" name = "descuento[{{count}}]" id="descuento{{count}}" value="{{item.descuento}}" required onchange = "calcularaValorTotalXProducto({{count}})"></td>
                                <td><input style="width: 70px" type="checkbox" {% if item.iva12 >0%} checked {% endif%} id="tieneiva{{count}}" name = "tieneiva[{{count}}]" onchange = "calcularaValorTotalXProducto({{count}})"></td>
                                <td id ="total{{count}}" name = "total[{{count}}]" >{{item.subTotal}}</td>
                                <td><input type = "text" size= "4" name = "ice[{{count}}]" value="{{item.ice}}" id="ice{{count}}" onchange = "calcularaValorTotalXProducto({{count}})"></td>
                                <td> <button class='btn btn-danger link-boton accion'  type='button' onclick = 'eliminarFila(this)'>
                                        <i class='glyphicon glyphicon-trash icon-white'></i></button>
                                </td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-offset-8 col-md-4">
            <div class="col-xs-10 col-sm-8 text-right">SubTotal Sin Impuesto</div> 
            <div class="col-xs-2 col-sm-4">
                <p class="" id="subTotalSinImpuesto">{{entity.totalSinImpuestos}}</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">Sub Total 12%</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="subTotal12">{{entity.subTotalIva12}}</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">Sub Total 0%</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="subTotal0">{{entity.subTotalIva0}}</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">Descuento</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="descuento">{{entity.totalDescuento}}</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">Valor ICE</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="ice">{{entity.valorICE}}</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">IVA 12%</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="iva12">{{entity.iva12}}</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">VALOR TOTAL</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="valorTotal">{{entity.valorTotal}}</p>
            </div>
        </div>
        <div class="col-lg-offset-3 col-lg-6 botones">
            <button class="btn btn-success" type="submit">
                <i class="fa fa-save"></i>
                Crear
            </button>
        </div>
    </form>
{% endblock %}
{% block javascript %}
    <script src="{{asset('recursos/bower_components/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('recursos/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('recursos/js/script.js')}}"></script>
    <script>
                                    var count = $('#productos > tbody:last tr').length;
                                    $("#nuevoProducto").click(function (e) {
                                        count = count + 1;
                                        $('#productos > tbody:last').append('<tr class = "filaProducto">'
                                                + '<td><input type = "text" class= "hidden count" value = "' + count + '"><input type = "text" value = "1" size= "4" name = "cantidad[' + count + ']" id="cantidad' + count + '" onchange = "calcularaValorTotalXProducto(' + count + ')"></td>'
                                                + '<td><input type = "text" id="nombreProducto' + count + '" name = "nombreProducto[' + count + ']"></td>'
                                                + '<td><input type = "text"  size= "4" name = "precio[' + count + ']" id="precio' + count + '" required onchange = "calcularaValorTotalXProducto(' + count + ')"></td>'
                                                + '<td><input type = "text" value = "0" size= "4" name = "descuento[' + count + ']" id="descuento' + count + '" required onchange = "calcularaValorTotalXProducto(' + count + ')"></td>'
                                                + '<td><input style="width: 70px" type="checkbox" id="tieneiva' + count + '" name = "tieneiva[' + count + ']" onchange = "calcularaValorTotalXProducto(' + count + ')"></td>'
                                                + '<td id ="total' + count + '" name = "total[' + count + ']"></td>'
                                                + '<td><input type = "text" value = "0" size= "4" name = "ice[' + count + ']" id="ice' + count + '" onchange = "calcularaValorTotalXProducto(' + count + ')"></td>'
                                                + "<td> <button class='btn btn-danger link-boton accion'  type='button' onclick = 'eliminarFila(this)'>"
                                                + "<i class='glyphicon glyphicon-trash icon-white'></i></button></td></tr>"
                                                + '</tr>');

                                        $("#cantidad" + count).rules("add", {
                                            required: true,
                                            number: true,
                                        });
                                        $("#nombreProducto" + count).rules("add", {
                                            required: true,
                                        });
                                        $("#precio" + count).rules("add", {
                                            required: true,
                                            number: true,
                                        });
                                        $("#descuento" + count).rules("add", {
                                            required: true,
                                            number: true,
                                        });
                                        $("#ice" + count).rules("add", {
                                            required: true,
                                            number: true,
                                        });
                                    });

                                    $("#formCargarCompra").validate({
                                        errorClass: "my-error-class",
                                        validClass: "my-valid-class",
                                        rules: {
                                            nombre: {
                                                required: true,
                                            },
                                            email: {
                                                email: true,
                                            },
                                            tipoIdentificacion: {
                                                required: true,
                                            },
                                            identificacion: {
                                                required: true,
                                            },
                                        }

                                    });
                                    function calcularaValorTotalXProducto(pos) {
                                        var cantidad = parseFloat($("#cantidad" + pos).val());
                                        var precioUnitario = parseFloat($("#precio" + pos).val());
                                        var descuento = parseFloat($("#descuento" + pos).val());
                                        if (cantidad && precioUnitario && (descuento || descuento === 0)) {
                                            var total = parseFloat(cantidad * precioUnitario - descuento);
                                            $("#total" + pos).text(total.toFixed(2));
                                            calcularResumenValores();
                                        }
                                    }

                                    function eliminarFila(e) {
                                        $(e).parent().parent().remove();
                                        calcularResumenValores();
                                    }
                                    function calcularResumenValores() {
                                        var subTotalSinImpuesto = 0;
                                        var subTotal12 = 0;
                                        var subTotal0 = 0;
                                        var descuento = 0;
                                        var ice = 0;
                                        var iva12 = 0;
                                        var valorTotal = 0;

                                        $("tr.filaProducto").each(function (index, element) {
                                            var id = $(element).find(".count").val();
                                            if (parseFloat($("#total" + id).text()) && parseFloat($("#descuento" + id).val()) >= 0) {
                                                subTotalSinImpuesto += parseFloat($("#total" + id).text());
                                                descuento += parseFloat($("#descuento" + id).val());
                                                if ($("#tieneiva" + id).is(':checked')) {
                                                    subTotal12 += parseFloat($("#total" + id).text());
                                                    iva12 = subTotal12 * 12 / 100;
                                                } else {
                                                    subTotal0 += parseFloat($("#total" + id).text());
                                                }

                                                ice += parseFloat($("#ice" + id).val());
                                            }
                                        });
                                        $("#subTotalSinImpuesto").text(subTotalSinImpuesto.toFixed(2));
                                        $("#subTotal12").text(subTotal12.toFixed(2));
                                        $("#subTotal0").text(subTotal0.toFixed(2));
                                        $("#descuento").text(descuento.toFixed(2));
                                        $("#ice").text(ice.toFixed(2));
                                        $("#iva12").text(iva12.toFixed(2));
                                        valorTotal = subTotalSinImpuesto + iva12 + ice;
                                        $("#valorTotal").text(valorTotal.toFixed(2));
                                    }

    </script>
{% endblock %}