<?php
################################################################################
# @Name : histo_load.php
# @Desc : Display Statistics by catgories
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 13/03/2014
# @Version : 3.0.7
################################################################################

$values = array();
$xnom = array();
$qtotal = mysql_query("SELECT count(*) FROM tincidents");
$rtotal=mysql_fetch_array($qtotal);
$libchart="Charge de travail actuelle par technicien";
$query = mysql_query("
	SELECT CONCAT_WS('. ', left(tusers.firstname, 1),  tusers.lastname) as Technicien, ROUND((SUM(tincidents.time_hope-tincidents.time))/60) as Charge
	FROM
	tincidents 
	INNER JOIN tusers 
	ON
	(tincidents.technician=tusers.id ) WHERE 
	tusers.disable='0' AND
	tincidents.disable='0' AND
	tincidents.time_hope-tincidents.time>0 AND
	(tincidents.state='1' OR tincidents.state='2' OR tincidents.state='6')
	GROUP BY tusers.firstname ORDER BY Charge DESC
");
while ($row=mysql_fetch_array($query)) 
{
	$r=$row[1];
	$name=substr($row[0],0,42);
	array_push($values, $r);
	array_push($xnom, $name);
} 
$container="container5";
include('./stat_histo.php');
echo "<div id=\"$container\" style=\"min-width: 300px; height: 400px; margin: 0 auto\"></div>";
?>