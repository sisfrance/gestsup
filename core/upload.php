<?php
################################################################################
# @Name : upload.php
# @Desc : upload attached files 
# @call : ticket.php
# @parameters : 
# @Autor : Flox
# @Create : 
# @Update : 21/10/2014
# @Version : 3.0.10
################################################################################

//initialize variables 
if(!isset($extensionFichier)) $extensionFichier = '';
if(!isset($_GET['id'])) $_GET['id'] = '';
if(!isset($nomorigine)) $nomorigine = '';
if(!isset($number)) $number = '';
if(!isset($_FILES['file1']['name'])) $_FILES['file1']['name'] = '';
if(!isset($_FILES['file2']['name'])) $_FILES['file2']['name'] = '';
if(!isset($_FILES['file3']['name'])) $_FILES['file3']['name'] = '';
if(!isset($_FILES['file4']['name'])) $_FILES['file4']['name'] = '';
if(!isset($_FILES['file5']['name'])) $_FILES['file5']['name'] = '';
if(!isset($file1_rename)) $file1_rename = '';
if(!isset($file2_rename)) $file2_rename = '';
if(!isset($file3_rename)) $file3_rename = '';
if(!isset($file4_rename)) $file4_rename = '';
if(!isset($file5_rename)) $file5_rename = '';

//change special character in filename
$a = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'š', 'ž', "'", " ", "/", "%", "?", ":", "!", "’", ",");
$b = array("a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "oe", "u", "u", "u", "u", "y", "y", "s", "z", "-", "-", "-", "-", "", "-", "", "-", "-");

$file1_rename = str_replace($a,$b,$_FILES['file1']['name']);
$file2_rename = str_replace($a,$b,$_FILES['file2']['name']);
$file3_rename = str_replace($a,$b,$_FILES['file3']['name']);
$file4_rename = str_replace($a,$b,$_FILES['file4']['name']);
$file5_rename = str_replace($a,$b,$_FILES['file5']['name']);

//black list exclusion for extension
$blacklist =  array('php','php3' ,'php4', 'js', 'htm', 'html', 'phtml');

//for new ticket
if ($_GET['id']=="") $_GET['id']=$number;

if($_FILES['file1']['name'])
{
	//if id directory not exist, create it
	if (is_dir("./upload/$_GET[id]")) echo ""; else mkdir ("./upload/$_GET[id]/", 0777);
	$filename = $_FILES['file1']['name'];
	//secure check for extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($ext,$blacklist) ) {
        $repertoireDestination = dirname(__FILE__)."../../upload/$_GET[id]/$file1_rename";
		if (move_uploaded_file($_FILES['file1']['tmp_name'], $repertoireDestination)) 
		{
		} else {
			echo "Erreur de transfert vérifier le chemin ".$repertoireDestination;
		}
		$query = "UPDATE tincidents SET img1='$file1_rename' WHERE id='$_GET[id]'";
		$execution = mysql_query($query) or die('Erreur SQL !<br><br>'.mysql_error());
    } else {echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>Blocage de sécurité:</strong> Type de fichier interdit.<br></div>';}
}
if($_FILES['file2']['name'])
{ 
	//if id directory not exist, create it
	if (is_dir("./upload/$_GET[id]")) echo ""; else mkdir ("./upload/$_GET[id]/", 0777);
	$filename = $_FILES['file2']['name'];
	//secure check for extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($ext,$blacklist) ) {
        $repertoireDestination = dirname(__FILE__)."../../upload/$_GET[id]/";
    	if (move_uploaded_file($_FILES['file2']['tmp_name'], $repertoireDestination.$file2_rename)   ) 
    	{
    	} else {echo "Erreur de transfert vérifier le chemin ".$repertoireDestination;
    	}
    	$query = "UPDATE tincidents SET img2='$file2_rename' WHERE id='$_GET[id]'";
    	$execution = mysql_query($query) or die('Erreur SQL !<br><br>'.mysql_error());
    } else {        echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>Blocage de sécurité:</strong> Type de fichier interdit.<br></div>';}
}
if($_FILES['file3']['name'])
{
	//if id directory not exist, create it
	if (is_dir("./upload/$_GET[id]")) echo ""; else mkdir ("./upload/$_GET[id]/", 0777);
	$filename = $_FILES['file3']['name'];
	//secure check for extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($ext,$blacklist) ) {
        $repertoireDestination = dirname(__FILE__)."../../upload/$_GET[id]/";
    	if (move_uploaded_file($_FILES['file3']['tmp_name'], $repertoireDestination.$file3_rename)   ) 
    	{
    	} else {echo "Erreur de transfert vérifier le chemin ".$repertoireDestination;
    	}
    	$query = "UPDATE tincidents SET img3='$file3_rename' WHERE id='$_GET[id]'";
    	$execution = mysql_query($query) or die('Erreur SQL !<br><br>'.mysql_error());
    } else {        echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>Blocage de sécurité:</strong> Type de fichier interdit.<br></div>';}
}
if($_FILES['file4']['name'])
{
	//if id directory not exist, create it
	if (is_dir("./upload/$_GET[id]")) echo ""; else mkdir ("./upload/$_GET[id]/", 0777);
	$filename = $_FILES['file4']['name'];
	//secure check for extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($ext,$blacklist) ) {
       	$repertoireDestination = dirname(__FILE__)."../../upload/$_GET[id]/";
    	if (move_uploaded_file($_FILES['file4']['tmp_name'], $repertoireDestination.$file4_rename)   ) 
    	{
    	} else {echo "Erreur de transfert vérifier le chemin ".$repertoireDestination;
    	}
    	$query = "UPDATE tincidents SET img4='$file4_rename' WHERE id='$_GET[id]'";
    	$execution = mysql_query($query) or die('Erreur SQL !<br><br>'.mysql_error());
    } else {        echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>Blocage de sécurité:</strong> Type de fichier interdit.<br></div>';}
}
if($_FILES['file5']['name'])
{
	//if id directory not exist, create it
	if (is_dir("./upload/$_GET[id]")) echo ""; else mkdir ("./upload/$_GET[id]/", 0777);
	$filename = $_FILES['file5']['name'];
	//secure check for extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!in_array($ext,$blacklist) ) {
      	$repertoireDestination = dirname(__FILE__)."../../upload/$_GET[id]/";
    	if (move_uploaded_file($_FILES['file5']['tmp_name'], $repertoireDestination.$file5_rename)   ) 
    	{
    	} else {
    	echo "Erreur de transfert vérifier le chemin ".$repertoireDestination;
    	}
    	$query = "UPDATE tincidents SET img5='$file5_rename' WHERE id='$_GET[id]'";
    	$execution = mysql_query($query) or die('Erreur SQL !<br><br>'.mysql_error());
    } else {echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>Blocage de sécurité:</strong> Type de fichier interdit.<br></div>';}
}
?>