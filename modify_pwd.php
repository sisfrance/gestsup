<?php
################################################################################
# @Name : modify_pwd.php
# @Description : change password popup
# @Call : /dashboard.php
# @Parameters : 
# @Author : Flox
# @Create : 05/02/2012
# @Update : 03/07/2019
# @Version : 3.1.42
################################################################################

//initialize variables 
if(!isset($_POST['modifypwd'])) $_POST['modifypwd'] = ''; 
if(!isset($_POST['oldpwd'])) $_POST['oldpwd'] = ''; 
if(!isset($_POST['newpwd1'])) $_POST['newpwd1'] = ''; 
if(!isset($_POST['newpwd2'])) $_POST['newpwd2'] = ''; 
if(!isset($updated)) $updated = ''; 
if(!isset($oldpassword)) $oldpassword = ''; 
if(!isset($secure_password)) $secure_password = ''; 
if(!isset($boxtext)) $boxtext = ''; 
  
if($_POST['modifypwd'] && $_SESSION['user_id'])
{
	
	//get user informations
	$qry=$db->prepare("SELECT `salt`,`password` FROM `tusers` WHERE id=:id");
	$qry->execute(array('id' => $_SESSION['user_id']));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	//check old password
	$oldpassword=0;
	if(password_verify($_POST['oldpwd'],$row['password'])) {$oldpassword=1;}
		
	
	if($_POST['oldpwd']=="" || $_POST['newpwd1']=="" || $_POST['newpwd2']=="") //check empty password
	{
		$boxtext='<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i> <b>'.T_('Erreur').' :</b> '.T_('Veuillez remplir tous les champs').'.</center></div>';
	}
	elseif ($oldpassword!='1') //check old password
	{
		$boxtext='<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i> <b>'.T_('Erreur').' :</b> '.T_('Votre ancien mot de passe est erroné').'.</center></div>';
	}
	elseif ($_POST['newpwd1']!=$_POST['newpwd2']) //check new passwords
	{
		$boxtext='<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i> <b>'.T_('Erreur').' :</b> '.T_('Les deux nouveaux mot de passes sont différents').'.</center></div>';
	}
	elseif($rparameters['user_password_policy'] && $_POST['newpwd1'] && (strlen($_POST['newpwd1'])<$rparameters['user_password_policy_min_lenght'])) //password policy
	{
		$boxtext='<div class="alert alert-danger"><i class="icon-remove"></i> <strong>'.T_('Erreur').' :</strong> '.T_('Le mot de passe doit faire').' '.$rparameters['user_password_policy_min_lenght'].' '.T_('caractères minimum').'</div>';
	}
	elseif($rparameters['user_password_policy'] && $_POST['newpwd1'] && ($rparameters['user_password_policy_special_char'] && !preg_match('/[^a-zA-Z\d]/', $_POST['newpwd1']))) //password policy
	{
		$boxtext='<div class="alert alert-danger"><i class="icon-remove"></i> <strong>'.T_('Erreur').' :</strong> '.T_('Le mot de passe doit contenir un caractère spécial').'</div>';
	}
	elseif($rparameters['user_password_policy_min_maj'] && (!preg_match('/[A-Z]/', $_POST['newpwd1']) || !preg_match('/[a-z]/', $_POST['newpwd1']))) //password policy
	{
		$boxtext='<div class="alert alert-danger"><i class="icon-remove"></i> <strong>'.T_('Erreur').' :</strong> '.T_('Le mot de passe doit au moins une lettre majuscule et une minuscule').'</div>';
	}
	else
	{
		if($_POST['newpwd1']!='') { //update password
			$date=date('Y-m-d');
			$hash = password_hash($_POST['newpwd1'], PASSWORD_DEFAULT);
			$qry=$db->prepare("UPDATE `tusers` SET `chgpwd`=0, `last_pwd_chg`=:last_pwd_chg, `password`=:password WHERE `id`=:id");
			$qry->execute(array('last_pwd_chg' => $date,'password' => $hash,'id' => $_SESSION['user_id']));
			$updated=1;
		}
	} 
}
if($updated==1)
{
	$boxtitle="<i class='icon-lock blue bigger-120'></i> ".T_('Modification du mot de passe');
	$boxtext= '<div class="alert alert-block alert-success"><center><i class="icon-ok green"></i>	'.T_('Votre mot de passe a été changé avec succès').'.</center></div>';
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
else
{
	$boxtext=$boxtext.'
	<form name="form" method="POST" action="" id="form">
		<input name="modifypwd" type="hidden" value="1">
		<label for="oldpwd" >'.T_('Ancien mot de passe').' :</label> 
		<input  name="oldpwd" type="password" >
		<label for="newpwd1" >'.T_('Nouveau mot de passe').' :</label> 
		<input  name="newpwd1" type="password" >
		<label for="newpwd2" >'.T_('Nouveau mot de passe').' :</label> 
		<input  name="newpwd2" type="password" >
	</form>
	';
}
$boxtitle="<i class='icon-lock blue bigger-120'></i> ".T_('Modification du mot de passe');
$valid=T_('Modifier');
$action1="$('form#form').submit();";
$cancel=T_('Fermer');
$action2="$( this ).dialog( \"close\" ); ";
include "./modalbox.php"; 
?>