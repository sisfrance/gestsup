<?php
################################################################################
# @Name : install_update.php
# @Desc : install update 
# @call : admin.php
# @parameters : 
# @Autor : Flox
# @Create : 06/11/2012
# @Update : 07/01/2014
# @Version : 3.0.3
################################################################################

//initialize variables 
if(!isset($command)) $command= '';
if(!isset($error)) $error= '';
if(!isset($_POST['step'])) $_POST['step']= '';

//defaults values
if(!isset($step)) $step= '1';

//modify steps
if($_POST['step']==1) $step=2;
if($_POST['step']==2) $step=3;
if($_POST['step']==3) $step=4;
if($_POST['step']==4) $step=5;
if($_POST['step']==5) $step=6;
if($_POST['step']==6) $step=7;
if($_POST['step']==7) $step=8;

//date
$date = date("Y-m-d");

//display backup warning
if ($step==1)
{
	$boxtitle="<i class='icon-save red'></i> Réaliser une sauvegarde";
	$boxtext='
	<form name="form" method="POST" action="" id="form">
		<input name="step" type="hidden" value="1">
		<input name="install" type="hidden" value="1">
		Il est recommandé de réaliser une sauvegarde avant lancer la mise à jour (base de donnée et fichiers).
		<br />
		<br />
		<a target="about_blank" href="http://gestsup.fr/index.php?page=install#sav">Plus d\'informations</a>
	</form>
	';
	$valid="Continuer";
	$action1="$('form#form').submit();";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
	include "./modalbox.php"; 
}
//display time warning
if ($step==2)
{
	$boxtitle="<i class='icon-time red'></i> Temps de l'installation";
	$boxtext='
	<form name="form" method="POST" action="" id="form">
		<input name="step" type="hidden" value="2">
		<input name="install" type="hidden" value="1">
		Attention cette procédure peut prendre du temps en fonction de votre base actuelle.
	</form>
	';
	$valid="Continuer";
	$action1="$('form#form').submit();";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
	include "./modalbox.php"; 
}
//start display
if ($step==3)
{
	$boxtitle="<i class='icon-bolt red'></i> Lancement de l'installation";
	$boxtext='
	<form name="form" method="POST" action="" id="form">
		<input name="step" type="hidden" value="3">
		<input name="install" type="hidden" value="1">
		Voulez-vous lancer l\'installation de '.$installfile.' ?
	</form>
	';
	$valid="Lancer";
	$action1="$('form#form').submit();";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
	include "./modalbox.php"; 
}
//extract last version or last patch
if ($step==4)
{	
	//create temporary directory
	if(file_exists('./download/tmp')) {} else {mkdir("./download/tmp");} 
	//extract data into temporary directory
	$zip = new ZipArchive;
    $res = $zip->open('./download/'.$installfile.'');
    if ($res === TRUE) {
        $zip->extractTo('./download/tmp/');
        $zip->close();
	}
	//check extract
	if(file_exists('./download/tmp/changelog.php'))
	{
		$result="- Extraction des fichiers: <i class=\"icon-ok-sign icon-large green\"></i><br />";
	    $step=5;
    } else {
		$result="-Extraction des fichiers: <i class=\"icon-remove-sign icon-large red\"></i> open=$res <br />";
		$error=1;
	}
}
//install SQL update
if ($step==5)
{
	//case version update
	if ($type=='version')
	{
		//find list of sql update
		$matches = glob("./download/tmp/_SQL/*.sql"); 
		foreach ($matches as $filename) {
			//delete name directory
			$filename=explode ('./download/tmp/_SQL/',$filename);
			$filename=$filename[1];
			//find source version of this file
			$src=explode('_',$filename);
			$src=$src[1];
			//find destination version of this file
			$dst=explode('_to_',$filename);
			$dst=explode('.sql',$dst[1]);
			$dst=$dst[0];
			//keep only superior patch
			$subsrc=explode('.',$src);
			$subactu=explode('.',$vactu);
			if ($subsrc[0]>=$subactu[0])
			{
				if ($subsrc[1]>=$subactu[1]) 
				{
					if ($subsrc[2]>=$subactu[2]) 
					{
						//import script
						$sql_file=file_get_contents('./download/tmp/_SQL/'.$filename.'');
						$sql_file=explode(";", $sql_file);
						foreach ($sql_file as $value) {
							mysql_query($value);
						} 
					}
				}
			}
		}
		//check
		$qvactu = mysql_query("SELECT * FROM `tparameters`");
		$rvactu = mysql_fetch_array($qvactu);
		$vactu="$rvactu[version]";
		if ($vactu==$vserv) {
			$result=$result."- Modification base de données: <i class=\"icon-ok-sign icon-large green\"></i><br />";
			$step=6;
		} else {
			$result=$result."- Modification base de données: <i class=\"icon-remove-sign icon-large red\"></i><br />";
			$error=1;
		}
	}
	//case patch update
	if ($type=='patch')
	{
		$storefilename='update_'.$vactu.'_to_'.$pservfull[0].'.sql';
		$sql_file=file_get_contents('./download/tmp/update_'.$vactu.'_to_'.$pservfull[0].'.sql');
		$sql_file=explode(";", $sql_file);
		foreach ($sql_file as $value) {
			mysql_query($value);
		}
		//check
		$qvactu = mysql_query("SELECT * FROM `tparameters`");
		$rvactu = mysql_fetch_array($qvactu);
		$vactu="$rvactu[version]";
		if ($vactu==$pservfull[0]) {
			$result=$result."- Modification base de données: <i class=\"icon-ok-sign icon-large green\"></i><br />";
			$step=6;
		} else {
			$result=$result."- Modification base de données: <i class=\"icon-remove-sign icon-large red\"></i><br />"; 
			$error=1;
		}
		//remove patch.sql to exclude this file for the next copy 
		unlink('./download/tmp/'.$storefilename.'');
	}
}
//copy lastest files
if ($step==6)
{
	//backup current connect.php file
	copy('./connect.php', './backup/connect.php'); 
	//recursive copy with new files
	function recurse_copy($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					recurse_copy($src . '/' . $file,$dst . '/' . $file); 
				} 
				else { 
					copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	}  
	recurse_copy("./download/tmp/","./");
	//restore current connect.php file
	rename('./backup/connect.php', './connect.php'); 
	$result=$result."- Copie des nouveaux fichiers: <i class=\"icon-ok-sign icon-large green\"></i><br />";
	$step=7;
}
//clean temporary folder.
if ($step==7)
{
	//delete download file
	unlink("./download/$installfile");
	//remove temporary directory
	function rrmdir($dir) {
	   if (is_dir($dir)) {
		 $objects = scandir($dir);
		 foreach ($objects as $object) {
		   if ($object != "." && $object != "..") {
			 if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
		   }
		 }
		 reset($objects);
		 rmdir($dir);
	   }
	 }
	$dir="./download/tmp/";
	rrmdir($dir);
	$result=$result."- Nettoyage de l'installation: <i class=\"icon-ok-sign icon-large green\"></i><br />";
	$step=8;
}
if ($step==8)
{
	$boxtitle="<i class='icon-circle-arrow-up red'></i> Rapport d'installation";
	$boxtext='
	<form name="form" method="POST" action="" id="form">
		<input name="install" type="hidden" value="1">
		L\'installation c\'est correctement déroulée:<br /><br />
		'.$result.'<br />
		Afin de finaliser la procédure, déconnectez vous, videz le cache de votre navigateur, et re-lancer l\'application.
	</form>
	';
	$valid="Continuer";
	$action1="$( this ).dialog( \"close\" ); ";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
	include "./modalbox.php"; 
	
} elseif ($error==1) {
	$boxtitle="<i class='icon-circle-arrow-up red'></i> Rapport d'installation";
	$boxtext='
	<form name="form" method="POST" action="" id="form">
		<input name="install" type="hidden" value="1">
		Une erreur est survenue pendant l\'installation, il est recommandé de restaurer votre base de données et vos fichiers, puis de lancer la procédure manuellement.:<br /><br />
		'.$result.'<br />
	</form>
	';
	$valid="Continuer";
	$action1="$( this ).dialog( \"close\" ); ";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
	include "./modalbox.php"; 
}
?>