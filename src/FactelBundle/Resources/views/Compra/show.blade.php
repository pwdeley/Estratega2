@extends('Layout')
{% block panel_title %}

    <i class="fa fa-bar-chart-o fa-fw"></i>Factura Compra <strong>Nro: {{entity.numeroFactura}}</strong>
    <div class="pull-right">
        <div class="btn-group">
            <button class="btn btn-info btn-xs dropdown-toggle"
                    type="button" data-toggle="dropdown">
                <i class="fa fa-list"></i>
                Acciones <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <li><a href="{{ path('compra-nueva') }}"><i class="fa fa-plus-circle"></i> Cargar Compra</a></li>
                <li><a href="{{ path('compra') }}"><i class="fa fa-list"></i> Compras</a></li>
                <li><a href="{{ path('compra_edit',{ 'id': entity.id }) }}"><i class="fa fa-pencil-square-o"></i> Editar</a></li>
                <li class="divider"></li>
                <li><a href="{{ path('compra_eliminar',{ 'id': entity.id }) }}"><i class="glyphicon glyphicon-trash"></i> Eliminar Compra</a></li>  
            </ul>
        </div>

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
    <div class="container-fluid factura">
        <fieldset class="border  col-sm-12 ">
            <legend class="border">Comprobante</legend>
            <div class="control-group" id="comprobante">
                <div class="col-sm-12 col-md-6"><strong>Factura Compra Nro: </strong>{{entity.numeroFactura}}</div>
                <div class="col-sm-12 col-md-6"><strong>Fecha Emisi&oacute;n: </strong>{{entity.fechaEmision |date('d/m/Y')}}</div>
                <div class="col-sm-12 col-md-6" style="padding: 0 !important; margin-top: 10px;">
                    <div class="col-sm-12"><strong>N&uacute;mero Autorizaci&oacute;n</strong></div>
                    <div class="col-sm-12">{{entity.numeroAutorizacion}}</div>
                </div>
                <div class="col-sm-12 col-md-6" style="padding: 0 !important; margin-top: 10px;">
                    <div class="col-sm-12"><strong>Clave Acceso</strong></div>
                    <div class="col-sm-12">{{entity.claveAcceso}}</div>
                </div>
            </div>
        </fieldset>

        <fieldset class="border col-sm-12">

            <legend class="border">Proveedor</legend>

            <div class="form-group">
                <div class="col-sm-12"><p class="text-center"><strong>Ruc: {{entity.identificacionProveedor}}</strong></p></div>
                <div class="col-md-6">
                    <strong>Razon Social: </strong>{{entity.razonSocialProveedor}}
                </div>
                <div class="col-md-6">
                    <strong>Nombre Comercial: </strong>{{entity.nombreComercialProveedor}}
                </div>
                <div class="col-md-6">
                    <strong>Direcci&oacute;n Matriz: </strong>{{entity.direccionMatrizProveedor}}
                </div>
                <div class="col-md-6">
                    <strong>Direcci&oacute;n Establecimiento: </strong>{{entity.direccionEstabProveedor}}
                </div>
            </div>
        </fieldset>
        <legend class="border">Productos</legend>
        <div class="dataTable_wrapper table-responsive col-lg-12">  
            <table class="table table-striped table-bordered table-hover" id="productos">
                <thead>
                <thead>
                    <tr>
                        <th >Cantidad</th>
                        <th>C&oacute;digo</th>
                        <th>Descripci&oacute;n</th>
                        <th>Precio</th>
                        <th>Descuento</th>
                        <th>SubTotal</th>
                        <th>IVA</th>
                        <th>ICE</th>
                    </tr>
                </thead>
                <tbody>
                    {% for item in entity.detallesCompra%}
                        <tr>
                            <td>{{item.cantidad}}</td>
                            <td>{{item.codigoProducto}}</td>
                            <td>{{item.nombre}}</td>
                            <td>{{item.precioUnitario}}</td>
                            <td>{{item.descuento}}</td>
                            <td>{{item.subTotal}}</td>
                            <td>{{item.iva12}}</td>
                            <td>{{item.ice}}</td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-8">
        <div class="col-xs-10 col-sm-8 text-right">SubTotal Sin Impuesto</div> 
        <div class="col-xs-2 col-sm-4">
            <p class="text-left" >{{entity.totalSinImpuestos}}</p>
        </div>
        <div class="col-xs-10 col-sm-8 text-right">Sub Total 12%</div>
        <div class="col-xs-2 col-sm-4">
            <p class="text-left">{{entity.subTotalIva12}}</p>
        </div>
        <div class="col-xs-10 col-sm-8 text-right">Sub Total 0%</div>
        <div class="col-xs-2 col-sm-4">
            <p class="text-left">{{entity.subTotalIva0}}</p>
        </div>
        <div class="col-xs-10 col-sm-8 text-right">Descuento</div>
        <div class="col-xs-2 col-sm-4">
            <p class="text-left">{{entity.totalDescuento}}</p>
        </div>
        <div class="col-xs-10 col-sm-8 text-right">Valor ICE</div>
        <div class="col-xs-2 col-sm-4">
            <p class="text-left">{{entity.valorICE}}</p>
        </div>
        <div class="col-xs-10 col-sm-8 text-right">IVA 12%</div>
        <div class="col-xs-2 col-sm-4">
            <p class="text-left">{{entity.iva12}}</p>
        </div>
        <div class="col-xs-10 col-sm-8 text-right">Propina</div>
        <div class="col-xs-2 col-sm-4">
            <p class="text-left" id="propina">{{entity.propina}}</p>
        </div>
        <div class="col-xs-10 col-sm-8 text-right">VALOR TOTAL</div>
        <div class="col-xs-2 col-sm-4">
            <p class="text-left" id="valorTotal">{{entity.valorTotal}}</p>
        </div>
    </div>
{% endblock %}