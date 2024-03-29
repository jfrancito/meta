var App = (function () {
  'use strict';
        //console.log("entro");
  App.dataTables = function( ){

    //We use this to apply style to certain elements
    $.extend( true, $.fn.dataTable.defaults, {
      dom:
        "<'row be-datatable-header'<'col-sm-6'l><'col-sm-6'f>>" +
        "<'row be-datatable-body'<'col-sm-12'tr>>" +
        "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    } );

    $("#tnso").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        "autoWidth": true,
        scrollX:"400px",
        scrollY:"2000px",
        
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });


    $("#nso").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });


    $("#nsv").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        scrollX: true,
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });




    $("#tran").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        "ordering": false,
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });



    $("#item1").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });


    $("#item2").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });

    $("#item3").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });


    $("#ple").dataTable({
        dom: 'Bfrtip',
        "scrollX": true,
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[250, 500, -1], [250, 500, "All"]],
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });


    $("#maux").dataTable({
        dom: 'Bfrtip',
        "scrollX": true,
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[250, 500, -1], [250, 500, "All"]],
        columnDefs:[{
            targets: "_all",
            sortable: false
        }]
    });

    $("#table1").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        order : [[ 0, "asc" ]]
    });

    $("#tableactivo").dataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ],
        "lengthMenu": [[500, 1000, -1], [500, 1000, "All"]],
        order : [[ 0, "asc" ]],
        "scrollX": true
    });




    //Remove search & paging dropdown
    $("#table2").dataTable({
      pageLength: 6,
      dom:  "<'row be-datatable-body'<'col-sm-12'tr>>" +
            "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    });

    //Enable toolbar button functions
    $("#table3").dataTable({
      buttons: [
        'copy', 'excel', 'pdf', 'print'
      ],
      "lengthMenu": [[6, 10, 25, 50, -1], [6, 10, 25, 50, "All"]],
      dom:  "<'row be-datatable-header'<'col-sm-6'l><'col-sm-6 text-right'B>>" +
            "<'row be-datatable-body'<'col-sm-12'tr>>" +
            "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    });


  };

  return App;
})(App || {});
