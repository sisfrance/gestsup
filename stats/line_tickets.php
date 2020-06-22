<?php
################################################################################
# @Name : line_num_tickets.php
# @Desc : Display Statistics
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 24/09/2014
# @Version : 3.0.10
################################################################################

$user_id=$_SESSION['user_id'];

//count create period
$req= mysql_query( "SELECT count(*) FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' and date_create not like '0000-00-00 00:00:00'  and date_create like '$_POST[year]-$_POST[month]-%' AND disable='0'");
$res = mysql_fetch_array($req);
$count=$res[0];

//count create period
$req= mysql_query( "SELECT count(*) FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' and date_res not like '0000-00-00 00:00:00' and date_res like '$_POST[year]-$_POST[month]-%' AND disable='0'");
$res = mysql_fetch_array($req);
$count2=$res[0];

//count current open
$req3= "SELECT count(*) FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' AND disable='0' AND state!=3 AND state!=4";
$req3= mysql_query($req3);
$res3 = mysql_fetch_array($req3);
$count3=$res3[0];

//count total 
$req4= "SELECT count(*) FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' AND disable='0'";
$req4= mysql_query($req4);
$res4 = mysql_fetch_array($req4);
$count4=$res4[0];

//query for year selection
if (($_POST['month'] == '%') && ($_POST['year']!=='%'))
{
    $values1 = array();
    $values2 = array();
    $xnom1 = array();
    $xnom2 = array();
	$libchart="Evolution des tickets ouverts et fermés pour l\'année $_POST[year]";
	$sql1= "SELECT month(date_create) as x,count(*) as y FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' and date_create not like '0000-00-00 00:00:00' and date_create like '$_POST[year]-$_POST[month]-%' AND disable='0' group by x ";
	$sql2= "SELECT month(date_res) as x,count(*) as y FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' and date_res not like '0000-00-00 00:00:00' and date_res like '$_POST[year]-$_POST[month]-%' AND disable='0' group by x ";
	$result1 = mysql_query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
	$result2 = mysql_query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
	// push data in table
	while($data = mysql_fetch_array($result1))
	{
		array_push($values1 ,$data['y']);
		array_push($xnom1 ,$data['x']);
	}
	while($data = mysql_fetch_array($result2))
	{
		array_push($values2 ,$data['y']);
		array_push($xnom2 ,$data['x']);
	}
}
//query for month selection
else if ($_POST['month']!='%')
{
    $values1 = array();
    $values2 = array();
    $xnom1 = array();
    $xnom2 = array();
	$monthm=$_POST['month'];
	$libchart="Évolution des tickets ouverts et fermés pour le mois de $mois[$monthm] $_POST[year]";
	$sql1= "SELECT day(date_create) as x,count(*) as y FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' and date_create not like '0000-00-00 00:00:00'  and date_create like '$_POST[year]-$_POST[month]-%' AND disable='0' group by x ";
	$sql2= "SELECT day(date_res) as x,count(*) as y FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' and date_res not like '0000-00-00 00:00:00' and date_res like '$_POST[year]-$_POST[month]-%' AND disable='0' group by x ";
	$result1 = mysql_query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
	$result2 = mysql_query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
	// push data in table
	while($data = mysql_fetch_array($result1)){
    	array_push($values1 ,$data['y']);
    	array_push($xnom1 ,$jour[$data['x']]);
	}
	while($data = mysql_fetch_array($result2)){
    	array_push($values2 ,$data['y']);
    	array_push($xnom2 ,$jour[$data['x']]);
	}
}
//query for all years selection
else if ($_POST['year']=='%')
{
    $values1 = array();
    $values2 = array();
    $xnom1 = array();
    $xnom2 = array();
	$libchart="Évolution des tickets ouverts et fermés sur toutes les années";
	$sql1= "SELECT year(date_create) as x,count(*) as y FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' and date_create not like '0000-00-00 00:00:00'  and date_create like '$_POST[year]-$_POST[month]-%' AND disable='0' group by x ";
	$sql2= "SELECT year(date_res) as x,count(*) as y FROM `tincidents` WHERE technician LIKE '$_POST[tech]' and criticality like '$_POST[criticality]' and type LIKE '$_POST[type]' and category LIKE '$_POST[category]' and date_res not like '0000-00-00 00:00:00' and date_res like '$_POST[year]-$_POST[month]-%' AND disable='0' group by x ";
	$result1 = mysql_query($sql1) or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
	$result2 = mysql_query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
	// push data in table
	while($data = mysql_fetch_array($result1)){array_push($values1 ,$data['y']); array_push($xnom1 ,$data['x']);}	
	while($data = mysql_fetch_array($result2)){array_push($values2 ,$data['y']); array_push($xnom2 ,$data['x']);}	
}

if ($res[0]!=0) 
{
	$liby="Nombre de tickets";
	$container="container1";		
	include('./stat_line.php');
}
else { echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Aucun ticket ouvert et fermé dans la plage indiqué.</div>';}

//display query on debug mode
if($rparameters['debug']==1)
{
    echo "sql1: $sql1 <br />sql2: $sql2 <br />";
    print_r($values1);echo "<br />";
    for($i=0;$i<sizeof($values1);$i++) 
    { 
    $last=sizeof($values1)-1;
    if ($i!=$last) echo '['.$xnom1[$i].','.$values1[$i].'],'; else echo '['.$xnom1[$i].','.$values1[$i].']';
    } 
    echo "<br />";
    print_r($values2);echo "<br />";
    for($i=0;$i<sizeof($values2);$i++) 
    { 
    $last=sizeof($values2)-1;
    if ($i!=$last) echo '['.$xnom2[$i].','.$values2[$i].'],'; else echo '['.$xnom2[$i].','.$values2[$i].']';
    } 
}
?>	