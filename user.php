<?php
################################################################################
# @Name : user.php 
# @Desc : admin user
# @call : admin.php
# @Autor : Flox
# @Create : 12/01/2011
# @Update : 27/12/2013
# @Version : 3.0.1
################################################################################

//initialize variables 
if(!isset($_SERVER['QUERY_URI'])) $_SERVER['QUERY_URI'] = '';
if(!isset($_POST['Modifier'])) $_POST['Modifier'] = '';
if(!isset($_POST['Ajouter'])) $_POST['Ajouter'] = '';
if(!isset($_POST['cancel'])) $_POST['cancel'] = '';
if(!isset($_POST['addview'])) $_POST['addview'] = '';
if(!isset($_POST['profil'])) $_POST['profil'] = '';
if(!isset($_POST['name'])) $_POST['name'] = '';
if(!isset($_POST['company'])) $_POST['company'] = '';
if(!isset($_POST['address1'])) $_POST['address1'] = '';
if(!isset($_POST['address2'])) $_POST['address2'] = '';
if(!isset($_POST['zip'])) $_POST['zip'] = '';
if(!isset($_POST['city'])) $_POST['city'] = '';
if(!isset($_POST['custom1'])) $_POST['custom1'] = '';
if(!isset($_POST['custom2'])) $_POST['custom2'] = '';
if(!isset($_POST['password'])) $_POST['password'] = '';
if(!isset($_POST['category'])) $_POST['category'] = '%';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';
if(!isset($_POST['firstname'])) $_POST['firstname'] = '';
if(!isset($_POST['lastname'])) $_POST['lastname'] = '';
if(!isset($_POST['viewname'])) $_POST['viewname'] = '';
if(!isset($_POST['service'])) $_POST['service'] = '';
if(!isset($_POST['function'])) $_POST['function'] = '';
if(!isset($password)) $password = '';
if(!isset($addeview)) $addview = '';
if(!isset($category)) $category = '%';
if(!isset($_POST['chgpwd'])) $_POST['chgpwd'] = '';
if(!isset($_GET['id'])) $_GET['id'] = '';
if(!isset($_GET['deleteview'])) $_GET['deleteview'] = '';
if(!isset($_GET['action'])) $_GET['action'] = '';
if(!isset($_GET['ldap'])) $_GET['ldap'] = '';

//special char rename
$_POST['firstname'] = str_replace("'","\'",$_POST['firstname']); 
$_POST['lastname'] = str_replace("'","\'",$_POST['lastname']);

// Si une modification est demandé alors on met a jour la table tusers puis on redirige l'utilisateur vers le listing des utilisateurs
if($_POST['Modifier'])
{
	//no update already crytped password if no change
	$q = mysql_query("SELECT * FROM `tusers` where id LIKE '$_GET[id]'"); 
	$r = mysql_fetch_array($q);
	if($_POST['password']!=$r['password'] || $r['password']=='' ) 
	{
		$salt = substr(md5(uniqid(rand(), true)), 0, 5); //generate a random key
		$_POST['password']=md5($salt . md5($_POST['password'])); //store in md5, md5 password + salt
	} else {
		$salt=$r['salt'];
	}
	
	$requete = "UPDATE tusers SET
	firstname='$_POST[firstname]',
	lastname='$_POST[lastname]',
	password='$_POST[password]',
	salt='$salt',
	mail='$_POST[mail]',
	phone='$_POST[phone]',
	profile='$_POST[profile]',
	login='$_POST[login]',
	fax='$_POST[fax]',
	service='$_POST[service]',
	function='$_POST[function]',
	company='$_POST[company]',
	address1='$_POST[address1]',
	address2='$_POST[address2]',
	zip='$_POST[zip]',
	city='$_POST[city]',
	custom1='$_POST[custom1]',
	custom2='$_POST[custom2]',
	skin='$_POST[skin]',
	chgpwd='$_POST[chgpwd]' WHERE id LIKE '$_GET[id]'";
	
	$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
	
	if($_POST['viewname']){
		$query = "INSERT INTO tviews (uid,name,category,subcat) VALUES ('$_GET[id]','$_POST[viewname]', '$_POST[category]', '$_POST[subcat]')";
		$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	}
	//redirect
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$_SERVER['QUERY_URI'].'");
	// -->
	</script>';
}

if($_POST['Ajouter']){
	//crypt password md5 + salt
	$salt = substr(md5(uniqid(rand(), true)), 0, 5); // Generate a random key
	$_POST['password']=md5($salt . md5($_POST['password'])); // store in md5, md5 password + salt
	
	$requete = "INSERT INTO tusers (firstname,lastname,password,salt,mail,phone,fax,company,address1,address2,zip,city,custom1,custom2,profile,login,chgpwd,skin) VALUES ('$_POST[firstname]','$_POST[lastname]','$_POST[password]','$salt','$_POST[mail]','$_POST[phone]','$_POST[fax]','$_POST[company]','$_POST[address1]','$_POST[address2]','$_POST[zip]','$_POST[city]','$_POST[custom1]','$_POST[custom2]','$_POST[profile]','$_POST[login]','$_POST[chgpwd]','$_POST[skin]')";
	$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
	//redirect
	$www = "./index.php?page=admin&subpage=user";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}


if($_POST['cancel']){
	//redirect
	$www = "./index.php?page=dashboard&userid=$uid&state=%";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}
//view Part
if ($_GET['deleteview']=="1")
{
$query = "DELETE FROM tviews WHERE id = '$_GET[viewid]'";
$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	//redirect
	$www = "./index.php?page=admin&subpage=user&action=edit&id=$_GET[id]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
//Display head page
if ($rright['admin_user_profile']!='0')
{
	if($_GET['ldap']!=1)
	{
		//count users
		$q = mysql_query("SELECT COUNT(*) FROM tusers where disable='0'");
		$r = mysql_fetch_array($q);
		$q1 = mysql_query("SELECT COUNT(*) FROM tusers where disable='1'");
		$r2 = mysql_fetch_array($q1);
		echo '
		<div class="page-header position-relative">
			<h1>
				<i class="icon-user"></i>  Gestion des utilisateurs
				<small>
					<i class="icon-double-angle-right"></i>
					&nbsp;Nombre: '.$r[0].' &nbsp;&nbsp;<i>('.$r2[0].' Désactivés)</i>
				</small>
			</h1>
		</div>';
	}
}
//display edit user page 
if ($_GET['action']=='edit')
{
	//get user data
	$requser1 = mysql_query("SELECT * FROM `tusers` where id LIKE '$_GET[id]'"); 
	$user1 = mysql_fetch_array($requser1);
	
	//display edit form.
	echo '
		<div class="col-sm-8">
			<div class="widget-box">
				<div class="widget-header">
					<h4>Fiche utilisateur:</h4>
					<span class="widget-toolbar">
						<button value="Modifier" id="Modifier" name="Modifier" type="submit" form="1" class="btn btn-minier btn-success">
							<i class="icon-save bigger-140"></i>
						</button>
					</span>
				</div>
				<div class="widget-body">
					<div class="widget-main no-padding">
						<form id="1" name="form" method="POST"  action="" class="form-horizontal">
							<fieldset>
								<label for="firstname">Prénom:</label>
								<input name="firstname" type="text" value="'; if($user1['firstname']) echo "$user1[firstname]"; else echo ""; echo'" />
								<div class="space-4"></div>
								<label for="lastname">Nom:</label>
								<input name="lastname" type="text" value="'; if($user1['lastname']) echo "$user1[lastname]"; else echo ""; echo'" />
								<div class="space-4"></div>
								<label for="login">Identifiant:</label>
								<input name="login" type="text" value="'; if($user1['login']) echo "$user1[login]"; else echo ""; echo'" />
								<div class="space-4"></div>
								<label for="password">Mot de passe:</label>
								<input name="password" type="password" value="'; if($user1['password']) echo "$user1[password]"; else echo ""; echo'" />
								<div class="space-4"></div>
								<label for="mail">Adresse mail:</label>
								<input name="mail" type="text" value="'; if($user1['mail']) echo "$user1[mail]"; else echo ""; echo'" />
								<div class="space-4"></div>
								<label for="phone">Téléphone:</label>
								<input name="phone" type="text" value="'; if($user1['phone']) echo "$user1[phone]"; else echo ""; echo'" />
								<div class="space-4"></div>
								<label for="fax">Fax:</label>
								<input name="fax" type="text" value="'; if($user1['fax']) echo "$user1[fax]"; else echo ""; echo'" />
								<div class="space-4"></div>
								<label for="service">Service:</label>
								<select  name="service" >
									<option value=""></option>';
									$query = mysql_query("SELECT * FROM tservices ORDER BY name");
									while ($row=mysql_fetch_array($query)) 
									{
										echo "<option value=\"$row[id]\">$row[name]</option>";
										if ($user1['service']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
									} 
									echo '
								</select>
								<div class="space-4"></div>
								<label for="function">Fonction:</label>
								<input name="function" type="text" value="'; if($user1['function']) echo "$user1[function]"; else echo ""; echo'" />
								';
								//display advanced user informations
								if ($rparameters['user_advanced']!='0')
								{
								echo '
									<div class="space-4"></div>
									<label for="company">Société:</label>
									<input name="company" type="text" value="'; if($user1['company']) echo "$user1[company]"; else echo ""; echo'" />
									<div class="space-4"></div>
									<label for="address1">Adresse 1:</label>
									<input name="address1" type="text" value="'; if($user1['address1']) echo "$user1[address1]"; else echo ""; echo'" />
									<div class="space-4"></div>
									<label  for="address2">Adresse 2:</label>
									<input name="address2" type="text" value="'; if($user1['address2']) echo "$user1[address2]"; else echo ""; echo'" />
									<div class="space-4"></div>
									<label  for="city">Ville:</label>
									<input name="city" type="text" value="'; if($user1['city']) echo "$user1[city]"; else echo ""; echo'" />
									<div class="space-4"></div>
									<label for="zip">Code Postal:</label>
									<input name="zip" type="text" value="'; if($user1['zip']) echo "$user1[zip]"; else echo ""; echo'" />
									<div class="space-4"></div>
									<label for="custom1">Champ personalisé 1:</label>
									<input name="custom1" type="text" value="'; if($user1['custom1']) echo "$user1[custom1]"; else echo ""; echo'" />
									<div class="space-4"></div>
									<label for="custom2">Champ personalisé 2:</label>
									<input name="custom2" type="text" value="'; if($user1['custom2']) echo "$user1[custom2]"; else echo ""; echo'" />
									<div class="space-4"></div>
								';
								}
								//display theme selection
								echo '
								<hr />
								<label class="control-label bolder blue" for="skin">Thème:</label>
								<div class="space-4"></div>
								<select name="skin">
									<option style="background-color:#438EB9;" '; if ($user1['skin']==''){echo "selected";} echo ' value="">Bleu (Défaut)</option>
									<option style="background-color:#222A2D;" '; if ($user1['skin']=='skin-1'){echo "selected";} echo ' value="skin-1">Noir</option>
									<option style="background-color:#C6487E;" '; if ($user1['skin']=='skin-2'){echo "selected";} echo ' value="skin-2">Rose</option>
									<option style="background-color:#D0D0D0;" '; if ($user1['skin']=='skin-3'){echo "selected";} echo ' value="skin-3">Gris</option>
								</select>
								';
								//display group rattachment if exist
								$query = mysql_query("SELECT count(*) FROM tgroups, tgroups_assoc WHERE tgroups.id=tgroups_assoc.group AND tgroups_assoc.user='$_GET[id]'");
								$row=mysql_fetch_array($query);
								if ($row[0]!=0)
								{
									echo '<hr />';
									echo '<label class="control-label bolder blue" for="group">Membre des Groupes:</label>';
									$query = mysql_query("SELECT tgroups.id as id, tgroups.name as name  FROM tgroups, tgroups_assoc WHERE tgroups.id=tgroups_assoc.group AND tgroups_assoc.user='$_GET[id]'");
									while ($row=mysql_fetch_array($query))
									{
										echo "<div class=\"space-4\"></div>- <a href=\"./index.php?page=admin&subpage=group&action=edit&id=$row[id]\"> $row[name]</a>";
									}	
								}
								// Display profile list
								if ($rright['admin_user_profile']!='0')
								{
									echo '
									<hr />
									<label class="control-label bolder blue" for="profile">Profile:</label>
									<div class="controls">
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="4" '; if ($user1['profile']=='4')echo "checked"; echo '> <span class="lbl"> Administrateur </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="0" '; if ($user1['profile']=='0')echo "checked"; echo '> <span class="lbl"> Technicien </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="3" '; if ($user1['profile']=='3')echo "checked"; echo '> <span class="lbl"> Superviseur </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="1" '; if ($user1['profile']=='1')echo "checked"; echo '> <span class="lbl"> Utilisateur avec pouvoir </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="2" '; if ($user1['profile']=='2')echo "checked"; echo '> <span class="lbl"> Utilisateur </span>
											</label>
										</div>
									</div>
									<hr />
									<label class="control-label bolder blue" for="chgpwd">Forcer le changement du mot de passe:</label>
									<label>
											<input type="radio" class="ace" disable="disable" name="chgpwd" value="1" '; if ($user1['chgpwd']=='1')echo "checked"; echo '> <span class="lbl"> Oui </span>
									</label>
									<label>
											<input type="radio" class="ace" name="chgpwd" value="0" '; if ($user1['chgpwd']=='0')echo "checked"; echo '> <span class="lbl"> Non </span>
									</label>
									';
								}
								else
								{
									echo '<input type="hidden" name="profile" value="'.$user1['profile'].'" '; if ($user1['profile']=='2')echo "checked"; echo '>';
								}
								//display personal view
								if ($rright['admin_user_view']!='0')
								{
									echo '
										<hr />
										<label class="control-label bolder blue" for="view">Vues personnelles: <i>(associe des catégories à l\'utilisateur)</i></label>
										<div class="space-4"></div>';
											//check if connected user have view
											$query = mysql_query("SELECT * FROM `tviews` WHERE uid='$_GET[id]'");
											$row=mysql_fetch_array($query);
											if ($row[0]!='')
											{
												//display actives views
												$query = mysql_query("SELECT * FROM `tviews` WHERE uid='$_GET[id]' ORDER BY uid");
												while ($row=mysql_fetch_array($query))
												{
													$cname= mysql_query("SELECT name FROM `tcategory` WHERE id='$row[category]'"); 
													$cname= mysql_fetch_array($cname);
													
													if ($row['subcat']!='')
													{
														$sname= mysql_query("SELECT name FROM `tsubcat` WHERE id='$row[subcat]'"); 
														$sname= mysql_fetch_array($sname);
													} else {$sname='';}
													echo "- $row[name]: ($cname[name] > $sname[0]) 
													<a title=\"Supprimer cette Vue\" href=\"index.php?page=admin&subpage=user&action=edit&id=$_GET[id]&viewid=$row[id]&deleteview=1\"><img alt=\"delete\" src=\"./images/delete.png\" style=\"border-style: none\" /></a>
													<br />";
												}
												echo '<br />';
											}
											//display add view form
											echo '
												Catégorie:
												<select name="category" onchange="submit()" style="width:100px" >
													<option value="%"></option>';
													$query = mysql_query("SELECT * FROM tcategory ORDER BY name");
													while ($row=mysql_fetch_array($query)) 
													{
														echo "<option value=\"$row[id]\">$row[name]</option>";
														if ($_POST['category']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
													} 
													echo '
												</select>
												<div class="space-4"></div>
												Sous-Catégorie:
												<select name="subcat" onchange="submit()" style="width:90px">
													<option value="%"></option>';
													if($_POST['category']!='%')
													{$query = mysql_query("SELECT * FROM tsubcat WHERE cat LIKE $_POST[category] ORDER BY name");}
													else
													{$query = mysql_query("SELECT * FROM tsubcat ORDER BY name");}
													while ($row=mysql_fetch_array($query))
													{
														echo "<option value=\"$row[id]\">$row[name]</option>";
														if ($_POST['subcat']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
													} 
													echo '
												</select>
												<div class="space-4"></div>
												Nom: <input name="viewname" type="" value="'.$_POST['name'].'" size="20" />';
								}
								echo'
							</fieldset>
							<div class="form-actions center">
								<button value="Modifier" id="Modifier" name="Modifier" type="submit"  class="btn btn-sm btn-success">
									<i class="icon-ok-sign bigger-120"></i>
									Modifier
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
}
else if ($_GET['action']=="add")
{
	//display add form
	echo '
		<div class="col-sm-5">
			<div class="widget-box">
				<div class="widget-header">
					<h4>Ajout d\'un utilisateur:</h4>
					<span class="widget-toolbar">
						<button title="Ajouter un utilisateur" value="Ajouter" id="Ajouter" name="Ajouter" type="submit" form="1" class="btn btn-minier btn-success">
							<i class="icon-save bigger-140"></i>
						</button>
					</span>
				</div>
				<div class="widget-body">
					<div class="widget-main no-padding">
						<form id="1" name="form" method="POST"  action="">
							<fieldset>
								<label for="firstname">Prénom:</label>
								<input name="firstname" type="text" value="" />
								<div class="space-4"></div>
								<label  for="lastname">Nom:</label>
								<input name="lastname" type="text" value="" />
								<div class="space-4"></div>
								<label for="login">Identifiant:</label>
								<input name="login" type="text" value="" />
								<div class="space-4"></div>
								<label  for="password">Mot de passe:</label>
								<input name="password" type="password" value="" />
								<div class="space-4"></div>
								<label for="mail">Adresse mail:</label>
								<input name="mail" type="text" value="" />
								<div class="space-4"></div>
								<label  for="phone">Téléphone:</label>
								<input name="phone" type="text" value="" />
								<div class="space-4"></div>
								<label  for="fax">Fax:</label>
								<input name="fax" type="text" value="" />
								<div class="space-4"></div>
								<label  for="service">Service:</label>
								<select  name="service" >
									<option value=""></option>';
									$query = mysql_query("SELECT * FROM tservices ORDER BY name");
									while ($row=mysql_fetch_array($query)) 
									{
										echo "<option value=\"$row[id]\">$row[name]</option>";
									} 
									echo '
								</select>
								<div class="space-4"></div>
								<label for="function">Fonction:</label>
								<input name="function" type="text" value="" />
								';
								//display advanced user informations
								if ($rparameters['user_advanced']!='0')
								{
								echo '
									<div class="space-4"></div>
									<label  for="company">Société:</label>
									<input name="company" type="text" value="" />
									<div class="space-4"></div>
									<label   for="address1">Adresse 1:</label>
									<input name="address1" type="text" value="" />
									<div class="space-4"></div>
									<label   for="address2">Adresse 2:</label>
									<input name="address2" type="text" value="" />
									<div class="space-4"></div>
									<label for="city">Ville:</label>
									<input name="city" type="text" value="" />
									<div class="space-4"></div>
									<label for="zip">Code Postal:</label>
									<input name="zip" type="text" value="" />
									<div class="space-4"></div>
									<label for="custom1">Champ personalisé 1:</label>
									<input name="custom1" type="text" value="" />
									<div class="space-4"></div>
									<label for="custom2">Champ personalisé 2:</label>
									<input name="custom2" type="text" value="" />
								';
								}
								//display theme selection
								echo '
								<hr />
								<label class="control-label bolder blue" for="skin">Thème:</label>
								<div class="space-4"></div>
								<select name="skin">
									<option style="background-color:#438EB9;" value="">Bleu (Défaut)</option>
									<option style="background-color:#222A2D;" value="skin-1">Noir</option>
									<option style="background-color:#C6487E;" value="skin-2">Rose</option>
									<option style="background-color:#D0D0D0;" value="skin-3">Gris</option>
								</select>
								';
								// Display profile list
								if ($rright['admin_user_profile']!='0')
								{
									echo '
									<hr />
									<label class="control-label bolder blue" for="profile">Profile:</label>
									<div class="controls">
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="4"> <span class="lbl"> Administrateur </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="0"> <span class="lbl"> Technicien </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="3"> <span class="lbl"> Superviseur </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="1"> <span class="lbl"> Utilisateur avec pouvoir </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" checked value="2"> <span class="lbl"> Utilisateur </span>
											</label>
										</div>
									</div>
									<hr />
									<label class="control-label bolder blue" for="chgpwd">Forcer le changement du mot de passe:</label>
									<div class="controls">
										<label>
											<input type="radio" class="ace" disable="disable" name="chgpwd" value="0"> <span class="lbl"> Oui </span>
										</label>
										<label>
											<input type="radio" class="ace" name="chgpwd" checked value="1"> <span class="lbl"> Non </span>
										</label>
									</div>
									';
								}
								else
								{
									echo '<input type="hidden" name="profile" value="">';
								}
								//display personal view
								if ($rright['admin_user_view']!='0')
								{
									echo '
										<hr />
										<label class="control-label bolder blue" for="view">Vues personnelles: <i>(associe des catégories à l\'utilisateur)</i></label>
										<div class="controls">
											';
											//check if connected user have view
											$query = mysql_query("SELECT * FROM `tviews` WHERE uid='$_GET[id]'");
											$row=mysql_fetch_array($query);
											if ($row[0]!='')
											{
												//display actives views
												$query = mysql_query("SELECT * FROM `tviews` WHERE uid='$_GET[id]' ORDER BY uid");
												while ($row=mysql_fetch_array($query))
												{
													$cname= mysql_query("SELECT name FROM `tcategory` WHERE id='$row[category]'"); 
													$cname= mysql_fetch_array($cname);
													
													if ($row['subcat']!=0)
													{
														$sname= mysql_query("SELECT name FROM `tsubcat` WHERE id='$row[subcat]'"); 
														$sname= mysql_fetch_array($sname);
													} else {$sname='';}
													echo "- $row[name]: ($cname[name] > $sname[0]) 
													<a title=\"Supprimer cette Vue\" href=\"index.php?page=admin&subpage=user&action=edit&id=$_GET[id]&viewid=$row[id]&deleteview=1\"><img alt=\"delete\" src=\"./images/delete.png\" style=\"border-style: none\" /></a>
													<br />";
												}
												echo '<br />';
											}
											//display add view form
											echo '
												Catégorie:
												<select name="category" onchange="submit()" style="width:100px" >
													<option value="%"></option>';
													$query = mysql_query("SELECT * FROM tcategory ORDER BY name");
													while ($row=mysql_fetch_array($query)) 
													{
														echo "<option value=\"$row[id]\">$row[name]</option>";
														if ($_POST['category']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
													} 
													echo '
												</select>
												<br />
												Sous-Catégorie:
												<select name="subcat" onchange="submit()" style="width:90px">
													<option value="%"></option>';
													if($_POST['category']!='%')
													{$query = mysql_query("SELECT * FROM tsubcat WHERE cat LIKE $_POST[category] ORDER BY name");}
													else
													{$query = mysql_query("SELECT * FROM tsubcat ORDER BY name");}
													while ($row=mysql_fetch_array($query))
													{
														echo "<option value=\"$row[id]\">$row[name]</option>";
														if ($_POST['subcat']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
													} 
													echo '
												</select>
												<br />
												Nom: <input name="viewname" type="" value="'.$_POST['name'].'" size="20" />
										</div>';
								}
								echo'
							</fieldset>
							<div class="form-actions center">
								<button value="Ajouter" id="Ajouter" name="Ajouter" type="submit"  class="btn btn-sm btn-success">
									<i class="icon-ok-sign bigger-120"></i>
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
}
else if ($_GET['action']=="delete")
{
$requete = "DELETE FROM tusers WHERE id = '$_GET[id]'";
$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
	//redirection vers la page d'accueil
	$www = "./index.php?page=admin&subpage=user";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}
else if ($_GET['action']=="disable")
{
$requete = "UPDATE tusers set disable=1 WHERE id = '$_GET[id]'";
$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
	//redirection vers la page d'accueil
	$www = "./index.php?page=admin&subpage=user";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}
else if ($_GET['action']=="enable")
{
$requete = "UPDATE tusers set disable=0 WHERE id = '$_GET[id]'";
$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
	//home page redirection
	$www = "./index.php?page=admin&subpage=user";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}
else if ($_GET['ldap']=="1")
{
	include('./core/ldap.php');
}
// Else display users
else
{
	//Display Buttons
	echo '
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<p>
				<button onclick=\'window.location.href="index.php?page=admin&subpage=user&action=add";\' class="btn btn-sm btn-success">
					<i class="icon-plus"></i> Ajouter un utilisateur
				</button>
		';
		if($rparameters['ldap']==1)
		{
			echo '
				<button onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;ldap=1";\' class="btn btn-sm btn-info">
					<i class="icon-refresh"></i> Synchronisation LDAP
				</button>
			';
		}
	echo'
			</p>
		</div>
		<br />	
	';
	//Display user table
	echo '
		<table id="sample-table-1" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th><i class="icon-user"></i> Identifiant</th>
					<th><i class="icon-male"></i> Nom Prénom</th>
					<th><i class="icon-envelope-alt"></i> Adresse Mail</th>
					<th><i class="icon-phone"></i> Téléphone</th>
					<th><i class="icon-group"></i> Profile</th>
					<th><i class="icon-certificate"></i> Dernière connexion</th>
					<th><i class="icon-play"></i> Actions</th>
				</tr>
			</thead>
			<tbody>';
				//build each line
				$query = mysql_query("SELECT * FROM `tusers` WHERE profile LIKE '$_GET[profileid]' ORDER BY lastname");
				while ($row=mysql_fetch_array($query)) 
				{
					//find profile name
					$q = mysql_query("select name FROM tprofiles where level='$row[profile]'");
					$r = mysql_fetch_array($q) ;
					//display last login if exist
					if($row['last_login']=='0000-00-00 00:00:00') $lastlogin=''; else $lastlogin=$row['last_login'];
					//display line
					echo '
						<tr>
							<td onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&id='.$row['id'].'";\' >'.$row['login'].'</td>
							<td onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&id='.$row['id'].'";\' >'.$row['lastname'].' '.$row['firstname'].' </td>
							<td onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&id='.$row['id'].'";\' >'.$row['mail'].'</td>
							<td onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&id='.$row['id'].'";\' >'.$row['phone'].'</td>
							<td onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&id='.$row['id'].'";\' >'.$r['name'].'</td>
							<td onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&id='.$row['id'].'";\' >'.$lastlogin.'</td>
							<td width="100">
							<div class="hidden-phone visible-desktop btn-group">';
								if ($row['disable']!=1)
								{
									echo '
										<button title="Désactiver l\'utilisateur" onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;id='.$row['id'].'&amp;action=disable";\' class="btn btn-xs btn-success">
											<i class="icon-ok bigger-120"></i>
										</button>
									';
								} else 
								{
									echo '
									<button title="Activer l\'utilisateur" onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;id='.$row['id'].'&amp;action=enable";\' class="btn btn-xs btn-danger">
										<i class="icon-ban-circle bigger-120"></i>
									</button>
									';
								}
								echo '
									<button title="Editer l\'utilisateur" onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;action=edit&amp;id='.$row['id'].'";\' class="btn btn-xs btn-warning">
										<i class="icon-pencil bigger-120"></i>
									</button>
								';
								if($row['id']!=$_SESSION['user_id'])
								{
								echo '
									<button title="Supprimer l\'utilisateur" onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;action=delete&amp;id='.$row['id'].'";\' class="btn btn-xs btn-danger">
										<i class="icon-trash bigger-120"></i>
									</button>
								';
								}
								echo '
							</div>
							</td>
						</tr>
					';
				}
				echo '
			</tbody>
		</table>
	';
}
?>