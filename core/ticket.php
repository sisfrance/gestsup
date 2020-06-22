<?php
################################################################################
# @Name : ./core/ticket.php 
# @Desc : actions page for ticket
# @call : ./ticket.php
# @Autor : Flox
# @Version : 3.0.11
# @Create : 28/10/2013
# @Update : 23/05/2015
################################################################################

//initialize variable

if(!isset($u_group)) $u_group = '';

if(!isset($_POST['close'])) $_POST['close'] = '';
if(!isset($_POST['text'])) $_POST['text'] = '';
if(!isset($_POST['send'])) $_POST['send'] = '';
if(!isset($_POST['action'])) $_POST['action'] = '';
if(!isset($_POST['edituser'])) $_POST['edituser'] = '';
if(!isset($_POST['start_availability'])) $_POST['start_availability'] = '';
if(!isset($_POST['end_availability'])) $_POST['end_availability'] = '';
if(!isset($_POST['availability_planned'])) $_POST['availability_planned'] = '';

if(!isset($start_availability)) $start_availability = '';
if(!isset($end_availability)) $end_availability = '';
if(!isset($error)) $error="0";

//display user modalbox
if($_GET['action']=='adduser' || $_GET['action']=='edituser') include('./ticket_useradd.php');
//display category modalbox
if($_GET['action']=='addcat' || $_GET['action']=='editcat') include('./ticket_catadd.php');
//display template modalbox
if($_GET['action']=='template') include('./ticket_template.php');

//find incident number for new ticket
if($_GET['action']=='new')
{
	$query = mysql_query("SELECT MAX(id) FROM tincidents");
	$row=mysql_fetch_array($query);
	$_GET['id'] =$row[0]+1;
}

//action delete ticket
if ($_GET['action']=="delete") 
{
	//disable ticket
	$query = 'UPDATE tincidents SET disable=1 WHERE id=\''.$_GET['id'].'\'';
	$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	//display delete message
	echo '<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i>	Ticket supprimé.</center></div>';
	//redirect
	echo "<SCRIPT LANGUAGE='JavaScript'>
				<!--
				function redirect()
				{
				window.location='./index.php?page=dashboard&state=$_GET[state]&userid=$_GET[userid]'
				}
				setTimeout('redirect()',$rparameters[time_display_msg]);
				-->
		</SCRIPT>";
}

//master query
$globalquery = mysql_query("SELECT * FROM tincidents WHERE id LIKE '$_GET[id]'");
$globalrow=mysql_fetch_array($globalquery); 

//if change criticality go to down page for availabity
if($_POST['criticality']==$rparameters['availability_condition_value']){
    $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."#down"; 
    //redirect
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$url.'");
	-->
	</script>';
}

//if change criticality go to down page for availabity
if($_POST['criticality'] || $_POST['priority'] || $_POST['date_hope']){
    $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."#down"; 
    //redirect
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$url.'");
	-->
	</script>';
}

//user group or tech group detection switch values
if(substr($_POST['user'], 0, 1) =='G')
{
 	$u_group = explode("_", $_POST['user']);

    $_POST['user']='';

	$u_group=$u_group[1];



} elseif ($globalrow['u_group']!= 0 && $_POST['user']=='')
{
	$u_group=$globalrow['u_group'];
	$_POST['user']='';
}

if(substr($_POST['technician'], 0, 1) =='G')
{
 	$t_group = explode("_", $_POST['technician']);
	$t_group=$t_group[1];
	$_POST['technician']='';
} elseif ($globalrow['t_group']!=0 && $_POST['technician']=='')
{
	$t_group=$globalrow['t_group'];
	$_POST['technician']='';
} 
//send mail to admin if it's enable in parameters


if($_POST['send']) 
{
	if ($rparameters['mail_newticket']==1)
	{
		//find username
		$userquery = mysql_query("SELECT * FROM tusers WHERE id='$uid'");
		$userrow=mysql_fetch_array($userquery);	
		
		////mail parameters
		if($rparameters['mail_from_adr']=='')
		{
			if ($userrow['mail']!='') $from=$userrow['mail']; else $from=$rparameters['mail_cc'];
		} else {
			$from=$rparameters['mail_from_adr'];
		}
		
		$to=$rparameters['mail_newticket_address'];
		$object="Un nouvel incident à été déclaré par $userrow[lastname] $userrow[firstname]: $_POST[title]";
		$message = "
		L'incident n°$_GET[id] à été déclaré par l'utilisateur $userrow[lastname] $userrow[firstname].<br />
		<br />
		<u>Objet:</u><br />
		$_POST[title]<br />		
		<br />	
		<u>Description:</u><br />
		$_POST[text]<br />
		<br />
		Pour plus d'informations vous pouvez consulter le ticket sur <a href=\"$rparameters[server_url]/index.php?page=ticket&id=$_GET[id]\">$rparameters[server_url]/index.php?page=ticket&id=$_GET[id]</a>. ";
		require('./core/message.php');
	} 
}

//database inputs if submit
if($_POST['modify']||$_POST['quit']||$_POST['mail']||$_POST['upload']||$save=="1"||$_POST['send']||$_POST['action']) 
{
	//escape special char in sql query 
	$_POST['description'] = mysql_real_escape_string($_POST['text']);
	$_POST['resolution'] = mysql_real_escape_string($_POST['text2']);
	$_POST['title'] = mysql_real_escape_string($_POST['title']);
	
	//remove <br><br><br> generate to space display 
	$_POST['description']=str_replace("<br><br><br>","","$_POST[description]");
	$_POST['resolution']=str_replace("<br><br><br>","","$_POST[resolution]");
	
	//remove xml tag if detect (resolv problem to past from word to firefox)
	$detect1 = stripos($_POST['description'], '<!--[if gte mso 9]>');
	if ($detect1 !== false) {$_POST['description']=strip_tags($_POST['description']);}
	$detect2 = stripos($_POST['resolution'], '<!--[if gte mso 9]>');
	if ($detect2 !== false) {$_POST['resolution']=strip_tags($_POST['resolution']);}
	
	//check mandatory fields
    if($rright['ticket_date_hope_mandatory']!=0) {if($_POST['date_hope']=='' && ($_SESSION['user_id']==$_POST['technician'])) {$error="Date de résolution estimé vide.";} elseif ($error=='0') {$error='0';}} else {$error='0';}
    if($rright['ticket_priority_mandatory']!=0) {if($_POST['priority']=='') {$error="La priorité est vide.";} elseif ($error=='0') {$error='0';}} else {$error='0';}
    if($rright['ticket_criticality_mandatory']!=0) {if($_POST['criticality']=='') {$error="La criticité vide.";} elseif ($error=='0') {$error='0';}} else {$error='0';}

	//merge hour and date from availability part
	if ($_POST['start_availability_d'])
	{
	    $start_availability = DateTime::createFromFormat('d/m/Y', $_POST['start_availability_d']);
	    $start_availability = $start_availability->format('Y-m-d');
	    $start_availability="$start_availability $_POST[start_availability_h]";
	    $end_availability = DateTime::createFromFormat('d/m/Y', $_POST['end_availability_d']);
	    $end_availability = $end_availability->format('Y-m-d');
	    $end_availability="$end_availability $_POST[end_availability_h]";
	}
	////Generate Thread to technician and technician group transfert
		//detect tech group change to group
		if ($t_group!=$globalrow['t_group'] && $globalrow['technician']==0 && $t_group!='' && $globalrow['t_group']!=0 && $error=='0') {
			$query = "INSERT INTO tthreads (ticket,date,author,text,type,group1,group2) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', '',2,'$globalrow[t_group]','$t_group')";
			$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		}
		//detect tech change to tech
		if ($_POST['technician']!=$globalrow['technician'] && $globalrow['technician']!=0 && $_POST['technician']!='' && $error=='0') {
			$query = "INSERT INTO tthreads (ticket,date,author,text,type,tech1,tech2) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', '',2,'$globalrow[technician]','$_POST[technician]')";
			$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		}
		//detect techgroup change to tech
		if ($globalrow['t_group']!=0 && $_POST['technician'] && $error=='0') {
			$query = "INSERT INTO tthreads (ticket,date,author,text,type,group1,tech2) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', '',2,'$globalrow[t_group]','$_POST[technician]')";
			$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		}
		//detect tech change to techgroup
		if ($globalrow['technician']!=0 && $t_group && $error=='0') {
			$query = "INSERT INTO tthreads (ticket,date,author,text,type,tech1,group2) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', '',2,'$globalrow[technician]','$t_group')";
			$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		}
	////Generate Thread to technician and technician group attribution
		//detect technician attribution
		if ($globalrow['technician']==0 && $_POST['technician']!='' && $globalrow['t_group']==0 && $globalrow['creator']!=$_SESSION['user_id'] && $error=='0')
		{
				$query = "INSERT INTO tthreads (ticket,date,author,type,tech1) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', 1, '$_POST[technician]')";
				$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		}
		//detect group attribution
		if ($globalrow['t_group']==0 && $t_group!='' && $globalrow['technician']==0 && $error=='0')
		{
				$query = "INSERT INTO tthreads (ticket,date,author,type,group1) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', 1, '$t_group')";
				$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		}
	
	//auto modify state from 5 to 1 if technician change
    if (($_POST['technician']!=''&& $globalrow['state']=='5' && $_POST['state']!='3' ) || ($t_group!='' && $globalrow['state']=='5' && $_POST['state']!='3')) $_POST['state']='1';	
	
	//insert resolution date if state is change to resolve (3)
	if ($_POST['state']=='3' && $globalrow['state']!='3' && ($_POST['date_res']=='' || $_POST['date_res']=='0000-00-00 00:00:00')) $_POST['date_res']=date("Y-m-d H:i:s");
	
	//unread ticket if another technician add thread
	if (($_POST['resolution']!='') && ($globalrow['technician']!=$_SESSION['user_id'])) $techread=0; 
	
	//auto-attribute ticket to technician if user attachment is detected
	if ($_POST['user'])
	{
    	$query = mysql_query('SELECT * FROM `tusers_tech` WHERE user='.$_POST['user'].'');
        $row=mysql_fetch_array($query);
        if($row['tech']!='') $_POST['technician']=$row['tech'];
	}
	
	//get user service to insert in tinicidents table
	if($_POST['user'])
	{
    	$query = mysql_query('SELECT service FROM `tusers` WHERE id='.$_POST['user'].' ');
        $row=mysql_fetch_array($query);
        if($_POST['state']!=3) $u_service=$row[0]; 
	} else $u_service=$globalrow['u_service'];
	
    
	//SQL queries
	if (($_GET['action']=='new') && ($error=="0"))
	{
		//modify read state
		if($globalrow['technician']!=$_SESSION['user_id']) $techread=0; //unread ticket case when creator is not techncian  
		if($_POST['technician']==$_SESSION['user_id']) $techread=1; //read ticket



        // Recuperation du groupe d'utilisateur lors de l'insert ticket //
        $stugroup= "SELECT `group` FROM tgroups_assoc WHERE user='$uid'";
        $qugroup= mysql_query($stugroup) or die('Erreur TEST SQL !'.$stugroup.'<br /><br />'.mysql_error());
        $u_group = mysql_fetch_assoc($qugroup)["group"];


       //insert ticket
		$query= "INSERT INTO tincidents (user,type,u_group,u_service,technician,t_group,title,description,serialnumber,materiel,timetravel,warranty,mntcontract,stateimpr,date_create,date_hope,date_res,priority,criticality,state,creator,time,time_hope,category,subcat,techread,place,start_availability,end_availability,availability_planned) VALUES ('$_POST[user]','$_POST[type]','$u_group','$u_service','$_POST[technician]','$t_group','$_POST[title]','$_POST[description]','$_POST[serialnumber]','$_POST[materiel]','$_POST[timetravel]','$_POST[warranty]','$_POST[mntcontract]','$_POST[stateimpr]','$_POST[date_create]','$_POST[date_hope]','$_POST[date_res]','$_POST[priority]','$_POST[criticality]','$_POST[state]','$_SESSION[user_id]','$_POST[time]','$_POST[time_hope]','$_POST[category]','$_POST[subcat]','$techread','$_POST[ticket_places]','$start_availability','$end_availability','$_POST[availability_planned]')";
		$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	    if ($rparameters['debug']==1) {echo $query;}
	} elseif ($error=="0")  {


		//modify read state
		if($_POST['technician']==$_SESSION['user_id']) $techread=1; //read ticket  
		if($globalrow['technician']=='') $techread=1; //read ticket case when it's an unassigned ticket.
		//update ticket
		$query = "UPDATE tincidents SET 
		user='$_POST[user]',
		type='$_POST[type]',
		u_group='$u_group',
		u_service='$',
		u_service='$u_service',
		technician='$_POST[technician]',
		t_group='$t_group',
		title='$_POST[title]',
		description='$_POST[description]',
        serialnumber='$_POST[serialnumber]',
		materiel='$_POST[materiel]',
        timetravel='$_POST[timetravel]',
        warranty='$_POST[warranty]',
        mntcontract='$_POST[mntcontract]',
        stateimpr='$_POST[stateimpr]',
		date_create='$_POST[date_create]',
		date_hope='$_POST[date_hope]',
		date_res='$_POST[date_res]',
		priority='$_POST[priority]',
		criticality='$_POST[criticality]',
		state='$_POST[state]',
		time='$_POST[time]',
		time_hope='$_POST[time_hope]',
		category='$_POST[category]',
		subcat='$_POST[subcat]',
		techread='$techread',
		place='$_POST[ticket_places]',
		start_availability='$start_availability',
		end_availability='$end_availability',
		availability_planned='$_POST[availability_planned]'
		WHERE
		id LIKE '$_GET[id]'";
		if ($rparameters['debug']==1) {echo $query;}
		$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	}
	
	//threads text updates
	if($_POST['resolution']!='' && $_POST['resolution']!='<br><br><br>')
	{
		if($_GET['threadedit'])
		{
			//update thread
			$query = "UPDATE tthreads SET text='$_POST[resolution]' WHERE id='$_GET[threadedit]'";
			$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		} else {
			//generate new thread for this ticket
			$query = "INSERT INTO tthreads (ticket,date,author,text,type) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', '$_POST[resolution]',0)";
			$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		}
		//bug with special with redirect
		//$down="#down";
	}

	
	//threads insert close state
	if($_POST['state']=='3' && $globalrow['state']!='3')
	{
		$query = "INSERT INTO tthreads (ticket,date,author,type) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', 4)";
		$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	}
	//uploading files
	include "./core/upload.php";
	
	//auto send mail
	if(($rparameters['mail_auto']==1) && ($_POST['upload']=='')  && ($_POST['modify'] || $_POST['quit']) ){include('./core/auto_mail.php');}
	
	//display message
	if($error=="0")
	{
	    echo '<div class="alert alert-block alert-success"><center><i class="icon-ok green"></i>	Ticket sauvegardé. </center></div>';
	} else {
	    // new page ticket redirect
        echo '<div class="alert alert-danger"><i class="icon-remove"></i> <strong>Erreur:</strong> '.$error.' </div>';
	}
	//intialize variable for next thread
	$_POST['text2']='';

	//redirect for quit case
	if ($_POST['quit'] || $_POST['send'])
	{
		//redirect
		$www = "./index.php?page=dashboard&userid=$_GET[userid]&state=$_GET[state]&category=$_GET[category]&subcat=$_GET[subcat]&viewid=$_GET[viewid]";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		-->
		</script>';
	}
	
	//send mail
	if($_POST['mail'])
	{
		//redirect
		$www = "./index.php?page=preview_mail&id=$_GET[id]&userid=$_GET[userid]&state=$_GET[state]&category=$_GET[category]&subcat=$_GET[subcat]&viewid=$_GET[viewid]";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
	}
	
    if($error=="0")
    {
	//global redirect
    echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
		    	window.location='./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]&state=$_GET[state]&category=$_GET[category]&subcat=$_GET[subcat]&viewid=$_GET[viewid]&action=$_POST[action]&edituser=$_POST[edituser]&cat=$_POST[category]&editcat=$_POST[subcat]$down'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
		</SCRIPT>";
    }		
}
if($_POST['close']) 
{
	//update tincidents
	$query = "UPDATE tincidents SET state='3', techread='0' WHERE id='$_GET[id]'";
	$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	//update thread
	$query = "INSERT INTO tthreads (ticket, date, type, author) VALUES ('$_GET[id]','$datetime','4','$_SESSION[user_id]')";
	$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
}
if($_POST['cancel']) 
{
echo '<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i>	Annulation pas de modification.</center></div>';
echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='./index.php?page=dashboard&userid=$_GET[userid]&state=$_GET[state]'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
}
  
//unread ticket technician
if (($globalrow['techread']=="0")&&($globalrow['technician']==$_SESSION['user_id'])) 
{
	$query = "UPDATE tincidents SET techread='1' WHERE id='$_GET[id]'";
	$exec = mysql_query($query);
}
//find previous and next ticket
$query = mysql_query("SELECT MIN(id) FROM tincidents WHERE id > '$_GET[id]' AND id IN (SELECT id FROM tincidents WHERE technician='$_SESSION[user_id]' AND state='$globalrow[state]' AND id not like '$_GET[id]')");
$next = mysql_fetch_array($query);
$query = mysql_query("SELECT MAX(id) FROM tincidents WHERE id < '$_GET[id]' AND id IN (SELECT id FROM tincidents WHERE technician='$_SESSION[user_id]' AND state='$globalrow[state]' AND id not like '$_GET[id]')");
$previous = mysql_fetch_array($query);

//percentage calc of ticket resolution
if ($globalrow['time_hope']!=0)
{
	$percentage=($globalrow['time']*100)/$globalrow['time_hope'];
	$percentage=round($percentage);
	if (($globalrow['time']!='1') && ($globalrow['time_hope']!='1') && ($globalrow['time_hope']>=$globalrow['time'])) $percentage="($percentage%)"; else $percentage='';
}

//cut title for long case
$nbtitle=strlen($globalrow['title']);
if ($nbtitle>50)
{
	$title=substr($globalrow['title'], 0, 50);
	$title="$title...";
} else $title=$globalrow['title'];

?>