<?php
################################################################################
# @Name : pie_cat.php
# @Desc : Display Statistics of chart 2 number of tickets by catgories
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 25/11/2014
# @Version : 3.0.11
################################################################################

//array declaration
$values = array();
$xnom = array();

//display title
$libchart="Répartition du nombre de tickets par catégories";
if ($_POST['category']!="%")
{
	$query1 = "
	SELECT tsubcat.name as cat, COUNT(*) as nb
	FROM tincidents INNER JOIN tsubcat ON (tincidents.subcat=tsubcat.id)
	WHERE 
	tincidents.category LIKE '$_POST[category]' AND
	tincidents.disable='0' AND
	tincidents.type LIKE '$_POST[type]' AND
	criticality like '$_POST[criticality]' AND
	tincidents.date_create LIKE '%-$_POST[month]-%' AND
	tincidents.date_create LIKE '$_POST[year]-%' AND
	tincidents.technician LIKE '$_POST[tech]'
	GROUP BY tsubcat.name 
	ORDER BY nb
	DESC limit 0,10
	";
}
else 
{
	$query1 = "
		SELECT tcategory.name as cat, COUNT(*) as nb
		FROM tincidents INNER JOIN tcategory ON (tincidents.category=tcategory.id)
		WHERE 
		tincidents.disable='0' AND
    	tincidents.type LIKE '$_POST[type]' AND
    	criticality like '$_POST[criticality]' AND
    	tincidents.date_create LIKE '%-$_POST[month]-%' AND
    	tincidents.date_create LIKE '$_POST[year]-%' AND
    	tincidents.technician LIKE '$_POST[tech]'
		GROUP BY tcategory.name 
		ORDER BY nb
		DESC limit 0,10";
}
$query = mysql_query($query1);
while ($row=mysql_fetch_array($query)) 
{
	$name=substr($row[0],0,35);
	$name=str_replace("'","\'",$name); 
	array_push($values, $row[1]);
	array_push($xnom, $name);
} 
$container='container4';
include('./stat_pie.php');
echo "<div id=\"$container\"></div>"; 
if ($rparameters['debug']==1)echo $query1;
?>