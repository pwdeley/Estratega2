@extends('Layout')
@section('block css')
    <!-- DataTables CSS -->
    <link href="{{asset('recursos/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')}}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{asset('recursos/bower_components/datatables-responsive/css/dataTables.responsive.css')}}" rel="stylesheet">
@endsection

@section('block panel_title')
    <i class="fa fa-bar-chart-o fa-fw"></i> Nueva Factura Compra
    <div class="pull-right">

    </div>
@endsection

@section('block content')
    {% for flashMessage in app.session.flashbag.get('notice') %}
        <div class="col-sm-12 alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4><strong>Ha ocurrido un error!</strong></h4>
            
        </div>
    {% endfor %}
    {% for flashMessage in app.session.flashbag.get('confirm') %}
        <div class="col-sm-12 alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
          
        </div>
    {% endfor %}
    <div class="col-md-12">

    </div>
    <form method="POST" action="{{asset('guardar-compra-fisica')}}" id="formCargarCompra">
        <div class="col-md-12">
            <fieldset class="border col-sm-12">
                <legend class="border">Datos Factura</legend>
                <div class="form-group">
                    <div class="col-sm-12 col-md-3"> 
                        <label class="control-label">Fecha Emisi&oacute;n: *</label> 
                        <input type="text" class="form-control" id="fecha" size="3" name="fechaEmision" required="required" /> 
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <label class="control-label">Numero Factura*</label>
                        <input type="text" id="numeroFactura" class="form-control" name="numeroFactura" required="required">
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <label class="control-label">Numero Autorizacion*</label>
                        <input type="text" id="numeroAutorizacion"  class="form-control" name="numeroAutorizacion" required="required" > 
                    </div>
                </div>
            </fieldset>
            <fieldset class="border col-sm-12">
                <legend class="border">Proveedor</legend>

                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label">Nombre*</label>
                        <input type="text" id="nombre" class="form-control" name="nombre" required="required">
                    </div>
                    <div class="col-md-3">
                        <label class="control-label">Identificaci&oacute;n*</label>
                        <input type="text" id="identificacion"  class="form-control" name="identificacion" required="required" > 
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">Direcci&oacute;n *</label>
                        <input type="text" id="direccion" class="form-control" name="direccion" required="required" >
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

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-offset-8 col-md-4">
            <div class="col-xs-10 col-sm-8 text-right">SubTotal Sin Impuesto</div> 
            <div class="col-xs-2 col-sm-4">
                <p class="" id="subTotalSinImpuesto">0.00</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">Sub Total 12%</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="subTotal12">0.00</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">Sub Total 0%</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="subTotal0">0.00</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">Descuento</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="descuento">0.00</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">Valor ICE</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="ice">0.00</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">IVA 12%</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="iva12">0.00</p>
            </div>
            <div class="col-xs-10 col-sm-8 text-right">VALOR TOTAL</div>
            <div class="col-xs-2 col-sm-4">
                <p class="text-left" id="valorTotal">0.00</p>
            </div>
        </div>
        <div class="col-lg-offset-3 col-lg-6 botones">
            <button class="btn btn-success" type="submit">
                <i class="fa fa-save"></i>
                Crear
            </button>
        </div>
    </form>
@endsection
@section('block javascript')
    <script src="{{asset('recursos/bower_components/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('recursos/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('recursos/js/script.js')}}"></script>
    <script>
        var count = 0;
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
@endsection