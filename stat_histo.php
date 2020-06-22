<?php
/*
Author: Flox
File: stat_histo.php
Description: display histogram
Version: 1.0
Creation date: 06/11/2012
Last update: 06/11/2012
*/
?>
<script src="./components/Highcharts-2.3.3/js/modules/exporting.js"></script>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
	
            chart: {
                renderTo: '<?php echo $container; ?>',
                type: 'column',
				backgroundColor:'transparent'
            },
            title: {
                text: '<?php echo $libchart; ?>'
            },
            subtitle: {
                text: 'Nombre d\'heures de travail restantes dans les tickets ouverts'
            },
            xAxis: {
                categories: [
                   <?php
					for($i=0;$i<sizeof($xnom);$i++) 
					{ 
						$k=sizeof($values);
						$k=$k-1;
						if ($i==$k) echo "\"$xnom[$i]\""; else echo "\"$xnom[$i]\"".','; 
					} 
					?>
                ]
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Nombre d\'heures'
                }
            },
            legend: {
                layout: 'vertical',
                backgroundColor: '#FFFFFF',
                align: 'left',
                verticalAlign: 'top',
                x: 100,
                y: 70,
                floating: true,
                shadow: true
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.x +': '+ this.y +' h';
                }
            },
			
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
                series: [
				{
					name: 'Charge de travail en heures',
					data: [
					 <?php
					for($i=0;$i<sizeof($values);$i++) 
					{ 
						$k=sizeof($values);
						$k=$k-1;
						if ($i==$k) echo "['$xnom[$i]', $values[$i]]"; else echo "['$xnom[$i]', $values[$i]],";
					} 
					?>
					]
				}
    
				]
        });
    });
    
});
</script>
