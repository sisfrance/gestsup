<?php
################################################################################
# @Name : /core/auto_mail.php
# @Desc : page to send automail
# @call : ticket.php, newticket.php
# @paramters : ticket id destinataires
# @Autor : Flox
# @Update : 23/01/2014
# @Version : 3.0.9
################################################################################

//initialize variables 
if(!isset($send)) $send = ''; 

//check if open mail have already sent
$query = mysql_query("SELECT * FROM tmails WHERE incident='$_GET[id]'");
$row = mysql_fetch_array($query);

//case for send open mail
if ($row[0]=='')
{
	//auto send open notification mail
	$send=1;
	include('./core/mail.php');
	//insert mail table
	$query= "INSERT INTO tmails (incident,open,close) VALUES ('$_GET[id]','1','0')";
	$exec = mysql_query($query);
}
//case for close close mail
if ($_POST['state']=='3')
{
	if ($row['open']=='1')
	{
		//check if is the first close mail
		if ($row['close']=='0')
		{
			$send=1;
			//auto send close notification mail
			include('./core/mail.php');
			//update mail table
			$query= "UPDATE tmails SET close='1' WHERE incident='$_GET[id]'";
			$exec = mysql_query($query);
		} else {
			//close mail already sent
		}
	} else {
		//close not sent because no open mail was sent
	}
}	
?>