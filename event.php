<?php
################################################################################
# @Name : event.php
# @Desc : display popup event
# @call : index.php
# @parameters :  
# @Author : Flox
# @Create : 20/07/2011
# @Update : 20/09/2013
# @Version : 3.0
################################################################################

//initialize variables 
if(!isset($date)) $date = ''; 
if(!isset($hour)) $hour = ''; 

if(!isset($_GET['disable'])) $_GET['disable'] = ''; 
if(!isset($_GET['event'])) $_GET['event'] = ''; 
if(!isset($_GET['hide'])) $_GET['hide'] = ''; 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_GET['technician'])) $_GET['technician'] = ''; 
if(!isset($_GET['planning'])) $_GET['planning'] = ''; 

if(!isset($_POST['direct'])) $_POST['direct'] = '';
if(!isset($_POST['date'])) $_POST['date'] = '';
if(!isset($_POST['hide'])) $_POST['hide'] = '';


//disable event
if ($_GET['event']!='' && $_GET['disable']==1)
{
	$query = "UPDATE tevents SET disable='1' where id='$_GET[event]'";
	$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
}
//display event
if($_GET['hide']!=1)
{
	$q = mysql_query("SELECT * FROM `tevents` WHERE technician LIKE '$_SESSION[user_id]' and disable='0' and type='1'"); 
	while ($event=mysql_fetch_array($q))
	{
		$devent=explode(" ",$event['date_start']);
		//day check
		if ($devent[0]<=$daydate) 
		{
			//hour check
			$currenthour=date("H:i:s");
			$eventhour=explode(" ",$event['date_start']);
			if ($currenthour>$eventhour[1])
			{
				//get ticketdata
				$qticket = mysql_query("SELECT * FROM tincidents WHERE id LIKE '$event[incident]'");
				$rticket=mysql_fetch_array($qticket); 
				$ticket_description=substr("$rticket[description]",0,150);
				
				//send data to box
				$boxtitle="<i class='icon-bell red bigger-120'></i>  Rappel pour le ticket n°$event[incident]";
				$boxtext="
					<u>Titre:</u><br /> $rticket[title]
					<div class=\"space-4\"></div>				
					<u>Description:</u><br /> $ticket_description ...
				";
				$valid="Voir le ticket";
				$action1="document.location.href='./index.php?page=ticket&id=$event[incident]&hide=1'";
				$cancel="Acrediter";
				$action2="document.location.href='./index.php?page=dashboard&&userid=$_GET[userid]&state=%&event=$event[id]&disable=1'";
				include "./modalbox.php"; 
			}
		}
	}
}
//add new events
if ($_GET['action']=='addevent' || $_GET['action']=='addcalendar')
{
	//database inputs
	if($_POST['date'] || $_POST['direct'])
	{
		if ($_GET['action']!='addcalendar')
		{
			if ($_POST['direct']!='') $date=$_POST['direct']; else  $date="$_POST[date] $_POST[hour]";
			$requete = "INSERT INTO tevents (technician,incident,date_start,type) VALUES ('$_SESSION[user_id]','$_GET[id]','$date','1')";
			$execution = mysql_query($requete);
			//redirect
			$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
		} else {
			$requete = "INSERT INTO tevents (technician,incident,date_start,date_end,type) VALUES ('$_SESSION[user_id]','$_GET[id]','$_POST[date] $_POST[hour]','$_POST[date_fin] $_POST[hour_fin]','2')";
			$execution = mysql_query($requete);
			//redirect
			$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
		}
	}
	//calc dates
	$date = date("Y-m-d H:i");
	$day= date('Y-m-d',strtotime("+1 day ", strtotime($date)));
	$afterday= date('Y-m-d',strtotime("+2 day ", strtotime($date)));
	$week= date('Y-m-d',strtotime("+7 day ", strtotime($date)));
	$month= date('Y-m-d',strtotime("+1 month ", strtotime($date)));
	$year= date('Y-m-d',strtotime("+1 year ", strtotime($date)));
	
	//display form
	if ($_GET['id']!="" && $_POST['hide']!=1)
	{
		if ($_GET['action']!='addcalendar')
		{
			$boxtitle="<i class='icon-bell-alt orange '></i> Ajouter un rappel";
			$boxtext="
			<form name=\"form\" method=\"POST\" action=\"\" id=\"form\">
				<label>Date:</label> 
				<input type=\"text\" name=\"date\" id=\"date\" />
				<input type=\"hidden\" name=\"hide\" id=\"hide\" value=\"1\"/>
				<div class=\"space-4\"></div>
				<label>Heure:</label> 
				<select id=\"hour\" name=\"hour\" >
					<option value=\"00:00:00\">00h00</option>
					<option value=\"01:00:00\">01h00</option>
					<option value=\"02:00:00\">02h00</option>
					<option value=\"04:00:00\">03h00</option>
					<option value=\"05:00:00\">05h00</option>
					<option value=\"06:00:00\">06h00</option>
					<option value=\"07:00:00\">07h00</option>
					<option selected value=\"08:00:00\">08h00</option>
					<option value=\"09:00:00\">09h00</option>
					<option value=\"10:00:00\">10h00</option>
					<option value=\"11:00:00\">11h00</option>
					<option value=\"12:00:00\">12h00</option>
					<option value=\"13:00:00\">13h00</option>
					<option value=\"14:00:00\">14h00</option>
					<option value=\"15:00:00\">15h00</option>
					<option value=\"16:00:00\">16h00</option>
					<option value=\"17:00:00\">17h00</option>
					<option value=\"18:00:00\">18h00</option>
					<option value=\"19:00:00\">19h00</option>
					<option value=\"20:00:00\">20h00</option>
					<option value=\"21:00:00\">21h00</option>
					<option value=\"22:00:00\">22h00</option>
					<option value=\"23:00:00\">23h00</option>
			</select>
			<hr />
			<input type=\"radio\" name=\"direct\" value=\"$day 08:00:00\"> Demain <br />
			<input type=\"radio\" name=\"direct\" value=\"$afterday 08:00:00\"> Après demain <br />
			<input type=\"radio\" name=\"direct\" value=\"$week 08:00:00\"> Dans une semaine <br />
			<input type=\"radio\" name=\"direct\" value=\"$month 08:00:00\"> Dans un mois <br />
			<input type=\"radio\" name=\"direct\" value=\"$year 08:00:00\"> Dans un an<br />
			";
		} else {
			$boxtitle="<i class='icon-calendar purple'></i> Planifier une intervention";
			$boxtext="
			<form name=\"form\" method=\"POST\" action=\"\" id=\"form\">
				<label>Date Début:</label> 
				<input type=\"text\" name=\"date\" id=\"date\" />
				<input type=\"hidden\" name=\"hide\" id=\"hide\" value=\"1\"/>
				<div class=\"space-4\"></div>
				<label>Heure Début:</label> 
				<select class=\"textfield\" id=\"hour\" name=\"hour\" >
					<option value=\"00:00:00\">00h00</option>
					<option value=\"01:00:00\">01h00</option>
					<option value=\"02:00:00\">02h00</option>
					<option value=\"04:00:00\">03h00</option>
					<option value=\"05:00:00\">05h00</option>
					<option value=\"06:00:00\">06h00</option>
					<option value=\"07:00:00\">07h00</option>
					<option selected value=\"08:00:00\">08h00</option>
					<option value=\"09:00:00\">09h00</option>
					<option value=\"10:00:00\">10h00</option>
					<option value=\"11:00:00\">11h00</option>
					<option value=\"12:00:00\">12h00</option>
					<option value=\"13:00:00\">13h00</option>
					<option value=\"14:00:00\">14h00</option>
					<option value=\"15:00:00\">15h00</option>
					<option value=\"16:00:00\">16h00</option>
					<option value=\"17:00:00\">17h00</option>
					<option value=\"18:00:00\">18h00</option>
					<option value=\"19:00:00\">19h00</option>
					<option value=\"20:00:00\">20h00</option>
					<option value=\"21:00:00\">21h00</option>
					<option value=\"22:00:00\">22h00</option>
					<option value=\"23:00:00\">23h00</option>
				</select>
				<hr />
				<label>Date de fin:</label> 
				<input type=\"text\" name=\"date_fin\" id=\"date_fin\" />
				<div class=\"space-4\"></div>
				<label>Heure de fin:</label> 
				<select class=\"textfield\" id=\"hour_fin\" name=\"hour_fin\" >
					<option value=\"00:00:00\">00h00</option>
					<option value=\"01:00:00\">01h00</option>
					<option value=\"02:00:00\">02h00</option>
					<option value=\"04:00:00\">03h00</option>
					<option value=\"05:00:00\">05h00</option>
					<option value=\"06:00:00\">06h00</option>
					<option value=\"07:00:00\">07h00</option>
					<option selected value=\"08:00:00\">08h00</option>
					<option value=\"09:00:00\">09h00</option>
					<option value=\"10:00:00\">10h00</option>
					<option value=\"11:00:00\">11h00</option>
					<option value=\"12:00:00\">12h00</option>
					<option value=\"13:00:00\">13h00</option>
					<option value=\"14:00:00\">14h00</option>
					<option value=\"15:00:00\">15h00</option>
					<option value=\"16:00:00\">16h00</option>
					<option value=\"17:00:00\">17h00</option>
					<option value=\"18:00:00\">18h00</option>
					<option value=\"19:00:00\">19h00</option>
					<option value=\"20:00:00\">20h00</option>
					<option value=\"21:00:00\">21h00</option>
					<option value=\"22:00:00\">22h00</option>
					<option value=\"23:00:00\">23h00</option>
				</select>
				";
		}
			echo "	
		</form>
		<script type=\"text/javascript\">
			window.jQuery || document.write(\"<script src='./template/assets/js/jquery-2.0.3.min.js'>\"+\"<\"+\"/script>\");
		</script>

		<script type=\"text/javascript\">
			jQuery(function($) {
				$.datepicker.setDefaults( $.datepicker.regional[ \"fr\" ] );
				$( \"#date\" ).datepicker({ 
					dateFormat: 'yy-mm-dd',
					minDate: 0
				});
				$( \"#date_fin\" ).datepicker({ 
					dateFormat: 'yy-mm-dd',
					minDate: 0
				});
			});	
		</script>	
		";
	}

	$valid="Ajouter";
	$action1="$('form#form').submit();";
	$cancel="Annuler";
	$action2="$( this ).dialog( \"close\" ); ";
	include "./modalbox.php"; 
}
?>