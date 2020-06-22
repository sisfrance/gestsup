<?php
session_start();
require "connect.php";
	
	mysql_query("SET NAMES 'utf8'");
	setlocale(LC_TIME, "fr_FR");

	//initialize variables 
	if(!isset($userreg)) $userreg = ''; 
	if(!isset($u_group)) $u_group = ''; 
	if(!isset($globalrow['u_group'])) $globalrow['u_group'] = ''; 
	if(!isset($_POST['user'])) $_POST['user'] = ''; 
	if(!isset($_POST['technician'])) $_POST['technician'] = '';
	if(!isset($_GET['action'])) $_GET['action'] = '';
	
	if (!isset($_SESSION['profile_id'])) {
		if($_GET['action'] != "print") {
			header('Location: index.php?action=print&id='.$_GET['id']);
		}
	} else {
	//master query
	$globalquery = mysql_query("
		SELECT *, tincidents.user AS tid, tincidents.id AS tinid
		FROM tincidents
		INNER JOIN tusers ON tusers.id = tincidents.user
		WHERE tincidents.id LIKE '$_GET[id]'
	");
	$globalrow=mysql_fetch_array($globalquery);
	if($_SESSION['user_id'] == $globalrow['tid'] OR $_SESSION['rank'] <= 3){
	
	if($_GET['id'] != $globalrow['tinid']) {
		echo "<h1>Ce ticket n'existe pas !</h1>";
		die();
	};
		
	//echo 'Impression du ticket n°'.$_GET['id'].':  '.$globalrow['title'].'';
	?>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Impression Ticket n°<?php echo $_GET['id']; ?></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<style>
		p {
			font-size: 14px;
		}
	</style>
	</head>
	<body onload="window.print();">
	<div class="container">
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<img style="position:absolute;top:0;" height="50px" src="Logo.jpg">
				<p style="font-size:10px" class="text-right"><b>Date d'ouverture:&nbsp;</b>
					<?php
						if ($globalrow['date_create']!='0000-00-00')
						{
							$resultdatecreate = substr($globalrow['date_create'], 0, 10);
							echo $resultdatecreate;
						}
					?>	
				</p>
				<p style="font-size:10px" class="text-right"><b>Date de fermeture:&nbsp;</b>
					<?php
						if ($globalrow['date_res']!='0000-00-00')
						{
							$resultdatecres = substr($globalrow['date_res'], 0, 10);
							echo $resultdatecres;
						}
					?>	
				</p>
				<p style="font-size:10px" class="text-right"><b>Temps passé:&nbsp;</b>
					<?php

 function convertiii( $lesMinutes )
 
 { 
 
     $heures = floor( $lesMinutes / 60 );
 
     $minutes = $lesMinutes % 60 ;

     if(empty($minutes)) {

     	return( $heures . "H");

     } else {

     	return( $heures . "H " . $minutes . "mn" );

     }
 
 }

echo convertiii($globalrow['time']);

/*						$timeeed = $globalrow['time']*60;
						echo 'time : '.var_dump($timeeed)."\n";
						echo 'grow : '.var_dump($globalrow['time'])."\n";
						echo strftime( "&nbsp;&nbsp;&nbsp;&nbsp;%HH %Mm", $timeeed);
						unset($timeeed);*/
					?>
				</p>
			</div>
			<div class="col-md-1"></div>
		</div>
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<h4>Client <small>(ticket n°<?php echo $_GET['id']; ?>)</small></h4>
				<p>Nom:
					<?php
						if (($globalrow['u_group']==0 && $u_group=='') || $_POST['user']!="")
						{
							if ($_POST['user'])	{$user=$_POST['user'];}	else {$user=$globalrow['user'];}
							$query = mysql_query("SELECT * FROM tusers WHERE id LIKE $user");
							$row = mysql_fetch_array($query);
							echo "$row[lastname] $row[firstname]";
						} else {
							if (($globalrow['u_group']!=$u_group) && $u_group!=''){$group=$u_group;}else {$group=$globalrow['u_group'];}
							$query = mysql_query("SELECT * FROM `tgroups` WHERE id=$group");
							$row = mysql_fetch_array($query);
							echo "[G] $row[name]";
						}
					?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Société:
					<?php
						$query = mysql_query("
							SELECT c.name
							FROM tincidents i
							INNER JOIN tusers u ON u.id = i.user
							INNER JOIN tcompany c ON c.id = u.company
							WHERE u.lastname='".$row[lastname] ."' AND u.firstname='".$row[firstname]."'
							GROUP BY c.name
						");
						$row = mysql_fetch_array($query);
						echo $row['name'];
					?>
				</p>
				<p>Adresse:
					<?php
						if (($globalrow['u_group']==0 && $u_group=='') || $_POST['user']!="")
						{
							if ($_POST['user'])	{$user=$_POST['user'];}	else {$user=$globalrow['user'];}
							$query = mysql_query("
								SELECT *, tcompany.zip AS tczip, tcompany.city AS tccity, tcompany.address AS tcaddress
								FROM tcompany
								INNER JOIN tusers ON tusers.company = tcompany.id
								WHERE tusers.id LIKE $user
							");
							$row = mysql_fetch_array($query);
							echo "$row[tcaddress] - $row[tczip] - $row[tccity]";
						} else {
							if (($globalrow['u_group']!=$u_group) && $u_group!=''){$group=$u_group;}else {$group=$globalrow['u_group'];}
							$query = mysql_query("SELECT * FROM `tgroups` WHERE id=$group");
							$row = mysql_fetch_array($query);
						}
					?>
				</p>
				<!--<p>Tél.:
					<?php /*
						if (($globalrow['u_group']==0 && $u_group=='') || $_POST['user']!="")
						{
							if ($_POST['user'])	{$user=$_POST['user'];}	else {$user=$globalrow['user'];}
							$query = mysql_query("SELECT * FROM tusers WHERE id LIKE $user");
							$row = mysql_fetch_array($query);
							echo "$row[phone]";
						} else {
							if (($globalrow['u_group']!=$u_group) && $u_group!=''){$group=$u_group;}else {$group=$globalrow['u_group'];}
							$query = mysql_query("SELECT * FROM `tgroups` WHERE id=$group");
							$row = mysql_fetch_array($query);
						}
					*/ ?>
				</p>-->
			</div>
			<div class="col-md-1"></div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<h4>Intervention</h4>
				<p>Technicien:
					<?php
					//selected value
					if ($globalrow['t_group']!=0)
					{
						$query = mysql_query("SELECT * FROM `tgroups` WHERE id=$globalrow[t_group]");
						$row = mysql_fetch_array($query);
						echo "[G] $row[name]";
					} else {
						if ($_POST['technician'])
						{
							$querytech = mysql_query("SELECT * FROM `tusers` WHERE disable='0' and id LIKE '$_POST[technician]' ");
						} else {
							$querytech = mysql_query("SELECT * FROM `tusers` WHERE disable='0' and id LIKE '$globalrow[technician]' ");		
						}
						$row=mysql_fetch_array($querytech);
						$resultlastfirst = substr($row[firstname], 0, 1).'&nbsp;'.substr($row[lastname], 0, 1).'';
						echo $resultlastfirst;
					}
					?>
				</p>
				<p>Nature de l'intervention:
					<?php echo $globalrow['title']; ?>
				</p>
				<p>Catégorie:&nbsp;
					<?php
						$query= mysql_query("SELECT * FROM `tcategory` WHERE id=$globalrow[category] ");
						$row=mysql_fetch_array($query);
						echo "$row[name]";	
						$query= mysql_query("SELECT * FROM `tsubcat` WHERE id=$globalrow[subcat] ");
						$row=mysql_fetch_array($query);
						echo "&nbsp;$row[name]";	
					?>		
				</p>
				<p><b>Numéro de série:</b> <?php echo $globalrow['serialnumber']; ?>
				</p>
				<p>Temps de trajet aller/retour: <?php echo $globalrow['timetravel']; ?>
				</p>
				<p></p>
			</div>
			<div class="col-md-1"></div>
		</div>
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<p><u><b>Description du problème:</u></b><br><br>
					<?php echo strip_tags($globalrow['description']); ?>
				</p>
				<br>
				<p><b><u>Détail de l'intervention et pièces remplacés:</u></b><br><br>
					<?php
						$query = mysql_query("SELECT * FROM tthreads WHERE ticket='$_GET[id]' and type='0' ORDER BY date");
						while ($row=mysql_fetch_array($query)) {
							echo strip_tags($row[text]);
						}
					?>
				</p>
				<br>
				<p>Garantie: <?php echo $globalrow['warranty']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contrat de maintenance: <?php echo $globalrow['mntcontract']; ?>
				</p>
				<!--<p>Date de résolution:&nbsp;
					<?php/*
						if ($globalrow['date_res']!='0000-00-00 00:00:00')
						{
							echo $globalrow['date_res'];
						}
					*/ ?>	
				</p>-->
			</div>
			<div class="col-md-1"></div>
		</div>
		<div style="position:absolute;bottom:0;width:100%;" class="row">
			<hr>
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<h5 class="text-center">Bon pour accord de fin d'intervention&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pour le Client "Lu et approuvé"</h5>
				<div style="
					border-width: 1px;
					border-style: solid;
					border-color: black;
					height: 50px;
					margin-left: 100px;
					margin-right: 100px;
				">
				</div>
				<br>
				<p class="text-center" style="font-size:8px;">JCD54 - 6 allée Pelletier Doisy - 56000 - Villers-lès-Nancy - France - Tél.: 03.83.61.44.77 - Fax: 03.83.44.16.32 - E-mail: support@jcd54.fr</p>
			</div>
			<div class="col-md-1"></div>
		</div>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	</div>
	</body>
	</html>
<?php
//mysql_close($connexion);
} else {
		echo "<h1>Ce ticket ne vous appartient pas!</h1>";
		die();
} // end right
} // end login
?>