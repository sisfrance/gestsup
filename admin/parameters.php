<?php
################################################################################
# @Name : parameters.php
# @Desc : admin parameters
# @call : /admin.php
# @paramters : 
# @Author : Flox
# @Create : 12/01/2011
# @Update : 21/04/2015
# @Version : 3.0.11
################################################################################

//initialize variables 
if(!isset($extensionFichier)) $extensionFichier = '';
if(!isset($id_)) $id_ = '';
if(!isset($logo)) $logo = '';
if(!isset($filename)) $filename = '';
if(!isset($mail_auto)) $mail_auto = '';
if(!isset($user_advanced)) $user_advanced= '';
if(!isset($mail_auth)) $mail_auth= '';
if(!isset($mail_secure)) $mail_secure= '';
if(!isset($nomorigine)) $nomorigine = '';
if(!isset($action)) $action = '';
if(!isset($_POST['submit_general'])) $_POST['submit_general'] = '';
if(!isset($_POST['submit_connector'])) $_POST['submit_connector'] = '';
if(!isset($_POST['submit_function'])) $_POST['submit_function'] = '';
if(!isset($_POST['mail_username'])) $_POST['mail_username'] = '';
if(!isset($_POST['mail_password'])) $_POST['mail_password'] = '';
if(!isset($_POST['mail_secure'])) $_POST['mail_secure'] = '';
if(!isset($_POST['user_advanced'])) $_POST['user_advanced'] = '';
if(!isset($_POST['user_register'])) $_POST['user_register'] = '';
if(!isset($_POST['mail'])) $_POST['mail']= '';
if(!isset($_POST['mail_auth'])) $_POST['mail_auth']= '';
if(!isset($_POST['mail_auto'])) $_POST['mail_auto']= '';
if(!isset($_POST['mail_newticket'])) $_POST['mail_newticket']= '';
if(!isset($_POST['mail_newticket_address'])) $_POST['mail_newticket_address']= '';
if(!isset($_POST['mail_link'])) $_POST['mail_link']= '';
if(!isset($_POST['mail_smtp'])) $_POST['mail_smtp']= '';
if(!isset($_POST['mail_port'])) $_POST['mail_port']= '';
if(!isset($_POST['ldap'])) $_POST['ldap']= '';
if(!isset($_POST['ldap_auth'])) $_POST['ldap_auth']= '';
if(!isset($_POST['ldap_type'])) $_POST['ldap_type']= '';
if(!isset($_POST['ldap_server'])) $_POST['ldap_server']= '';
if(!isset($_POST['ldap_port'])) $_POST['ldap_port']= '';
if(!isset($_POST['ldap_domain'])) $_POST['ldap_domain']= '';
if(!isset($_POST['ldap_url'])) $_POST['ldap_url']= '';
if(!isset($_POST['ldap_user'])) $_POST['ldap_user']= '';
if(!isset($_POST['ldap_password'])) $_POST['ldap_password']= '';
if(!isset($_POST['test_ldap'])) $_POST['test_ldap']= '';
if(!isset($_POST['planning'])) $_POST['planning']= '';
if(!isset($_POST['debug'])) $_POST['debug']= '';
if(!isset($_POST['notify'])) $_POST['notify']= '';
if(!isset($_POST['imap'])) $_POST['imap']= '';
if(!isset($_POST['imap_server'])) $_POST['imap_server']= '';
if(!isset($_POST['imap_port'])) $_POST['imap_port']= '';
if(!isset($_POST['imap_user'])) $_POST['imap_user']= '';
if(!isset($_POST['imap_password'])) $_POST['imap_password']= '';
if(!isset($_POST['inbox'])) $_POST['inbox']= '';
if(!isset($_POST['procedure'])) $_POST['procedure']= '';
if(!isset($_POST['ticket_places'])) $_POST['ticket_places']= '';
if(!isset($_POST['availability'])) $_POST['availability']= '';
if(!isset($_POST['asset'])) $_POST['asset']= '';
if(!isset($_POST['availability_all_cat'])) $_POST['availability_all_cat']= '';
if(!isset($_POST['category'])) $_POST['category']= '';
if(!isset($_POST['depcategory'])) $_POST['depcategory']= '';
if(!isset($_POST['meta_state'])) $_POST['meta_state']= '';
if(!isset($_POST['availability_dep'])) $_POST['availability_dep']= '';
if(!isset($_POST['dash_date'])) $_POST['dash_date']= '';
if(!isset($_POST['availability_condition_type'])) $_POST['availability_condition_type']= $rparameters['availability_condition_type'];
if(!isset($_POST['availability_condition_value'])) $_POST['availability_condition_value']= $rparameters['availability_condition_type'];
if(!isset($_GET['action'])) $_GET['action']= '';
if(!isset($_GET['tab'])) $_GET['tab']= '';
if(!isset($_GET['ldaptest'])) $_GET['ldaptest']= '';
if(!isset($_GET['deleteavailability'])) $_GET['deleteavailability']= '';
if(!isset($_GET['deleteavailabilitydep'])) $_GET['deleteavailabilitydep']= '';
if(!isset($_FILES['logo']['name'])) $_FILES['logo']['name'] = '';

//delete logo file
if($_GET['action']=="deletelogo")
{
	$requete = "UPDATE tparameters SET logo=''";
	$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
		$www = "./index.php?page=admin&subpage=parameters";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>'; 
}
	
if($_POST['submit_general'])
{
	//upload logo file
	if($_FILES['logo']['name'])
	{
	    $filename = $_FILES['logo']['name'];
	   
	    //secure upload excluding certain extension files
	    $blacklist =  array('php','php3' ,'php4', 'js', 'htm', 'html', 'phtml');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!in_array($ext,$blacklist) ) {
            $repertoireDestination = "./upload/logo/";
    		if (move_uploaded_file($_FILES['logo']['tmp_name'], $repertoireDestination.$_FILES['logo']['name'])   ) 
    		{
    		} else {
    		echo "Erreur de transfert vérifier le chemin ".$repertoireDestination;
    		}
        } else {
            echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>Blocage de sécurité:</strong> Type de fichier interdit.<br></div>';
            $filename='logo.png';
        }
	}
	else $filename=$rparameters['logo'];
	
	//Escape special char to sql query
	$_POST['mail_from_name']=mysql_real_escape_string($_POST['mail_from_name']);
	$_POST['mail_txt']=mysql_real_escape_string($_POST['mail_txt']);
	$_POST['company']=mysql_real_escape_string($_POST['company']);
	
	//secure HTML chars
	$_POST['company']=strip_tags($_POST['company']);
	
	//update general tab
	$requete = "UPDATE tparameters SET 
	company='$_POST[company]',
	server_url='$_POST[server_url]',
	maxline='$_POST[maxline]',
	mail_txt='$_POST[mail_txt]',
	mail_cc='$_POST[mail_cc]',
	mail_from_name='$_POST[mail_from_name]',
	mail_from_adr='$_POST[mail_from_adr]',
	mail_color_title='$_POST[mail_color_title]',
	mail_color_bg='$_POST[mail_color_bg]',
	mail_color_text='$_POST[mail_color_text]',
	mail_link='$_POST[mail_link]',
	logo='$filename',
	time_display_msg='$_POST[time_display_msg]',
	auto_refresh='$_POST[auto_refresh]',
	notify='$_POST[notify]',
	user_advanced='$_POST[user_advanced]',
	user_register='$_POST[user_register]',
	mail_auto='$_POST[mail_auto]',
	mail_newticket='$_POST[mail_newticket]',
	mail_newticket_address='$_POST[mail_newticket_address]',
	debug='$_POST[debug]',
	`order`='$_POST[order]',
	`ticket_places`='$_POST[ticket_places]',
	`ticket_type`='$_POST[ticket_type]',
	`meta_state`='$_POST[meta_state]',
	`dash_date`='$_POST[dash_date]'
	WHERE
	id='1'
	";
	$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
	//web redirect
		$www = "./index.php?page=admin&subpage=parameters&tab=general";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>'; 
}
if($_POST['submit_connector'] || $_POST['test_ldap'])
{
	//update connector tab
	$requete = "UPDATE tparameters SET 
	mail='$_POST[mail]',
	mail_smtp='$_POST[mail_smtp]',
	mail_port='$_POST[mail_port]',
	mail_secure='$_POST[mail_secure]',
	mail_auth='$_POST[mail_auth]',
	mail_username='$_POST[mail_username]',
	mail_password='$_POST[mail_password]',
	ldap='$_POST[ldap]',
	ldap_auth='$_POST[ldap_auth]',
	ldap_type='$_POST[ldap_type]',
	ldap_server='$_POST[ldap_server]',
	ldap_port='$_POST[ldap_port]',
	ldap_user='$_POST[ldap_user]',
	ldap_password='$_POST[ldap_password]',
	ldap_domain='$_POST[ldap_domain]',
	ldap_url='$_POST[ldap_url]',
	imap='$_POST[imap]',
	imap_server='$_POST[imap_server]',
	imap_port='$_POST[imap_port]',
	imap_user='$_POST[imap_user]',
	imap_password='$_POST[imap_password]',
	imap_inbox='$_POST[inbox]'
	WHERE
	id='1'
	";
	$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
	//web redirect
	$www = './index.php?page=admin&subpage=parameters&tab=connector&ldaptest='.$_POST['test_ldap'].'';
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>'; 

}
if($_POST['submit_function'])
{
	//update function tab
	$query = "UPDATE tparameters SET 
	`planning`='$_POST[planning]',
	`procedure`='$_POST[procedure]',
	`asset`='$_POST[asset]',
	`availability`='$_POST[availability]',
	`availability_all_cat`='$_POST[availability_all_cat]',
	`availability_condition_type`='$_POST[availability_condition_type]',
	`availability_condition_value`='$_POST[availability_condition_value]',
	`availability_dep`='$_POST[availability_dep]'
	WHERE
	id='1'
	";
	$exec= mysql_query($query) or die(mysql_error());
	//add cat ot availibility list
	if($_POST['category']!=0)
	{
        $query = "INSERT INTO tavailability (category,subcat) VALUES ('$_POST[category]', '$_POST[subcat]')";
		$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	}
	//add dependncy cat ot availibility list
	if($_POST['depcategory']!=0)
	{
        $query = "INSERT INTO tavailability_dep (category,subcat) VALUES ('$_POST[depcategory]', '$_POST[depsubcat]')";
		$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	}
	//find input name for target values
	$queryyears = mysql_query("SELECT DISTINCT YEAR(date_create) FROM tincidents");
	while ($rowyear=mysql_fetch_array($queryyears))
	{
		$querysubcat = mysql_query("SELECT * FROM `tavailability`");
	    while ($rowsubcat=mysql_fetch_array($querysubcat))
	    {
	    	$inputname="target_$rowyear[0]_$rowsubcat[subcat]";
	    	if($_POST[$inputname]) {
	    		//check existing values
				$check= mysql_query("SELECT * FROM `tavailability_target` WHERE year='$rowyear[0]' AND subcat='$rowsubcat[subcat]'"); 
				$check= mysql_fetch_array($check);
	    		if ($check[0])
	    		{
	    			$query = "UPDATE tavailability_target SET target=$_POST[$inputname] WHERE year=$rowyear[0] AND subcat=$rowsubcat[subcat]";
					$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	    		} else {
	    			$query = "INSERT INTO tavailability_target (year,subcat,target) VALUES ('$rowyear[0]', '$rowsubcat[subcat]', '$_POST[$inputname]')";
					$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	    		}
	    	}
	    }
	}
	
	/*
	
	//web redirect
	$www = "./index.php?page=admin&subpage=parameters&tab=function";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>'; 
	*/
}
//remove cat from availibility list
if ($_GET['deleteavailability']!='')
{
    $query = "DELETE FROM tavailability WHERE id = '$_GET[deleteavailability]'";
    $exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
}
//remove dep cat from availibility dependancy list
if ($_GET['deleteavailabilitydep']!='')
{
    $query = "DELETE FROM tavailability_dep WHERE id = '$_GET[deleteavailabilitydep]'";
    $exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
}
?>
<div class="page-header position-relative">
	<h1>
		<i class="icon-cog"></i>  Paramètres de l'application
	</h1>
</div>
<div class="col-sm-12">	
	<div class="tabbable">
		<ul class="nav nav-tabs" id="myTab">
			<li <?php if ($_GET['tab']=='general' || $_GET['tab']=='') echo 'class="active"'; ?>>
				<a href="./index.php?page=admin&subpage=parameters&tab=general">
					<i class="green icon-wrench bigger-110"></i>
					Général
				</a>
			</li>
			<li <?php if ($_GET['tab']=='connector') echo 'class="active"'; ?>>
				<a href="./index.php?page=admin&subpage=parameters&tab=connector">
					<i class="blue icon-link bigger-110"></i>
					Connecteurs
				</a>
			</li>
			<!--
			<li <?php //if ($_GET['tab']=='function') echo 'class="active"'; ?>>
				<a href="./index.php?page=admin&subpage=parameters&tab=function">
					<i class="orange icon-puzzle-piece bigger-110"></i>
					Fonctions
				</a>
			</li>-->
		</ul>
		<div class="tab-content">
			<div id="general" class="tab-pane <?php if ($_GET['tab']=='general' || $_GET['tab']=='') echo 'active'; ?>">
				<form enctype="multipart/form-data" method="post" action="">
					<div class="profile-user-info profile-user-info-striped">
						<div class="profile-info-row">
							<div class="profile-info-name">
								<i class="icon-desktop"></i>
								Affichage: 
							</div>
							<div class="profile-info-value">
								<span id="username">
									<label for="maxline">Nombre de tickets par page: </label>
									<input type="text" size="2" name="maxline" value="<?php echo $rparameters['maxline']; ?>">
									<i title="Si cette valeur est trop grande cela peut ralentir l'application" class="icon-question-sign blue bigger-110"></i>
									<div class="space-4"></div>
									<label for="time_display_msg">Temps d'affichage des messages d'actions :</label>
									<input name="time_display_msg" type="text" value="<?php echo $rparameters['time_display_msg']; ?>" size="4" /> ms<br />
									<label for="auto_refresh">Actualisation automatique :</label>
									<input name="auto_refresh" type="text" value="<?php echo $rparameters['auto_refresh']; ?>" size="3" /> s 
									<i title="Si la valeur est à 0, alors l'actualisation automatique est désactivée. Attention, cette fonction peut faire clignoter l'écran selon les navigateurs." class="icon-question-sign blue bigger-110"></i><br />
									<div class="space-4"></div>
									<label for="order">Ordre de trie des tickets :</label>
									<select class="textfield" id="order" name="order" >
										<option <?php if ($rparameters['order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create') echo "selected "; ?> value="tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create">Etat > Priorité > Criticité > Date de création</option>
										<option <?php if ($rparameters['order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope') echo "selected "; ?> value="tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope">Etat > Priorité > Criticité > Date de résolution estimé</option>
										<option <?php if ($rparameters['order']=='tstates.number, tincidents.date_hope, tincidents.priority, tincidents.criticality') echo "selected "; ?> value="tstates.number, tincidents.date_hope, tincidents.priority, tincidents.criticality">Etat > Date de résolution estimé > Priorité > Criticité </option>
										<option <?php if ($rparameters['order']=='tstates.number, tincidents.date_hope, tincidents.criticality, tincidents.priority') echo "selected "; ?> value="tstates.number, tincidents.date_hope, tincidents.criticality, tincidents.priority">Etat > Date de résolution estimé > Criticité > Priorité </option>
										<option <?php if ($rparameters['order']=='tstates.number, tincidents.criticality, tincidents.date_hope, tincidents.priority') echo "selected "; ?> value="tstates.number, tincidents.criticality, tincidents.date_hope, tincidents.priority">Etat > Criticité > Date de résolution estimé > Priorité   </option>
										<option <?php if ($rparameters['order']=='id') echo "selected "; ?>  value="id">Numéro de ticket</option>
									</select>
									<i title="Détermine l'ordre de classement des tickets dans la liste des tâches." class="icon-question-sign blue bigger-110"></i><br />
									<div class="space-4"></div>
									<label for="dash_date">Date affichée dans la liste des tickets:</label>
									<select class="textfield" id="dash_date" name="dash_date" >
										<option <?php if ($rparameters['dash_date']=='date_create') echo "selected "; ?> value="date_create">Date de création</option>
										<option <?php if ($rparameters['dash_date']=='date_hope') echo "selected "; ?>  value="date_hope">Date de résolution estimé</option>
									</select>
									<i title="Détermine quelle date afficher dans al liste des tickets." class="icon-question-sign blue bigger-110"></i><br />
									<div class="space-4"></div>
									<label>
										<input type="checkbox" <?php if ($rparameters['meta_state']==1) echo "checked"; ?> name="meta_state" class="ace" value="1">
										<span class="lbl">&nbsp;Gestion du meta état "a traiter"</span>
										<i title="Permet d'afficher un nouvel état regroupant les états en attente de PEC, en cours, et en attente de retour." class="icon-question-sign blue bigger-110"></i>									</label>
								   </label>
								   <div class="space-4"></div>
								    &nbsp;&nbsp;&nbsp;&nbsp;<a target="about_blank" href="./monitor.php">Ecran de supervision</a>
								</span>
							</div>
						</div>
						<div class="profile-info-row">
							<div class="profile-info-name">
								<i class="icon-ticket"></i>
								Tickets: 
							</div>
							<div class="profile-info-value">
								<span id="username">
									<label>
										<input type="checkbox" <?php if ($rparameters['ticket_places']==1) echo "checked"; ?> name="ticket_places" class="ace" value="1">
										<span class="lbl">&nbsp;Gestion des lieux</span>
										<i title="Permet un rattachement du ticket à une localité, une liste des lieux est éditable dans la section liste, un nouveau champ sera disponible sur le ticket." class="icon-question-sign blue bigger-110"></i>
									</label>
									<div class="space-4"></div>
									<label>
										<input type="checkbox" <?php if ($rparameters['ticket_type']==1) echo "checked"; ?> name="ticket_type" class="ace" value="1">
										<span class="lbl">&nbsp;Gestion des types</span>
										<i title="Permet de definir un type à un ticket (ex: Demande, Incident...), ajoute une ligne sur le ticket, la liste des types est administrable dans Administration > Liste" class="icon-question-sign blue bigger-110"></i>
									</label>
								</span>
							</div>
						</div>
						<div class="profile-info-row">
							<div class="profile-info-name">
								<i class="icon-volume-up"></i>
								Son: 
							</div>
							<div class="profile-info-value">
								<span id="username">
									<label>
										<input class="ace" type="checkbox" <?php if ($rparameters['notify']==1) echo "checked"; ?> name="notify" value="1">
										<span class="lbl">&nbsp;Activer la notification sonore pour les nouvelles demandes</span>
										<i title="Active l'avertisseur sonore pour le technicien si un utilisateur déclare un ticket (fonctionne uniquement sur Chrome, Firefox et Safari)" class="icon-question-sign blue bigger-110"></i>
									</label>
								</span>
							</div>
						</div>
						<div class="profile-info-row">
							<div class="profile-info-name">
								<i class="icon-user"></i>
								Utilisateurs: 
							</div>
							<div class="profile-info-value">
								<span id="username">
									<label> 
										<input class="ace" type="checkbox" <?php if ($rparameters['user_advanced']==1) echo "checked"; ?> name="user_advanced" value="1">
										<span class="lbl">&nbsp;Utiliser les propriétés utilisateur avancés</span>
										<i title="Ajoute des champs suplémentaire aux propriétés utilisateurs, Société, FAX, Adresses... " class="icon-question-sign blue bigger-110"></i>
									</label>
									<br />
									<label> 
										<input class="ace" type="checkbox" <?php if ($rparameters['user_register']==1) echo "checked"; ?> name="user_register" value="1">
										<span class="lbl">&nbsp;Les utilisateurs peuvent s'enregistrer.</span>
										<i title="Ajoute un bouton sur la page de connexion, permettant la création de nouveaux utilisateurs." class="icon-question-sign blue bigger-110"></i>
									</label>
								</span>
							</div>
						</div>
						<div class="profile-info-row">
							<div class="profile-info-name">
								<i class="icon-envelope"></i>
								Messages: 
							</div>
							<div class="profile-info-value">
								<span id="username">
									<label>
										<input class="ace" type="checkbox" <?php if ($rparameters['mail_auto']==1) echo "checked"; ?> name="mail_auto" value="1" />
										<span class="lbl">&nbsp;Envoi de mail automatique à l'utilisateur lors de l'ouverture ou fermeture d'un ticket par un technicien</span>
									</label>
									<br />
									<label>
										<input class="ace" type="checkbox" <?php if ($rparameters['mail_newticket']==1) echo "checked"; ?> name="mail_newticket" value="1" />
										<span class="lbl">&nbsp;Envoi de mail automatique à l'administrateur lors de l'ouverture d'un ticket par un utilisateur</span>
									</label>
									<?php 
										if ($rparameters['mail_newticket']=='1') 
										{
											echo "
											<div class=\"space-4\"></div>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Adresse Mail: <input name=\"mail_newticket_address\" type=\"\" value=\"$rparameters[mail_newticket_address]\" size=\"30\" />
											<div class=\"space-4\"></div>
											";
										}
									?>
									<div class="space-4"></div>
									<i class="icon-caret-right blue"></i> Texte début du mail: <input name="mail_txt" type="text" value="<?php echo $rparameters['mail_txt']; ?>" size="80" />
									<i title="Vous pouvez utiliser du code HTML (Exemple: <br />, <b></b>...)" class="icon-question-sign blue bigger-110"></i>
									<div class="space-4"></div>
									<i class="icon-caret-right blue"></i> Adresse en copie: <input name="mail_cc" type="text" value="<?php echo $rparameters['mail_cc']; ?>" size="30" /><br />
									<div class="space-4"></div>
									<i class="icon-caret-right blue"></i> Intitulé de l'émetteur: <input name="mail_from_name" type="text" value="<?php echo $rparameters['mail_from_name']; ?>" size="30" /><br />
									<div class="space-4"></div>
									<i class="icon-caret-right blue"></i> Adresse de l'émetteur: <input name="mail_from_adr" type="text" value="<?php echo $rparameters['mail_from_adr']; ?>" size="30" />
									<i title="Adresse d'envoi de tous les messages de l'application, si ce paramètre est vide les messages seront enovyées avec l'adresse mail de l'utilisateur connecté. Certains serveurs SMTP peuvent exiger que l'émetteur soit le même que le compte de connexion. " class="icon-question-sign blue bigger-110"></i><br />
									<div class="space-4"></div>
									<label>
										<input class="ace" type="checkbox" <?php if ($rparameters['mail_link']==1) echo "checked"; ?> name="mail_link" value="1">
										<span class="lbl">&nbsp;Intégrer un lien vers GestSup</span>
									</label>
									<div class="space-4"></div>
									<i class="icon-caret-right blue"></i> Couleur du titre: #<input  style="background-color: <?php echo "#$rparameters[mail_color_title]"; ?>;" name="mail_color_title" type="text" value="<?php echo $rparameters['mail_color_title']; ?>" size="6" /><br />
									<div class="space-4"></div>
									<i class="icon-caret-right blue"></i> Couleur du fond: #<input  style="background-color: <?php echo "#$rparameters[mail_color_bg]"; ?>;" name="mail_color_bg" type="text" value="<?php echo $rparameters['mail_color_bg']; ?>" size="6" /><br />
									<div class="space-4"></div>
									<i class="icon-caret-right blue"></i> Couleur du texte: #<input  style="background-color: <?php echo "#$rparameters[mail_color_text]"; ?>;" name="mail_color_text" type="text" value="<?php echo $rparameters['mail_color_text']; ?>" size="6" /><br />			
								</span>
							</div>						
						</div>
						<div class="profile-info-row">
							<div class="profile-info-name">
								<i class="icon-bug"></i>
								Debug: 
							</div>
							<div class="profile-info-value">
								<span id="username">
									<label> 
										<input class="ace" type="checkbox" <?php if ($rparameters['debug']==1) echo "checked"; ?> name="debug" value="1">
										<span class="lbl">&nbsp;Activer le mode de débogage</span>
										<i title="Active le mode débuggage afin d'afficher les éléments de résolution de problèmes." class="icon-question-sign blue bigger-110"></i>
									</label>
								</span>
							</div>
						</div>
						<br />
						<br />
						<center>
							<button name="submit_general" id="submit_general" value="submit_general" type="submit" class="btn btn-primary">
								<i class="icon-ok bigger-120"></i>
								Valider
							</button>
						</center>
						<div class="space-4"></div>
					</div>
				</form>
			</div>
			<div id="connector" class="tab-pane <?php if ($_GET['tab']=='connector') echo 'active'; ?>">
				<form enctype="multipart/form-data" method="post" action="">
					<div class="profile-user-info profile-user-info-striped">
						<div class="profile-info-row">
							<div class="profile-info-name"> 
								<i class="icon-envelope"></i>
								&nbsp;SMTP:
							</div>
							<div class="profile-info-value">
								<label>
									<input class="ace" type="checkbox" <?php if ($rparameters['mail']==1) echo "checked"; ?> name="mail" value="1">
									<span class="lbl">&nbspActiver la liaison SMTP
									<i title="Connecteur permettant l'envoi de mails depuis GestSup vers un serveur de messagerie, afin que les mails puissent être envoyés." class="icon-question-sign blue"></i>
								</label>
								<div class=\"space-4\"></div>
								<?php
								if ($rparameters['mail']==1) 
								{
									echo '
									<div class="space-4"></div>
									<label for="mail_smtp"><i class="icon-caret-right blue"></i> Serveur SMTP:</label>
									<input name="mail_smtp" type="text" value="'.$rparameters['mail_smtp'].'" size="20" disabled/>
									<i title="Adresse IP ou Nom de votre serveur de messagerie (Exemple: 192.168.0.1 ou SRVMSG ou smtp.free.fr ou auth.smtp.1and1.fr) " class="icon-question-sign blue bigger-110"></i>
									<div class="space-4"></div>
									<label for="mail_port"><i class="icon-caret-right blue"></i> Port:</label>
									<select class="textfield" id="mail_port" name="mail_port" >
										<option ';if ($rparameters['mail_port']==25) echo "selected "; echo' value="25">25</option>
										<option ';if ($rparameters['mail_port']==465) echo "selected "; echo' value="465">465 (SSL)</option>
										<option ';if ($rparameters['mail_port']==587) echo "selected "; echo' value="587">587 (TLS)</option>
									</select>
									<i title="Port du serveur de messagerie par défaut le port 25 est utilisé, pour les connexion sécurisé les ports 465 et 587 sont utilisés. (exemple: OVH port 587 1&1 port 25)" class="icon-question-sign blue bigger-110"></i>
									<div class="space-4"></div>
									<label for="mail_secure"><i class="icon-caret-right blue"></i> Préfixe :</label>
									<select class="textfield" id="mail_secure" name="mail_secure" >
										<option ';if ($rparameters['mail_secure']==0) echo "selected "; echo' value="0">Aucun</option>
										<option ';if ($rparameters['mail_secure']=='SSL') echo "selected "; echo' value="SSL">ssl//</option>
										<option ';if ($rparameters['mail_secure']=='TLS') echo "selected "; echo' value="TLS">tls//</option>
									</select>
									 ';
									    if ($rparameters['mail_secure']=='SSL' || $rparameters['mail_secure']=='TLS') {echo'<i><font color="red">(Attention l\'extension php_openssl doit être activée)</font></i>';} else {echo'<i title="Si votre serveur de messagerie est sécurisé avec SSL ou TLS (Exemple: Gmail utilise TLS)."  class="icon-question-sign blue bigger-110"></i>';} 
									 echo '
								
									<div class="space-4"></div>
									<label>
										<input class="ace" type="checkbox"'; if ($rparameters['mail_auth']==1) {echo "checked";}  echo ' name="mail_auth" value="1">
										<span class="lbl">&nbsp;Serveur SMTP Authentifié</span>
										<i title="Cochez cette case si votre serveur de messagerie nécessite un identifiant et mot de passe pour envoyer des messages." class="icon-question-sign blue bigger-110"></i>
									</label>
								
									';
										if ($rparameters['mail_auth']==1) 
										{
										echo "
											<div class=\"space-4\"></div>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Utilisateur: <input name=\"mail_username\" type=\"text\" value=\"$rparameters[mail_username]\" size=\"30\"  disabled/>
											<div class=\"space-4\"></div>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Mot de passe: <input name=\"mail_password\" type=\"password\" value=\"$rparameters[mail_password]\" size=\"30\"  disabled/>
                                            ";
										}
								}
								?>
							</div>
						</div>
						<div class="profile-info-row">
							<div class="profile-info-name"> 
								<i class="icon-book"></i>
								&nbsp;LDAP:
							</div>
							<div class="profile-info-value">
								<div class="control-group">	
									<label>
										<input class="ace" type="checkbox" <?php if ($rparameters['ldap']==1) echo "checked"; ?> name="ldap" value="1">
										<span class="lbl">&nbspActiver la liaison LDAP</span>
										<i title="Connecteur permettant la synchronisation entre l'annuaire d'entreprise (Active Directory ou OpenLDAP) et GestSup" class="icon-question-sign blue"></i>
									</label>
									<?php if ($rparameters['ldap']=='1') 
									{
										echo "<hr />
										<label>
											<input class=\"ace\" type=\"checkbox\""; if ($rparameters['ldap_auth']==1) echo "checked"; echo " name=\"ldap_auth\" value=\"1\">
											<span class=\"lbl\">&nbsp;Activer l'authentification GestSup avec LDAP
											<i title=\"Active l'authentification des utilisateurs dans GesStup, avec les identifiants présents dans l'annuaire LDAP. Cela ne désactive pas l'authentification avec la base utilisateurs de GestSup.\" class=\"icon-question-sign blue \"></i>
										</label>
										<div class=\"space-4\"></div>
										Type de serveur LDAP: 
										<select id=\"ldap_type\" name=\"ldap_type\" >
											<option ";if ($rparameters['ldap_type']==0) echo "selected "; echo" value=\"0\">Active Directory</option>
											<option ";if ($rparameters['ldap_type']==1) echo "selected "; echo" value=\"1\">OpenLDAP</option>
										</select>
										<i title=\"Selectionner si votre serveur d'annuaire est Windows Active Direcory ou OpenLDAP.\" class=\"icon-question-sign blue bigger-110\"></i><br />
										<div class=\"space-4\"></div>
										Nom du serveur LDAP:
										<input name=\"ldap_server\" type=\"text\" value=\"$rparameters[ldap_server]\" size=\"20\" />
										<i title=\"Adresse IP ou nom netbios du serveur d'annuaire, sans suffixe DNS (Exemple: 192.168.0.1 ou SVRAD). \" class=\"icon-question-sign blue bigger-110\"></i><br />
										<div class=\"space-4\"></div>
										Port LDAP: 
										<select id=\"ldap_port\" name=\"ldap_port\" >
											<option ";if ($rparameters['ldap_port']==389) echo "selected "; echo" value=\"389\">389</option>
											<option ";if ($rparameters['ldap_port']==636) echo "selected "; echo" value=\"636\">636</option>
										</select>
										<i title=\"Le port par défaut est 389 si vous utilisez un serveur LDAPS (sécurisé) le port est 636. \" class=\"icon-question-sign blue bigger-110\"></i> <br />
										<div class=\"space-4\"></div>
										Domaine:
										<input name=\"ldap_domain\" type=\"text\" value=\"$rparameters[ldap_domain]\" size=\"20\" />
										<i title=\"Nom du domaine FQDN (Exemple: exemple.local).\" class=\"icon-question-sign blue bigger-110\"></i> <br />
										<div class=\"space-4\"></div>
										Emplacement des utilisateurs:
										<input name=\"ldap_url\" type=\"text\" value=\"$rparameters[ldap_url]\" size=\"20\" />
										<i title=\"Emplacement dans l'annuaire des utilisateurs. Par défaut pour active directory cn=users, si vous utiliser des unités d'organisation alors ou=ouname2,ou=ouname1..., attention il ne doit pas être suffixé du domaine. \" class=\"icon-question-sign blue bigger-110\"></i> <br />
										<div class=\"space-4\"></div>
										Utilisateur: <input name=\"ldap_user\" type=\"text\" value=\"$rparameters[ldap_user]\" size=\"20\" />
										<i title=\"Utilisateur présent dans l'annuaire LDAP, pour OpenLDAP l'utilisateur doit être à la racine et de type CN\" class=\"icon-question-sign blue bigger-110\"></i> <br />
										<div class=\"space-4\"></div>
										Mot de passe:
										<input name=\"ldap_password\" type=\"password\" value=\"$rparameters[ldap_password]\" size=\"20\" /><br />
										<br />
										<button name=\"test_ldap\" value=\"1\" type=\"submit\" class=\"btn btn-xs btn-info\">
											<i class=\"icon-refresh bigger-120\"></i>
											Test du connecteur LDAP
										</button>
										<br /><br />
										";
										//check LDAP parameters
										if($_GET['ldaptest']==1) {
										include('./core/ldap.php');
										echo "&nbsp;&nbsp;$ldap_connection<br />";
										} 
									}
									?>
								</div>
							</div>
						</div>
						<div class="profile-info-row">
							<div class="profile-info-name"> 
								<i class="icon-download-alt"></i>
								&nbsp;IMAP:
							</div>
							<div class="profile-info-value">
								<label>
									<input class="ace" type="checkbox" <?php if ($rparameters['imap']==1) echo "checked"; ?> name="imap" value="1">
									<span class="lbl">&nbspActiver la liaison IMAP
									<i title="Connecteur permettant de créer des tickets automatiquement en interrogeant une boite mail. Une fois le mail converti en ticket le message passe en lu dans la boite de messagerie. Attention une tâche planifiée doit être crée afin d'interroger de manière régulière la boite mail (cf FAQ)" class="icon-question-sign blue"></i>
								</label>
								<div class=\"space-4\"></div>
								<?php
								if ($rparameters['imap']=='1') 
								{
									echo "
										Serveur de Messagerie POP/IMAP: <input name=\"imap_server\" type=\"text\" value=\"$rparameters[imap_server]\" size=\"20\" />
										<i title=\"Adresse IP ou nom netbios ou nom FQDN du serveur POP ou IMAP de messagerie (ex: imap.free.fr)\" class=\"icon-question-sign blue bigger-110\"></i>
										<div class=\"space-4\"></div>
										Protocole : 
										<select id=\"imap_port\" name=\"imap_port\" >
											<option ";if ($rparameters['imap_port']==143) echo "selected "; echo" value=\"143\">IMAP (port: 143)</option>
											<option ";if ($rparameters['imap_port']=="110/pop3") echo "selected "; echo" value=\"110/pop3\">POP (port: 110)</option>
											<option ";if ($rparameters['imap_port']=="993/imap/ssl/novalidate-cert") echo "selected "; echo"value=\"993/imap/ssl/novalidate-cert\">IMAP sécurisé (port: 993)</option>
											<option ";if ($rparameters['imap_port']=="993/pop/ssl") echo "selected "; echo"value=\"993/pop/ssl\">POP sécurisé (port: 993)</option>
										</select>
										<i title=\"Protocle utilisé sur le serveur POP ou IMAP sécurisé ou non (ex: pour imap.free.fr selectionner IMAP\" class=\"icon-question-sign blue bigger-120\"></i>
										<div class=\"space-4\"></div>
										Dossier racine : 
										<select id=\"inbox\" name=\"inbox\" >
											<option ";if ($rparameters['imap_inbox']=='INBOX') echo "selected "; echo" value=\"INBOX\">INBOX</option>
											<option ";if ($rparameters['imap_inbox']=='') echo "selected "; echo" value=\"\">Aucun</option>
										</select>
										<i title=\"Dossier racine ou se trouve les messages entrants (par défaut INBOX)\" class=\"icon-question-sign blue bigger-110\"></i>
										<div class=\"space-4\"></div>
										Adresse de messagerie: 
										<input name=\"imap_user\" type=\"text\" value=\"$rparameters[imap_user]\" size=\"25\" />
										<i title=\"Adresse de la boite de messagerie à relever.\" class=\"icon-question-sign blue bigger-110\"></i>
										<div class=\"space-4\"></div>
										Mot de passe: 
										<input name=\"imap_password\" type=\"password\" value=\"$rparameters[imap_password]\" size=\"20\" /><br />
										<div class=\"space-4\"></div>
										<button name=\"test_imap\" OnClick=\"window.open('./mail2ticket.php')\"  value=\"test_imap\" type=\"submit\" class=\"btn btn-xs btn-info\">
											<i class=\"icon-download-alt bigger-120\"></i>
											Lancer la recupération de messages
										</button>
										
									";
								}
								?>
								<br />
								<br />
								<center>
									<button name="submit_connector" id="submit_connector" value="submit_connector" type="submit" class="btn btn-primary">
										<i class="icon-ok bigger-120"></i>
										Valider
									</button>
								</center>
								<div class="space-4"></div>
							</div>
						</div>
					</div>	
				</form>
			</div>
			<div id="function" class="tab-pane <?php if ($_GET['tab']=='function') echo 'active'; ?>">
				<form enctype="multipart/form-data" method="POST" action="">
					<div class="profile-user-info profile-user-info-striped">
					    <?php include('./plugins/availability/admin/parameters.php') ?>
						<div class="profile-info-row">
							<div class="profile-info-name"> 
								<i class="icon-calendar"></i>
								Calendrier:
							</div>
							<div class="profile-info-value">
								<label>
									<input class="ace" type="checkbox" <?php if ($rparameters['planning']==1) echo "checked"; ?> name="planning" value="1">
									<span class="lbl">&nbsp;Activer la fonction Calendrier</span>
									<i title="Active la gestion de planning, nouvel onglet et gestion dans les tickets" class="icon-question-sign blue"></i>
								</label>
							</div>
						</div>
						<!--
						<div class="profile-info-row">
							<div class="profile-info-name"> 
								<i class="icon-desktop"></i>
								Matériels:
							</div>
							<div class="profile-info-value">
								<label>
									<input class="ace" type="checkbox" <?php if ($rparameters['asset']==1) echo "checked"; ?> name="asset" value="1">
									<span class="lbl">&nbsp;Activer la fonction Matériel</span>
									<i title="Active la gestion des matériels" class="icon-question-sign blue"></i>
								</label>
							</div>
						</div>
						-->
						<div class="profile-info-row">
							<div class="profile-info-name"> 
								<i class="icon-book"></i>
								Procédure:
							</div>
							<div class="profile-info-value">
								<label>
									<input class="ace" type="checkbox" <?php if ($rparameters['procedure']==1) echo "checked"; ?> name="procedure" value="1" /> 
									<span class="lbl">&nbsp;Activer la fonction Procédure</span>
									<i title="Active la gestion des procedures" class="icon-question-sign blue"></i>
								</label>
							</div>
							<div class="space-4"></div>
							<center>
								<button name="submit_function" id="submit_function" value="submit_function" type="submit" class="btn btn-primary">
									<i class="icon-ok bigger-120"></i>
									Valider
								</button>
							</center>
							<br />
						</div>
					</div>
					<div class="space-4"></div>
				</form>
			</div>
		</div>
	</div>
</div>