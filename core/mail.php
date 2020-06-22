<?php
################################################################################
# @Name : /core/mail.php
# @Desc : page to send mail
# @Call : /preview_mail.php, /core_automail.php
# @Parameters : ticket id destinataires
# @Autor : Flox
# @Update : 15/07/2014
# @Version : 3.0.10
################################################################################

//initialize variables 
if(!isset($_POST['usercopy'])) $_POST['usercopy'] = '';
if(!isset($_POST['usercopy2'])) $_POST['usercopy2'] = '';
if(!isset($_POST['usercopy3'])) $_POST['usercopy3'] = '';
if(!isset($_POST['usercopy4'])) $_POST['usercopy4'] = '';
if(!isset($_POST['usercopy5'])) $_POST['usercopy5'] = '';
if(!isset($_POST['usercopy6'])) $_POST['usercopy6'] = '';
if(!isset($_POST['receiver'])) $_POST['receiver'] = ''; 
if(!isset($_POST['withattachment'])) $_POST['withattachment'] = ''; 
if(!isset($fname11)) $fname11 = '';
if(!isset($fname21)) $fname21 = '';
if(!isset($fname31)) $fname31 = '';
if(!isset($fname41)) $fname41 = '';
if(!isset($fname51)) $fname51 = '';
if(!isset($resolution)) $resolution = '';

//database queries to find values for create mail	
$globalquery = mysql_query("SELECT * FROM tincidents WHERE id LIKE '$_GET[id]'");
$globalrow=mysql_fetch_array($globalquery);

$userquery = mysql_query("SELECT * FROM tusers WHERE id LIKE '$globalrow[user]'");
$userrow=mysql_fetch_array($userquery);	
	
$techquery = mysql_query("SELECT * FROM tusers WHERE id LIKE '$globalrow[technician]'");
$techrow=mysql_fetch_array($techquery);
	
$creatorquery = mysql_query("SELECT * FROM tusers WHERE id LIKE '$_SESSION[user_id]'");
$creatorrow=mysql_fetch_array($creatorquery);
	
$querystate = mysql_query("SELECT name FROM tstates WHERE id LIKE '$globalrow[state]'");
$staterow=mysql_fetch_array($querystate);
	
$querycat = mysql_query("SELECT * FROM tcategory WHERE id LIKE '$globalrow[category]'");
$catrow=mysql_fetch_array($querycat);
	
$querysubcat = mysql_query("SELECT * FROM tsubcat WHERE id LIKE '$globalrow[subcat]'");
$subcatrow=mysql_fetch_array($querysubcat);

//generate resolution
$query = mysql_query("SELECT * FROM tthreads WHERE ticket='$_GET[id]' ORDER BY date");
while ($row=mysql_fetch_array($query)) 
{
	//remove display date from old post 
	$find_old=explode(" ", $row['date']);
	$find_old=$find_old[1];
	if ($find_old!='12:00:00') $date_thread=date_convert($row['date']); else  $date_thread='';
		
	if($row['type']==0)
	{
		//text backline format
		$text=nl2br($row['text']);
		
		//test if author is not the technician
		if ($row['author']!=$globalrow['technician'])
		{
			//find author name
			$qauthor = mysql_query("SELECT * FROM tusers WHERE id='$row[author]'");
			$rauthor=mysql_fetch_array($qauthor);
			$resolution="$resolution <b> $date_thread $rauthor[firstname] $rauthor[lastname]: </b><br /> $text  <hr />";
		} else {
			if ($date_thread!='')
			{
				$resolution="$resolution <b>$date_thread:</b><br />$text<hr />";
			} else {
				$resolution="$resolution  $text <hr />";
			}
		}
	} 
	if ($row['type']==1)
	{
		//tech name
		$qtech3=mysql_query("SELECT * FROM tusers WHERE id='$row[tech1]'");
		$rtech3=mysql_fetch_array($qtech3);
		$resolution="$resolution <b>$date_thread:</b> : Attribution de l'incident à $rtech3[firstname] $rtech3[lastname].<br /><br />";
	}
	if($row['type']==2)
	{
		//tech name
		$qtech1 = mysql_query("SELECT * FROM tusers WHERE id='$row[tech1]'");
		$rtech1=mysql_fetch_array($qtech1);
		$qtech2 = mysql_query("SELECT * FROM tusers WHERE id='$row[tech2]'");
		$rtech2=mysql_fetch_array($qtech2);
		$resolution="$resolution <b>$date_thread:</b> : Transfert du ticket de $rtech1[firstname] $rtech1[lastname] à $rtech2[firstname] $rtech2[lastname]. <br /><br />";
	}
	
}
	
$description = $globalrow['description'];
	
//dates conversions
$date_create = date_cnv("$globalrow[date_create]");
$date_hope = date_cnv("$globalrow[date_hope]");
$date_res = date_cnv("$globalrow[date_res]");
	
//mail object for states
$qobject = mysql_query("SELECT * FROM tstates WHERE id LIKE '$globalrow[state]'");
$robject=mysql_fetch_array($qobject);
$objet="$robject[mail_object] pour le ticket n°$_GET[id]: $globalrow[title]";

$destinataire="$userrow[mail]";

//check if unique sender mail adress exist else get creator mail adress
if($rparameters['mail_from_adr']==''){$emetteur=$creatorrow['mail'];} else {$emetteur=$rparameters['mail_from_adr'];}

//interger link parameter
if ($rparameters['mail_link']==1)
{
	$link=", ou consultez votre ticket sur ce lien: <a href=\"$rparameters[server_url]/index.php?page=ticket&id=$_GET[id]\">$rparameters[server_url]/index.php?page=ticket&id=$_GET[id]</a>";	
} else $link=".";
	
/*$msg="
	<html>
		<body>
			<font face=\"Arial\">
				<table  width=\"820px\" cellspacing=\"0\" cellpadding=\"10\">
					<tr bgcolor=\"$rparameters[mail_color_title]\" >
					  <th><font size=\"4\" color=\"FFFFFF\"> &nbsp; $objet &nbsp;</font></th>
					</tr>
					<tr bgcolor=\"$rparameters[mail_color_bg]\" >
					  <td>
						$rparameters[mail_txt]<br />
						<br />
						<table  border=\"1\" bordercolor=\"$rparameters[mail_color_title]\" cellspacing=\"0\"  cellpadding=\"5\">
							<tr>
								<td><font color=\"$rparameters[mail_color_text]\"><b>Titre:</b></b> $globalrow[title]</font></td>
								<td><font color=\"$rparameters[mail_color_text]\"><b>Catégorie:</b></b> $catrow[1] - $subcatrow[2]</td>
							</tr>
							<tr>
								<td width=\"400px\"><font color=\"$rparameters[mail_color_text]\"><b>Demandeur:</b></b> $userrow[lastname] $userrow[firstname]</font></td>
								<td width=\"400px\"><font color=\"$rparameters[mail_color_text]\"><b>Technicien en charge:</b> $techrow[lastname] $techrow[firstname]</font></td>
							</tr>
							<tr>
								<td><font color=\"$rparameters[mail_color_text]\"><b>Etat:</b> $staterow[0]</font></td>
								<td><font color=\"$rparameters[mail_color_text]\"><b>Date de la demande:</b> $date_create</font></td>	
							</tr> 
							<tr>
								<td colspan=\"2\"><font color=\"$rparameters[mail_color_text]\"><b>Description:</b><br /> $description</font></td>
								
							</tr>
							<tr>
								<td colspan=\"2\"><font color=\"$rparameters[mail_color_text]\"><b>Résolution:</b><br /> $resolution</font></td>
							</tr>
							<tr>
								<td width=\"400px\"><font color=\"$rparameters[mail_color_text]\"><b>Date estimée de résolution:</b></b> $date_hope</font></td>
								<td width=\"400px\"><font color=\"$rparameters[mail_color_text]\"><b>Date de résolution:</b> $date_res</font></td>
							</tr>
						</table>
						<br />
						<hr />
						Pour toutes informations complémentaires sur votre ticket, vous pouvez joindre $techrow[firstname] $techrow[lastname] au $techrow[phone]
						$link
					  </td>
					</tr>
				</table>
			</font>
		</body>
	</html>"."\r\n";*/
	
	$msg = "Bonjour,<br/>
		Vous trouverez dans ce mail un lien vers l'impression de l'intervention que nous avons effectuée dans vos locaux. Merci nous renvoyer la feuille signée à cette adresse E-mail: ticket.jcd54.fr ou par fax au: 03.83.44.16.32<br/>
		Si vous n'êtes pas connecté à l'interface lors de votre demande d'impression de ticket, le site vous redirigera vers la page de connexion. Il sera donc nécessaire de cliquer une nouvelle fois sur ce lien.<br/>
		Lien: <a target='_blank' href='http://ticket.jcd54.fr/ticket_print.php?id=".$_GET['id']."'>http://ticket.jcd54.fr/ticket_print.php?id=".$_GET['id']."</a><br/>
		Ticket n°".$_GET['id']."<br/>
		Cordialement JCD54.<br/>";

if ($send==1)
{
	include("components/PHPMailer_v5.1/class.phpmailer.php"); 
	$mail = new PHPmailer();
	$mail->CharSet = 'UTF-8'; //ISO-8859-1 possible if characters problems
	$mail->isSendMail(); //$mail->isSendMail(); works for 1&1
	if($rparameters['mail_secure']=='SSL') 
	{$mail->Host = "ssl://$rparameters[mail_smtp]";} 
	elseif($rparameters['mail_secure']=='TLS') 
	{$mail->Host = "tls://$rparameters[mail_smtp]";} 
	else 
	{$mail->Host = "$rparameters[mail_smtp]";}
	$mail->SMTPAuth = $rparameters['mail_auth'];
	if ($rparameters['debug']==1) $mail->SMTPDebug = 2;
	if ($rparameters['mail_secure']!=0) $mail->SMTPSecure = $rparameters['mail_secure'];
	if ($rparameters['mail_port']!=25) $mail->Port = $rparameters['mail_port'];
	$mail->Username = "$rparameters[mail_username]";
	$mail->Password = "$rparameters[mail_password]";
	$mail->IsHTML(true); 
	$mail->From = "$emetteur";
	$mail->FromName = "$rparameters[mail_from_name]";
	//generate adresse list
	if ($_POST['receiver']!='none') {
		if ($globalrow['u_group']!=0)
		{
			$qgroup = mysql_query("SELECT mail FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=$globalrow[u_group] AND tusers.disable=0");
			while ($row=mysql_fetch_array($qgroup)) $mail->AddAddress("$row[0]");
		} else $mail->AddAddress("$userrow[mail]");
	}
	$mail->AddReplyTo("$techrow[mail]");
	if ($rparameters['mail_cc']!='') {
		$addresses = explode(";",$rparameters['mail_cc']);
		foreach($addresses as $mailCC){
			$mail->AddCC("$mailCC");
		}
	}
	if ($_POST['usercopy']!='')
	{ 
		if(substr($_POST['usercopy'], 0, 1) =='G') 
		{
			$groupid= explode("_", $_POST['usercopy']);
			$qgroup = mysql_query("SELECT mail FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=$groupid[1] AND tusers.disable=0");
			while ($row=mysql_fetch_array($qgroup)) $mail->AddCC("$row[0]");
		} else $mail->AddCC("$_POST[usercopy]");
	}
	if ($_POST['usercopy2']!='')
	{ 
		if(substr($_POST['usercopy2'], 0, 1) =='G') 
		{
			$groupid= explode("_", $_POST['usercopy2']);
			$qgroup = mysql_query("SELECT mail FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=$groupid[1] AND tusers.disable=0");
			while ($row=mysql_fetch_array($qgroup)) $mail->AddCC("$row[0]");
		} else $mail->AddCC("$_POST[usercopy2]");
	}
	if ($_POST['usercopy3']!='')
	{ 
		if(substr($_POST['usercopy3'], 0, 1) =='G') 
		{
			$groupid= explode("_", $_POST['usercopy3']);
			$qgroup = mysql_query("SELECT mail FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=$groupid[1] AND tusers.disable=0");
			while ($row=mysql_fetch_array($qgroup)) $mail->AddCC("$row[0]");
		} else $mail->AddCC("$_POST[usercopy3]");
	}
	if ($_POST['usercopy4']!='')
	{ 
		if(substr($_POST['usercopy4'], 0, 1) =='G') 
		{
			$groupid= explode("_", $_POST['usercopy4']);
			$qgroup = mysql_query("SELECT mail FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=$groupid[1] AND tusers.disable=0");
			while ($row=mysql_fetch_array($qgroup)) $mail->AddCC("$row[0]");
		} else $mail->AddCC("$_POST[usercopy4]");
	}
	if ($_POST['usercopy5']!='')
	{ 
		if(substr($_POST['usercopy5'], 0, 1) =='G') 
		{
			$groupid= explode("_", $_POST['usercopy5']);
			$qgroup = mysql_query("SELECT mail FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=$groupid[1] AND tusers.disable=0");
			while ($row=mysql_fetch_array($qgroup)) $mail->AddCC("$row[0]");
		} else $mail->AddCC("$_POST[usercopy5]");
	}
	if ($_POST['usercopy6']!='')
	{ 
		if(substr($_POST['usercopy6'], 0, 1) =='G') 
		{
			$groupid= explode("_", $_POST['usercopy6']);
			$qgroup = mysql_query("SELECT mail FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=$groupid[1] AND tusers.disable=0");
			while ($row=mysql_fetch_array($qgroup)) $mail->AddCC("$row[0]");
		} else $mail->AddCC("$_POST[usercopy6]");
	}
    if ($_POST['withattachment']==1)
    {
    	if ($globalrow['img1']!='')$mail->AddAttachment("./upload/$_GET[id]/$globalrow[img1]");
    	if ($globalrow['img2']!='')$mail->AddAttachment("./upload/$_GET[id]/$globalrow[img2]");
    	if ($globalrow['img3']!='')$mail->AddAttachment("./upload/$_GET[id]/$globalrow[img3]");
    	if ($globalrow['img4']!='')$mail->AddAttachment("./upload/$_GET[id]/$globalrow[img4]");
    	if ($globalrow['img5']!='')$mail->AddAttachment("./upload/$_GET[id]/$globalrow[img5]");
    }
	$mail->Subject = "$objet";
	$mail->Body = "$msg";
	if (!$mail->Send()){
    	echo '<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i> <b>Message non envoyé, vérifier la configuration de votre serveur de messagerie.</b> (';
        	echo $mail->ErrorInfo;
    	echo ')</center></div>';
	}
	else {
		echo '<div class="alert alert-block alert-success"><center><i class="icon-envelope green"></i> Message envoyé.</center></div>';
		//redirect
		echo "
		<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
		window.location='./index.php?page=dashboard&&state=$_GET[state]&userid=$_GET[userid]'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
		</SCRIPT>
		";
	}
	$mail->SmtpClose();
}


///////Functions

// Date conversion
function date_cnv ($date) 
{return substr($date,8,2) . "/" . substr($date,5,2) . "/" . substr($date,0,4);}
?>