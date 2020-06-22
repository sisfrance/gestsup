<?php
################################################################################
# @Name : pie_load.php
# @Desc : Display Statistics of chart6 by catgories
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 25/11/2014
# @Version : 3.0.11
################################################################################

$values = array();
$xnom = array();
$libchart="Répartition de la charge de travail par catégories";
$unit='h';
$current_month=date('m');
$current_year=date('Y');


//total
if ($_POST['category']=='') $_POST['category']='%';
$qtotal = mysql_query("SELECT count(*) FROM tincidents WHERE category NOT LIKE '0' and category LIKE '$_POST[category]'");
$rtotal=mysql_fetch_array($qtotal);

if ($_POST['category']!='%')
{
	if (($_POST['year']==$current_year) && ($_POST['month']==$current_month))
	{
		$query1 = "
		SELECT tsubcat.name as subcat, (SUM(tincidents.time_hope-tincidents.time))/60 as time
		FROM `tincidents` 
		INNER JOIN tsubcat
		ON (tincidents.subcat=tsubcat.id )
		WHERE
		tincidents.technician LIKE '$_POST[tech]' AND
		criticality like '$_POST[criticality]' AND
		tincidents.category LIKE '$_POST[category]'  AND
		tincidents.type LIKE '$_POST[type]' AND
		tincidents.time_hope-tincidents.time > 0 AND
		tincidents.disable='0'  
		GROUP BY tsubcat.name
		ORDER BY time DESC
	";
	} else {
		$query1 = "
		SELECT tsubcat.name as subcat, tincidents.time/60 as time
		FROM `tincidents` 
		INNER JOIN tsubcat
		ON (tincidents.subcat=tsubcat.id )
		WHERE
		tincidents.technician LIKE '$_POST[tech]' AND
		tincidents.type LIKE '$_POST[type]' AND
		criticality like '$_POST[criticality]' AND
		tincidents.category LIKE '$_POST[category]'  AND
		tincidents.disable='0' AND
		tincidents.date_create LIKE '$_POST[year]-%' AND
		tincidents.date_create LIKE '%-$_POST[month]-%' AND
		tincidents.state='3' 
		GROUP BY tsubcat.name
		ORDER BY time DESC
	";
	}
} else {
	if (($_POST['year']==$current_year) && ($_POST['month']==$current_month))
	{
		$query1 = "
			SELECT tcategory.name as technicien, ((tincidents.time_hope-tincidents.time))/60 as time
			FROM `tincidents`
			INNER JOIN tcategory 
			ON (tincidents.category=tcategory.id ) 
			WHERE 
			tincidents.technician LIKE '$_POST[tech]' AND
			tincidents.type LIKE '$_POST[type]' AND
			criticality like '$_POST[criticality]' AND
			tincidents.category LIKE '$_POST[category]' AND
			tincidents.disable='0' AND
			tincidents.time_hope-tincidents.time > 0 AND
			(tincidents.state='1' OR tincidents.state='2' OR tincidents.state='6' )
			GROUP BY tcategory.name
			ORDER BY time DESC
			";
	} else {
			$query1 = "
			SELECT tcategory.name as technicien, tincidents.time/60 as time
			FROM `tincidents`
			INNER JOIN tcategory 
			ON (tincidents.category=tcategory.id ) 
			WHERE 
			tincidents.technician LIKE '$_POST[tech]' AND
			tincidents.type LIKE '$_POST[type]' AND
			tincidents.category LIKE '$_POST[category]' AND
			criticality like '$_POST[criticality]' AND
			tincidents.disable='0' AND
			tincidents.date_create LIKE '$_POST[year]-%' AND
			tincidents.date_create LIKE '%-$_POST[month]-%' AND
			tincidents.state='3' 
			GROUP BY tcategory.name
			ORDER BY time DESC
			";
	}
}

$query = mysql_query($query1);
while ($row=mysql_fetch_array($query)) 
{ 
    $data=round($row[1], 0);
	$name=substr($row[0],0,42);
	array_push($values, $data);
	array_push($xnom, $name);
} 
$container='container6';
include('./stat_pie.php');
echo "<div id=\"$container\"></div>";
if ($rparameters['debug']==1)echo $query1;
?>