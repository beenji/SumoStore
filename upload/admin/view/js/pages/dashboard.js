$(function(){

    // Add todo
    $('#new_todo').keyup(function(e) {
        var key = e.which ? e.which : e.keyCode,
            elem = $(this);

        if (key == 13) {
            // Submit!
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: './common/home/add_todo?token=' + sessionToken, 
                data: {todo: elem.val()}, 
                success: function(response) {
                    if (response.todo != undefined) {
                        $('#list_todo li:last-child').before('<li><dl><dt><input type="checkbox" value="' + response.id + '" /></dt><dd>' + response.todo + '</dd></dl></li>')
                        elem.val('');
                    }
                }
            });
        }
    });

    $('#add_todo').click(function() {
        var e = jQuery.Event("keyup", {keyCode: 13});

        $('#new_todo').trigger(e);
    });

    $('#list_todo').on('click', 'input[type="checkbox"]', function() {
        var elem = $(this);

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: './common/home/complete_todo?token=' + sessionToken,
            data: {todo_id: elem.val()},
            success: function(response) {
                if (response === true) {
                    // Good!
                    $('dd', elem.closest('dl')).wrap("<s></s>");
                    elem.prop('disabled', true);
                }
            }
        })
    });


    
    if (!jQuery.plot) {
        return;
    }

    function showTooltip(x, y, contents) {
        $("<div id='tooltip'>" + contents + "</div>").css({
            position: "absolute",
            display: "none",
            top: y + 5,
            left: x + 5,
            border: "1px solid #000",
            padding: "5px",
            'color':'#fff',
            'border-radius':'2px',
            'font-size':'11px',
            "background-color": "#000",
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    } 

    /* Orders */
    if ($('#sales_chart').length) {
        $.plot($('#sales_chart'), [{
            data: returns,
            label: returnsLabel
        }, {
            data: orders,
            label: ordersLabel
        }], {
            series: {
                lines: {
                    show: true,
                    lineWidth: 2, 
                    fill: false,
                    fillColor: { colors: [{ opacity: 0.3 }, { opacity: 0.3}] }
                },
                fillColor: "rgba(0, 0, 0, 1)",
                points: {
                    show: true,
                    fill: true
                },
                shadowSize: 2
            },
            legend:{
                show: true,
                 position:"nw",
                 backgroundColor: "green",
                 container: $("#sales_chart_legend")
            },
            grid: {
                labelMargin: 10,
                axisMargin: 500,
                hoverable: true,
                clickable: true,
                tickColor: "rgba(0,0,0,0.15)",
                borderWidth: 0
            },
            colors: ["#40a5c3", "#f97f32"],
            xaxis: {
                autoscaleMargin: 0,
                tickFormatter: function(obj) {
                    if (ordersTickers[obj] != undefined) {
                        return ordersTickers[obj];
                    }

                    return obj;
                },
                tickDecimals: 0
            },
            yaxis: {
                autoscaleMargin: 1,
                ticks: 5,
                tickDecimals: 0,
                min: 0
            }
        });
    }

    /* Visitors */
    if ($('#visitors_chart').length) {
        $.plot($('#visitors_chart'), [{
            data: customers,
            label: customersLabel
        }], {
            series: {
                lines: {
                    show: true,
                    lineWidth: 2, 
                    fill: false,
                     fillColor: { colors: [{ opacity: 0.3 }, { opacity: 0.3}] }
                },
                fillColor: "rgba(0, 0, 0, 1)",
                points: {
                    show: true,
                    fill: true
                },
                shadowSize: 2
            },
            legend:{
                show: true,
                 position:"nw",
                 backgroundColor: "green",
                 container: $("#visitors_chart_legend")
            },
            grid: {
                labelMargin: 10,
                axisMargin: 500,
                hoverable: true,
                clickable: true,
                tickColor: "rgba(0,0,0,0.15)",
                borderWidth: 0
            },
            colors: ["#629e14", "#374558"],
            xaxis: {
                autoscaleMargin: 0,
                ticks: 11,
                tickDecimals: 0,
                tickFormatter: function(obj) {
                    if (customersTickers[obj] != undefined) {
                        return customersTickers[obj];
                    }

                    return obj;
                }
            },
            yaxis: {
                autoscaleMargin: 0,
                ticks: 5,
                tickDecimals: 0,
                min: 0
            }
        });
    }
    
    /* Pie */
    if ($('#country_chart').length) {
        $.plot('#country_chart', customersPerCountry, {
            series: {
                pie: {
                    show: true,
                    innerRadius: 0.55,
                    shadow:{
                    top: 5,
                    left: 15,
                    alpha:0.3
                },
                stroke:{
                    color:'#333',
                    width:0
                },
                    label: {
                        show: false
                    },
                        highlight:{
                            opacity: 0.08
                        }
                    }
                },
            grid: {
                hoverable: true,
                clickable: true
            },
            colors: ["#93e529", "#ffffff", "#f97f32", "#40a5c3"],
            legend: {
                show: false
            }
        });
    }

    var previousPoint = null;

    $("#sales_chart, #visitors_chart").bind("plothover", function (event, pos, item) {
        var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(0),
                    y = item.datapoint[1].toFixed(0);
                
                showTooltip(item.pageX, item.pageY, y + ' ' + item.series.label.toLowerCase());
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
});
