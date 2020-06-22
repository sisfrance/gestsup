<?php
################################################################################
# @Name : stat_line.php
# @Desc : display line graphic
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 06/10/2012
# @Update : 24/09/2014
# @Version : 3.0.10
################################################################################
?>

<script type="text/javascript" src="./template/assets/js/jquery-2.0.3.min.js"></script>
<script src="./components/Highcharts-2.3.3/js/highcharts.js"></script>
<script src="./components/Highcharts-2.3.3/js/modules/exporting.js"></script>
<script type="text/javascript">
	$(function () {
		var chart1;
		$(document).ready(function() {
			chart1 = new Highcharts.Chart({
				chart: {
					renderTo: 'container1',
					type: 'line',
					marginRight: 130,
					marginBottom: 25,
					backgroundColor:'#EEE'
				},
				title: {
					text: '<?php echo $libchart; ?>',
					x: -20 //center
				},
				subtitle: {
					text: "<?php echo "<u>Total ouverts:</u> $count / <u>Total fermés:</u> $count2  / <u>Total en cours:</u> $count3 / <u>Total depuis le début:</u> $count4"; ?>",
					x: -20
				},
				xAxis: {
				allowDecimals:false
				},
				yAxis: {
					allowDecimals:false,
					title: {
						text: '<?php echo $liby; ?>'
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				tooltip: {
					formatter: function() {
							return '<b>'+ this.series.name +'</b><br/>'+
							this.x +': '+ this.y +' Tickets';
					}
				},
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'top',
					x: -10,
					y: 100,
					borderWidth: 0
				},
				series: 
				[
				    {
    					name: 'Tickets ouverts',
    					data: [
        					<?php
        					for($i=0;$i<sizeof($values1);$i++) 
							{ 
							    $last=sizeof($values1)-1;
						        if ($i!=$last) echo '['.$xnom1[$i].','.$values1[$i].'],'; else echo '['.$xnom1[$i].','.$values1[$i].']';
							} 
        					?>
    					]
				    },
				    {
				        name: 'Tickets fermés',
    					data: [
        					<?php
        					for($i=0;$i<sizeof($values2);$i++) 
							{ 
							    $last=sizeof($values2)-1;
						        if ($i!=$last) echo '['.$xnom2[$i].','.$values2[$i].'],'; else echo '['.$xnom2[$i].','.$values2[$i].']';
							}
        					?>
    					]
				    }
				    
				]
			});
		});
	});
</script>
					
<div id="container1" style="min-width: 400px; height: 400px; margin: 0 auto"></div>