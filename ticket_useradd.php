<?php
################################################################################
# @Name : newticket_useradd.php
# @Desc : dd and modify user
# @call : ./ticket.php
# @parameters :  
# @Author : Flox
# @Update : 07/03/2014
# @Version : 3.0.8
################################################################################

//initialize variables 
if(!isset($_GET ['id'])) $_GET['id'] = ''; 
if(!isset($_GET ['edituserid'])) $_GET['edituserid'] = ''; 
if(!isset($_POST['adduser'])) $_POST['adduser'] = ''; 
if(!isset($_POST['add'])) $_POST['add'] = ''; 
if(!isset($_POST['modifyuser'])) $_POST['modifyuser'] = ''; 
if(!isset($_POST['firstname'])) $_POST['firstname'] = ''; 
if(!isset($_POST['lastname'])) $_POST['lastname'] = ''; 
if(!isset($_POST['usermail'])) $_POST['usermail'] = ''; 
if(!isset($_POST['company'])) $_POST['company'] = ''; 

//special char rename
$_POST['firstname'] = mysql_real_escape_string($_POST['firstname']);
$_POST['lastname'] = mysql_real_escape_string($_POST['lastname']);

//secure code injection
$_POST['firstname']=strip_tags($_POST['firstname']);
$_POST['lastname']=strip_tags($_POST['lastname']);

//submit actions
if($_POST['add'])
{
	$requete = "INSERT INTO tusers (profile,firstname,lastname,phone,mail,company) VALUES ('2','$_POST[firstname]','$_POST[lastname]','$_POST[phone]','$_POST[usermail]','$_POST[company]')";
	$execution = mysql_query($requete);
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';

}
if($_POST['modifyuser'])
{
	$requete = "UPDATE tusers SET lastname='$_POST[lastname]', phone='$_POST[phone]', mail='$_POST[usermail]', firstname='$_POST[firstname]', company='$_POST[company]' where id like '$_GET[edituser]'";
	$execution = mysql_query($requete);
	
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
//case for create new user
if ($_GET['action']=="adduser")
{
	$boxtitle="<i class='icon-user blue bigger-120'></i> Ajouter un nouvel utilisateur";
	$boxtext= "
	<form name=\"form\" method=\"POST\" action=\"\" id=\"form\">
		<input  name=\"add\" type=\"hidden\" value=\"1\">
		<label >Prénom:</label> 
		<input  name=\"firstname\" type=\"text\" size=\"26\">
		<br />
		<label for=\"name\">Nom:</label> 
		<input  name=\"lastname\" type=\"text\" size=\"26\">
		<br />
		<label for=\"phone\">Tel:</label> 
		<input  name=\"phone\" type=\"text\" size=\"26\">
		<br />
		<label for=\"usermail\">Mail:</label> 
		<input  name=\"usermail\" type=\"text\" value=\"\" size=\"26\">";
		//display advanced user informations
		if ($rparameters['user_advanced']!=0)
		{
		    $boxtext=$boxtext."
		    <label for=\"company\">Société:</label><br />
		    <select id=\"company\" name=\"company\">";
    	    $qcompany= mysql_query("SELECT * FROM `tcompany` ORDER BY name ASC");
			while ($rcompany=mysql_fetch_array($qcompany)) {$boxtext= $boxtext.'<option value="'.$rcompany['id'].'">'.$rcompany['name'].'</option>';} 
        	$boxtext= $boxtext.'</select>
            <a target="blank" href="./index.php?page=admin&subpage=list&table=tcompany&action=disp_add"> <i class="icon-plus-sign green bigger-130" title="Ajouter une société" ></i></a>
        	';
		}
		$boxtext=$boxtext."
		<br /><br />
		<a target=\"blank\" href=\"./index.php?page=admin&subpage=user&action=add\">Plus de champs...</a>
		<br />		
	</form>
	";
	$valid="Ajouter";
	$action1="$('form#form').submit();";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
}
else
{
	$boxtitle="<i class='icon-user blue bigger-120'></i> Modification d'un utilisateur";
	$query = mysql_query("SELECT * FROM tusers WHERE id LIKE '$_GET[edituser]'");
	$row=mysql_fetch_array($query);
	$boxtext= "
	<form name=\"form\" method=\"POST\" action=\"\" id=\"form\">
		<input  name=\"modifyuser\" type=\"hidden\" value=\"1\">
		<label>Prénom:</label> 
		<input  name=\"firstname\" type=\"text\" size=\"26\" value=\"$row[firstname]\">
		<br />
		<label>Nom:</label> 
		<input  name=\"lastname\" type=\"text\" size=\"26\" value=\"$row[lastname]\">
		<br />
		<label>Tel:</label> 
		<input  name=\"phone\" type=\"text\" size=\"26\" value=\"$row[phone]\">
		<br />
		<label>Mail:</label> 
		<input  name=\"usermail\" type=\"text\" size=\"26\" value=\"$row[mail]\" >";
		//display advanced user informations
		if ($rparameters['user_advanced']!=0)
		{
		    $boxtext=$boxtext."
		    <label for=\"company\">Société:</label><br />
		    <select id=\"company\" name=\"company\">";
    	    $qcompany= mysql_query("SELECT * FROM `tcompany` ORDER BY name ASC");
			while ($rcompany=mysql_fetch_array($qcompany)) {$boxtext= $boxtext.'<option value="'.$rcompany['id'].'">'.$rcompany['name'].'</option>';} 
			$query= mysql_query("SELECT * FROM `tcompany` WHERE id like '$row[company]'");
			$row=mysql_fetch_array($query);	
			$boxtext= $boxtext."<option value=\"$row[id]\" selected>$row[name]</option>";
        	$boxtext= $boxtext.'</select>
        	<a target="blank" href="./index.php?page=admin&subpage=list&table=tcompany&action=disp_add"> <i class="icon-plus-sign green bigger-130" title="Ajouter une société" ></i></a>
            ';
		}
		$boxtext=$boxtext."
		<br /><br />
		<a target=\"blank\" href=\"./index.php?page=admin&subpage=user&action=edit&id=$_GET[edituser]\">Plus de champs...</a>
		<br />		
	</form>
	";
	$valid="Modifier";
	$action1="$('form#form').submit();";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
}
include "./modalbox.php"; 
?>