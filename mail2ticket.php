<?php
################################################################################
# @Name : mail2ticket.php
# @Desc : convert mail in ticket
# @call : /index_auth.php
# @paramters : 
# @Autor : Flox
# @Create : 07/04/2013
# @Update : 16/07/2014
# @Version : 3.0.10
################################################################################

//initialize counter
$count=0;

//connexion script with database parameters
require "connect.php";

//define current time
$datetime = date("Y-m-d H:i:s");

//load parameters table
$qparameters = mysql_query("SELECT * FROM `tparameters`"); 
$rparameters= mysql_fetch_array($qparameters);

//hostname building
$hostname = '{'.$rparameters['imap_server'].':'.$rparameters['imap_port'].'}'.$rparameters['imap_inbox'].'';

//connect to inbox

//default case
$inbox = imap_open($hostname,$rparameters['imap_user'],$rparameters['imap_password']) or die('Impossible de se connecter au serveur de Messagerie: ' . imap_last_error());

// Exchange 2010
//$inbox = imap_open('{'.$rparameters['imap_server'].':'.$rparameters['imap_port'].'/pop3/novalidate-cert}'.$rparameters['imap_inbox'].'', $rparameters['imap_user'], $rparameters['imap_password'],NULL,1,array('DISABLE_AUTHENTICATOR'=>'GSSAPI')) or die('Impossible de se connecter au serveur de Messagerie: ' . imap_last_error());


//display header
echo'
<html lang="fr">
	<head>
		<meta charset="UTF-8" />
	</head>
';

if($inbox) 
{ 
	//grab mail
	echo "<u></u>Connexion à la boite au lettre en cours: <font color=green>ok</font><br /><br />";
	$emails = imap_search($inbox,'ALL');
	 //if emails are returned
    if($emails) {
		//for every email...
		foreach($emails as $email_number) {
			//get information specific to this email
			$overview = imap_fetch_overview($inbox, $email_number, 0);
			$seen=$overview[0]->seen;
			//if message is not read
			if ($seen==0)
			{
				$count=$count+1;
				//get mail data
				$message = imap_fetchbody($inbox, $email_number, 1);
				$header = imap_headerinfo($inbox, $email_number);
				$subject = $overview[0] -> subject;
				$from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
				
				//special char convert
				$message=quoted_printable_decode($message);
				$subject=mb_decode_mimeheader($subject);
				$subject = str_replace('_', ' ', $subject);
				
				//Escape special char to SQL query
				$message=mysql_real_escape_string($message);
				$subject=mysql_real_escape_string($subject);
			
				//find gestsup userid from mail address
				$query= mysql_query("SELECT id FROM `tusers` where mail='$from' ");
				$row=mysql_fetch_array($query);
				if($row[0])
				{
					$user_id=$row[0];
				} else {
					$user_id='';
					$message='De '.$from.':\n'.$message;	
				}
				
				// create ticket
				$query= "INSERT INTO tincidents 
				(user,technician,title,description,date_create,techread,state,criticality,disable) 
				VALUES
				('$user_id','0','$subject', '$message','$datetime','0','5','4','0')";
				$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
				
				echo "&nbsp> Import du message $count: $subject: <font color=green>ok</font><br />";
			}
		}
	}
	imap_close($inbox);
	echo "<br />Total: Récupération de $count messages depuis <b>$rparameters[imap_server]</b> depuis le port <b>$rparameters[imap_port]</b><br />";

} else {
	echo "Erreur de connexion IMAP";
}
echo '</html>';
?>