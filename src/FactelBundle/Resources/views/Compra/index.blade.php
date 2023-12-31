@extends('Layout')
{% block css %}
    <!-- DataTables CSS -->
    <link href="{{asset('recursos/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css')}}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{asset('recursos/bower_components/datatables-responsive/css/dataTables.responsive.css')}}" rel="stylesheet">
{% endblock %}
{% block panel_title %}
    <i class="fa fa-bar-chart-o fa-fw"></i> Compras
    <div class="pull-right">
        <a href="{{ path('compra-nueva') }}">
            <button class="btn btn-primary link-boton">
                <i class="fa fa-plus-circle"></i>
                Cargar Compra
            </button>
        </a>
    </div>
{% endblock %}
{% block content %}
    <div class="dataTable_wrapper table-responsive col-lg-12">  
        <a href="../../../Entity/Compra.php"></a>
        <table class="table table-striped table-bordered table-hover" id="compra-table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>No.</th>
                    <th>Proveedor</th>
                    <th>RUC Proveedor</th>
                    <th>Fecha Emisión</th>
                    <th>SubTotal 12%</th>
                    <th>SubTotal 0%</th>
                    <th>IVA</th>
                    <th>ICE</th>
                    <th>Total</th>
                    <th>Factura Fisica</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

{% endblock %}
{% block javascript %}
    <!-- DataTables JavaScript -->
    <script src="{{asset('recursos/bower_components/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('recursos/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('recursos/js/script.js')}}"></script>
    <script>
        var oTable = $("#compra-table").dataTable({
            columnDefs: [
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return "<a href='" + row[0] + "'>" + data + "</a>";
                    }
                },
                {"visible": false, "targets": [0]}
            ],
            responsive: true,
            bAutoWidth: false,
            sAjaxSource: "{{ path('all_compra')}}",
            bProcessing: true,
            bServerSide: true,
            bSort: false,
            aLengthMenu: [[5, 10, 15], [5, 10, 15]],
            language: {
                sProcessing: "Procesando...",
                sLengthMenu: "Mostrar _MENU_",
                sZeroRecords: "No se encontraron resultados",
                sEmptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
                sInfoEmpty: "Mostrando del 0 al 0 de un total de 0 registros",
                sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                sInfoPostFix: "",
                sSearch: "Buscar:",
                sUrl: "",
                sInfoThousands: ",",
                sLoadingRecords: "Cargando...",
                oPaginate: {
                    sFirst: "Primero",
                    sLast: "Último",
                    sNext: "Siguiente",
                    sPrevious: "Anterior"
                },
                oAria: {
                    sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sSortDescending: ": Activar para ordenar la columna de manera descendente"
                },
            },
        });
        $('#compra-table_filter input').unbind();
        $('#compra-table_filter input').bind('keyup', function (e) {
            if (e.keyCode == 13) {
                oTable.fnFilter($(this).val());
            }
        });
    </script>

{% endblock %}