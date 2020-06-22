<?php
/*
Author: Flox
Filename: /core/message.php
Description: page to send mail
Parameters: $from, $to, $message, $object
call: newticket_u.php, 
Version: 1.0
creation date: 21/11/2012
Last update: 21/11/2012
*/

// initialize variables 

require("components/PHPMailer_v5.1/class.phpmailer.php"); 
$mail = new PHPmailer();
$mail->CharSet = 'UTF-8'; //ISO-8859-1 possible if characters problems
$mail->IsSMTP();
$mail->Host = "$rparameters[mail_smtp]";
$mail->SMTPAuth = $rparameters['mail_auth'];
if ($rparameters['mail_secure']=='465') $mail->SMTPSecure = 'ssl';
if ($rparameters['mail_secure']=='587') $mail->SMTPSecure = 'tls';
if ($rparameters['mail_secure']=='465') $mail->Port = 465;
if ($rparameters['mail_secure']=='587') $mail->Port = 587;
$mail->Username = "$rparameters[mail_username]";
$mail->Password = "$rparameters[mail_password]";
$mail->IsHTML(true); // Envoi en html
	 
$mail->From = "$from";
$mail->FromName = "$from";
$mail->AddAddress("$to");
$mail->AddReplyTo("$from");
$mail->Subject = "$object";
$mail->Body = "$message";
if (!$mail->Send())
{
	echo '<div id="erreur"><img src="./images/access.png" alt="erreur" style="border-style: none" alt="img" />';
	echo $mail->ErrorInfo;
	echo '</div>';
}
$mail->SmtpClose();
?>