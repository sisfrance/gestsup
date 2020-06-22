<?php
################################################################################
# @Name : backup.php
# @Desc : save and restore page
# @call : /admin/admin.php
# @paramters : 
# @Autor : Flox
# @Update : 13/03/2014
# @Version : 3.0.7
################################################################################

//initialize variables 
if(!isset($action)) $action = '';
if(!isset($command)) $command = '';
if(!isset($date)) $date = '';
if(!isset($_FILES['restore']['name'])) $_FILES['restore']['name'] = '';
if(!isset($_FILES['logo']['name'])) $_FILES['logo']['name'] = '';
if(!isset($_POST['upload'])) $_POST['upload'] = '';
if(!isset($_GET['action'])) $_GET['action'] = '';

//date
$date = date("Y_m_d_H_i_s");

if ($_GET['action']=="backup")
{
	//dump SQL
	require('./core/mysqldump.php');
	$file = "./_SQL/backup-gestsup-$rparameters[version]-$date.sql";
	dumpMySQL("$serveur", "$user", "$password", "$base", 3, "$file");
	
	//backup files
	function Zip($source, $destination)
	{
		if (!extension_loaded('zip') || !file_exists($source)) {
			return false;
		}

		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}

		$source = str_replace('\\', '/', realpath($source));

		if (is_dir($source) === true)
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

			foreach ($files as $file)
			{
			    if (strpos($file,'backup_gestsup') == false) {
    				$file = str_replace('\\', '/', $file);
    
    				// Ignore "." and ".." folders
    				if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
    					continue;
    
    				$file = realpath($file);
                    
    				if (is_dir($file) === true)
    				{
    					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
    				}
    				else if (is_file($file) === true)
    				{
    					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
    				}
			    }
			}
		}
		else if (is_file($source) === true)
		{
			$zip->addFromString(basename($source), file_get_contents($source));
		}

		return $zip->close();
	}
	Zip('./', './backup/backup_gestsup_'.$rparameters['version'].'_'.$date.'.zip');	
	//check backup
	if(file_exists('./backup/backup_gestsup_'.$rparameters['version'].'_'.$date.'.zip')) 
	{
		echo'
			<div class="alert alert-block alert-success">
				<button type="button" class="close" data-dismiss="alert">
					<i class="icon-remove"></i>
				</button>
				<i class="icon-ok green"></i>
				La sauvegarde à été réalisée avec succès, une copie de l\'archive se trouve dans le répertoire ./backup de votre serveur web.
			</div>
		';
		$step=5;
	} else {
		echo'
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">
				<i class="icon-remove"></i>
			</button>
			<strong>
				<i class="icon-remove"></i>
				Erreur
			</strong>
			La sauvegarde de l\'application à échoué.
			<br>
		</div>
		';
		$error=1;
	}

	//redirect
	$www = "./index.php?page=admin&subpage=backup&download=backup_gestsup_$rparameters[version]_$date.zip";
	echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
	
}
if($_POST['upload'])
{
//next dev
}

?>
<div class="page-header position-relative">
	<h1>
		<i class="icon-save"></i>  Sauvegardes de GestSup
	</h1>
</div>
<center>
	<button onclick='window.location.href="./index.php?page=admin&subpage=backup&action=backup"' type="submit" class="btn btn-primary">
		<i class="icon-download bigger-140"></i>
		Lancer la sauvegarde
	</button>
	<!--<button onclick='window.open("/phpmyadmin/index.php?db=<?php echo $base; ?>")' type="submit" class="btn btn-primary">
		<i class="icon-cog bigger-140"></i>
		Administrer la base de donnée
	</button>-->
</center>