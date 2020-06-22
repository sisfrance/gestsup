<?php
################################################################################
# @Name : /core/message.php
# @Desc : page to send mail
# @call : /core/ticket.php 
# @parameters : $from, $to, $message, $object
# @Author : Flox
# @Create : 21/11/2012
# @Update : 08/04/2014
# @Version : 3.0.8
################################################################################

require("components/PHPMailer_v5.1/class.phpmailer.php"); 
$mail = new PHPmailer();
$mail->CharSet = 'UTF-8'; //ISO-8859-1 possible if characters problems
$mail->isSendMail(); //$mail->isSendMail(); works for 1&1
$mail->Host = "$rparameters[mail_smtp]";
$mail->SMTPAuth = $rparameters['mail_auth'];
if ($rparameters['debug']==1) $mail->SMTPDebug = 2;
if ($rparameters['mail_secure']!=0) $mail->SMTPSecure = $rparameters['mail_secure'];
if ($rparameters['mail_port']!=25) $mail->Port = $rparameters['mail_port'];
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