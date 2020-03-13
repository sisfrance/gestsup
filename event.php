<?php
################################################################################
# @Name : event.php
# @Description : display popup event
# @Call : index.php
# @Parameters :  
# @Author : Flox
# @Create : 20/07/2011
# @Update : 10/10/2019
# @Version : 3.1.45 p1
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

if(!isset($_POST['addevent'])) $_POST['addevent'] = ''; 
if(!isset($_POST['addcalendar'])) $_POST['addcalendar'] = ''; 

$db_id=strip_tags($db->quote($_GET['id']));

if(!isset($_POST['event_date'])) $_POST['event_date'] = '';
if(!isset($_POST['event_hour'])) $_POST['event_hour'] = '';
if(!isset($_POST['event_direct'])) $_POST['event_direct'] = '';
if(!isset($_POST['calendar_date_start'])) $_POST['calendar_date_start'] = '';
if(!isset($_POST['calendar_hour_start'])) $_POST['calendar_hour_start'] = '';
if(!isset($_POST['calendar_date_end'])) $_POST['calendar_date_end'] = '';
if(!isset($_POST['calendar_hour_end'])) $_POST['calendar_hour_end'] = '';

//disable event
if ($_GET['event']!='' && $_GET['disable']==1)
{
	$qry=$db->prepare("UPDATE `tevents` SET `disable`='1' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['event']));
}
//display event
$qry=$db->prepare("SELECT id,date_start,incident FROM `tevents` WHERE technician=:technician and disable='0' and type='1'");
$qry->execute(array('technician' => $_SESSION['user_id'])); 
while($event=$qry->fetch()) 
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
			//get ticket data
			$qry=$db->prepare("SELECT `title` FROM `tincidents` WHERE id=:id");
			$qry->execute(array('id' => $event['incident']));
			$rticket=$qry->fetch();
			$qry->closeCursor();
			
			//send data to box
			$boxtitle="<i class='icon-bell red bigger-120'></i>  Rappel pour le ticket n°$event[incident]";
			$boxtext="
				<u>Titre:</u><br /> $rticket[title]
				<div class=\"space-4\"></div>				
			";
			$valid="Voir le ticket";
			$action1="document.location.href='./index.php?page=ticket&id=$event[incident]&hide=1'";
			$cancel="Accréditer";
			$action2="document.location.href='./index.php?page=dashboard&userid=$_GET[userid]&state=%25&event=$event[id]&disable=1'";
			include "./modalbox.php"; 
		}
	}
}
$qry->closeCursor();

//database inputs event if posted data
if($_GET['id'] && (($_POST['event_date'] && $_POST['event_hour']) || $_POST['event_direct']) && $rright['ticket_event'])
{
	//get ticket title
	$qry=$db->prepare("SELECT `id`,`title` FROM `tincidents` WHERE id=:id");
	$qry->execute(array('id' => $_GET['id']));
	$rticket=$qry->fetch();
	$qry->closeCursor();
		
	if($_POST['event_direct']!='') {$date=$_POST['event_direct'];} else {$date="$_POST[event_date] $_POST[event_hour]";}
	$qry=$db->prepare("INSERT INTO `tevents` (`technician`,`incident`,`date_start`,`type`,`title`,`classname`) VALUES (:technician,:incident,:date_start,'1',:title,'label-warning')");
	$qry->execute(array(
		'technician' => $_SESSION['user_id'],
		'incident' => $_GET['id'],
		'date_start' => $date,
		'title' => "Rappel ticket $rticket[id] : $rticket[title]"
		));
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
	
}

//database inputs calendar if posted data
if($_GET['id'] && $_POST['calendar_date_start'] && $_POST['calendar_hour_start'] && $_POST['calendar_date_end'] && $_POST['calendar_hour_end'] && $rright['ticket_calendar'])
{
	//get ticket title
	$qry=$db->prepare("SELECT `id`,`title` FROM `tincidents` WHERE id=:id");
	$qry->execute(array('id' => $_GET['id']));
	$rticket=$qry->fetch();
	$qry->closeCursor();
		
	$qry=$db->prepare("INSERT INTO `tevents` (`technician`,`incident`,`date_start`,`date_end`,`type`,`title`,`classname`) VALUES (:technician,:incident,:date_start,:date_end,'2',:title,'label-success')");
		$qry->execute(array(
			'technician' => $globalrow['technician'],
			'incident' => $_GET['id'],
			'date_start' => "$_POST[calendar_date_start] $_POST[calendar_hour_start]",
			'date_end' => "$_POST[calendar_date_end] $_POST[calendar_hour_end]",
			'title' => "Ticket $rticket[id] : $rticket[title]"
			));
		
		//redirect
		$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
	
}

//add new event
if(($_POST['addevent'] || $_POST['addcalendar']) && $_GET['id'])
{
	//calculate dates
	$date = date("Y-m-d H:i");
	$day= date('Y-m-d',strtotime("+1 day ", strtotime($date)));
	$afterday= date('Y-m-d',strtotime("+2 day ", strtotime($date)));
	$week= date('Y-m-d',strtotime("+7 day ", strtotime($date)));
	$month= date('Y-m-d',strtotime("+1 month ", strtotime($date)));
	$year= date('Y-m-d',strtotime("+1 year ", strtotime($date)));
	
	//display form
	if($_GET['id'])
	{
		if($_POST['addevent'])
		{
			$boxsize='height: 450,';
			$boxtitle='<i class=\'icon-bell-alt orange \'></i> '.T_('Ajouter un rappel');
			$boxtext='
			<form name="form" method="POST" action="" id="form">
				<label>'.T_('Date').':</label> 
				<div class="space-1"></div>
				<input autocomplete="off" type="text" name="event_date" id="event_date"  />
				<div class="space-4"></div>
				<label>'.T_('Heure').':</label> 
				<select id="event_hour" name="event_hour" autofocus="true" >
					<option value="00:00:00">00h00</option>
					<option value="01:00:00">01h00</option>
					<option value="02:00:00">02h00</option>
					<option value="04:00:00">03h00</option>
					<option value="05:00:00">05h00</option>
					<option value="06:00:00">06h00</option>
					<option value="07:00:00">07h00</option>
					<option selected value="08:00:00">08h00</option>
					<option value="09:00:00">09h00</option>
					<option value="10:00:00">10h00</option>
					<option value="11:00:00">11h00</option>
					<option value="12:00:00">12h00</option>
					<option value="13:00:00">13h00</option>
					<option value="14:00:00">14h00</option>
					<option value="15:00:00">15h00</option>
					<option value="16:00:00">16h00</option>
					<option value="17:00:00">17h00</option>
					<option value="18:00:00">18h00</option>
					<option value="19:00:00">19h00</option>
					<option value="20:00:00">20h00</option>
					<option value="21:00:00">21h00</option>
					<option value="22:00:00">22h00</option>
					<option value="23:00:00">23h00</option>
			</select>
			<hr />
			<input type="radio" name="event_direct" value="'.$day.' 08:00:00"> '.T_('Demain').' <br />
			<input type="radio" name="event_direct" value="'.$afterday.' 08:00:00"> '.T_('Après demain').' <br />
			<input type="radio" name="event_direct" value="'.$week.' 08:00:00"> '.T_('Dans une semaine').' <br />
			<input type="radio" name="event_direct" value="'.$month.' 08:00:00"> '.T_('Dans un mois').' <br />
			<input type="radio" name="event_direct" value="'.$year.' 08:00:00"> '.T_('Dans un an').'<br />
			';
		} elseif($_POST['addcalendar']) {
			$boxsize='height: 480,width: 350,';
			$boxtitle='<i class=\'icon-calendar\'></i> '.T_('Planifier une intervention');
			$boxtext='
			<form name="form" method="POST" action="" id="form">
				<label>'.T_('Début').' :</label> 
				
				<input size="8" autocomplete="off" type="text" name="calendar_date_start" id="calendar_date_start" />
				<select class="textfield" id="calendar_hour_start" name="calendar_hour_start" autofocus="true" >
					<option value="00:00:00">00h00</option>
					<option value="01:00:00">01h00</option>
					<option value="02:00:00">02h00</option>
					<option value="04:00:00">03h00</option>
					<option value="05:00:00">05h00</option>
					<option value="06:00:00">06h00</option>
					<option value="07:00:00">07h00</option>
					<option selected value="08:00:00">08h00</option>
					<option value="09:00:00">09h00</option>
					<option value="10:00:00">10h00</option>
					<option value="11:00:00">11h00</option>
					<option value="12:00:00">12h00</option>
					<option value="13:00:00">13h00</option>
					<option value="14:00:00">14h00</option>
					<option value="15:00:00">15h00</option>
					<option value="16:00:00">16h00</option>
					<option value="17:00:00">17h00</option>
					<option value="18:00:00">18h00</option>
					<option value="19:00:00">19h00</option>
					<option value="20:00:00">20h00</option>
					<option value="21:00:00">21h00</option>
					<option value="22:00:00">22h00</option>
					<option value="23:00:00">23h00</option>
				</select>
				<br />
				<br />
				<label>'.T_('Fin').' :</label> 
				<input size="8" autocomplete="off" type="text" name="calendar_date_end" id="calendar_date_end" />
				<select class="textfield" id="calendar_hour_end" name="calendar_hour_end" >
					<option value="00:00:00">00h00</option>
					<option value="01:00:00">01h00</option>
					<option value="02:00:00">02h00</option>
					<option value="04:00:00">03h00</option>
					<option value="05:00:00">05h00</option>
					<option value="06:00:00">06h00</option>
					<option value="07:00:00">07h00</option>
					<option selected value="08:00:00">08h00</option>
					<option value="09:00:00">09h00</option>
					<option value="10:00:00">10h00</option>
					<option value="11:00:00">11h00</option>
					<option value="12:00:00">12h00</option>
					<option value="13:00:00">13h00</option>
					<option value="14:00:00">14h00</option>
					<option value="15:00:00">15h00</option>
					<option value="16:00:00">16h00</option>
					<option value="17:00:00">17h00</option>
					<option value="18:00:00">18h00</option>
					<option value="19:00:00">19h00</option>
					<option value="20:00:00">20h00</option>
					<option value="21:00:00">21h00</option>
					<option value="22:00:00">22h00</option>
					<option value="23:00:00">23h00</option>
				</select>
				<br /><br /><br /><br /><br /><br />
				<br /><br /><br /><br /><br /><br />
				';
		}
		echo "	
		</form>
		";
	}
	$valid=T_('Ajouter');
	$action1="$('form#form').submit();";
	$cancel=T_('Annuler');
	//$action2="$( this ).dialog( \"close\" ); ";
	$action2="window.location = window.location.href;";
	include "./modalbox.php"; 
}
?>