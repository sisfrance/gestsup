<?php
//connexion script with database parameters
require "connect.php";
mysql_query("SET NAMES 'UTF8'");
setlocale(LC_TIME, "fr_FR");

//initialize variables
if(!isset($userreg)) $userreg = '';
if(!isset($u_group)) $u_group = '';
if(!isset($globalrow['u_group'])) $globalrow['u_group'] = '';
if(!isset($_POST['user'])) $_POST['user'] = '';
if(!isset($_POST['technician'])) $_POST['technician'] = '';

//master query
$globalquery = mysql_query("SELECT * FROM tincidents WHERE id LIKE '$_GET[id]'");
$globalrow=mysql_fetch_array($globalquery);

// Secure
if($_GET['id'] != $globalrow['id']) {
	header('Location: index.php?page=dashboard');
	die();
};

// Création de la ligne d'en-tête
$entete = array("Ticket ID","Nom", "Prénom", "Société", "Adresse", "ville", "code postal", "Téléphone", "Technicien", "Nature de l'intervention", "Catégorie", "Numéro de série", "Date d'ouverture", "Date de fermeture", "Temps de trajet", "Temps passé", "Garantie", "Contrat de maintenance", "Description", "Détail");

//Initialisation des variables
$csvid = $_GET['id'];

if (($globalrow['u_group']==0 && $u_group=='') || $_POST['user']!="")
{
    if ($_POST['user'])	{$user=$_POST['user'];}	else {$user=$globalrow['user'];}
    $query = mysql_query("SELECT * FROM tusers WHERE id LIKE $user");
    $row = mysql_fetch_array($query);
} else {
    if (($globalrow['u_group']!=$u_group) && $u_group!=''){$group=$u_group;}else {$group=$globalrow['u_group'];}
    $query = mysql_query("SELECT * FROM `tgroups` WHERE id=$group");
    $row = mysql_fetch_array($query);
}
$csvnom = $row[lastname];
$csvprenom = $row[firstname];

$query = mysql_query("
	SELECT c.name
	FROM tincidents i
	INNER JOIN tusers u ON u.id = i.user
	INNER JOIN tcompany c ON c.id = u.company
    WHERE u.lastname='$csvnom' AND u.firstname='$csvprenom'
	GROUP BY c.name
");
$row = mysql_fetch_array($query);
$csvsociete = $row['name'];

if (($globalrow['u_group']==0 && $u_group=='') || $_POST['user']!="")
{
    if ($_POST['user'])	{$user=$_POST['user'];}	else {$user=$globalrow['user'];}
    $query = mysql_query("
		SELECT * FROM tusers
		INNER JOIN tcompany ON tcompany.id = tusers.company
		WHERE tusers.id LIKE $user
	");
    $row = mysql_fetch_array($query);
} else {
    if (($globalrow['u_group']!=$u_group) && $u_group!=''){$group=$u_group;}else {$group=$globalrow['u_group'];}
    $query = mysql_query("SELECT * FROM `tgroups` WHERE id=$group");
    $row = mysql_fetch_array($query);
}
$csvadresse = $row[address];
$csvville = $row[city];
$csvcp = $row[zip];

if (($globalrow['u_group']==0 && $u_group=='') || $_POST['user']!="")
{
    if ($_POST['user'])	{$user=$_POST['user'];}	else {$user=$globalrow['user'];}
    $query = mysql_query("SELECT * FROM tusers WHERE id LIKE $user");
    $row = mysql_fetch_array($query);
} else {
    if (($globalrow['u_group']!=$u_group) && $u_group!=''){$group=$u_group;}else {$group=$globalrow['u_group'];}
    $query = mysql_query("SELECT * FROM `tgroups` WHERE id=$group");
    $row = mysql_fetch_array($query);
}
$csvtl = $row[phone];

if ($globalrow['t_group']!=0)
{
    $query = mysql_query("SELECT * FROM `tgroups` WHERE id=$globalrow[t_group]");
    $row = mysql_fetch_array($query);
    echo "[G] $row[name]";
} else {
    if ($_POST['technician'])
    {
        $querytech = mysql_query("SELECT * FROM `tusers` WHERE disable='0' and id LIKE '$_POST[technician]' ");
    } else {
        $querytech = mysql_query("SELECT * FROM `tusers` WHERE disable='0' and id LIKE '$globalrow[technician]' ");
    }
    $row=mysql_fetch_array($querytech);
    $resultlastfirst = $row[firstname].' '.$row[lastname];
}
$csvtech = $resultlastfirst;

$csvtitre = $globalrow['title'];

$qprecsvcat = mysql_query("SELECT * FROM `tcategory` WHERE id=$globalrow[category] ");
$precsvcat = mysql_fetch_array($qprecsvcat);
$qprecsvsubcat = mysql_query("SELECT * FROM `tsubcat` WHERE id=$globalrow[subcat] ");
$precsvsubcat = mysql_fetch_array($qprecsvsubcat);
$csvcat = $precsvcat[name].' '.$precsvsubcat[name];

$csvserial = $globalrow['serialnumber'];
$csvdatecrea = $globalrow['date_create'];
$csvdateres = $globalrow['date_res'];
$csvtravel = $globalrow['timetravel'];

 function convertiii( $lesMinutes )
 
 { 
 
     $heures = floor( $lesMinutes / 60 );
 
     $minutes = $lesMinutes % 60 ;

     if(empty($minutes)) {

     	return( $heures . "H");

     } else {

     	return( $heures . "H " . $minutes . "mn" );

     }
 
 }
 
$csvtps = convertiii($globalrow['time']);


$precsvdescr = $globalrow['description'];
/*$precsvdescr = str_replace(array('</div>', '<br>', '</p>'), "¥", $precsvdescr);
$precsvdescr = strip_tags($precsvdescr);
$precsvdescr = preg_replace('#\s#U', '', $precsvdescr);
$precsvdescr = preg_replace('#\[(.*)\](.*)\[(.*)\]#U', '$2', $precsvdescr);*/
$precsvdescr = str_replace( '&nbsp', '', $precsvdescr);
$precsvdescr = str_replace( '<br>', '', $precsvdescr);
$precsvdescr = str_replace( '\r\n', '', $precsvdescr);
$precsvdescr = str_replace( '\r', '', $precsvdescr);
$precsvdescr = str_replace( '\n', '', $precsvdescr);
$precsvdescr = str_replace( '\t', '', $precsvdescr);
$precsvdescr = str_replace( ';', ',', $precsvdescr);
$precsvdescr = strip_tags($precsvdescr);
$csvdescr = preg_replace("#\n|\t|\r#","",$precsvdescr);

$precsvdetail = "";
$query = mysql_query("SELECT * FROM tthreads WHERE ticket='$_GET[id]' and type='0' ORDER BY date");
while ($row=mysql_fetch_array($query)) {
    $row[text] = str_replace( '&nbsp', '', $row[text]);
    $row[text] = str_replace( '<br>', '', $row[text]);
    $row[text] = str_replace( '\r\n', '', $row[text]);
    $row[text] = str_replace( ';', ',', $row[text]);
    $row[text] = strip_tags($row[text]);

    $precsvdetail .= $row[text];
}/*
$precsvdetail = strip_tags($precsvdetail);
$precsvdetail = str_replace(array('</div>', '<br>', '</p>'), "¥", $precsvdetail);
$precsvdetail = str_replace('. ', ".¥", $precsvdetail);
$precsvdetail = preg_replace('#\s#U', ' ', $precsvdetail);
$precsvdetail = preg_replace('#\[(.*)\](.*)\[(.*)\]#U', '$2', $precsvdetail);*/
$csvdetail = str_replace( ';', ',', $precsvdetail);


$csvgarantie = $globalrow['warranty'];
$csvmaintenance = $globalrow['mntcontract'];

//Var name
$csvdateexport = substr($csvdatecrea, 0, 10);
$filenamecsv = 'export_'.$csvdateexport.'_'.$csvid;

//Header
header("Content-Type: text/csv; charset=UTF8");
header("Content-disposition: filename=$filenamecsv.csv");

// Création du contenu du tableau
$lignes = array();
$lignes[] = array($csvid, $csvnom, $csvprenom, $csvsociete, $csvadresse, $csvville, $csvcp, $csvtl, $csvtech, $csvtitre, $csvcat, $csvserial, $csvdatecrea, $csvdateres, $csvtravel, $csvtps, $csvgarantie, $csvmaintenance, $csvdescr, $csvdetail);

$separateur = ";";

// Affichage de la ligne de titre, terminée par un retour chariot
echo implode($separateur, $entete)."\r\n";

// Affichage du contenu du tableau
foreach ($lignes as $ligne) {
	echo implode($separateur, $ligne)."\r\n";
}
mysql_close($connexion); ?>
