<?php
################################################################################
# @Name : update.php
# @Desc : page to update GestSup
# @call : admin.php
# @parameters : 
# @Autor : Flox
# @Create : 20/01/2011
# @Update : 20/01/2014
# @Version : 3.0.5
################################################################################

//initialize variables 
if(!isset($contents[0])) $contents[0] = '';
if(!isset($_POST['update_channel'])) $_POST['update_channel'] = '';
if(!isset($_POST['check'])) $_POST['check'] = '';
if(!isset($_POST['download'])) $_POST['download'] = '';
if(!isset($_POST['install'])) $_POST['install'] = '';
if(!isset($_POST['install_update'])) $_POST['install_update'] = '';
if(!isset($_GET['install_update'])) $_GET['install_update'] = '';
if(!isset($findpatch)) $findpatch = '';

//display title
echo '
<div class="page-header position-relative">
	<h1>
		<i class="icon-circle-arrow-up"></i>  Mise à jour de GestSup
	</h1>
</div>
';

//update update channel parameter
if($_POST['update_channel']) 
{
	$query="UPDATE tparameters SET update_channel='$_POST[update_channel]'";
	mysql_query($query);
}
$qupdatechannel = mysql_query("SELECT update_channel FROM tparameters");
$rupdatechannel = mysql_fetch_array($qupdatechannel);
$rupdatechannel = $rupdatechannel[0];

//update server parameters
$ftp_server="gestsup.fr";
$ftp_user_name="gestsup";
$ftp_user_pass="gestsup";

//find current version
$vactu=$rparameters['version'];

//find number of current patch
$pactu=explode('.',$rparameters['version']);
$pactu=$pactu[2];

//check lastest version
$conn_id = ftp_connect($ftp_server,21,5) or die("Connexion au serveur FTP impossible (Vérifier votre connexion Internet et votre pare-feu sur le port 21)");
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
$pasv = ftp_pasv($conn_id, true);
$contents = ftp_nlist($conn_id, "./versions/current/$rparameters[update_channel]/gestsup*");
if($contents) {
	$vserv=explode("_",$contents[0]);
	$vserv=explode(".zip",$vserv['1']);
	$vserv=$vserv[0];
} else {$vserv='';}

////check patch + 1
$n1=$pactu+1;
$filter=explode('.',$rparameters['version']);
$filter="$filter[0].$filter[1].$n1";
$conn_id = ftp_connect($ftp_server,21,5) or die("Connexion au serveur FTP impossible (Vérifier votre connexion Internet et votre pare-feu sur le port 21)");
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
$pasv = ftp_pasv($conn_id, true);
$contents = ftp_nlist($conn_id, "./versions/current/$rparameters[update_channel]/patch_$filter.zip");
if($contents) {
	$pserv=explode("_",end($contents));
	$pservfull=explode(".zip",$pserv[1]);
	$pserv=explode(".",$pservfull[0]);
	$pserv=$pserv[2];
} else $pserv='';

//generate name of current version
$vactuname=explode('.',$rparameters['version']);
if($vactuname[2]==0) $vactuname=''; else $vactuname="($vactuname[0].$vactuname[1] patch $vactuname[2])";
 

//check update server
if ($vserv!=''){
	$serverstate='<i class="icon-ok-sign icon-large green"></i> <font color="green">Serveur de mise à jour GestSup disponible.</font>';
	
	//compare versions
	if ($vactu==$vserv)
	{
		$message='<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Votre version <strong class="green"><small>'.$vserv.'</small></strong> est à jour.	</div>';
		$findversion=0;
		$checkpatch=1;
	}
	else if ($vactu<$vserv)
	{
		$message='<div class="alert alert-block alert-success"><i class="icon-ok green"></i> La version <strong class="green"><small>'.$vserv.'</small></strong> est disponible.</div>';
		$findversion=1;
	}
	else if ($vactu>$vserv)
	{
		$findversion=0;
		$checkpatch=1;
	}

	//compare patchs
	if($findversion==0 && $checkpatch==1)
	{
		if ($pserv=='')
		{
			$message='<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Votre version <strong class="green"><small>'.$vactu.'</small></strong> est à jour.</div>';
			$findpatch=0;
		}
		else if ($pactu<$pserv)
		{
			$message='<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Le patch <strong class="green"><small>'.$pserv.'</small></strong> de votre version <strong class="green"><small>'.$vactu.'</small></strong> est disponible, vous pouvez lancer le téléchargement.</div>';
			$findpatch=1;
		}
		else if ($pactu>$pserv)
		{
			$message='<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Le patch <strong class="green"><small>'.$pserv.'</small></strong> du serveur est inférieur à celui installée.</div>';
			$findpatch=0;
		}
	}
	//display check message
	if($_POST['check']) echo $message;

	//downloads
	if($_POST['download'])
	{
		if ($vactu<$vserv) //version
		{
			$serveur_file="/versions/current/$rupdatechannel/gestsup_$vserv.zip";
			$monmicro_file="./download/gestsup_$vserv.zip";
			$conn_id = ftp_connect($ftp_server);
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
			if ((!$conn_id) || (!$login_result)) {
				echo'<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Le téléchargement de la dernière version à échoué, vérifiez les droits d\'écriture sur le repertoire ./download.</div>';
				die;
			}
			$pasv = ftp_pasv($conn_id, true);
			$download = ftp_get($conn_id, $monmicro_file, $serveur_file, FTP_BINARY);
			if (!$download) 
			{
				echo'<div class="alert alert-danger"><i class="icon-remove"></i><strong> Erreur:</strong> Le téléchargement de la dernière version à échoué.</div>';
			}
			else 
			{
				echo '<div class="alert alert-block alert-success"><i class="icon-ok green"></i> La version	<strong class="green"><small>'.$vserv.'</small></strong> à été téléchargé dans le repertoire "./download" du serveur web.</div>';
			}
			ftp_quit($conn_id);
		}
		else if ($pactu<$pserv) //patch
		{
			$serveur_file="/versions/current/$rupdatechannel/patch_$pservfull[0].zip";
			$monmicro_file="./download/patch_$pservfull[0].zip";
			$conn_id = ftp_connect($ftp_server);
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
			if ((!$conn_id) || (!$login_result)) {
				echo'<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Le téléchargement du dernier patch à échoué. (connexion impossible)</div>';
				die;
			} 
			$download = ftp_get($conn_id, $monmicro_file, $serveur_file, FTP_BINARY);
			if (!$download) 
			{
				echo'<div class="alert alert-danger"><i class="icon-remove"></i><strong> Erreur:</strong> Le téléchargement du dernier patch à échoué. (Téléchargement impossible)</div>';
			}
			else 
			{
				echo '<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Le patch	<strong class="green"><small>'.$pserv.'</small></strong> à été téléchargé dans le repertoire "./download" du serveur web.</div>';
			}
			ftp_quit($conn_id);
		} else {
			echo '<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Votre version <strong class="green"><small>'.$vactu.'</small></strong>	est à jour, pas de téléchargement nécessaire.</div>';
		}
	}
	//install version
	if($_POST['install'])
	{
		if ($findpatch==0 && $findversion==0)
		{
			echo '<div class="alert alert-danger"><i class="icon-remove"></i> <strong>Erreur:</strong> Installation impossible votre version est à jour.</div>';
		} 
		if($findversion!=0) 
		{
			if(file_exists("./download/gestsup_$vserv.zip"))
			{
				$installfile="gestsup_$vserv.zip";
				$type="version";
				include("./core/install_update.php");
			} else {
				echo '<div class="alert alert-danger"><i class="icon-remove"></i> <strong>Erreur:</strong> Vous devez d\'abord télécharger la dernière version '.$vserv.'.</div>';
			}
		}
		if($findpatch!=0)
		{
			if(file_exists("./download/patch_$pservfull[0].zip"))
			{
				$installfile="patch_$pservfull[0].zip";
				$type="patch";
				include("./core/install_update.php");
			} else {
				echo '<div class="alert alert-danger"><i class="icon-remove"></i> <strong>Erreur:</strong> Vous devez d\'abord télécharger le dernier patch '.$pserv.'</div>';
			}
		}
	}
	
} else {
	$serverstate='<i class="icon-remove-sign icon-large red"></i> <font color="red">Serveur de mise à jour GestSup indisponible, ou vous avez un problème de connection internet ou vous n\'avez pas autorisé le port 21 sur votre firewall.</font>';
}

//display informations
echo'
	<div class="profile-user-info profile-user-info-striped">
		<div class="profile-info-row">
			<div class="profile-info-name"> Version actuelle: </div>
			<div class="profile-info-value">
				<span id="username">'.$rparameters['version'].' <font size="1">'.$vactuname.'</font></span>
			</div>
		</div>
		<div class="profile-info-row">
			<div class="profile-info-name"> Canal: </div>
			<div class="profile-info-value">
				<span id="username">
					<form method="POST" name="form">
						<select name="update_channel" onchange="submit()">
							<option value="stable" '; if ($rupdatechannel=='stable') echo 'selected'; echo '>Stable</option>
							<option value="beta" '; if ($rupdatechannel=='beta') echo 'selected'; echo '>Bêta</option>
						</select>
					</form>
				</span>
			</div>
		</div>
		<div class="profile-info-row">
			<div class="profile-info-name"> Serveur de MAJ: </div>
			<div class="profile-info-value">
				<span id="username">'.$serverstate.'</span>
			</div>
		</div>
	</div>
	<br />
	<br />
	<br />
	<br />
	<center>
		<form method="POST" action="">
			<button  name="check" value="check" type="submit" class="btn btn-primary">
				<i class="icon-ok-sign bigger-120"></i>
				Vérifier 
			</button>
			<button  name="download" value="download" type="submit" class="btn btn-primary">
				<i class="icon-download-alt bigger-120"></i>
				Télécharger
			</button>
			<button name="install" value="install" type="submit" class="btn btn-primary">
				<i class="icon-hdd bigger-120"></i>
				Installation automatique
			</button>
		</form>
			<br />
			<button onclick=\'window.open("http://gestsup.fr/index.php?page=install#update")\' type="submit" class="btn btn-primary">
				<i class="icon-hdd bigger-120"></i>
				Installation manuel
			</button>
			<button onclick=\'window.open("./index.php?page=admin&subpage=backup")\' type="submit" class="btn btn-danger">
				<i class="icon-save bigger-120"></i>
				Réaliser une sauvegarde
			</button>
	</center>

';
?>