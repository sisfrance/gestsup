<?php
################################################################################
# @Name : ticket_useradd.php
# @Description : dd and modify user
# @Call : ./ticket.php
# @Parameters :  
# @Author : Flox
# @Create : 07/03/2014
# @Update : 24/10/2018
# @Version : 3.1.36
################################################################################

//initialize variables 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_GET['edituserid'])) $_GET['edituserid'] = ''; 
if(!isset($_POST['adduser'])) $_POST['adduser'] = ''; 
if(!isset($_POST['add'])) $_POST['add'] = ''; 
if(!isset($_POST['modifyuser'])) $_POST['modifyuser'] = ''; 
if(!isset($_POST['firstname'])) $_POST['firstname'] = ''; 
if(!isset($_POST['lastname'])) $_POST['lastname'] = ''; 
if(!isset($_POST['phone'])) $_POST['phone'] = ''; 
if(!isset($_POST['mobile'])) $_POST['mobile'] = ''; 
if(!isset($_POST['usermail'])) $_POST['usermail'] = ''; 
if(!isset($_POST['company'])) $_POST['company'] = ''; 

//secure text string and remove special char
$_POST['firstname']=strip_tags($_POST['firstname']);
$_POST['lastname']=strip_tags($_POST['lastname']);
$_POST['phone']=strip_tags($_POST['phone']);
$_POST['mobile']=strip_tags($_POST['mobile']);
$_POST['usermail']=strip_tags($_POST['usermail']);
$_POST['company']=strip_tags($_POST['company']);

if($_POST['add'] && $rright['ticket_user_actions']!=0) //add user
{
	$qry=$db->prepare("INSERT INTO `tusers` (`profile`,`firstname`,`lastname`,`phone`,`mobile`,`mail`,`company`) VALUES (2,:firstname,:lastname,:phone,:mobile,:mail,:company)");
	$qry->execute(array(
		'firstname' => $_POST['firstname'],
		'lastname' => $_POST['lastname'],
		'phone' => $_POST['phone'],
		'mobile' => $_POST['mobile'],
		'mail' => $_POST['usermail'],
		'company' => $_POST['company']
		));
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';

}elseif($_POST['modifyuser'] && $rright['ticket_user_actions']!=0) //modify user
{
	$qry=$db->prepare("
	UPDATE `tusers` SET
	`firstname`=:firstname, 
	`lastname`=:lastname, 
	`phone`=:phone, 
	`mobile`=:mobile, 
	`mail`=:mail, 
	`company`=:company
	WHERE `id`=:id
	");
	$qry->execute(array(
		'firstname' => $_POST['firstname'],
		'lastname' => $_POST['lastname'],
		'phone' => $_POST['phone'],
		'mobile' => $_POST['mobile'],
		'mail' => $_POST['usermail'],
		'company' => $_POST['company'],
		'id' => $_GET['edituser']
		));

	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
//case for create new user
if ($_GET['action']=="adduser" && $rright['ticket_user_actions']!=0)
{
	$boxtitle='<i class=\'icon-user blue bigger-120\'></i> '.T_('Ajouter un nouvel utilisateur');
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input  name="add" type="hidden" value="1">
		<label for="firstname">'.T_('Prénom').':</label> 
		<input name="firstname" type="text" size="26">
		<br />
		<label for="lastname">'.T_('Nom').':</label> 
		<input name="lastname" type="text" size="26">
		<br />
		<label for="phone">'.T_('Tel. fixe').':</label> 
		<br />
		<input name="phone" type="text" size="26">
		<br />
		<label for="mobile">'.T_('Tel. portable').':</label> 
		<br />
		<input name="mobile" type="text" size="26">
		<br />
		<label for="usermail">'.T_('Mail').':</label> 
		<br />
		<input name="usermail" type="text" value="" size="26">';
		//display advanced user informations
		if ($rparameters['user_advanced']!=0)
		{
		    $boxtext=$boxtext.'
		    <label for="company">'.T_('Société').':</label><br />
		    <select id="company" style="width:200px;" name="company">';
			
			$qry=$db->prepare("SELECT `id`,`name` FROM `tcompany` ORDER BY name ASC");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				//translate none state
				if ($row['id']==0)
				{
					$boxtext.='<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
				} else {
					$boxtext.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
				}
			}
			$qry->closeCursor();
    	  
			$boxtext= $boxtext.'
			</select>
            <a target="blank" href="./index.php?page=admin&subpage=list&table=tcompany&action=disp_add"> <i class="icon-plus-sign green bigger-130" title="'.T_('Ajouter une société').'" ></i></a>';
		}
		$boxtext=$boxtext.'
		<br /><br />
		<a target="blank" href="./index.php?page=admin&subpage=user&action=add">'.T_('Plus de champs').'...</a>
		<br />		
	</form>
	';
	$valid=T_('Ajouter');
	$action1="$('form#form').submit();";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
elseif($rright['ticket_user_actions']!=0) //case for modify an existing user
{
	$boxtitle='<i class=\'icon-user blue bigger-120\'></i> '.T_('Modification d\'un utilisateur');
	//get user data
	$qry=$db->prepare("SELECT `firstname`,`lastname`,`phone`,`mobile`,`mail`,`company` FROM `tusers` WHERE id=:id");
	$qry->execute(array('id' => $_GET['edituser']));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input  name="modifyuser" type="hidden" value="1">
		<label>'.T_('Prénom').':</label> 
		<input name="firstname" type="text" size="26" value="'.$row['firstname'].'">
		<br />
		<label>'.T_('Nom').':</label> 
		<input name="lastname" type="text" size="26" value="'.$row['lastname'].'">
		<br />
		<label>'.T_('Tel. fixe').':</label> 
		<br />
		<input name="phone" type="text" size="26" value="'.$row['phone'].'">
		<br />
		<label>'.T_('Tel. mobile').':</label> 
		<br />
		<input name="mobile" type="text" size="26" value="'.$row['mobile'].'">
		<br />
		<label>'.T_('Mail').':</label> 
		<br />
		<input name="usermail" type="text" size="26" value="'.$row['mail'].'" >
		';
		//display advanced user informations
		if ($rparameters['user_advanced']!=0)
		{
		    $boxtext=$boxtext.'
		    <label for="company">'.T_('Société').':</label><br />
		    <select id="company" style="width:200px;" name="company">';
			
			$qry2=$db->prepare("SELECT `id`,`name` FROM `tcompany` ORDER BY name ASC");
			$qry2->execute();
			while($row2=$qry2->fetch()) 
			{
				$qry3=$db->prepare("SELECT `id` FROM `tcompany` WHERE id LIKE :id");
				$qry3->execute(array('id' => $row['company']));
				$row3=$qry3->fetch();
				$qry3->closeCursor();
				
				if ($row3['id']==$row2['id']) {$selected='selected';} else {$selected='';}
				//translate non state
				if ($row2['id']==0)
				{
					$boxtext.='<option value="'.$row2['id'].'" '.$selected.'>'.T_($row2['name']).'</option>';
				} else {
					$boxtext.='<option value="'.$row2['id'].'" '.$selected.'>'.$row2['name'].'</option>';
				}
			}
			$qry2->closeCursor();
    	   
        	$boxtext= $boxtext.'</select>
        	<a target="blank" href="./index.php?page=admin&subpage=list&table=tcompany&action=disp_add"> <i class="icon-plus-sign green bigger-130" title="'.T_('Ajouter une société').'" ></i></a>
            ';
		}
		$boxtext=$boxtext.'
		<br /><br />
		<a target="blank" href="./index.php?page=admin&subpage=user&action=edit&userid='.$_GET['edituser'].'">'.T_('Plus de champs').'...</a>
		<br />		
	</form>
	';
	$valid=T_('Modifier');
	$action1="$('form#form').submit();
	$( this ).dialog( \"close\" );";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
include "./modalbox.php"; 
?>