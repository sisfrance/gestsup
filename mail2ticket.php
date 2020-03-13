<?php
################################################################################
# @Name : mail2ticket.php
# @Description : convert mail to ticket
# @call : parameters in connector tab or using an external cron job
# @parameters : 
# @Author : Flox
# @Create : 07/04/2013
# @Update : 23/09/2019
# @Version : 3.1.45 p1
################################################################################

//initialize variables 
if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';

//locales
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if ($lang=='fr') {$_GET['lang'] = 'fr_FR';}
else {$_GET['lang'] = 'en_US';}

define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/locale');
define('DEFAULT_LOCALE', '($_GET[lang]');
require_once('components/php-gettext/gettext.inc');
$encoding = 'UTF-8';
$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;
T_setlocale(LC_MESSAGES, $locale);
T_bindtextdomain($_GET['lang'], LOCALE_DIR);
T_bind_textdomain_codeset($_GET['lang'], $encoding);
T_textdomain($_GET['lang']);

//define encodage type 
header('Content-Type: text/html; charset=utf-8');

//call phpimap component
require_once('components/PhpImap/__autoload.php');

//functions
require_once('core/functions.php');

//function to add attachment in image on ticket
function func_attachement($c_ticket_number,$c_name_dir_upload,$mail,$db,$mailbox,$count,$contentype)
{
	$c_name_dir_ticket = $c_name_dir_upload.$c_ticket_number; 
	//move attachment to upload directory
	$tabAttachments = $mail->getAttachments();
	foreach ($tabAttachments as $tabAttachment){
		if(!is_dir($c_name_dir_upload.$c_ticket_number))
		{
			$oldmask = umask(0);
			@mkdir($c_name_dir_upload.$c_ticket_number,0777);
			umask($oldmask);
		}
		//case image inside in mail
		//if($tabAttachment->disposition=="inline" || $tabAttachment->disposition==null) #4015
		if($tabAttachment->disposition=="inline" || $tabAttachment->disposition=="INLINE" || $tabAttachment->disposition==null) 
		{
			$c_name_file = basename($tabAttachment->filePath);
			echo '['.$mailbox.'] [mail '.$count.'] Image into body: <span style="color:green">'.$c_name_file.'</span><br />';
			$dispo=$tabAttachment->disposition;
			echo '['.$mailbox.'] [mail '.$count.'] Disposition: <span style="color:green">'.$dispo.'</span><br />';
			//check if link are not present #4371 from apple mail
			if($contentype=='textPlain')
			{
				//update freeslot
				$c_name_file = $tabAttachment->name;
				//black list exclusion for extension
				$blacklistedfile=0;
				$blacklist =  array('php', 'php1', 'php2','php3' ,'php4' ,'php5', 'php6', 'php7', 'php8', 'php9', 'php10', 'js', 'htm', 'html', 'phtml', 'exe', 'jsp' ,'pht', 'shtml', 'asa', 'cer', 'asax', 'swf', 'xap', 'phphp', 'inc', 'htaccess', 'sh', 'py', 'pl', 'jsp', 'asp', 'cgi', 'json', 'svn', 'git', 'lock', 'yaml', 'com', 'bat', 'ps1', 'cmd', 'vb', 'hta', 'reg', 'ade', 'adp', 'app', 'asp', 'bas', 'bat', 'cer', 'chm', 'cmd', 'com', 'cpl', 'crt', 'csh', 'der', 'exe', 'fxp', 'gadget', 'hlp', 'hta', 'inf', 'ins', 'isp', 'its', 'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt', 'mdw', 'mdz', 'msc', 'msh', 'msh1', 'msh2', 'mshxml', 'msh1xml', 'msh2xml', 'msi', 'msp', 'mst', 'ops', 'pcd', 'pif', 'plg', 'prf', 'prg', 'pst', 'reg', 'scf', 'scr', 'sct', 'shb', 'shs', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'tmp', 'url', 'vb', 'vbe', 'vbs', 'vsmacros', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xnk');
				$ext=explode('.',$c_name_file);
				foreach ($ext as &$value) {
					$value=strtolower($value);
					if(in_array($value,$blacklist)) {$blacklistedfile=1;} 
				}
				if(!$blacklistedfile)
				{
					$qry=$db->prepare("SELECT `img1`,`img2`,`img3`,`img4`,`img5` FROM `tincidents` WHERE id=:id");
					$qry->execute(array('id' => $c_ticket_number));
					$row=$qry->fetch();
					$qry->closeCursor();
					
					//find the first free slot else not display attach input
					if ($row['img1']=="") {$freeslot="img1";}
					else if ($row['img2']=="") {$freeslot="img2";}
					else if ($row['img3']=="") {$freeslot="img3";}
					else if ($row['img4']=="") {$freeslot="img4";}
					else if ($row['img5']=="") {$freeslot="img5";}

					if(isset($freeslot)){
						echo '['.$mailbox.'] [mail '.$count.'] Freeslot selected: <span style="color:green">'.$freeslot.'</span><br />';
						$db_file_name=strip_tags($db->quote($c_name_file));
						$db->query("UPDATE tincidents SET $freeslot=$db_file_name WHERE id='$c_ticket_number'");
					} else {
						echo '['.$mailbox.'] [mail '.$count.'] Freeslot selected: <span style="color:red">no free slot found</span><br />';
					}
				} else {
					echo '['.$mailbox.'] [mail '.$count.'] Blacklisted file: <span style="color:red">'.$c_name_file.'</span><br />';
				}
			}
			
			
		} 
		//case attachment in mail
		else 
		{
			$c_name_file = $tabAttachment->name;
			//black list exclusion for extension
			$blacklistedfile=0;
			$blacklist =  array('php', 'php1', 'php2','php3' ,'php4' ,'php5', 'php6', 'php7', 'php8', 'php9', 'php10', 'js', 'htm', 'html', 'phtml', 'exe', 'jsp' ,'pht', 'shtml', 'asa', 'cer', 'asax', 'swf', 'xap', 'phphp', 'inc', 'htaccess', 'sh', 'py', 'pl', 'jsp', 'asp', 'cgi', 'json', 'svn', 'git', 'lock', 'yaml', 'com', 'bat', 'ps1', 'cmd', 'vb', 'hta', 'reg', 'ade', 'adp', 'app', 'asp', 'bas', 'bat', 'cer', 'chm', 'cmd', 'com', 'cpl', 'crt', 'csh', 'der', 'exe', 'fxp', 'gadget', 'hlp', 'hta', 'inf', 'ins', 'isp', 'its', 'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt', 'mdw', 'mdz', 'msc', 'msh', 'msh1', 'msh2', 'mshxml', 'msh1xml', 'msh2xml', 'msi', 'msp', 'mst', 'ops', 'pcd', 'pif', 'plg', 'prf', 'prg', 'pst', 'reg', 'scf', 'scr', 'sct', 'shb', 'shs', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'tmp', 'url', 'vb', 'vbe', 'vbs', 'vsmacros', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xnk');
			$ext=explode('.',$c_name_file);
			foreach ($ext as &$value) {
				$value=strtolower($value);
				if(in_array($value,$blacklist)) {$blacklistedfile=1;} 
			}
			if(!$blacklistedfile)
			{
				
				echo '['.$mailbox.'] [mail '.$count.'] Attachment: <span style="color:green">'.$c_name_file.'</span><br />';
				$dispo=$tabAttachment->disposition;
				echo '['.$mailbox.'] [mail '.$count.'] Disposition: <span style="color:green">'.$dispo.'</span><br />';
				
				$qry=$db->prepare("SELECT `img1`,`img2`,`img3`,`img4`,`img5` FROM `tincidents` WHERE id=:id");
				$qry->execute(array('id' => $c_ticket_number));
				$row=$qry->fetch();
				$qry->closeCursor();
				
				//find the first free slot else not display attach input
				if ($row['img1']=="") {$freeslot="img1";}
				else if ($row['img2']=="") {$freeslot="img2";}
				else if ($row['img3']=="") {$freeslot="img3";}
				else if ($row['img4']=="") {$freeslot="img4";}
				else if ($row['img5']=="") {$freeslot="img5";}

				if(isset($freeslot)){
					echo '['.$mailbox.'] [mail '.$count.'] Freeslot selected: <span style="color:green">'.$freeslot.'</span><br />';
					$db_file_name=strip_tags($db->quote($c_name_file));
					$db->query("UPDATE tincidents SET $freeslot=$db_file_name WHERE id='$c_ticket_number'");
				} else {
					echo '['.$mailbox.'] [mail '.$count.'] Freeslot selected: <span style="color:red">no free slot found</span><br />';
				}
			} else {
				echo '['.$mailbox.'] [mail '.$count.'] Blacklisted file: <span style="color:red">'.$c_name_file.'</span><br />';
			}
		}
		/*
		echo "DEBUG";
		echo "tabAttachment->filePath";
		echo $tabAttachment->filePath;
		echo "<br>";
		echo "c_name_dir_ticket=$c_name_dir_ticket c_name_file=$c_name_file<br />";
		*/
		rename($tabAttachment->filePath,$c_name_dir_ticket.'/'.$c_name_file); 
		//echo $tabAttachment->filePath;
	}
	return $mail->replaceInternalLinks('upload/'.$c_ticket_number);
}

//initialize counter
$count=0;

//connexion script with database parameters
require "connect.php";

//switch SQL MODE to allow empty values with lastest version of MySQL
$db->exec('SET sql_mode = ""');

//load parameters table
$qry=$db->prepare("SELECT * FROM `tparameters`");
$qry->execute();
$rparameters=$qry->fetch();
$qry->closeCursor();

//display error parameter
if ($rparameters['debug']==1) {
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', 'Off');
	error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

//case to certificat failure  
if($rparameters['imap_ssl_check']==0) {$ssl_check='/novalidate-cert';} else {$ssl_check='';}

//hostname building
$hostname = '{'.$rparameters['imap_server'].':'.$rparameters['imap_port'].''.$ssl_check.'}'.$rparameters['imap_inbox'].'';

//connect to in-box
$c_name_dir_upload =  __DIR__.'/upload/';

if($rparameters['imap_server'])
{
	echo 'IMAP server : <span style="color:green">'.$rparameters['imap_server'].'</span><br />';
} else {
	echo 'IMAP server : <span style="color:red">No IMAP server detected</span><br /><br />';
}
if($rparameters['imap_port'])
{
	echo 'IMAP port : <span style="color:green">'.$rparameters['imap_port'].'</span><br />';
} else {
	echo 'IMAP port : <span style="color:red">No IMAP port detected</span><br /><br />';
}

echo 'IMAP connection string : <span style="color:green">'.$hostname.'</span><br />';

//define mailbox to check
$mailboxes=array();
if ($rparameters['imap_mailbox_service']==1)
{
	array_push($mailboxes, $rparameters['imap_user']);
	$qry=$db->prepare("SELECT `id`,`mail`,`password`,`service_id` FROM `tparameters_imap_multi_mailbox`");
	$qry->execute();
	while($row=$qry->fetch()) 
	{
		array_push($mailboxes, $row['mail']);		
	}
	$qry->closeCursor();
	echo 'IMAP connector mode : <span style="color:green">MULTI</span><br /><br />';
} else {
	array_push($mailboxes, $rparameters['imap_user']);
	echo 'IMAP connector mode : <span style="color:green">SINGLE</span><br /><br />';
	if(preg_match('/gs_en/',$rparameters['imap_password'])) {$rparameters['imap_password']=gs_crypt($rparameters['imap_password'], 'd' , $rparameters['server_private_key']);}
	$mailbox_password=$rparameters['imap_password'];
}

foreach ($mailboxes as $mailbox)
{
	if ($rparameters['imap_mailbox_service']==1) {
		$qry=$db->prepare("SELECT `password` FROM `tparameters_imap_multi_mailbox` WHERE mail=:mail");
		$qry->execute(array('mail' => $mailbox));
		$row=$qry->fetch();
		$qry->closeCursor();
		
		if (!$row['password']) {
			if(preg_match('/gs_en/',$rparameters['imap_password'])) {$rparameters['imap_password']=gs_crypt($rparameters['imap_password'], 'd' , $rparameters['server_private_key']);}
			$mailbox_password=$rparameters['imap_password'];
		} else {
			if(preg_match('/gs_en/',$row['password'])) {$row['password']=gs_crypt($row['password'], 'd' , $rparameters['server_private_key']);}
			$mailbox_password=$row['password'];
		}
	}
	
	//connect to mailbox
	$con_mailbox = new PhpImap\Mailbox($hostname, $mailbox, $mailbox_password,$c_name_dir_upload) or die(T_('Impossible de se connecter au serveur de Messagerie: ') . imap_last_error());
	if (!$con_mailbox || $rparameters['imap']==0) {
		echo '['.$mailbox.'] Connection to mailbox : <span style="color:red">KO</span><br />';
	} else {
		//check mail in mailbox
		$mailsIds = $con_mailbox ->searchMailBox('ALL');
		if(!$mailsIds) {
			echo '['.$mailbox.'] Detect mail in mailbox : <span style="color:orange">KO</span><br />';
		} else {
			echo '['.$mailbox.'] Detect mail in mailbox : <span style="color:green">OK</span><br />';
		}
		
		//treatment for all mail inside mailbox
		$seen=0;
		$tab_MailsInfos =  $con_mailbox ->getMailsInfo($mailsIds);		
		foreach ($tab_MailsInfos as $tab_MailsInfo){
			if($tab_MailsInfo->seen==0)
			{
				$seen=1;
				$count=$count+1;
				$mail = $con_mailbox ->getMail($tab_MailsInfo->uid);
				$from = $mail->fromAddress;
				$subject = $mail->subject;
				if($rparameters['server_timezone']){date_default_timezone_set($rparameters['server_timezone']); $datetime = date('Y-m-d H:i:s');} else {$datetime = $mail->date;}
				$blacklist_mail=0;
				if(!$subject){$subject=T_('(Sans objet)');} //default subject 
				//detect blacklist mail or domain for exclusion
				if($rparameters['imap_blacklist']!='')
				{
					$mail_blacklist=explode(';',$rparameters['imap_blacklist']);
					foreach ($mail_blacklist as $value) {
						//check if each blacklist value exit in source mail as sender
						if(preg_match("/$value/i", $from) && $value){$blacklist_mail=1;}
					}
				}
				if($blacklist_mail==1) {
					echo '['.$mailbox.'] [mail '.$count.'] Import mail "'.$subject.'": <span style="color:red">KO (blacklist detected on '.$from.')</span><br />';
				} 
				else
				{
					//check if mail is HTML
					if($mail->textHtml == NULL){
						$contentype='textPlain';
						$message = nl2br($mail->textPlain);
						$description = nl2br($mail->textPlain);
					}else{
						$contentype='textHtml';
						$message = $mail->textHtml;
						$description = $mail->textHtml;
					}
					
					//special char convert
					$subject = str_replace('_', ' ', $subject);

					//find gestsup userid from mail address
					$qry=$db->prepare("SELECT `id` FROM `tusers` WHERE mail=:mail AND disable=:disable");
					$qry->execute(array('mail' => $from,'disable' => 0));
					$row=$qry->fetch();
					$qry->closeCursor();
					if($row['id'])
					{
						$user_id=$row['id'];
						$c_FromMessage='';
					} else {
						$user_id='0';
						$c_FromMessage='De '.$from.':<br />';
					}
					
					//detect ticket number in subject to update an existing ticket
					$c_reg = "/n°(.*?):/i"; //regex for extract ticket number
					preg_match($c_reg, $subject, $matches); // extract ticket number
					@$find_ticket_number = $matches[1];
					$find_ticket_number=str_replace(' ','',$find_ticket_number);
					if ($find_ticket_number!="")
					{
						//get attachement and image 
						if($contentype=='textHtml') { 
							$message = (isset($c_FromMessage)?$c_FromMessage:'').func_attachement($find_ticket_number,$c_name_dir_upload,$mail,$db,$mailbox,$count,$contentype);
						} else { //case plain text with attachment
							(isset($c_FromMessage)?$c_FromMessage:'').func_attachement($find_ticket_number,$c_name_dir_upload,$mail,$db,$mailbox,$count,$contentype);
						}
						//delete ticket part from mail to keep only answer
						$end_tag='---- '.T_('Repondre au dessus du ticket').' ----';
						$start_tag='---- '.T_('Repondre au dessus de cette ligne').' ----';
						$end_mail=explode($end_tag,$message);
						$end_mail=$end_mail[1];
						$start_mail=explode($start_tag,$message);
						$start_mail=$start_mail[0];
						$message=$start_mail.$end_mail;	
						
						//sanitize HTML code
						$message=str_replace('text-decoration:underline;','',$message);
						$message=preg_replace('/(<(style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $message); //remove style in outlook client
						$message=preg_replace('/(<(base)\b[^>]*>)/is', "", $message); //remove base link
						if (!preg_match("/<HTML/i",$message)){$message=strip_tags($message,'<p><a><span><br><div>');}
						
						//insert thread in ticket
						$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`) VALUES (:ticket,:date,:author,:text)");
						$qry->execute(array(
							'ticket' => $find_ticket_number,
							'date' => $datetime,
							'author' => $user_id,
							'text' => $message
							));
						
						echo '['.$mailbox.']  [mail '.$count.'] Import mail "'.$subject.'": <span style="color:green">OK</span><br />';
						if($rparameters['debug']==1) 
						{
							echo '['.$mailbox.'] [mail '.$count.'] Update ticket: <span style="color:green">OK (ID=<a href="index.php?page=ticket&id='.$find_ticket_number.'" target="_blank\" >'.$find_ticket_number.'</a>)</span><br />';
							echo '['.$mailbox.'] [mail '.$count.'] Content type detected: <span style="color:green">'.$contentype.'</span><br />';
						}
						//update unread state
						$qry=$db->prepare("UPDATE `tincidents` SET `techread`=:techread WHERE `id`=:id");
						$qry->execute(array(
							'techread' => 0,
							'id' => $find_ticket_number
							));
						
					} else {
						//create ticket
						$qry=$db->prepare("INSERT INTO `tincidents` (`user`,`technician`,`title`,`description`,`date_create`,`techread`,`state`,`criticality`,`disable`,`place`,`creator`) 
						VALUES (:user,:technician,:title,:description,:date_create,:techread,:state,:criticality,:disable,:place,:creator)");
						$qry->execute(array(
							'user' => $user_id,
							'technician' => 0,
							'title' => $subject,
							'description' => '',
							'date_create' => $datetime,
							'techread' => 0,
							'state' => $rparameters['ticket_default_state'],
							'criticality' => 4,
							'disable' => 0,
							'place' => 0,
							'creator' => $user_id
							));
						
						//get ticket number
						$c_ticket_number = $db->lastInsertId();
						
						//insert threads
						$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`type`) VALUES (:ticket,:date,'6')");
						$qry->execute(array('ticket' => $c_ticket_number,'date' => $datetime));
						
						//check if current mailbox is attached with service
						if ($rparameters['imap_mailbox_service']==1)
						{
							//get service id for current mailbox
							$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE id IN (SELECT service_id FROM `tparameters_imap_multi_mailbox` WHERE mail=:mail)");
							$qry->execute(array('mail' => $mailbox));
							$row=$qry->fetch();
							$qry->closeCursor();

							if($row['id']) {
								echo '['.$mailbox.'] [mail '.$count.'] Service associate with this mailbox: <span style="color:green">'.$row['name'].' ('.$row['id'].')</span><br />';
								$qry=$db->prepare("UPDATE `tincidents` SET `u_service`=:u_service WHERE `id`=:id");
								$qry->execute(array(
									'u_service' => $row['id'],
									'id' => $c_ticket_number
									));
							} else {
								echo '['.$mailbox.'] [mail '.$count.'] Service associate with this mailbox: <span style="color:red">None</span><br />';
							}
						}
						
						echo '['.$mailbox.'] [mail '.$count.'] Import mail "'.$subject.'": <span style="color:green">OK</span><br />';
						if($rparameters['debug']==1) 
						{
							echo '['.$mailbox.'] [mail '.$count.'] Create new ticket: <span style="color:green">OK (ID=<a href="index.php?page=ticket&id='.$c_ticket_number.'" target="_blank\" >'.$c_ticket_number.'</a>)</span><br />';
							echo '['.$mailbox.'] [mail '.$count.'] Content type detected: <span style="color:green">'.$contentype.'</span><br />';
						}
						//get attachement and images from mail
						$message = (isset($c_FromMessage)?$c_FromMessage:'').func_attachement($c_ticket_number,$c_name_dir_upload,$mail,$db,$mailbox,$count,$contentype);
						
						if($contentype=='textPlain')
						{
							if(isset($c_FromMessage)) {$description=$c_FromMessage.$description;}
							$qry=$db->prepare("UPDATE `tincidents` SET `description`=:description WHERE `id`=:id");
							$qry->execute(array(
								'description' => $description,
								'id' => $c_ticket_number
								));
						}
						else //html case
						{
							//remove outlook string to avoid underline application problem
							$message=str_replace("text-decoration:underline;", "", $message);
							$message=preg_replace('/(<(base)\b[^>]*>)/is', "", $message); //remove base link
							$qry=$db->prepare("UPDATE `tincidents` SET `description`=:description WHERE `id`=:id");
							$qry->execute(array(
								'description' => $message,
								'id' => $c_ticket_number
								));
							
						}
						
						//send mail to user 
						if($rparameters['mail_auto_user_newticket'])
						{
							$send=1;
							$_GET['id']=$c_ticket_number;
							include('core/mail.php');
							echo '['.$mailbox.'] [mail '.$count.'] SEND Mail to sender: <span style="color:green">OK (mail_auto_user_newticket parameter enable)</span><br />';
						}
					}
					//post treatment actions
					if ($rparameters['imap_post_treatment']=='move' && $rparameters['imap_post_treatment_folder']!='')
					{
						//move mail
						$con_mailbox->moveMail($tab_MailsInfo->uid,$rparameters['imap_post_treatment_folder']);
						echo '['.$mailbox.'] [mail '.$count.'] Post-treatment action: <span style="color:green">MOVE ('.$rparameters['imap_post_treatment_folder'].' folder)</span><br />';
					}elseif ($rparameters['imap_post_treatment']=='delete')
					{
						//delete mail
						imap_delete($con_mailbox->getImapStream(),$tab_MailsInfo->uid,FT_UID);
						echo '['.$mailbox.'] [mail '.$count.'] Post-treatment action: <span style="color:green">DELETE</span><br />';
					} else {
						//unread mail
						echo '['.$mailbox.'] [mail '.$count.'] Post-treatment action: <span style="color:green">UNREAD</span><br />';
					}
				} //END for each no blacklist mail
			} //END for each unread mail 
		} //END for each mail
		if($seen==0) {echo '['.$mailbox.'] Check new mail: <span style="color:green">No new mail detected</span><br />';}
	} //END for each mailbox
echo "<br />";
sleep(1); //timeout 1 seconds to limit network trafic
}
echo "Total $count mail received</b><br />";
?>