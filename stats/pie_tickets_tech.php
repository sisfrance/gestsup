<?php
################################################################################
# @Name : pie_tickets_tech.php
# @Desc : Display Statistics chart 1
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 25/11/2014
# @Version : 3.0.11
################################################################################

$values = array();
$xnom = array();
$libchart="Tickets par techniciens";
$unit='tickets';

//total
$qtotal = mysql_query("SELECT count(*) FROM tincidents WHERE disable='0'");
$month1=mysql_fetch_array($qtotal);

$query1 = "
SELECT CONCAT_WS('. ', left(tusers.firstname, 1),  tusers.lastname) as Technicien, count(*) as Resolu 
FROM tincidents 
INNER JOIN tusers 
ON (tincidents.technician=tusers.id ) 
WHERE tusers.disable=0 AND
tincidents.technician LIKE '$_POST[tech]' AND
tincidents.type LIKE '$_POST[type]' AND
criticality like '$_POST[criticality]' AND
tincidents.category LIKE '$_POST[category]' AND
tincidents.date_create LIKE '%-$_POST[month]-%' AND
tincidents.date_create LIKE '$_POST[year]-%' AND
tincidents.disable LIKE '0'
GROUP BY tusers.firstname 
ORDER by Resolu DESC";
$query=mysql_query($query1);
while ($row=mysql_fetch_array($query)) 
{
	$name=substr($row[0],0,42);
	array_push($values, $row[1]);
	array_push($xnom, $name);
} 	
$container='container2';
include('./stat_pie.php');
echo "<div id=\"$container\"></div>";
if ($rparameters['debug']==1) echo $query1;
?>