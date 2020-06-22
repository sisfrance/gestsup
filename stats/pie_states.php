<?php
################################################################################
# @Name : pie_states.php
# @Desc : Display Statistics chart 3
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 25/11/2014
# @Version : 3.0.11
################################################################################

$values = array();
$xnom = array();
$qtotal = mysql_query("SELECT count(*) FROM tincidents WHERE disable='0'");
$rtotal=mysql_fetch_array($qtotal);
$libchart="Tickets par états";
$unit='tickets';
$query1 = "
SELECT tstates.name as sta, COUNT(*) as nb
FROM tincidents INNER JOIN tstates ON (tincidents.state=tstates.id)
WHERE tincidents.disable LIKE '0' AND
tincidents.technician LIKE '$_POST[tech]' AND
tincidents.type LIKE '$_POST[type]' AND
criticality like '$_POST[criticality]' AND
tincidents.category LIKE '$_POST[category]' AND
tincidents.date_create LIKE '%-$_POST[month]-%' AND
tincidents.date_create LIKE '$_POST[year]-%'
GROUP BY tstates.number
ORDER BY nb
DESC
";
$query=mysql_query($query1);
while ($row=mysql_fetch_array($query)) 
{
	array_push($values, $row[1]);
	array_push($xnom, $row['sta']);
} 
$container='container3';
include('./stat_pie.php');
echo "<div id=\"$container\" ></div>";
if ($rparameters['debug']==1)echo $query1;
?>