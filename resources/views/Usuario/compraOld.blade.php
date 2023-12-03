<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Finanzas &mdash; Estratega Contable</title>
  <style type="text/css">
    img{
      max-width: 150px;
    }
  </style>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../backend/assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../backend/assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href=../backend/assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../backend/assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="../backend/assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="../backend/assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../backend/assets/css/style.css">
  <link rel="stylesheet" href="../backend/assets/css/components.css">
<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<!-- /END GA --></head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>

        </form>
        <ul class="navbar-nav navbar-right">


          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="../backend/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">Hola, {{ Auth::user()->name}}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">Logged in 5 min ago</div>
              <a href="features-profile.html" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a>
              <a href="features-activities.html" class="dropdown-item has-icon">
                <i class="fas fa-bolt"></i> Activities
              </a>
              <a href="features-settings.html" class="dropdown-item has-icon">
                <i class="fas fa-cog"></i> Settings
              </a>
              <div class="dropdown-divider"></div>
              <form method="POST" action="{{ route('logout') }}">
                @csrf

                <a href=" {{ route('logout')}}"
                    onclick="event.preventDefault();
                this.closest('form').submit();"
                class="dropdown-item has-icon text-danger">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                  </a>

            </form>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="index.html">Estratega Contable</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">EC</a>
          </div>
          <ul class="sidebar-menu">

            <li class="menu-header">Ingresos</li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Ventas</span></a>

              <ul class="dropdown-menu">
                <li><a class="nav-link" href="factura">Facturación Electrónica</a></li>
                <li><a class="nav-link" href="layout-transparent.html">Retenciones en Ventas</a></li>
                <li><a class="nav-link" href="layout-top-navigation.html">Otros Ingresos</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-th"></i> <span>Reportes</span></a>
              <ul class="dropdown-menu">

                <li><a class="nav-link" href="bootstrap-badge.html">Ventas</a></li>
                <li><a class="nav-link" href="bootstrap-breadcrumb.html">Retenciones</a></li>

              </ul>
            </li>
            <li class="menu-header">Egresos</li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-th-large"></i> <span>Compras</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="compra">Compras Electrónicas</a></li>
                <li><a class="nav-link" href="components-user.html">Notas de Venta</a></li>
                <li><a class="nav-link" href="components-user.html">Retenciones Efectuadas</a></li>
                <li><a class="nav-link" href="components-user.html">Reporte de Compras</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>Empleados</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="forms-advanced-form.html">Información Personal</a></li>
                <li><a class="nav-link" href="forms-editor.html">Nómina</a></li>
                <li><a class="nav-link" href="forms-validation.html">Reporte Empleados</a></li>
              </ul>
            </li>
                       <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-plug"></i> <span>Registros</span></a>
              <ul class="dropdown-menu">

                <li><a class="nav-link" href="modules-chartjs.html">Ingreso de Diarios</a></li>
                <li><a class="nav-link" href="modules-datatables.html">Reportes de Diarios</a></li>

              </ul>
            </li>
            <li class="menu-header">Impuestos</li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-pencil-ruler"></i> <span>Declaraciones</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="modules-chartjs.html">Declaración de IVA</a></li>
                <li><a class="nav-link" href="modules-datatables.html">Impuesto a la Renta</a></li>
                <li><a class="nav-link" href="modules-datatables.html">Retenciones</a></li>
                <li><a class="nav-link" href="modules-datatables.html">Reporte de Impuestos</a></li>
              </ul>
            </li>
            <li class="menu-header">Estados Financieros</li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-bicycle"></i> <span>Estados Financieros</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="modules-chartjs.html">Balance General</a></li>
                <li><a class="nav-link" href="modules-datatables.html">Resultados Integrales</a></li>
                <li><a class="nav-link" href="modules-datatables.html">Movimiento de Patrimonio</a></li>
                <li><a class="nav-link" href="modules-datatables.html">Flujo de Caja</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-exclamation"></i> <span>Pendientes</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="features-activities.html">Actividades</a></li>
                <li><a class="nav-link" href="features-post-create.html">Notas</a></li>
              </ul>
            </li>

</aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">

          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                    <h2>Gestión de Compras</h2>
                    <br><br>
                </div>
                <div class="card-header">
                  <h4>Subir archivo .xml de compras</h4>
                  <br><br>
              </div>
                    {{-- <div>
                    <input type="file" name="archivo" class="form__file" required>
                    <button type="submit" class="btn_save">&#128452; Compra a subir:</button>
                    </div> --}}

      <!-- Leer compra xml -->
<form action="" method="POST" enctype="multipart/form-data">
  @csrf
  <input type="file" name="file" required>
  <br><br>
  <input type="text" name="name" placeholder="Nombre" required autocomplete="@disabled(true)">
  <br><br>
  <input type="submit" value="Guardar">
</form>

<h3>Archivos Cargados</h3>
<table>
  <thead>
    <tr>
      <th>Miniatura</th>
      <th>Archivo</th>
      <th>Tamaño</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @if (isset($files))
      @foreach ($files as $file)
      <tr>
        <td>@if ($file['picture'])<img src="
          {{$file['picture']}}">@endif</td>
        <td>
          <a href="{{$file['link']}}" target="_blank">{{$file['name']}}</a>
        </td>
        <td>
          {{$file['size']}}
        </td>
      </tr>
      @endforeach
    @endif
  </tbody>
</table>


<!-- Symfony -->

<div class="pull-right">
    <div class="btn-group">
        <button class="btn btn-info btn-xs dropdown-toggle"
                type="button" data-toggle="dropdown">
            <i class="fa fa-list"></i>
            Acciones <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" role="menu">
            <li><a href="{{ asset('compra-nueva') }}"><i class="fa fa-plus-circle"></i> Cargar Compra</a></li>
            <li><a href="{{ asset('compra') }}"><i class="fa fa-list"></i> Compras</a></li>
            <li class="divider"></li>
        </ul>
    </div>

</div>

<div class="container-fluid factura">
    <fieldset class="border  col-sm-12 ">
        <legend class="border">Comprobante</legend>
        <div class="control-group" id="comprobante">

            <div class="col-sm-12 col-md-6" style="padding: 0 !important; margin-top: 10px;">
                <div class="col-sm-12"><strong>N&uacute;mero Autorizaci&oacute;n</strong></div>

            </div>
            <div class="col-sm-12 col-md-6" style="padding: 0 !important; margin-top: 10px;">
                <div class="col-sm-12"><strong>Clave Acceso</strong></div>
            </div>
        </div>
    </fieldset>

    <fieldset class="border col-sm-12">

        <legend class="border">Proveedor</legend>


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

            </tbody>
        </table>
    </div>
</div>

















@section('content')
    {{-- @for('flashMessage in app.session.flashbag.get->('notice')')


        <div class="col-sm-12 alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4><strong>Ha ocurrido un error!</strong></h4>
            <p>{{ flashMessage }}</p>
        </div>
   @endfor
    {% for flashMessage in app.session.flashbag.get('confirm') %}
        <div class="col-sm-12 alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{{ flashMessage }}</p>
        </div>
    {% endfor %} --}}
    {{-- <div class="col-md-12">
        {{ form_start(form) }}
        {{ form_widget(form) }}
        {{ form_end(form) }}
    </div> --}}
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


      <!-- Botón de carga de compra -->

                    <div><br><br></div>
                    <div class="card-header-action">
                      <a href="cargarCompra" class="btn btn-primary">Cargar Compra .xml</a></div>
                    </div>




            {{-- <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Empresa</th>
                      <th>Comprador</th>
                      <th>Fecha</th>
                      <th>Factura No.</th>
                      <th>Proveedor</th>
                      <th>RUC</th>
                      <th>Subtotal</th>
                      <th>Descuento</th>
                      <th>ICE</th>
                      <th>Subtotal Iva 0%</th>
                      <th>Subtotal Iva 12%</th>
                      <th>Iva 12%</th>
                      <th>Propina</th>
                      <th>TOTAL</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($compras as $compra)

                      <tr>
                          <th>{{$compra->establecimiento_id}}</th>
                          <th>{{$compra->emisor_id}}</th>
                          <th>{{$compra->fechaEmision}}</th>
                          <th>{{$compra->numeroFactura}}</th>
                          <th>{{$compra->razonSocialProveedor}}</th>
                          <th>{{$compra->identificacionProveedor}}</th>
                          <th>{{$compra->totalSinImpuestos}}</th>
                          <th>{{$compra->totalDescuento}}</th>
                          <th>{{$compra->valorICE}}</th>
                          <th>{{$compra->subTotalIva0}}</th>
                          <th>{{$compra->subTotalIva12}}</th>
                          <th>{{$compra->iva12}}</th>
                          <th>{{$compra->propina}}</th>
                          <th>{{$compra->valorTotal}}</th>

                          <th><a class="btn btn-primary btn-action mr-1" data-toggle="tooltip" title="Edit"  data-confirm="Estás seguro?|Esta acción es delicada y es bajo tu responsabilidad. Quieres continuar?" data-confirm-yes="alert('Modificado')"><i class="fas fa-pencil-alt"></i></a></th>

                      </tr>

                      @endforeach


                  </tbody>
                </table>
              </div>
            </div> --}}
              </div>
            </div>

          </div>
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2024 <div class="bullet"></div> Diseñado por:  <a href="https://estrategacontable.com">Estratega Contable</a>
        </div>
        <div class="footer-right">

        </div>
      </footer>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="../backend/assets/modules/jquery.min.js"></script>
  <script src="../backend/assets/modules/popper.js"></script>
  <script src="../backend/assets/modules/tooltip.js"></script>
  <script src="../backend/assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../backend/assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../backend/assets/modules/moment.min.js"></script>
  <script src="../backend/assets/js/stisla.js"></script>

  <!-- JS Libraies -->
  <script src="../backend/assets/modules/jquery.sparkline.min.js"></script>
  <script src="../backend/assets/modules/chart.min.js"></script>
  <script src="../backend/assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
  <script src="../backend/assets/modules/summernote/summernote-bs4.js"></script>
  <script src="../backend/assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="../backend/assets/js/page/index.js"></script>

  <!-- Template JS File -->
  <script src="../backend/assets/js/scripts.js"></script>
  <script src="../backend/assets/js/custom.js"></script>
</body>
</html>
