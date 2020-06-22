<?php
################################################################################
# @Name : group.php
# @Desc : group manangement 
# @call : admin.php
# @parameters : 
# @Autor : Flox
# @Create : 06/07/2013
# @Update : 16/09/2013
# @Version : 3.0
################################################################################

//initialize variables 
if(!isset($_POST['Modifier'])) $_POST['Modifier'] = '';
if(!isset($_POST['cancel'])) $_POST['cancel'] = '';
if(!isset($_POST['add'])) $_POST['add'] = '';
if(!isset($_GET['user'])) $_GET['user'] = '';
if(!isset($_GET['type'])) $_GET['type'] = '0';

////submit actions
if($_POST['Modifier'])
{
	//excape special char in sql query 
	$_POST['name'] = mysql_real_escape_string($_POST['name']);
	//update name
	$query = "UPDATE tgroups SET
	name='$_POST[name]',
	type='$_POST[type]'
	WHERE id LIKE '$_GET[id]'";
	mysql_query($query);
	//add user
	$query = "INSERT INTO tgroups_assoc (`group`,`user`) VALUES ('$_GET[id]','$_POST[user]')";
	mysql_query($query);
}
if($_POST['add'])
{
	//excape special char in sql query 
	$_POST['name'] = mysql_real_escape_string($_POST['name']);
	//update name
	$query = "INSERT INTO tgroups (`name`,`type`) VALUES ('$_POST[name]','$_POST[type]')";
	mysql_query($query);
	//redirect
	$www = "./index.php?page=admin&subpage=group";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
	
}

if($_POST['cancel']){
	//redirection vers la page d'accueil
	$www = "./index.php?page=admin&subpage=group";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}

//delete user in group
if ($_GET['action']=="delete" && $_GET['user']!="")
{
$query = "DELETE FROM tgroups_assoc WHERE `group`='$_GET[id]' AND `user`='$_GET[user]'";
$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	//redirection vers la page d'accueil
	$www = "./index.php?page=admin&subpage=group&action=edit&id=$_GET[id]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
//delete group
if ($_GET['action']=="delete" && $_GET['user']=="")
{
$query = "DELETE FROM tgroups WHERE `id`='$_GET[id]'";
mysql_query($query);
$query = "DELETE FROM tgroups_assoc WHERE `group`='$_GET[id]'";
mysql_query($query);

	//redirection vers la page d'accueil
	$www = "./index.php?page=admin&subpage=group";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
//Display head page
//count group
$q = mysql_query("SELECT COUNT(*) FROM tgroups where disable='0'");
$r = mysql_fetch_array($q);
echo '
<div class="page-header position-relative">
	<h1>
		<i class="icon-group"></i>  Gestion des groupes
		<small>
			<i class="icon-double-angle-right"></i>
			&nbsp;Nombre: '.$r[0].'
		</small>
	</h1>
</div>';

////Edit group
if ($_GET['action']=='edit')
{
	//Get group data
	$qgroup = mysql_query("SELECT * FROM `tgroups` where id LIKE '$_GET[id]'"); 
	$rgroup = mysql_fetch_array($qgroup);
	
	//display edit form
	echo '
		<div class="col-sm-5">
			<div class="widget-box">
				<div class="widget-header">
					<h4>Edition d\'un groupe:</h4>
				</div>
				<div class="widget-body">
					<div class="widget-main no-padding">
						<form id="1" name="form" method="post"  action="">
							<fieldset>
								<label for="name">Nom:</label>
								<input name="name" type="text" value="'; if($rgroup['name']) echo "$rgroup[name]"; echo'" />
								<hr />
								<div class="radio">
									<label>
										<input value="0" '; if ($rgroup['type']=='0')echo "checked"; echo ' name="type" type="radio" class="ace">
										<span class="lbl"> Groupe d\'utilisateurs</span>
									</label>
								</div>
								<div class="radio">
									<label>
										<input value="1" '; if ($rgroup['type']=='1')echo "checked"; echo ' name="type" type="radio" class="ace">
										<span class="lbl"> Groupe de techniciens</span>
									</label>
								</div>
								<hr />
								<label for="user">Ajout d\'un nouveau membre:</label>
								<select name="user" >
									<option value=""></option>';
									$query = mysql_query("SELECT * FROM tusers WHERE disable=0 ORDER BY lastname");
									while ($row=mysql_fetch_array($query)) 
									{
										echo "<option value=\"$row[id]\">$row[lastname] $row[firstname]</option>";
									} 
								echo '
								</select>
								<hr />
								<label for="name">Membres actuels:</label>
								<br />';
								//display current users in this group
								$quser = mysql_query("SELECT tusers.firstname, tusers.lastname, tusers.id FROM `tusers`,tgroups_assoc WHERE tusers.id=tgroups_assoc.user AND tgroups_assoc.group=$_GET[id] AND tusers.disable=0");
								while ($ruser=mysql_fetch_array($quser)) 
								{
									echo "- <a title=\"Fiche Utilisateur\" href=\"./index.php?page=admin&subpage=user&action=edit&id=$ruser[2]\" >$ruser[0] $ruser[1]</a> 
									<a title=\"Enlever l'utilisateur du groupe\" href=\"./index.php?page=admin&amp;subpage=group&amp;id=$_GET[id]&amp;user=$ruser[2]&amp;action=delete\"><img src=\"./images/delete.png\" border=\"0\" /></a><br />";
								}
								echo '
							</fieldset>
							<div class="form-actions center">
								<button name="Modifier" value="Modifier" id="Modifier" type="submit"  class="btn btn-sm btn-success">
									<i class="icon-ok bigger-120"></i>
									Modifier
								</button>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<button name="cancel" value="cancel" type="submit" class="btn btn-sm btn-danger" >
									<i class="icon-undo bigger-120"></i>
									Retour
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	';
} else if($_GET['action']=="add") {
	//display edit form.
	echo '
		
		<div class="col-sm-5">
			<div class="widget-box">
				<div class="widget-header">
					<h4>Edition d\'un groupe:</h4>
					
				</div>
				<div class="widget-body">
					<div class="widget-main no-padding">
						<form id="1" name="form" method="post"  action="">
							<fieldset>
								<label for="name">Nom:</label>
								<input name="name" type="text" value="" />
								<hr />
								<div class="radio">
									<label>
										<input value="0" name="type" type="radio" class="ace">
										<span class="lbl"> Groupe d\'utilisateur</span>
									</label>
								</div>
								<div class="radio">
									<label>
										<input value="1" name="type" type="radio" class="ace">
										<span class="lbl"> Groupe de techniciens</span>
									</label>
								</div>
							</fieldset>
							<div class="form-actions center">
								<button name="add" value="add" id="add" type="submit"  class="btn btn-sm btn-success">
									<i class="icon-ok bigger-120"></i>
									Ajouter
								</button>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<button name="cancel" value="cancel" type="submit" class="btn btn-sm btn-danger" >
									<i class="icon-reply bigger-120"></i>
									Retour
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	';
} else {
	////display group list
	echo'
	<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
		<p>
			<button onclick=\'window.location.href="index.php?page=admin&subpage=group&action=add";\' class="btn btn-sm btn-success">
				<i class="icon-plus"></i>Ajouter un groupe
			</button>
		</p>
	</div>
	<br />';
	//display user table
	echo'
	<div class="col-sm-6">
		<div class="tabbable">
			<ul class="nav nav-tabs" id="myTab">';
				if($_GET['type']==0) {echo '<li class="active" >';} else {echo '<li>';} echo '
					<a  href="./index.php?page=admin&subpage=group&type=0">
						<i class="green icon-group bigger-110"></i>
						Groupe d\'utilisateur
					</a>
				</li>';
				if($_GET['type']==1) {echo '<li class="active" >';} else {echo '<li>';} echo '
					<a href="./index.php?page=admin&subpage=group&type=1">
						<i class="green icon-group bigger-110"></i>
						Groupe de Technicien
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<table id="sample-table-1" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Nom</th>
							<th>Membres</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>';
						//build each line
						$qgroup = mysql_query("SELECT * FROM `tgroups` WHERE type=$_GET[type] ORDER BY type,name ");
						while ($rgroup=mysql_fetch_array($qgroup)) 
						{
							echo "
							<tr>
								<td onclick=\"document.location='./index.php?page=admin&amp;subpage=group&amp;action=edit&amp;id=$rgroup[id]'\">
									$rgroup[name]
								</td>
								<td onclick=\"document.location='./index.php?page=admin&amp;subpage=group&amp;action=edit&amp;id=$rgroup[id]'\">";
									$quser = mysql_query("SELECT tusers.firstname, tusers.lastname FROM `tusers`,tgroups_assoc WHERE tusers.id=tgroups_assoc.user AND tgroups_assoc.group=$rgroup[id] AND tusers.disable=0");
									while ($ruser=mysql_fetch_array($quser)) 
									{
										echo "$ruser[0] $ruser[1]<br />";
									}
									echo '
								</td>
								<td>
									<button title="Editer" onclick=\'window.location.href="./index.php?page=admin&amp;subpage=group&amp;action=edit&amp;id='.$rgroup['id'].'";\' class="btn btn-xs btn-warning">
										<i class="icon-pencil bigger-120"></i>
									</button>
									<button title="Supprimer" onclick=\'window.location.href="./index.php?page=admin&amp;subpage=group&amp;id='.$rgroup['id'].'&amp;action=delete";\' class="btn btn-xs btn-danger">
										<i class="icon-trash bigger-120"></i>
									</button>
								</td>
							</tr>';
						}
						echo '
					</tbody>
				</table>
			</div>
		</div>
	</div>
	';
}
?>