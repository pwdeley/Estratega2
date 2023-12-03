$.datepicker.regional['es'] = {
    closeText: 'Cerrar',
    prevText: '<Ant',
    nextText: 'Sig>',
    currentText: 'Hoy',
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
    dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
    weekHeader: 'Sm',
    dateFormat: 'dd/mm/yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['es']);
$(function() {
    $("#fecha").datepicker({
        showButtonPanel: true,
        dateFormat: "dd/mm/yy",
        beforeShow: function() {
            $(".ui-datepicker").css('font-size', 12)
        }
    });
    $("#fechaFinT").datepicker({
        showButtonPanel: true,
        dateFormat: "dd/mm/yy",
        beforeShow: function() {
            $(".ui-datepicker").css('font-size', 12)
        }
    });
    
    $("#fechaInicial").datepicker({
        showButtonPanel: true,
        dateFormat: "yy-mm-dd",
        beforeShow: function() {
            $(".ui-datepicker").css('font-size', 12)
        }
    });
    $("#fechaFinal").datepicker({
        showButtonPanel: true,
        dateFormat: "yy-mm-dd",
        beforeShow: function() {
            $(".ui-datepicker").css('font-size', 12)
        }
    });
    $("#fechaDocModificado").datepicker({
        showButtonPanel: true,
        dateFormat: "dd/mm/yy",
        beforeShow: function() {
            $(".ui-datepicker").css('font-size', 12)
        }
    });
});

$(document).ready(function($) {
    if ($('#claveAcceso').val() !== undefined) {
        if ($('#datos').val() !== "") {
            CrearClaveAcceso();
        }
    } else {
        configurarTablas("#data-table");
    }
    
});

function configurarTablas(id) {

    $(id).DataTable({
        responsive: true,
        bAutoWidth: false,
        bSort: false,
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
        }
    });
}
function CrearClaveAcceso() {

    var datos = $('#datos').val();
    datos = datos.split(",");
    var fecha = $('#fecha').val();
    var claveAcceso = fecha.replace('/', '');
    claveAcceso = claveAcceso.replace('/', '');
    claveAcceso += datos[6];
    claveAcceso += datos[0];
    claveAcceso += datos[1];
    claveAcceso += datos[3] + datos[4];
    claveAcceso += datos[5];
    claveAcceso += "12345678";
    claveAcceso += datos[2];
    claveAcceso += Modulo11(claveAcceso).toString();
    $('#claveAcceso').text(claveAcceso);
}

function Modulo11(claveAcceso) {

    var multiplos = [2, 3, 4, 5, 6, 7];
    var i = 0;
    var cantidad = claveAcceso.length;
    var total = 0;
    while (cantidad > 0) {
        total += parseInt(claveAcceso.substring(cantidad - 1, cantidad)) * multiplos[i];
        i++;
        i = i % 6;
        cantidad--;
    }
    var modulo11 = 11 - total % 11;
    if (modulo11 == 11) {
        modulo11 = 0;
    } else if (modulo11 == 10) {
        modulo11 = 1;
    }

    return modulo11;

}
