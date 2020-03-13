<?php
################################################################################
# @Name : ./core/export_tickets.php
# @Description : dump csv files of current query
# @Call : /stat.php
# @Parameters : 
# @Author : Flox
# @Create : 27/01/2014
# @Update : 15/10/2019
# @Version : 3.1.45 p1
################################################################################

//locales
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if ($lang=='fr') {$_GET['lang'] = 'fr_FR';}
else {$_GET['lang'] = 'en_US';}

define('PROJECT_DIR', realpath('../'));
define('LOCALE_DIR', PROJECT_DIR .'/locale');
define('DEFAULT_LOCALE', '($_GET[lang]');
require_once('../components/php-gettext/gettext.inc');
$encoding = 'UTF-8';
$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;
T_setlocale(LC_MESSAGES, $locale);
T_bindtextdomain($_GET['lang'], LOCALE_DIR);
T_bind_textdomain_codeset($_GET['lang'], $encoding);
T_textdomain($_GET['lang']);

//initialize variables 
if(!isset($_GET['token'])) $_GET['token'] = ''; 
if(!isset($_COOKIE['token'])) $_COOKIE['token'] = ''; 
if(!isset($cnt_service)) $cnt_service=''; 

//database connection
require "../connect.php"; 

$db_userid=strip_tags($_GET['userid']);
$db_agency=strip_tags($_GET['agency']);
$db_technician=strip_tags($_GET['technician']);
$db_service=strip_tags($_GET['service']);
$db_type=strip_tags($_GET['type']);
$db_criticality=strip_tags($_GET['criticality']);
$db_category=strip_tags($_GET['category']);
$db_state=strip_tags($_GET['state']);
$db_month=strip_tags($_GET['month']);
$db_year=strip_tags($_GET['year']);

//secure connect from authenticated user
if($_GET['token']==$_COOKIE['token'] && $_GET['token']) 
{
	//get current date
	$daydate=date('Y-m-d');

	// output headers so that the file is downloaded rather than displayed
	header("Content-Type: text/csv; charset=utf-8");
	header("Content-Disposition: attachment; filename=\"$daydate-GestSup-export-tickets.csv\"");

	//load parameters table
	$qry = $db->prepare("SELECT * FROM `tparameters`");
	$qry->execute();
	$rparameters=$qry->fetch();
	$qry->closeCursor();
	
	//display error parameter
	if($rparameters['debug']==1) {
		ini_set('display_errors', 'On');
		ini_set('display_startup_errors', 'On');
		ini_set('html_errors', 'On');
		error_reporting(E_ALL);
	} else {
		ini_set('display_errors', 'Off');
		ini_set('display_startup_errors', 'Off');
		ini_set('html_errors', 'Off');
		error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
	}
	
	//load rights table
	$qry = $db->prepare("SELECT * FROM trights WHERE profile=(SELECT profile FROM tusers WHERE id=:id)");
	$qry->execute(array('id' => $db_userid));
	$rright=$qry->fetch();
	$qry->closeCursor();
	
	$where='';
	
	//get services associated with this user
	$qry = $db->prepare("SELECT service_id FROM `tusers_services` WHERE user_id=:user_id");
	$qry->execute(array('user_id' => $db_userid));
	$cnt_service=$qry->rowCount();
	$row=$qry->fetch();
	$qry->closeCursor();
	
	//case limit user service
	if ($rparameters['user_limit_service']==1 && $rright['admin']==0 && $_GET['service']=='%' && $cnt_service!=0)
	{
		//get services associated with this user
		$qry = $db->prepare("SELECT service_id FROM `tusers_services` WHERE user_id=:user_id");
		$qry->execute(array('user_id' => $db_userid));
		$cnt_service=$qry->rowCount();
		$row=$qry->fetch();
		$qry->closeCursor();
		
		if($cnt_service==0) {$where_service.='';}
		elseif($cnt_service==1) {
			$where.="u_service='$row[service_id]' AND ";
		} else {
			$cnt2=0;
			$qry = $db->prepare("SELECT service_id FROM `tusers_services` WHERE user_id=:user_id");
			$qry->execute(array('user_id' => $db_userid));
			$where.='(';
			while ($row=$qry->fetch())	
			{
				$cnt2++;
				$where.="u_service='$row[service_id]'";
				if ($cnt_service!=$cnt2) $where.=' OR '; 
			}
			$where.=' OR user='.$db_userid.' OR technician='.$db_userid.' ';
			$where.=') AND ';
			$qry->closecursor();
		}
	}

	//create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	
	//avoid UTF8 encoding problem
	fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
	
	//output the column headings
	$select='';
	fputcsv($output, array(T_('Numéro du ticket'), T_('Type'), T_('Technicien'), T_('Demandeur'), T_('Service'), T_('Service du demandeur'), T_('Agence'), T_('Date de première réponse'), T_('Société'),T_('Créateur'), T_('Catégorie'), T_('Sous-catégorie'), T_('Lieu'),T_('Titre'), T_('Temps passé'), T_('Date de création'),T_('Date de résolution estimée'), T_('Date de clôture'), T_('État'), T_('Priorité'), T_('Criticité')),";");
	$select.='sender_service,u_agency, img2, img1,';
	$where.="u_agency LIKE '$db_agency' AND";
	
	//special case to filter by meta state
	if($db_state=='meta') {
		$query="
		SELECT id,type,technician,user,u_service, $select creator,category,subcat,place,title,time,date_create,date_hope,date_res,state,priority,criticality 
		FROM tincidents 
		WHERE
		technician LIKE '$db_technician' AND
		u_service LIKE '$db_service' AND
		type LIKE '$db_type' AND
		criticality LIKE '$db_criticality' AND
		category LIKE '$db_criticality' AND
		(state=1 OR state=2 OR state=6) AND
		date_create LIKE '%-$db_month-%' AND
		date_create LIKE '$db_year-%' AND
		u_agency LIKE '$db_agency' AND
		$where
		disable=0
		";
	} else {
		$query="
		SELECT id,type,technician,user,u_service, $select creator,category,subcat,place,title,time,date_create,date_hope,date_res,state,priority,criticality 
		FROM tincidents 
		WHERE
		technician LIKE '$db_technician' AND
		u_service LIKE '$db_service' AND
		type LIKE '$db_type' AND
		criticality LIKE '$db_criticality' AND
		category LIKE '$db_criticality' AND
		state LIKE '$db_state' AND
		date_create LIKE '%-$db_month-%' AND
		date_create LIKE '$db_year-%' AND
		u_agency LIKE '$db_agency' AND
		$where
		disable=0
		";
	}
	if($rparameters['debug']) {echo $query;}
	$query = $db->query($query);
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
	{
		//detect technician group to display group name instead of technician name
		if ($row['technician']==0)
		{
			//check if group exist on this ticket
			$qry2=$db->prepare("SELECT t_group FROM tincidents WHERE id=:id");
			$qry2->execute(array('id' => $row['id']));
			$row2=$qry2->fetch();
			$qry2->closeCursor();
			if ($row2['t_group']!='0')
			{
				//get group name
				$qry2=$db->prepare("SELECT * FROM tgroups WHERE id=:id");
				$qry2->execute(array('id' => $row2['t_group']));
				$row2=$qry2->fetch();
				$qry2->closeCursor();
				$row['technician']="$row2[name]";
			}
		} else {
			$qry2=$db->prepare("SELECT firstname,lastname FROM tusers WHERE id=:id ");
			$qry2->execute(array('id' => $row['technician']));
			$resulttech=$qry2->fetch();
			$qry2->closeCursor();
			$row['technician']="$resulttech[firstname] $resulttech[lastname]";
		}
		
		$qry2=$db->prepare("SELECT name FROM ttypes WHERE id=:id ");
		$qry2->execute(array('id' => $row['type']));
		$resulttype=$qry2->fetch();
		$qry2->closeCursor();
		$row['type']=$resulttype['name'];
		
		$qry2=$db->prepare("SELECT name FROM tcompany,tusers WHERE tusers.company=tcompany.id AND tusers.id=:id");
		$qry2->execute(array('id' => $row['user']));
		$resultcompany=$qry2->fetch();
		$qry2->closeCursor();
		$row['img1']="$resultcompany[name]";
		
		//detect user group to display group name instead of user name
		if ($row['user']=='')
		{
			//check if group exist on this ticket
			$qry2=$db->prepare("SELECT u_group FROM tincidents WHERE id=:id");
			$qry2->execute(array('id' => $row['id']));
			$row2=$qry2->fetch();
			$qry2->closeCursor();
			if ($row2['u_group']!='0')
			{
				//get group name
				$qry2=$db->prepare("SELECT * FROM tgroups WHERE id=:id");
				$qry2->execute(array('id' => $row2['u_group']));
				$row2=$qry2->fetch();
				$qry2->closeCursor();
				$row['user']="$row2[name]";
			}
		} else {
			$qry2=$db->prepare("SELECT firstname,lastname FROM tusers WHERE id=:id");
			$qry2->execute(array('id' => $row['user']));
			$resultuser=$qry2->fetch();
			$qry2->closeCursor();
			$row['user']="$resultuser[firstname] $resultuser[lastname]";
		}
		
		//get sender service name
		$qry2=$db->prepare("SELECT name FROM tservices WHERE id=:id");
		$qry2->execute(array('id' => $row['sender_service']));
		$result_sender_service=$qry2->fetch();
		$qry2->closeCursor();
		$row['sender_service']="$result_sender_service[name]";
		//get agency name
		$qry2=$db->prepare("SELECT name FROM tagencies WHERE id=:id");
		$qry2->execute(array('id' => $row['u_agency']));
		$resultagency=$qry2->fetch();
		$qry2->closeCursor();
		$row['u_agency']="$resultagency[name]";
		//find date first answer
		$qry2=$db->prepare("SELECT MIN(date) FROM `tthreads` WHERE ticket=:ticket AND type=:type");
		$qry2->execute(array('ticket' => $row['id'],'type' => 0));
		$resultfirst=$qry2->fetch();
		$qry2->closeCursor();
		$row['img2']="$resultfirst[0]";
		
		$qry2=$db->prepare("SELECT name FROM tservices WHERE id=:id");
		$qry2->execute(array('id' => $row['u_service']));
		$resultservice=$qry2->fetch();
		$qry2->closeCursor();
		$row['u_service']="$resultservice[name]";
		 
		$qry2=$db->prepare("SELECT firstname,lastname FROM tusers WHERE id=:id");
		$qry2->execute(array('id' => $row['creator']));
		$resultcreator=$qry2->fetch();
		$qry2->closeCursor();
		$row['creator']="$resultcreator[firstname] $resultcreator[lastname]";
		
		$qry2=$db->prepare("SELECT * FROM tcategory WHERE id=:id");
		$qry2->execute(array('id' => $row['category']));
		$resultcat=$qry2->fetch();
		$qry2->closeCursor();
		$row['category']=$resultcat['name'];
		
		$qry2=$db->prepare("SELECT * FROM tsubcat WHERE id=:id");
		$qry2->execute(array('id' => $row['subcat']));
		$resultscat=$qry2->fetch();
		$qry2->closeCursor();
		$row['subcat']=$resultscat['name'];
		
		$qry2=$db->prepare("SELECT * FROM tplaces WHERE id=:id");
		$qry2->execute(array('id' => $row['place']));
		$resultplace=$qry2->fetch();
		$qry2->closeCursor();
		$row['place']=$resultplace['name'];
		
		$qry2=$db->prepare("SELECT * FROM tstates WHERE id=:id");
		$qry2->execute(array('id' => $row['state']));
		$resultstate=$qry2->fetch();
		$qry2->closeCursor();
		$row['state']=$resultstate['name'];
		
		$qry2=$db->prepare("SELECT * FROM tpriority WHERE id=:id");
		$qry2->execute(array('id' => $row['priority']));
		$resultpriority=$qry2->fetch();
		$qry2->closeCursor();
		$row['priority']=$resultpriority['name'];

		$qry2=$db->prepare("SELECT * FROM tcriticality WHERE id=:id");
		$qry2->execute(array('id' => $row['criticality']));
		$resultcriticality=$qry2->fetch();
		$qry2->closeCursor();
		$row['criticality']=$resultcriticality['name'];
		
		fputcsv($output, $row,';');
	}
	$qry->closeCursor();
} else {
	echo '<br /><br /><center><span style="font-size: x-large; color: red;"><b>'.T_("Erreur d'accès à la page, essayer de recharger la page statistique, si le problème persiste contacter votre administrateur").'.</b></span></center>';		
}
$db = null;
?>