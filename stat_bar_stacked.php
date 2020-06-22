<?php
################################################################################
# @Name : stat_bar_stacked.php
# @Desc : display bar graph to availabilty page
# @call : /availability.php
# @parameters : unit, values, names, libchart
# @Author : Flox
# @Create : 05/06/2014
# @Update : 05/06/2014
# @Version : 3.0.9
################################################################################
?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: '<?php echo $container; ?>',
                type: 'column'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: ['Disponilbité']
            },
            yAxis: {
                title: {
                    text: 'Pourcentage de disponibilité'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            legend: {
                align: 'right',
                x: -10,
                verticalAlign: 'top',
                y: 30,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'%<br/>'+
                        'Total: '+ this.point.stackTotal+'%<br/>';
                }
            },
            plotOptions: {
                column: {
                    stacking: 'percent',
                    dataLabels: {
                    	formatter: function(){
                    		return '<br/><b>'+this.y+'%</b><br/>';
                    	},
                        enabled: true,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                    }
                }
            },
            series: [{
                name: 'Indisponibilité',
                data: [<?php echo $tx2; ?>],
                color: '#AA4643'
            }, {
                name: 'Disponibilité',
                data: [<?php echo $tx; ?>],
                color: '#89A54E'
            }]
        });
    });
    
});
</script>
