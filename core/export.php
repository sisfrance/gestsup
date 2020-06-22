<?php
################################################################################
# @Name : ./core/export.php
# @Desc : dump csv files of current query
# @call : /dashboard.php
# @parameters : 
# @Autor : Flox
# @Create : 27/01/2014
# @Update : 28/01/2014
# @Version : 3.0.11
################################################################################

$daydate=date('Y-m-d');

// output headers so that the file is downloaded rather than displayed

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$daydate-GestSup-Export.csv\"");

require "../connect.php"; 

//load parameters table
$qparameters = mysql_query("SELECT * FROM `tparameters`"); 
$rparameters= mysql_fetch_array($qparameters);

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('Numero se ticket', 'Type', 'Technicien', 'Demandeur', 'Service', 'Createur', 'Categorie', 'Sous Categorie','Titre', 'Temps passe', 'Date de creation','Date de resolution estime', 'Date de cloture', 'Etat', 'Priorite', 'Criticite' ),";");

// fetch the data
$rows = mysql_query('SELECT id,type,technician,user,u_service,creator,category,subcat,title,time,date_create,date_hope,date_res,state,priority,criticality FROM tincidents WHERE disable=0');
//$rows = mysql_query('SELECT * FROM tincidents WHERE disable=0');
// loop over the rows, outputting them
while ($row = mysql_fetch_assoc($rows)) 
{
	//get data
	$querytech=mysql_query("SELECT firstname,lastname FROM tusers WHERE id LIKE '$row[technician]' "); 
	$resulttech=mysql_fetch_array($querytech);
	$row['technician']="$resulttech[firstname] $resulttech[lastname]";

	$querytype=mysql_query("SELECT name FROM ttypes WHERE id LIKE $row[type]"); 
	$resulttype=mysql_fetch_array($querytype);
	$row['type']=$resulttype['name'];
	
	$queryuser=mysql_query("SELECT firstname,lastname FROM tusers WHERE id LIKE '$row[user]'"); 
	$resultuser=mysql_fetch_array($queryuser);
	$row['user']="$resultuser[firstname] $resultuser[lastname]";
	
	$queryservice=mysql_query("SELECT name FROM tservices WHERE id LIKE '$row[u_service]'"); 
	$resultservice=mysql_fetch_array($queryservice);
	$row['u_service']="$resultservice[name]";
	
	$querycreator=mysql_query("SELECT firstname,lastname FROM tusers WHERE id LIKE '$row[creator]'"); 
	$resultcreator=mysql_fetch_array($querycreator);
	$row['creator']="$resultcreator[firstname] $resultcreator[lastname]";
	
	$querycat=mysql_query("SELECT * FROM tcategory WHERE id LIKE '$row[category]'"); 
	$resultcat=mysql_fetch_array($querycat);
	$row['category']=$resultcat['name'];
	
	$queryscat=mysql_query("SELECT * FROM tsubcat WHERE id LIKE '$row[subcat]'"); 
	$resultscat=mysql_fetch_array($queryscat);
	$row['subcat']=$resultscat['name'];
	
	$querystate=mysql_query("SELECT * FROM tstates WHERE id LIKE $row[state]"); 
	$resultstate=mysql_fetch_array($querystate);
	$row['state']=$resultstate['name'];

	$querypriority=mysql_query("SELECT * FROM tpriority WHERE number LIKE $row[priority]"); 
	$resultpriority=mysql_fetch_array($querypriority);
	$row['priority']=$resultpriority['name'];
	
	$querycriticality=mysql_query("SELECT * FROM tcriticality WHERE id LIKE $row[criticality]"); 
	$resultcriticality=mysql_fetch_array($querycriticality);
	$row['criticality']=$resultcriticality['name'];

  fputcsv($output, $row,";");
}

mysql_close($connexion); 

?>