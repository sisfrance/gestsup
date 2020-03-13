<?php
################################################################################
# @Name : asset_iface.php
# @Description : add or edit IP interface from an asset
# @call : ./asset.php
# @parameters :  
# @Author : Flox
# @Create : 22/03/2017
# @Update : 24/12/2018
# @Version : 3.1.37
################################################################################

//initialize variables 
if(!isset($_GET ['id'])) $_GET['id'] = '';
 
if(!isset($_POST['addiface'])) $_POST['addiface'] = ''; 
if(!isset($_POST['editiface'])) $_POST['editiface'] = ''; 

$db_id=strip_tags($db->quote($_GET['id']));
$db_iface=strip_tags($db->quote($_GET['iface']));

//submit actions
if($_POST['addiface'])
{
	$qry=$db->prepare("INSERT INTO `tassets_iface` (`role_id`,`asset_id`,`netbios`,`ip`,`mac`,`disable`) VALUES (:role_id,:asset_id,:netbios,:ip,:mac,:disable)");
	$qry->execute(array(
		'role_id' => $_POST['role'],
		'asset_id' => $_GET['id'],
		'netbios' => '',
		'ip' => '',
		'mac' => '',
		'disable' => 0
		));
	
	//redirect
	$www = "./index.php?page=asset&id=$_GET[id]&$url_get_parameters";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';

}
if($_POST['editiface'])
{
	$qry=$db->prepare("UPDATE `tassets_iface` SET `role_id`=:role_id WHERE `id`=:id");
	$qry->execute(array(
		'role_id' => $_POST['role'],
		'id' => $_GET['iface']
		));
	
	//redirect
	$www = "./index.php?page=asset&id=$_GET[id]&$url_get_parameters";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
//case for create new user
if ($_GET['action']=="addiface")
{
	$boxtitle='<img src=./images/plug.png /> '.T_('Ajouter une interface IP');
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input  name="addiface" type="hidden" value="1" />
		<label for="role">'.T_("Rôle de l'interface").':</label><br />
		<select id="role" name="role">';
			$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_iface_role` WHERE disable='0' ORDER BY name ASC");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				$boxtext= $boxtext.'<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}
			$qry->closeCursor();
			$boxtext= $boxtext.'
		</select>	
	</form>
	';
	$valid=T_('Ajouter');
	$action1="$('form#form').submit();";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
if ($_GET['action']=="editiface") //case for modify an existing user
{
	//get current iface
	$qry=$db->prepare("SELECT `role_id` FROM `tassets_iface` WHERE id=:id");
	$qry->execute(array('id' => $_GET['iface']));
	$role_id=$qry->fetch();
	$qry->closeCursor();
	
	$boxtitle='<img src=./images/plug.png /> '.T_('Modifier une interface IP');
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input  name="editiface" type="hidden" value="1" />
		<label for="role">'.T_("Rôle de l'interface").':</label><br />
		<select id="role" name="role">';
			$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_iface_role` WHERE disable='0' ORDER BY name ASC");
			$qry->execute(array('id' => $_GET['id']));
			while($row=$qry->fetch()) 
			{
				if ($row['id']==$role_id[0]) {$boxtext= $boxtext.'<option selected value="'.$row['id'].'">'.$row['name'].'</option>';} else {$boxtext= $boxtext.'<option value="'.$row['id'].'">'.$row['name'].'</option>';}
			}
			$qry->closeCursor();
			$boxtext= $boxtext.'
		</select>	
	</form>
	';
	$valid=T_('Modifier');
	$action1="$('form#form').submit();";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
include "./modalbox.php"; 
?>