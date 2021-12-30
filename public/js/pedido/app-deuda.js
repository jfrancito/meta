var App = (function () {
    'use strict';

    App.ChartJs = function () {
        var carpeta = $("#carpeta").val();


        var data_icl = $('.mdi-check-cliente').attr('data_icl');
        var data_pcl = $('.mdi-check-cliente').attr('data_pcl');
        var _token = $('#token').val();

        console.log(data_icl + ' ' + data_pcl);

        $.ajax({
            type: "POST",
            url: carpeta + "/ajax-tramo-deuda",
            data: {
                _token: _token,
                data_icl: data_icl,
                data_pcl: data_pcl,

            },
            beforeSend: function () {
                $("#canvasdeuda").html("<div class='lds-roller'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>");
            },
            success: function (data) {
                $("#canvasdeuda").empty();

               if (data["saldocuenta"].length>0) {
                function lineChart() {
                    //Set the chart colors
                    var color1 = tinycolor(App.color.danger).lighten(22);;
                    var color2 = tinycolor(App.color.success).lighten(22);
                    var color3 = tinycolor(App.color.warning).lighten(22);
                    var datos = [];
                    var color = '';
                    var i = 0;
                    for (i = 0; i < data["saldocuenta"].length; i++) {



                        if (i == 0) {
                            datos.push({
                                "label": data["saldocuenta"][i].COD_CONTRATO.substring(0,2),
                                "borderColor": color1,
                                "backgroundColor": color1.setAlpha(.8),
                                "data": [data["saldocuenta"][i].T_MT, data["saldocuenta"][i].T_030, data["saldocuenta"][i].T_3190, data["saldocuenta"][i].T_9118, data["saldocuenta"][i].T_181]
                            });
                        } else if (i == 1) {
                            datos.push({
                                "label": data["saldocuenta"][i].COD_CONTRATO.substring(0,2),
                                "borderColor": color2,
                                "backgroundColor": color2.setAlpha(.8),
                                "data": [data["saldocuenta"][i].T_MT, data["saldocuenta"][i].T_030, data["saldocuenta"][i].T_3190, data["saldocuenta"][i].T_9118, data["saldocuenta"][i].T_181]
                            });

                        } else {
                            datos.push({
                                "label": data["saldocuenta"][i].COD_CONTRATO.substring(0,2),
                                "borderColor": color3,
                                "backgroundColor": color3.setAlpha(.8),
                                "data": [data["saldocuenta"][i].T_MT, data["saldocuenta"][i].T_030, data["saldocuenta"][i].T_3190, data["saldocuenta"][i].T_9118, data["saldocuenta"][i].T_181]
                            });
                        };


                    };

                    console.log(data["saldocuenta"]);
                    console.log(datos);
                    //Get the canvas element
                    var ctx = document.getElementById("deudatramo");

                    var lineChartData = {
                        labels: ["MT", "0-30", "30-90", "90-180", ">180"],
                        datasets: datos
                    };
                    // function done() {
                    //     alert("haha");
                    //     var url = line.toBase64Image();
                    //     document.getElementById("url").src = url;
                    // }
                    var opt = {
                        responsive: true,
                        bezierCurve : false,
                        // animation: {
                        //     onComplete: done
                        // },
                        title: {
                            display: false,
                            text: 'Deuda'
                        },
                        tooltips: {
                            enabled: 'true',
                            mode: 'single',
                            callbacks: {
                                title: function (tooltipItem, data) {
                                    return "Tramo: " + data.labels[tooltipItem[0].index];
                                },
                                label: function (tooltipItems, data) {
                                    return "Deuda: " + tooltipItems.yLabel + ' S/';
                                },
                                footer: function (tooltipItem, data) { return "..."; }
                            }

                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },

                    }
                    var line = new Chart(ctx, {
                        type: 'line',
                        data: lineChartData,
                        options: opt
                    });
                }

                lineChart();
               } else {
                $("#canvasdeuda").html("<span>No se encontró detalle de la deuda.</span>");
                $("#canvasdeudacont").empty()
               }
            
            },
            error: function (data) {
                error500(data);
            }
        });

     
    };

    return App;
})(App || {});