<?php
################################################################################
# @Name : ./ticket_template.php
# @Desc : select template incident
# @call : /core/ticket.php
# @Autor : Flox
# @Version : 3.0.11
# @create : 05/12/2014
################################################################################

// initialize variables 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_POST['duplicate'])) $_POST['duplicate'] = ''; 
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';

if($_POST['duplicate'])
{
	$query= mysql_query("SELECT * FROM `tincidents` WHERE id='$_POST[template]'");
	$row=mysql_fetch_array($query);
	//escape special char to sql query
	$row['description']=mysql_real_escape_string($row['description']);
	$row['title']=mysql_real_escape_string($row['title']);

	
	if ($_SESSION['profile_id']==2)
	{
		//case for powerusers
		$query= "
		INSERT INTO tincidents (
		user,title,description,priority,state,time,category,subcat,date_create,technician,criticality,creator,type
		) VALUES (
		'$row[user]','$row[title]','$row[description]','$row[priority]','$row[state]','$row[time]','$row[category]','$row[subcat]','$datetime','$_SESSION[user_id]','$row[criticality]','$_SESSION[user_id]','$row[type]'
		)
		";
	} else {
		//case for technician
		$query= "
		INSERT INTO tincidents (
		user,title,description,priority,state,time,category,subcat,date_create,technician,criticality,creator,type
		) VALUES (
		'$row[user]','$row[title]','$row[description]','$row[priority]','$row[state]','$row[time]','$row[category]','$row[subcat]','$datetime','$row[technician]','$row[criticality]','$_SESSION[user_id]','$row[type]'
		)
		";
	}
	
	$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
	
	////threads insert
	//find id of new ticket
	$query= mysql_query("SELECT MAX(id) FROM `tincidents`");
	$newticketid=mysql_fetch_array($query);
	//find tickets from source ticket
	$query= mysql_query("SELECT * FROM `tthreads` WHERE ticket='$_POST[template]'");
	while ($row=mysql_fetch_array($query)) {
		//escape special char to sql query
		$row['text']=mysql_real_escape_string($row['text']);
		//insert new threads
		$query2= "INSERT INTO tthreads (ticket,date,author,text,type) VALUES ('$newticketid[0]','$datetime','$_SESSION[user_id]','$row[text]','$row[type]')";
		$exec = mysql_query($query2);
	}
	$boxtext= '<div class="alert alert-block alert-success"><center><i class="icon-ok green"></i>	Le modèle à été appliqué au ticket en cours.</center></div>';
} else {
	//display form
	$boxtext=' 
	<form name="form" method="POST" action="" id="form">
		<input name="duplicate" type="hidden" value="1">
		<label for="template">
		<select id="template" name="template">
			';
			$query= mysql_query("SELECT * FROM `ttemplates` order by name ASC");
			while ($row=mysql_fetch_array($query)) {
				$boxtext=$boxtext.'<option value="'.$row['incident'].'">'.$row['name'].'</option>';
			} 
			$boxtext=$boxtext.'
		</select>
	</form>
	';
}
$boxtitle="<i class='icon-tags blue bigger-120'></i> Liste des modèles";
$valid="Utiliser";
$action1="$('form#form').submit();";
$cancel="Fermer";
$action2="$( this ).dialog( \"close\" ); ";
include "./modalbox.php";
?>