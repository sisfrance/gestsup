<?php 
################################################################################
# @Name : ticket_userinfos.php
# @Description : get user information to display on ticket
# @call : ticket.php over ajax
# @parameters : 
# @Author : Flox
# @Create : 25/01/2019
# @Update : 13/05/2019
# @Version : 3.1.45 p1
################################################################################

//initialize variables 
if(!isset($_GET['token'])) $_GET['token']=''; 
if(!isset($_COOKIE['token'])) $_COOKIE['token']=''; 

//security check
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
	//check post value and token
	if($_POST['user'] && $_GET['token']==$_COOKIE['token'] && $_GET['token'])
	{
		//init var
		$service='';
		$agency='';
		$other_ticket='';
		
		//db connect
		require('../connect.php');
		
		//load parameters table
		$qry=$db->prepare("SELECT * FROM `tparameters`");
		$qry->execute();
		$rparameters=$qry->fetch();
		$qry->closeCursor();
		
		//display error parameter
		if($rparameters['debug']==1) {
			ini_set('display_errors', 'On');
			ini_set('display_startup_errors', 'On');
			ini_set('html_errors', 'On');
			error_reporting(E_ALL);
		} else {
			ini_set('display_errors', 'Off');
			ini_set('display_startup_errors', 'Off');
			ini_set('html_errors', 'Off');
			error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
		}
		
		//get user data
		$qry=$db->prepare("SELECT `phone`,`mobile`,`mail`,`function`,`company` FROM `tusers` WHERE id=:id");
		$qry->execute(array('id' => $_POST['user']));
		$user=$qry->fetch();
		$qry->closeCursor();
		
		$qry=$db->prepare("SELECT `name` FROM `tcompany` WHERE id=:id");
		$qry->execute(array('id' => $user['company']));
		$company=$qry->fetch();
		$qry->closeCursor();
		
		$qry=$db->prepare("SELECT `name` FROM `tcompany` WHERE id=:id AND id!=0");
		$qry->execute(array('id' => $user['company']));
		$company=$qry->fetch();
		$qry->closeCursor();
		
		$qry=$db->prepare("SELECT `name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`=0");
		$qry->execute(array('user_id' => $_POST['user']));
		while($row=$qry->fetch()) {$service.=' '.$row['name'];}
		$qry->closeCursor();
		
		$qry=$db->prepare("SELECT `name` FROM `tagencies` WHERE `id` IN (SELECT `agency_id` FROM `tusers_agencies` WHERE `user_id`=:user_id) AND `disable`=0");
		$qry->execute(array('user_id' => $_POST['user']));
		while($row=$qry->fetch()) {$agency.=' '.$row['name'];}
		$qry->closeCursor();
		
		$qry=$db->prepare("SELECT `id`,`title` FROM `tincidents` WHERE user=:user_id AND (`state`='1' OR `state`='2' OR `state`='6' OR `state`='5') AND `id`!=:ticket AND `disable`=0 ORDER BY id DESC LIMIT 0,3");
		$qry->execute(array('user_id' => $_POST['user'],'ticket' => $_GET['ticket']));
		while($row=$qry->fetch()) {$other_ticket.='&nbsp;<a title="'.$row['title'].'" href="./index.php?page=ticket&amp;id='.$row['id'].'">#'.$row['id'].'</a>';}
		$qry->closeCursor();
		
		$qry = $db->prepare("SELECT `id`,`netbios` FROM `tassets` WHERE `user`=:user_id AND `state`='2' AND `user`!='0' ORDER BY id DESC");
		$qry->execute(array('user_id' => $_POST['user']));
		$asset=$qry->fetch();
		$qry->closeCursor(); 
		
		//check if company limit tickets
		if($rparameters['company_limit_ticket'] )
		{
			$qry=$db->prepare("SELECT `tcompany`.`id`,`tcompany`.`limit_ticket_number`,`tcompany`.`limit_ticket_days`,`tcompany`.`limit_ticket_date_start` FROM `tcompany`,`tusers` WHERE `tusers`.`company`=`tcompany`.`id` AND `tusers`.id=:id");
			$qry->execute(array('id' => $_POST['user']));
			$rcompany=$qry->fetch();
			$qry->closeCursor();
			
			if($rcompany['limit_ticket_days']!=0 && $rcompany['limit_ticket_date_start']!='0000-00-00')
			{
				//generate date start and date end
				$date_start=$rcompany['limit_ticket_date_start'];
				
				//calculate end date	
				$date_start_conv = date_create($rcompany['limit_ticket_date_start']);
				date_add($date_start_conv, date_interval_create_from_date_string("$rcompany[limit_ticket_days] days"));
				$date_end=date_format($date_start_conv, 'Y-m-d');
			
				//count number of ticket remaining in period
				$qry=$db->prepare("SELECT COUNT(tincidents.id) FROM `tincidents`,`tusers` WHERE tusers.id=tincidents.user AND tusers.company=:company AND date_create BETWEEN :date_start AND :date_end AND tincidents.disable='0'");
				$qry->execute(array('company' => $rcompany['id'],'date_start' => $date_start,'date_end' => $date_end));
				$nbticketused=$qry->fetch();
				$qry->closeCursor();
				
				//check number of tickets in current range date
				if (date('Y-m-d')>$date_end || date('Y-m-d')<$date_start)
				{
					$nbticketremaining=0;
				} else {
					$nbticketremaining=$rcompany['limit_ticket_number']-$nbticketused[0];
				}
			} else {$nbticketremaining='';}
		} else {$nbticketremaining='';}
		
		//encode result ajax call
		if($user) {
			echo json_encode(
				array(
					"status" => "success",
					"phone" => $user["phone"],
					"mobile" => $user["mobile"],
					"mail" => $user["mail"],
					"function" => $user["function"],
					"company" => $company["name"],
					"service" => $service,
					"agency" => $agency,
					"asset_id" => $asset['id'],
					"asset_netbios" => $asset['netbios'],
					"other_ticket" => $other_ticket,
					"ticket_remaining" => $nbticketremaining
				)
			);
		} else {
			echo json_encode(array("status" => "failed"));
		}
	} else {
		echo json_encode(array("status" => "failed"));
	} 
} else {
	echo json_encode(array("status" => "failed"));
}
?>