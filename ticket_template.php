<?php
################################################################################
# @Name : ./ticket_template.php
# @Description : select and apply template ticket
# @Call : /core/ticket.php
# @Author : Flox
# @Update : 21/10/2014
# @Update : 21/03/2019
# @Version : 3.1.40
################################################################################

//initialize variables 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_POST['duplicate'])) $_POST['duplicate'] = ''; 
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($row['title'])) $row['title'] = '';
if(!isset($row['user'])) $row['user'] = '';
if(!isset($row['priority'])) $row['priority'] = '';
if(!isset($row['state'])) $row['state'] = '';
if(!isset($row['state'])) $row['state'] = '';
if(!isset($row['time'])) $row['time'] = '';
if(!isset($row['category'])) $row['category'] = '';
if(!isset($row['subcat'])) $row['subcat'] = '';
if(!isset($row['technician'])) $row['technician'] = '';
if(!isset($row['criticality'])) $row['criticality'] = '';
if(!isset($row['type'])) $row['type'] = '';

if($_POST['duplicate'] && $rright['ticket_template'])
{

	//get data from source ticket
	$qry=$db->prepare("SELECT * FROM `tincidents` WHERE id=:id");
	$qry->execute(array('id' => $_POST['template']));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	if ($_SESSION['profile_id']==2 || $_SESSION['profile_id']==1) //case for powerusers or users	 
	{
		$qry=$db->prepare("
		INSERT INTO `tincidents` 
		(
		`user`,
		`title`,
		`description`,
		`priority`,
		`state`,
		`time`,
		`category`,
		`subcat`,
		`date_create`,
		`technician`,
		`criticality`,
		`creator`,
		`place`,
		`type`
		) VALUES (
		:user,
		:title,
		:description,
		:priority,
		:state,
		:time,
		:category,
		:subcat,
		:date_create,
		:technician,
		:criticality,
		:creator,
		:place,
		:type
		)
		");
		$qry->execute(array(
			'user' => $_SESSION['user_id'],
			'title' => $row['title'],
			'description' => $row['description'],
			'priority' => $row['priority'],
			'state' => $row['state'],
			'time' => $row['time'],
			'category' => $row['category'],
			'subcat' => $row['subcat'],
			'date_create' => $datetime,
			'technician' => $row['technician'],
			'criticality' => $row['criticality'],
			'creator' => $_SESSION['user_id'],
			'place' => $row['place'],
			'type' => $row['type']
			));
	} else { //case for other profile
		$qry=$db->prepare("
		INSERT INTO `tincidents` 
		(
		`user`,
		`title`,
		`description`,
		`priority`,
		`state`,
		`time`,
		`category`,
		`subcat`,
		`date_create`,
		`technician`,
		`criticality`,
		`creator`,
		`place`,
		`type`
		) VALUES (
		:user,
		:title,
		:description,
		:priority,
		:state,
		:time,
		:category,
		:subcat,
		:date_create,
		:technician,
		:criticality,
		:creator,
		:place,
		:type
		)
		");
		$qry->execute(array(
			'user' => $row['user'],
			'title' => $row['title'],
			'description' => $row['description'],
			'priority' => $row['priority'],
			'state' => $row['state'],
			'time' => $row['time'],
			'category' => $row['category'],
			'subcat' => $row['subcat'],
			'date_create' => $datetime,
			'technician' => $row['technician'],
			'criticality' => $row['criticality'],
			'creator' => $_SESSION['user_id'],
			'place' => $row['place'],
			'type' => $row['type']
			));
	}
	//threads insert
	$newticketid=$db->lastInsertId(); //get id of created ticket
	//find threads of source ticket
	$qry=$db->prepare("SELECT `text`,`type` FROM `tthreads` WHERE ticket=:ticket");
	$qry->execute(array('ticket' => $_POST['template']));
	while($row=$qry->fetch()) 
	{
		//insert new threads
		$qry2=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`) VALUES (:ticket,:date,:author,:text,:type)");
		$qry2->execute(array(
			'ticket' => $newticketid,
			'date' => $datetime,
			'author' => $_SESSION['user_id'],
			'text' => $row['text'],
			'type' => $row['type']
	));
	}
	$qry->closeCursor();
	
	$boxtext= '<div class="alert alert-block alert-success"><center><i class="icon-ok green"></i>	'.T_('Le modèle a été appliqué au ticket en cours').'.</center></div>';
	echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]&state=$_GET[state]'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
	</SCRIPT>";
} elseif($rright['ticket_template']) {
	//display form
	$boxtext=' 
	<form name="form" method="POST" action="" id="form">
		<input name="duplicate" type="hidden" value="1">
		<label for="template">
		<select id="template" name="template">
			';
			$qry=$db->prepare("SELECT `incident`,`name` FROM `ttemplates` ORDER BY `name` ASC");
			$qry->execute(array('id' => $_GET['id']));
			while($row=$qry->fetch()) 
			{
				$boxtext=$boxtext.'<option value="'.$row['incident'].'">'.$row['name'].'</option>';
			}
			$qry->closeCursor();
			$boxtext=$boxtext.'
		</select>
	</form>
	';
}
$boxtitle="<i class='icon-tags blue bigger-120'></i>".T_('Liste des modèles');
$valid=T_('Utiliser');
$action1="$('form#form').submit();";
$cancel=T_('Fermer');
$action2="$( this ).dialog( \"close\" ); ";
include "./modalbox.php";
?>