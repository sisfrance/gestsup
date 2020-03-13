<?php
################################################################################
# @Name : ./core/ticket.php 
# @Description : actions page for tickets
# @Call : ./ticket.php
# @Author : Flox
# @Create : 28/10/2013
# @Update : 03/10/2019
# @Version : 3.1.45 p1
################################################################################

//initialize variable
if(!isset($_POST['close'])) $_POST['close'] = '';
if(!isset($_POST['text'])) $_POST['text'] = '';
if(!isset($_POST['send'])) $_POST['send'] = '';
if(!isset($_POST['action'])) $_POST['action'] = '';
if(!isset($_POST['edituser'])) $_POST['edituser'] = '';
if(!isset($_POST['editcat'])) $_POST['editcat'] = '';
if(!isset($_POST['start_availability'])) $_POST['start_availability'] = '';
if(!isset($_POST['end_availability'])) $_POST['end_availability'] = '';
if(!isset($_POST['availability_planned'])) $_POST['availability_planned'] = '';
if(!isset($_POST['u_agency'])) $_POST['u_agency'] = '';

$db_action=strip_tags($db->quote($_GET['action']));

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
	$qry=$db->prepare("SELECT MAX(`auto_increment`) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA =:database AND table_name = 'tincidents';");
	$qry->execute(array('database' => $db_name));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	$_GET['id']=$row[0];
	$db_id=$row[0];
}

//action delete ticket
if (($_GET['action']=="delete") && ($rright['ticket_delete']!=0) && $_GET['id'])
{
	$qry=$db->prepare("DELETE FROM `tincidents` WHERE id=:id"); //delete ticket
	$qry->execute(array('id' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `tevents` WHERE incident=:incident"); //delete associate events
	$qry->execute(array('incident' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `tthreads` WHERE ticket=:ticket"); //delete threads
	$qry->execute(array('ticket' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `tmails` WHERE incident=:incident"); //delete mails
	$qry->execute(array('incident' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `tsurvey_answers` WHERE ticket_id=:ticket_id"); //delete survey
	$qry->execute(array('ticket_id' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `ttemplates` WHERE incident=:incident"); //delete template
	$qry->execute(array('incident' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `ttoken` WHERE ticket_id=:ticket_id"); //delete token
	$qry->execute(array('ticket_id' => $_GET['id']));
	
	//remove upload files and folder if exist
	$upload_dir_to_remove='upload/'.$_GET['id'].'/';
	if(is_numeric($_GET['id']) && is_dir($upload_dir_to_remove)) 
	{
		//remove files before delete directory
		$files_to_remove = array_diff(scandir($upload_dir_to_remove), array('.','..'));
		foreach ($files_to_remove as $file_to_remove) {
			if(file_exists($upload_dir_to_remove.$file_to_remove)) {unlink($upload_dir_to_remove.$file_to_remove);}
		}
		rmdir($upload_dir_to_remove); //remove empty dir
	}
	
	//display delete message
	echo '<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i>	'.T_('Ticket supprimé').'.</center></div>';
	
	//redirect
	$url="./index.php?page=dashboard&state=$_GET[state]&userid=$_GET[userid]";
	$url=preg_replace('/%/','%25',$url);
	$url=preg_replace('/%2525/','%25',$url);
	echo "
	<SCRIPT LANGUAGE='JavaScript'>
		function redirect(){window.location='$url'}
		setTimeout('redirect()',$rparameters[time_display_msg]);
	</SCRIPT>
	";
}

//action to lock thread
if ($_GET['lock_thread'] && $rright['ticket_thread_private']!=0) 
{
	$qry=$db->prepare("UPDATE `tthreads` SET `private`='1' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['lock_thread']));
}

//action to unlock thread
if ($_GET['unlock_thread'] && $rright['ticket_thread_private']!=0) 
{
	$qry=$db->prepare("UPDATE `tthreads` SET `private`='0' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['unlock_thread']));
}

//master query
$qry=$db->prepare("SELECT * FROM `tincidents` WHERE id=:id");
$qry->execute(array('id' => $_GET['id']));
$globalrow=$qry->fetch();
$qry->closeCursor();

//user group detection switch values
if(substr($_POST['user'], 0, 1) =='G') 
{
 	$u_group=explode("_", $_POST['user']);
	$u_group=$u_group[1];
	$_POST['user']='';
} elseif ($globalrow['u_group']!=0 && $_POST['user']=='')
{
	$u_group=$globalrow['u_group'];
	$_POST['user']='';
}
//technician group detection switch values
if(substr($_POST['technician'], 0, 1) =='G') 
{
 	$t_group=explode("_", $_POST['technician']);
	$t_group=$t_group[1];
	$_POST['technician']='';
} elseif ($globalrow['t_group']!=0 && $_POST['technician']=='')
{
	$t_group=$globalrow['t_group'];
	$_POST['technician']='';
} 

//database inputs if submit
if($rparameters['debug']==1){ echo "<b><u>DEBUG MODE:</u></b><br /> <b>VAR:</b> save=$save post_modify=$_POST[modify] post_quit=$_POST[quit] post_mail=$_POST[mail] post_upload=$_POST[upload] post_send=$_POST[send] post_action=$_POST[action] get_action=$db_action post_category=$_POST[category] post_subcat=$_POST[subcat] post_technician=$_POST[technician] globalrow_technician=$globalrow[technician] post_u_service=$_POST[u_service] globalrow_u_service=$globalrow[u_service] post_u_agency=$_POST[u_agency] globalrow_u_agency=$globalrow[u_agency] post_asset_id=$_POST[asset_id] globalrow[asset_id]=$globalrow[asset_id] post_sender_service=$_POST[sender_service] globalrow_sender_service=$globalrow[sender_service] post_priority=$_POST[priority] post_title=$_POST[title]<br />";}
if($_POST['addcalendar']||$_POST['addevent']||$_POST['modify']||$_POST['quit']||$_POST['mail']||$_POST['upload']||$save=="1"||$_POST['send']||$_POST['action']) 
{
	//check mandatory fields
    if(($rright['ticket_priority_mandatory']!=0) && ($_POST['priority']=='')) {$error=T_('Merci de renseigner la priorité');}
    if(($rright['ticket_criticality_mandatory']!=0) && ($_POST['criticality']=='')) {$error=T_('Merci de renseigner la criticité');}
	if(($rright['ticket_description_mandatory']!=0) && ((ctype_space($_POST['text']) || $_POST['text']=='' || ctype_space(strip_tags($_POST['text']))==1 ) || strip_tags($_POST['text'])=='')) {$error=T_('Merci de renseigner la description de ce ticket');} 
    if(($rright['ticket_cat_mandatory']!=0) && (($_POST['category']==0) || ($_POST['subcat']==0))) {$error=T_("Merci de renseigner le champ catégorie et sous-catégorie");}
    if(($rright['ticket_asset_mandatory']!=0) && ($rparameters['asset']==1) && ($_POST['asset_id']==0)) {$error=T_("Merci de renseigner l'équipement");}
    if(($rright['ticket_type_mandatory']!=0) && ($rparameters['ticket_type']==1) && ($_POST['type']==0)) {$error=T_("Merci de renseigner le champ type");}
    if(($rright['ticket_agency_mandatory']!=0) && ($rparameters['user_agency']==1) && ($_POST['u_agency']==0)) {
		//check if current user have multiple agencies to display empty mandatory alert
		$qry2=$db->prepare("SELECT COUNT(*) FROM `tusers_agencies` WHERE user_id=:user_id");
		$qry2->execute(array('user_id' => $_SESSION['user_id']));
		$row2=$qry2->fetch();
		$qry2->closeCursor();
		
		if ($row2[0]>1 && $_SESSION['profile_id']!=4) {$error=T_("Merci de renseigner l'agence");}
	}
	if(($rright['ticket_tech_mandatory']!=0) && ($_POST['technician']=='0')) {$error=T_('Merci de renseigner le technicien associé à ce ticket');}
	//check user ticket limit 
	if ($rparameters['user_limit_ticket']==1 && $ruser['limit_ticket_number']!=0 && $ruser['limit_ticket_days']!=0 && $ruser['limit_ticket_date_start']!='0000-00-00' &&($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2))
	{
		//generate date start and date end
		$date_start=$ruser['limit_ticket_date_start'];
		
		//calculate end date	
		$date_start_conv = date_create($ruser['limit_ticket_date_start']);
		date_add($date_start_conv, date_interval_create_from_date_string("$ruser[limit_ticket_days] days"));
		$date_end=date_format($date_start_conv, 'Y-m-d');
	
		//count number of ticket remaining in period
		$qry=$db->prepare("SELECT COUNT(*) FROM `tincidents` WHERE user=:user AND date_create BETWEEN :date_create AND :date_end AND disable=:disable");
		$qry->execute(array(
			'user' => $_SESSION['user_id'],
			'date_create' => $date_start,
			'date_end' => $date_end,
			'disable' => 0
			));
		$nbticketused=$qry->fetch();
		$qry->closeCursor();
		
		//check number of tickets in current range date
		if (date('Y-m-d')>$date_end || date('Y-m-d')<$date_start)
		{
			$nbticketremaining=0;
		} else {
			$nbticketremaining=$ruser['limit_ticket_number']-$nbticketused[0];
		}
		
		if($nbticketremaining<=0) {$error=T_('Votre limite de ticket est atteinte, prenez contact avec votre administrateur pour créditer votre compte').'.';}
	}
	//check company limit ticket
	if ($rparameters['company_limit_ticket']==1 &&($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2))
	{
		//get company limit ticket parameters
		$qry=$db->prepare("SELECT * FROM `tcompany` WHERE id=:id");
		$qry->execute(array('id' => $ruser['company']));
		$rcompany=$qry->fetch();
		$qry->closeCursor();
		
		if ($rcompany['limit_ticket_number']!=0 && $rcompany['limit_ticket_days']!=0 && $rcompany['limit_ticket_date_start']!='0000-00-00' )
		{
			//generate date start and date end
			$date_start=$rcompany['limit_ticket_date_start'];
			
			//calculate end date	
			$date_start_conv = date_create($rcompany['limit_ticket_date_start']);
			date_add($date_start_conv, date_interval_create_from_date_string("$rcompany[limit_ticket_days] days"));
			$date_end=date_format($date_start_conv, 'Y-m-d');
		
			//count number of ticket remaining in period
			$qry=$db->prepare("SELECT COUNT(*) FROM `tincidents`,`tusers` WHERE tusers.id=tincidents.user AND tusers.company=:company AND date_create BETWEEN :date_start AND :date_end AND tincidents.disable=:disable");
			$qry->execute(array(
			'company' => $rcompany['id'],
			'date_start' => $date_start,
			'date_end' => $date_end,
			'disable' => 0
			));
			$nbticketused=$qry->fetch();
			$qry->closeCursor();
			
			//check number of tickets in current range date
			if (date('Y-m-d')>$date_end || date('Y-m-d')<$date_start)
			{
				$nbticketremaining=0;
			} else {
				$nbticketremaining=$rcompany['limit_ticket_number']-$nbticketused[0];
			}
			
			if($nbticketremaining<=0) {$error=T_('La limite de ticket attribuée pour votre société est atteinte, prenez contact avec votre administrateur pour créditer votre compte.');}
		}
	}
	
	//escape special char and secure string before database insert
	$_POST['description']=$_POST['text'];
	$_POST['resolution']=$_POST['text2'];
	if($error=='0') {$_POST['title']=strip_tags($_POST['title']);}

	//remove <br> generate by MS browser
	$_POST['description']=str_replace('<br><br><br>','',$_POST['description']);
	$_POST['resolution']=str_replace('<br><br><br>','',$_POST['resolution']);

	if($_POST['description']=='<br>'){$_POST['description']='';}
	if($_POST['resolution']=='<br>'){$_POST['resolution']='';}
	
	//convert date
	if ($_POST['start_availability'])
	{
		$start_availability=DateTime::createFromFormat('d/m/Y H:i:s',$_POST['start_availability']);
		$start_availability=$start_availability->format('Y-m-d H:i:s');
		$end_availability=DateTime::createFromFormat('d/m/Y H:i:s',$_POST['end_availability']);
		$end_availability=$end_availability->format('Y-m-d H:i:s');
	}
	
	//thread generation when no error detected
	if($error=='0')
	{
		//detect transfert tech group change to group
		if ($t_group!=$globalrow['t_group'] && $globalrow['technician']==0 && $t_group!='' && $globalrow['t_group']!=0 ) {
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`group1`,`group2`) VALUES (:ticket,:date,:author,:text,:type,:group1,:group2)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'text' => '','type' => 2,'group1' => $globalrow['t_group'],'group2' => $t_group));
		}
		//detect transfert tech change to tech
		if ($rright['ticket_tech']!=0 && $_POST['technician']!=$globalrow['technician'] && $globalrow['technician']!=0 && $_POST['technician']!='') {
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`tech1`,`tech2`) VALUES (:ticket,:date,:author,:text,:type,:tech1,:tech2)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'text' => '','type' => 2,'tech1' => $globalrow['technician'],'tech2' => $_POST['technician']));
		}
		//detect transfert techgroup change to tech
		if ($globalrow['t_group']!=0 && $_POST['technician']) {
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`group1`,`tech2`) VALUES (:ticket,:date,:author,:text,:type,:group1,:tech1)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'text' => '','type' => 2,'group1' => $globalrow['t_group'],'tech1' => $_POST['technician']));
		}
		//detect transfert tech change to techgroup
		if ($globalrow['technician']!=0 && $t_group) {
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`tech1`,`group2`) VALUES (:ticket,:date,:author,:text,:type,:tech1,:group2)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'text' => '','type' => 2,'tech1' => $globalrow['technician'],'group2' => $t_group));
		}
		//detect technician attribution
		if ($rright['ticket_tech']!=0 && $globalrow['technician']==0 && $_POST['technician']!='' && $_POST['technician']!='0' && $globalrow['t_group']==0 && $globalrow['creator']!=$_SESSION['user_id'])
		{
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`tech1`) VALUES (:ticket,:date,:author,:type,:tech1)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'type' => 1,'tech1' => $_POST['technician']));
		}
		//detect tech group attribution
		if ($globalrow['t_group']==0 && $t_group!='' && $globalrow['technician']==0)
		{
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`group1`) VALUES (:ticket,:date,:author,:type,:group1)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'type' => 1,'group1' => $t_group));
		}
		//generate thread for switch state
		if ($rright['ticket_state']!=0 && $globalrow['state']!=$_POST['state'] && $_POST['state']!=3 && $_POST['technician']!='')
		{
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,:type,:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'type' => 5,'state' => $_POST['state']));
		}
		
		//auto modify state from 5 to 1 if technician change (not attribute to wait tech)
		if ($globalrow['technician']==0 && $_POST['technician']!=0 && $globalrow['state']=='5' && $_POST['state']==$globalrow['state'] && $t_group=='') 
		{
			if($rparameters['debug']==1) {echo "<b>AUTO CHANGE STATE:</b> from 5 to 1 reason technician change detected (globalrow[state]=$globalrow[state] POST[state]=$_POST[state])<br />";}
			$_POST['state']='1';
			
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,:type,:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'type' => 5,'state' => $_POST['state']));
		}
		//auto modify state from 5 to 1 if technician group change (not attribute to wait tech)
		if ($globalrow['t_group']==0 && $globalrow['state']=='5' && $_POST['state']==$globalrow['state'] && $t_group!='') 
		{
			if($rparameters['debug']==1) {echo "<b>AUTO CHANGE STATE:</b> from 5 to 1 reason technician group change detected (globalrow[state]=$globalrow[state] POST[state]=$_POST[state] t_group=$t_group)<br />";}
			$_POST['state']='1';
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,:type,:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'type' => 5,'state' => $_POST['state']));
		}
		//auto modify state from 5 to 2 if technician add resolution thread (wait tech to current)
		if ((($_POST['resolution']!='') && ($_POST['resolution']!='\'\'')) && ($globalrow['technician']==$_SESSION['user_id']) && ($_POST['state']=='1')) 
		{
			if($rparameters['debug']==1) {echo "<b>AUTO CHANGE STATE</b> from 5 to 2 reason technician add resolution thread detected<br />";}
			$_POST['state']='2';		
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,:type,:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'type' => 5,'state' => $_POST['state']));
		}
		//auto modify state from 5 to 2 if technician add resolution thread on new ticket(wait tech to current)
		if (($_POST['resolution']!='') && ($_POST['resolution']!='\'\'') && ($_POST['technician']==$_SESSION['user_id']) && ($_POST['state']=='1') && ($_GET['action']=='new')) 
		{
			if($rparameters['debug']==1) {echo "<b>AUTO CHANGE STATE</b> from 5 to 2 reason technician add resolution thread on new ticket detected<br />";}
			$_POST['state']='2';
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,:type,:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'type' => 5,'state' => $_POST['state']));
		}
		//auto modify state to default state from parameters if tech is null (attribution state)
		if ($_POST['technician']=='' && $t_group=='') 
		{
			if($rparameters['debug']==1) {echo "<b>AUTO CHANGE STATE</b> to default state $rparameters[ticket_default_state] reason no technician or technician group associated with ticket<br />";}
			$_POST['state']=$rparameters['ticket_default_state'];
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,:type,:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'type' => 5,'state' => $_POST['state']));
		}
	}
	
	//insert resolution date if state is change to resolve (3)
	if($_POST['state']=='3' && $globalrow['state']!='3' && ($_POST['date_res']=='' || $_POST['date_res']=='0000-00-00 00:00:00')) {$_POST['date_res']=date("Y-m-d H:i:s");}
	
	//remove resolution date if state change from 3 to other state
	if($globalrow['state']=='3' && $_POST['state']!='3') {$_POST['date_res']='';}
	
	//unread ticket if another technician add thread
	if(($_POST['resolution']!='') && ($globalrow['technician']!=$_SESSION['user_id'])) $techread=0; 
	
	//auto-attribute ticket to technician if user attachment is detected
	if ($_POST['user'])
	{
		$qry=$db->prepare("SELECT `tech` FROM `tusers_tech` WHERE user=:user");
		$qry->execute(array('user' => $_POST['user']));
		$row=$qry->fetch();
		$qry->closeCursor();
        if($row['tech']!='') {
			if($rparameters['debug']==1) {echo '<br /><b>AUTO TECH CHANGE:</b> Auto assignement of this ticket, because technician attachment is detected.<br />';}
			$_POST['technician']=$row['tech'];
		}
	}
	
	//get user service to insert in tincidents table, or get selected service from field if ticket_service right
	if($_POST['user'] && !$_POST['u_service'] && $rright['ticket_service_disp']==0)
	{
		$qry=$db->prepare("SELECT `service_id` FROM `tusers_services` WHERE user_id=:user_id");
		$qry->execute(array('user_id' => $_POST['user']));
		$row=$qry->fetch();
		$qry->closeCursor();   		
        if($_POST['state']!=3) {$u_service=$row[0];} 
		elseif ($_POST['state']==3 && $_GET['action']=='new') {$u_service=$row[0];}
		else {$u_service=$globalrow['u_service'];} 
		if ($rparameters['debug']==1) {echo ' post_u_service='.$u_service.'<br />'; }
	} elseif ($_POST['u_service'] || $rright['ticket_service']!=0)
	{
		$u_service=$_POST['u_service'];
	} else {$u_service=$globalrow['u_service'];}
	
	if(!isset($u_service)) $u_service=0;
	
	//convert posted datetime to SQL format, if yyyy-mm-dd is detected
	if($_POST['date_create'] && !strpos($_POST['date_create'], "-"))
	{
		//convert datetime if time is specified
		if(strpos($_POST['date_create'], ":"))
		{$_POST['date_create'] = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['date_create']);}
		else 
		{$_POST['date_create'] = DateTime::createFromFormat('d/m/Y', $_POST['date_create']);}
	
		$_POST['date_create']=$_POST['date_create']->format('Y-m-d H:i:s');
	} elseif(!$_POST['date_create'] && $globalrow['date_create']) {$_POST['date_create'] = $globalrow['date_create'];}
	elseif(!$_POST['date_create'] && !$globalrow['date_create']) {$_POST['date_create'] = date('Y-m-d H:i:s');}
	
	if($_POST['date_hope'] && !strpos($_POST['date_hope'], "-"))
	{
		//convert datetime if time is specified
		if(strpos($_POST['date_hope'], ":"))
		{$_POST['date_hope'] = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['date_hope']);}
		else 
		{$_POST['date_hope'] = DateTime::createFromFormat('d/m/Y', $_POST['date_hope']);}
		
		$_POST['date_hope']=$_POST['date_hope']->format('Y-m-d');
	}
	if($_POST['date_res'] && !strpos($_POST['date_res'], "-"))
	{
		//convert datetime if time is specified
		if(strpos($_POST['date_res'], ":"))
		{$_POST['date_res'] = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['date_res']);}
		else 
		{$_POST['date_res'] = DateTime::createFromFormat('d/m/Y', $_POST['date_res']);}
	
		$_POST['date_res']=$_POST['date_res']->format('Y-m-d H:i:s');
	}
	
	//SQL queries
	if (($_GET['action']=='new') && ($error=="0"))
	{
		//modify read state
		if($globalrow['technician']!=$_SESSION['user_id']) {$techread=0;} //unread ticket case when creator is not technician  
		if($_POST['technician']==$_SESSION['user_id']) {$techread=1; $techread_date=date("Y-m-d H:i:s");} //read ticket  
		
		//insert ticket
		$qry=$db->prepare("INSERT INTO `tincidents` 
		(`user`,`type`,`u_group`,`u_service`,`u_agency`,`sender_service`,`technician`,`t_group`,`title`,`description`,`date_create`,`date_hope`,`date_res`,`priority`,`criticality`,`state`,`creator`,`time`,`time_hope`,`category`,`subcat`,`techread`,`techread_date`,`place`,`asset_id`,`start_availability`,`end_availability`,`availability_planned`)
		VALUES 
		(:user,:type,:u_group,:u_service,:u_agency,:sender_service,:technician,:t_group,:title,:description,:date_create,:date_hope,:date_res,:priority,:criticality,:state,:creator,:time,:time_hope,:category,:subcat,:techread,:techread_date,:place,:asset_id,:start_availability,:end_availability,:availability_planned)");
		$qry->execute(array(
			'user' => $_POST['user'],
			'type' => $_POST['type'],
			'u_group' => $u_group,
			'u_service' => $u_service,
			'u_agency' => $_POST['u_agency'],
			'sender_service' => $_POST['sender_service'],
			'technician' => $_POST['technician'],
			't_group' => $t_group,
			'title' => $_POST['title'],
			'description' => $_POST['description'],
			'date_create' => $_POST['date_create'],
			'date_hope' => $_POST['date_hope'],
			'date_res' => $_POST['date_res'],
			'priority' => $_POST['priority'],
			'criticality' => $_POST['criticality'],
			'state' => $_POST['state'],
			'creator' => $_SESSION['user_id'],
			'time' => $_POST['time'],
			'time_hope' => $_POST['time_hope'],
			'category' => $_POST['category'],
			'subcat' => $_POST['subcat'],
			'techread' => $techread,
			'techread_date' => $techread_date,
			'place' => $_POST['ticket_places'],
			'asset_id' => $_POST['asset_id'],
			'start_availability' => $start_availability,
			'end_availability' => $end_availability,
			'availability_planned' => $_POST['availability_planned']
			));
			
		
	} elseif ($error=="0")  {
		
		//modify read state
		if($_POST['technician']==$_SESSION['user_id']) {$techread=1; $techread_date=date("Y-m-d H:i:s");} //read ticket  
		if($globalrow['technician']=='') {$techread=1; $techread_date=date("Y-m-d H:i:s");} //read ticket case when it's an unassigned ticket.
		
		//check previous change, before update, concomitant user change #4471
		$qry=$db->prepare("SELECT `technician`,`state` FROM `tincidents` WHERE id=:id");
		$qry->execute(array('id' => $_GET['id']));
		$row=$qry->fetch();
		$qry->closeCursor();
		if($rright['ticket_tech']==0) {if($row['technician']!=$_POST['technician']) {$_POST['technician']=$row['technician'];}}
		if($rright['ticket_state']==0) {if($row['state']!=$_POST['state']) {$_POST['state']=$row['state'];}}
	
		//update ticket
		$query = "UPDATE tincidents SET 
		user='$_POST[user]',
		type='$_POST[type]',
		u_group='$u_group',
		u_service='$u_service',
		u_agency='$_POST[u_agency]',
		sender_service='$_POST[sender_service]',
		technician='$_POST[technician]',
		t_group='$t_group',
		title=$_POST[title],
		description=$_POST[description],
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
		techread_date='$techread_date',
		place='$_POST[ticket_places]',
		asset_id='$_POST[asset_id]',
		start_availability='$start_availability',
		end_availability='$end_availability',
		availability_planned='$_POST[availability_planned]'
		WHERE
		id LIKE $db_id";
		if ($rparameters['debug']==1) {echo "<br /><b>QUERY:</b><br /> $query<br />";}
		//$db->exec($query);	
		
		$qry=$db->prepare("UPDATE `tincidents` SET
		`user`=:user,
		`type`=:type,
		`u_group`=:u_group,
		`u_service`=:u_service,
		`u_agency`=:u_agency,
		`sender_service`=:sender_service,
		`technician`=:technician,
		`t_group`=:t_group,
		`title`=:title,
		`description`=:description,
		`date_create`=:date_create,
		`date_hope`=:date_hope,
		`date_res`=:date_res,
		`priority`=:priority,
		`criticality`=:criticality,
		`state`=:state,
		`time`=:time,
		`time_hope`=:time_hope,
		`category`=:category,
		`subcat`=:subcat,
		`techread`=:techread,
		`techread_date`=:techread_date,
		`place`=:place,
		`asset_id`=:asset_id,
		`start_availability`=:start_availability,
		`end_availability`=:end_availability,
		`availability_planned`=:availability_planned
		WHERE `id`=:id");
		$qry->execute(array(
			'user' => $_POST['user'],
			'type' => $_POST['type'],
			'u_group' => $u_group,
			'u_service' => $u_service,
			'u_agency' => $_POST['u_agency'],
			'sender_service' => $_POST['sender_service'],
			'technician' => $_POST['technician'],
			't_group' => $t_group,
			'title' => $_POST['title'],
			'description' => $_POST['description'],
			'date_create' => $_POST['date_create'],
			'date_hope' => $_POST['date_hope'],
			'date_res' => $_POST['date_res'],
			'priority' => $_POST['priority'],
			'criticality' => $_POST['criticality'],
			'state' => $_POST['state'],
			'time' => $_POST['time'],
			'time_hope' => $_POST['time_hope'],
			'category' => $_POST['category'],
			'subcat' => $_POST['subcat'],
			'techread' => $techread,
			'techread_date' => $techread_date,
			'place' => $_POST['ticket_places'],
			'asset_id' => $_POST['asset_id'],
			'start_availability' => $start_availability,
			'end_availability' => $end_availability,
			'availability_planned' => $_POST['availability_planned'],
			'id' => $_GET['id']
			));
	}
	//threads text generation
	if(($_POST['resolution']!='') && ($_POST['resolution']!="'<br>'") && ($_POST['resolution']!='\'\'') && ($error=='0'))
	{
		if($_GET['threadedit'])
		{
			//get author from thread
			$qry=$db->prepare("SELECT `author` FROM `tthreads` WHERE id=:id");
			$qry->execute(array('id' => $_GET['threadedit']));
			$row=$qry->fetch();
			$qry->closeCursor();
			
			//check your own ticket for update thread right
			if($row['author']==$_SESSION['user_id']) 
			{
				if ($rright['ticket_thread_edit']!=0)  {
					$qry=$db->prepare("UPDATE `tthreads` SET `text`=:text WHERE `id`=:id");
					$qry->execute(array(
						'text' => $_POST['resolution'],
						'id' => $_GET['threadedit']
						));
				}
			} else {
				if ($rright['ticket_thread_edit_all']!=0) {
					$qry=$db->prepare("UPDATE `tthreads` SET `text`=:text WHERE `id`=:id");
					$qry->execute(array(
						'text' => $_POST['resolution'],
						'id' => $_GET['threadedit']
						));
				}
			}
			
		} elseif ($_POST['resolution']!='') {
			//generate new thread for this ticket
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`private`) VALUES (:ticket,:date,:author,:text,:type,:private)");
			$qry->execute(array(
				'ticket' => $_GET['id'],
				'date' => $datetime,
				'author' => $_SESSION['user_id'],
				'text' => $_POST['resolution'],
				'type' => 0,
				'private' => $_POST['private']
				));
		}
	}

	//threads insert close state
	if($_POST['state']=='3' && $globalrow['state']!='3' && !$error)
	{
		$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`) VALUES (:ticket,:date,:author,:type)");
		$qry->execute(array(
			'ticket' => $_GET['id'],
			'date' => $datetime,
			'author' => $_SESSION['user_id'],
			'type' => 4
			));
	}
	
	//uploading files
	include "./core/upload.php";
	
	//auto send mail
	if(!$error)
	{
		if(($rparameters['mail_auto_user_newticket']==1) || ($rparameters['survey']==1) || ($rparameters['mail_auto']==1) || ($rparameters['mail_auto_user_modify']==1) || ($rparameters['mail_auto_tech_modify']==1) || ($rparameters['mail_auto_tech_attribution']==1) || ($rparameters['mail_auto_tech_modify']==1) || ($rparameters['mail_newticket']==1) && ($_POST['upload']=='')){include('./core/auto_mail.php');}
	}
	
	//display message
	if($error=="0")
	{
	    echo '<div class="alert alert-block alert-success"><center><i class="icon-ok green"></i>	'.T_('Ticket sauvegardé').'. </center></div>';
	if($_GET['action']=='new') {$hide_button=1;} //case press save button during saving ticket
	} else {
	    // new page ticket redirect
        echo '<div class="alert alert-danger"><i class="icon-remove"></i> <strong>'.T_('Erreur').' :</strong> '.T_($error).' </div>';
	}

	//redirect to ticket list for quit or send button
	if (($_POST['quit'] || $_POST['send']) && ($error=='0'))
	{
		echo '<script language="Javascript">
		<!--
		document.location.replace("./index.php?page=dashboard&'.$url_get_parameters.'");
		-->
		</script>';
	}
	
	//send mail
	if($_POST['mail'])
	{
		//redirect to preview mail page
		$url="./index.php?page=preview_mail&id=$_GET[id]&userid=$_GET[userid]&state=$_GET[state]&category=$_GET[category]&subcat=$_GET[subcat]&viewid=$_GET[viewid]&view=$_GET[view]&date_start=$_GET[date_start]&date_end=$_GET[date_end]";
		$url=preg_replace('/%/','%25',$url);
		$url=preg_replace('/%2525/','%25',$url);
		echo '
		<script language="Javascript">
			<!--
			document.location.replace("'.$url.'");
			// -->
		</script>
		';
	}
	
    if($error=="0" && !$_POST['addcalendar']&& !$_POST['addevent'])
    {
		//global redirect on current ticket
		$url="./index.php?page=ticket&id=$_GET[id]&action=$_POST[action]&edituser=$_POST[edituser]&cat=$_POST[category]&editcat=$_POST[subcat]&$url_get_parameters$down";
		$url=preg_replace('/%/','%25',$url);
		$url=preg_replace('/%2525/','%25',$url);
		echo "
		<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
				window.location='$url'	
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
		</SCRIPT>";
    }		
}
//modify ticket state on close ticket button
if($_POST['close'] && $rright['ticket_close']!=0) 
{
	//update tincidents
	$qry=$db->prepare("UPDATE `tincidents` SET `state`='3',`date_res`=:date_res WHERE `id`=:id");
	$qry->execute(array('date_res' => $datetime,'id' => $_GET['id']));
		
	//auto send mail
	$_POST['state']=3;
	if($rparameters['mail_auto']==1 && $_POST['upload']==''){include('./core/auto_mail.php');}
	
	if($_SESSION['profile_id']!=0 && $_SESSION['profile_id']!=4)
	{
		//unread ticket for technician only if user close ticket
		$qry=$db->prepare("UPDATE `tincidents` SET `techread`='0' WHERE `id`=:id");
		$qry->execute(array('id' => $_GET['id']));
	}
	//update thread
	$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`type`,`author`) VALUES (:ticket,:date,:type,:author)");
	$qry->execute(array(
		'ticket' => $_GET['id'],
		'date' => $datetime,
		'type' => 4,
		'author' => $_SESSION['user_id']
		));
	//redirect to tickets list
	echo '<div class="alert alert-block alert-success"><center><i class="icon-ok green"></i>	'.T_('Ticket clôturé').'. </center></div>';
	echo "
	<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
			window.location='./index.php?page=dashboard&$url_get_parameters'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
	</SCRIPT>";
}
//redirect to tickets list
if($_POST['cancel']) 
{
	echo '<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i>	'.T_('Annulation pas de modification').'.</center></div>';
	echo "
	<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
		window.location='./index.php?page=dashboard&$url_get_parameters'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
	</SCRIPT>";
}
  
//unread ticket technician
if (($globalrow['techread']=="0")&&($globalrow['technician']==$_SESSION['user_id'])) 
{
	$current_date_hour=date("Y-m-d H:i:s");
	$qry=$db->prepare("UPDATE `tincidents` SET `techread`=:techread,`techread_date`=:techread_date WHERE `id`=:id");
	$qry->execute(array(
		'techread' => 1,
		'techread_date' => $current_date_hour,
		'id' => $_GET['id']
		));
}
//find next ticket
$qry=$db->prepare("SELECT MIN(id) FROM `tincidents` WHERE id > :id AND id IN (SELECT id FROM tincidents WHERE technician=:technician AND state=:state AND id NOT LIKE :id) AND disable=:disable");
$qry->execute(array(
	'id' => $_GET['id'],
	'technician' => $_SESSION['user_id'],
	'state' => $globalrow['state'],
	'disable' => 0
));
$next=$qry->fetch();
$qry->closeCursor();
//find previous ticket
$qry=$db->prepare("SELECT MIN(id) FROM `tincidents` WHERE id < :id AND id IN (SELECT id FROM tincidents WHERE technician=:technician AND state=:state AND id NOT LIKE :id) AND disable=:disable");
$qry->execute(array(
	'id' => $_GET['id'],
	'technician' => $_SESSION['user_id'],
	'state' => $globalrow['state'],
	'disable' => 0
));
$previous=$qry->fetch();
$qry->closeCursor();

//calculate percentage of ticket resolution
if ($globalrow['time_hope']!=0 && ($rright['ticket_time_disp']!=0 && $rright['ticket_time_hope_disp']!=0))
{
	$percentage=($globalrow['time']*100)/$globalrow['time_hope'];
	$percentage=round($percentage);
	if (($globalrow['time']!='1') && ($globalrow['time_hope']!='1') && ($globalrow['time_hope']>=$globalrow['time'])) {$percentage=' <span title="'.T_("Pourcentage d'avancement du ticket basé sur le temps passé et estimé").'">('.$percentage.'%)</span> ';} else {$percentage='';}
}

//cut title for long case
$nbtitle=strlen($globalrow['title']);
if ($nbtitle>50)
{
	$title=substr($globalrow['title'], 0, 50);
	$title="$title...";
} else {$title=$globalrow['title'];}
?>