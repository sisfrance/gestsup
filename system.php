<?php
################################################################################
# @Name : system.php
# @Description :  admin system
# @Call : ./admin.php, install/index.php
# @Parameters : 
# @Author : Flox
# @Create : 10/11/2013
# @Update : 15/10/2019
# @Version : 3.1.45
################################################################################


//initialize variables 
if(!isset($_GET['page'])) $_GET['page'] = '';
if(!isset($_GET['subpage'])) $_GET['subpage'] = '';

//for install call
if($_GET['page']=='admin') 
{
	require ('./connect.php');
} else {
	require ('../connect.php');
	
	//load parameters table
	$qry=$db->prepare("SELECT * FROM `tparameters`");
	$qry->execute();
	$rparameters=$qry->fetch();
	$qry->closeCursor();
	
	//mobile detection
	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4)))
	{$mobile=1;} else {$mobile=0;}
}

//create private server key if not exist used to auto-installation URL
if($rparameters['server_private_key']=='') 
{
	$key=md5(uniqid());
	
	$qry=$db->prepare("UPDATE `tparameters` SET `server_private_key`=:server_private_key WHERE `id`=1");
	$qry->execute(array(
		'server_private_key' => $key
	));
}

//detect https connection
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {$http='https';} else {$http='http';}

//extract php info
ob_start();
phpinfo();
$phpinfo = array('phpinfo' => array());
if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
    foreach($matches as $match)
        if(strlen($match[1]))
            $phpinfo[$match[1]] = array();
        elseif(isset($match[3])){
			$ak=array_keys($phpinfo);
            $phpinfo[end($ak)][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
			}
        else
            {
			$ak=array_keys($phpinfo);
            $phpinfo[end($ak)][] = $match[2];
		}

//find PHP table informations, depends of PHP versions			
if (isset($phpinfo['Core'])!='') $vphp='Core';
elseif (isset($phpinfo['PHP Core'])!='') $vphp='PHP Core';
elseif (isset($phpinfo['HTTP Headers Information'])!='') $vphp='HTTP Headers Information'; 

//initialize variables 
if(!isset($_POST['Modifier'])) $_POST['Modifier'] = '';
if(!isset($phpinfo[$vphp]['file_uploads'][0])) $phpinfo[$vphp]['file_uploads'][0] = '';
if(!isset($phpinfo[$vphp]['memory_limit'][0])) $phpinfo[$vphp]['memory_limit'][0] = '';
if(!isset($phpinfo[$vphp]['upload_max_filesize'][0])) $phpinfo[$vphp]['upload_max_filesize'][0] = '';
if(!isset($phpinfo[$vphp]['post_max_size'][0])) $phpinfo[$vphp]['post_max_size'][0] = '';
if(!isset($phpinfo[$vphp]['max_execution_time'][0])) $phpinfo[$vphp]['max_execution_time'][0] = '';
if(!isset($phpinfo['date']['date.timezone'][0])) $phpinfo['date']['date.timezone'][0] = '';
if(!isset($i)) $i = '';
if(!isset($openssl)) $openssl = '';
if(!isset($rdb_name)) $rdb_name = '';
if(!isset($rdb_version)) $rdb_version = '';
if(!isset($ldap)) $ldap = '';
if(!isset($zip)) $zip = '';
if(!isset($imap)) $imap = '';
if(!isset($pdo_mysql)) $pdo_mysql = '';
if(!isset($ftp)) $ftp = '';
if(!isset($xml)) $xml = '';
if(!isset($curl)) $curl = '';
if(!isset($php_error)) $php_error = '';
if(!isset($php_warning)) $php_warning = '';

//SQL db connect
if ($_GET['page']!='admin') {require('../connect.php');}

//get rdb database version 
$qry=$db->prepare("SHOW VARIABLES");
$qry->execute();
while($row=$qry->fetch()) 
{
	if ($row[0]=="version") {
		$rdb_version=$row[1];
		if(strpos($rdb_version, 'MariaDB')) {
			$rdb_name='MariaDB';
			$rdb_icon=explode('-',$rdb_version);
			$rdb_icon=explode('.',$rdb_icon[0]);
			if($rdb_icon[0]>=10 && $rdb_icon[1]>=1) {$rdb_icon='ok';} else {$rdb_icon='ko';}
		} else {
			$rdb_name='MySQL';
			$rdb_icon='ok';
		}
	}
}
$qry->closeCursor();

//check OS
$os=$phpinfo['phpinfo']['System'];
$os= explode(" ",$os);
$os=$os[0];

//check and convert current ram value in MB value to check presrequites
$ram=$phpinfo[$vphp]['memory_limit'][0];
if(preg_match("/M/",$ram)) {$ram_mb=explode('M',$ram);$ram_mb=$ram_mb[0];}
if(preg_match("/m/",$ram)) {$ram_mb=explode('m',$ram);$ram_mb=$ram_mb[0];}
if(preg_match("/G/",$ram)) {$ram_mb=explode('G',$ram);$ram_mb=$ram_mb[0]*1024;}
if(preg_match("/g/",$ram)) {$ram_mb=explode('g',$ram);$ram_mb=$ram_mb[0]*1024;}
if(!$ram_mb) {$ram_mb=$phpinfo[$vphp]['memory_limit'][0];}

$max_filesize=$phpinfo[$vphp]['upload_max_filesize'][0];
if(preg_match("/M/",$max_filesize)) {$max_filesize_mb=explode('M',$max_filesize);$max_filesize_mb=$max_filesize_mb[0];}
if(preg_match("/m/",$max_filesize)) {$max_filesize_mb=explode('m',$max_filesize);$max_filesize_mb=$max_filesize_mb[0];}
if(preg_match("/G/",$max_filesize)) {$max_filesize_mb=explode('G',$max_filesize);$max_filesize_mb=$max_filesize_mb[0]*1024;}
if(preg_match("/g/",$max_filesize)) {$max_filesize_mb=explode('g',$max_filesize);$max_filesize_mb=$max_filesize_mb[0]*1024;}
if(!$max_filesize_mb) {$max_filesize_mb=$phpinfo[$vphp]['upload_max_filesize'][0];}

$post_max_size=$phpinfo[$vphp]['post_max_size'][0];
if(preg_match("/M/",$post_max_size)) {$post_max_size_mb=explode('M',$post_max_size);$post_max_size_mb=$post_max_size_mb[0];}
if(preg_match("/m/",$post_max_size)) {$post_max_size_mb=explode('m',$post_max_size);$post_max_size_mb=$post_max_size_mb[0];}
if(preg_match("/G/",$post_max_size)) {$post_max_size_mb=explode('G',$post_max_size);$post_max_size_mb=$post_max_size_mb[0]*1024;}
if(preg_match("/g/",$post_max_size)) {$post_max_size_mb=explode('g',$post_max_size);$post_max_size_mb=$post_max_size_mb[0]*1024;}
if(!$post_max_size_mb) {$post_max_size_mb=$phpinfo[$vphp]['upload_post_max_size'][0];}

//get web server name
$web_server=$_SERVER['SERVER_SOFTWARE'];
$web_server=explode('/',$web_server);
$web_server_name=strtolower($web_server[0]);
if(isset($web_server[1])) {
	$web_server_version=$web_server[1];
	$web_server_version=explode(' ',$web_server_version);
	$web_server_version=$web_server_version[0];
} else {
	$web_server_version=T_('Non disponible');
}

if($web_server_name!='nginx')
{
	//get apache version
	$apache=$phpinfo['apache2handler']['Apache Version'];
	$apache=preg_split('[ ]', $apache); 
	$apache=preg_split('[/]', $apache[0]);
	if(isset($apache[1])) {
		$apache_version=$apache[1]; 
		$apache_display_version=1;
		$apache_icon=explode(".",$apache[1]);
		if($apache_icon[0]>=2 && $apache_icon[1]>=4){$web_server_icon='apache_ok.png';} else {$web_server_icon='apache_ko.png';}
	} else {
		$apache_version=T_('Version non disponible, serveur sécurisé');
		$apache_display_version=0;
		$web_server_icon='apache_ok.png';
	}
} else {
	$web_server_icon='nginx_ok.png';
}



//get components versions
if ($_GET['page']!='admin')
{
	$phpmailer = file_get_contents('../components/PHPMailer/VERSION');
	$phpgettext = file_get_contents('../components/php-gettext/VERSION');
	$phpimap = file_get_contents('../components/PhpImap/VERSION');
	$highcharts = file_get_contents('../components/Highcharts/VERSION');
	$wol = file_get_contents('../components/wol/VERSION');
	$mysqldumpphp = file_get_contents('../components/mysqldump-php/VERSION');
	$fullcalendar = file_get_contents('../components/fullcalendar/VERSION');
	$jquery = file_get_contents('../components/jquery/VERSION');
	$jquery_ui = file_get_contents('../components/jquery-ui/VERSION');
	$bootstrap = file_get_contents('../components/bootstrap/VERSION');
	$datetimepicker = file_get_contents('../components/datetimepicker/VERSION');
	$moment = file_get_contents('../components/moment/VERSION');
} else {
	$phpmailer = file_get_contents('./components/PHPMailer/VERSION');
	$phpgettext = file_get_contents('./components/php-gettext/VERSION');
	$phpimap = file_get_contents('./components/PhpImap/VERSION');
	$highcharts = file_get_contents('./components/Highcharts/VERSION');
	$wol = file_get_contents('./components/wol/VERSION');	
	$mysqldumpphp = file_get_contents('./components/mysqldump-php/VERSION');	
	$fullcalendar = file_get_contents('./components/fullcalendar/VERSION');	
	$jquery = file_get_contents('./components/jquery/VERSION');	
	$jquery_ui = file_get_contents('./components/jquery-ui/VERSION');
	$bootstrap = file_get_contents('./components/bootstrap/VERSION');	
	$datetimepicker = file_get_contents('./components/datetimepicker/VERSION');	
	$moment = file_get_contents('./components/moment/VERSION');	
}

//get php session max lifetime parameter
$maxlifetime = ini_get("session.gc_maxlifetime");

//get db size
function formatfilesize($data) {
    if($data < 1024) {return $data . " bytes";}
    else if($data < 1024000) {return round(($data / 1024 ), 1) . "k";}
    else {return round(($data / 1024000), 1) . "MB";}
}
$db_size=0;
$qry=$db->prepare("SHOW TABLE STATUS");
$qry->execute();
while($row=$qry->fetch()){$db_size += $row["Data_length"] + $row["Index_length"];}
$qry->closeCursor();
$db_size=formatfilesize($db_size);

?>
<div class="profile-user-info profile-user-info-striped">
	<div class="profile-info-row">
		<div class="profile-info-name">  <?php echo T_('Serveur'); ?> : </div>
		<div class="profile-info-value">
			<span id="username">
				&nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/<?php echo strtolower($os); ?>_ok.png" style="border-style: none" alt="img" /> <?php echo "<b>OS :</b> {$phpinfo['phpinfo']['System']}<br />"; ?>
				<?php 
					echo '&nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/'.$web_server_icon.'" style="border-style: none" alt="img" /> <b>'.ucfirst($web_server_name).' :</b> '.$web_server_version.'<br />';
				?>
				&nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/<?php echo strtolower($rdb_name).'_'.$rdb_icon.'.png'; ?>" style="border-style: none" alt="img" /> <?php echo '<b>'.$rdb_name.' :</b> '.$rdb_version.' <i>('.T_('base').' : '.$db_name.' '.$db_size.')</i><br />'; ?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php 
				//check php version
				$php_version=phpversion();
				$php_version=explode(".",$php_version);
				if($php_version[0]<7){
					echo '<i class="icon-remove-sign icon-large red"></i> <b>PHP :</b>  '.T_('Votre version de PHP ').phpversion().T_(' est obsolète, installer au minimum la version 7.X').'.';
				}else{
					echo '<img src="./images/php_ok.png" style="border-style: none" alt="img" /> <b>PHP :</b> '.phpversion();
				}
				?>  
				<br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-ticket icon-large"></i> <b><?php echo T_('GestSup'); ?> :</b> <?php echo $rparameters['version']; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-time icon-large"></i> &nbsp;<b><?php echo T_('Horloge'); ?> :</b> <?php echo date('Y-m-d H:i:s'); ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-key icon-large"></i> <b><?php echo T_('Clé privée'); ?> :</b> <?php echo $rparameters['server_private_key']; ?> <i><?php echo T_("(Clé à ne pas divulguer)"); ?></i>
			</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name">  <?php echo T_('Client'); ?> : </div>
		<div class="profile-info-value">
			<span id="username">
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>Mobile :</b> <?php if($mobile) {echo 'Oui';} else {echo 'Non';} ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>Infos :</b> <?php echo $_SERVER['HTTP_USER_AGENT']; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b><?php if(strpos($_SERVER['REMOTE_ADDR'],':')) {echo 'IPv6';} else {echo 'IPv4';}  ?> :</b> <?php echo $_SERVER['REMOTE_ADDR']; ?><br />
			</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name">  <?php echo T_('Composants'); ?> : </div>
		<div class="profile-info-value">
			<span id="username">
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>PHPmailer :</b> <?php echo $phpmailer; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>PHPimap :</b> <?php echo $phpimap; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>PHPgettext :</b> <?php echo $phpgettext; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>PHPmysqldump :</b> <?php echo $mysqldumpphp; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>Highcharts :</b> <?php echo $highcharts; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>WOL :</b> <?php echo $wol; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>FullCalendar :</b> <?php echo $fullcalendar; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>jQuery :</b> <?php echo $jquery; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>jQuery-ui :</b> <?php echo $jquery_ui; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>Bootstrap :</b> <?php echo $bootstrap; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>DateTimePicker :</b> <?php echo $datetimepicker; ?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="green icon-puzzle-piece icon-large"></i> <b>Moment :</b> <?php echo $moment; ?><br />
			</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"><?php echo T_('Paramètres'); ?><br />PHP :</div>
		<div class="profile-info-value">
			<span id="username">
				<?php
				if ($phpinfo[$vphp]['file_uploads'][0]=="On") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>file_uploads</b> : '.T_('Activé').'<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>file_uploads :</b> '.T_('Désactivé').' <i>('.T_('Le chargement de fichiers sera impossible').')</i><br />';
				if ($ram_mb>=512) echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>memory_limit :</b> '.$phpinfo[$vphp]['memory_limit'][0].'<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>memory_limit :</b> '.$phpinfo[$vphp]['memory_limit'][0].' <i>('.T_('Il est préconisé d\'allouer plus de mémoire pour PHP valeur minimum 512M éditer votre fichier php.ini').')</i>.<br />';
				if ($max_filesize_mb>=5) echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>upload_max_filesize :</b> '.$max_filesize_mb.'M<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>upload_max_filesize : </b>'.$max_filesize_mb.'M <i> ('.T_('Il est préconisé d\'avoir une valeur supérieur ou égale à 5Mo, afin d\'attacher des pièces jointes volumineuses').')</i>.<br />';
				if ($post_max_size_mb>=5) echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>post_max_size :</b> '.$post_max_size_mb.'M <br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>post_max_size : </b>'.$post_max_size_mb.'M <i> ('.T_('Il est préconisé d\'avoir une valeur supérieur ou égale à 5Mo, afin d\'attacher des pièces jointes volumineuses').')</i>.<br />';
				if ($phpinfo[$vphp]['max_execution_time'][0]>="240") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>max_execution_time :</b> '.$phpinfo[$vphp]['max_execution_time'][0].'s<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>max_execution_time : </b>'.$phpinfo[$vphp]['max_execution_time'][0].'s <i>('.T_('Valeur conseillé 240s, modifier votre php.ini relancer apache et actualiser cette page').'.)</i><br />';
				if ($phpinfo['date']['date.timezone'][0]!='UTC') echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>date.timezone :</b> '.$phpinfo['date']['date.timezone'][0].'<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>date.timezone :</b> '.$phpinfo['date']['date.timezone'][0].' <i>('.T_('Il est préconisé de modifier la valeur date.timezone du fichier php.ini, et mettre "Europe/Paris" afin de ne pas avoir de problème d\'horloge').'.)</i><br />';
				?>
			</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"><?php echo T_('Extensions'); ?><br />PHP :</div>
		<div class="profile-info-value">
			<span id="username">
				<?php
				$textension = get_loaded_extensions();
				$nblignes = count($textension);
				if(!isset($textension[$i])) $textension[$i] = '';
				for ($i;$i<$nblignes;$i++)
				{
					if ($textension[$i]=='openssl') $openssl="1";
					if ($textension[$i]=='zip') $zip="1";
					if ($textension[$i]=='imap') $imap="1";
					if ($textension[$i]=='ldap') $ldap="1";
					if ($textension[$i]=='pdo_mysql') $pdo_mysql="1";
					if ($textension[$i]=='ftp') $ftp="1";
					if ($textension[$i]=='xml') $xml="1";
					if ($textension[$i]=='curl') $curl="1";
				}
				if ($pdo_mysql=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_pdo_mysql :</b> '.T_('Activée'); else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>php_pdo_mysql</b> '.T_('Désactivée, l\'interconnexion de base de données ne pourra être disponible');
				echo "<br />";
				if ($openssl=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_openssl :</b> '.T_('Activée'); else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>php_openssl</b> '.T_('Désactivée, si vous utilisé un serveur SMTP sécurisé les mails ne seront pas envoyés. (Installation Linux: apt-get install openssl)').'.';
				echo "<br />";
				if ($ldap=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_ldap :</b> '.T_('Activée'); else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>php_ldap</b> '.T_('Désactivée, aucune synchronisation ni authentification via un serveur LDAP ne sera possible (Installation Linux: apt-get install php7.0-ldap)').'.';
				echo "<br />";
				if ($zip=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_zip :</b> '.T_('Activée'); else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>php_zip</b> '.T_('Désactivée, la fonction de mise à jour automatique ne sera pas possible').'.';
				echo "<br />";
				if ($imap=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_imap :</b> '.T_('Activée'); else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>php_imap</b> '.T_('Désactivée, la fonction Mail2Ticket ne fonctionnera pas (Installation Linux: apt-get install php7.0-imap)').'.';
				echo "<br />";
				if ($ftp=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_ftp :</b> '.T_('Activée'); else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>php_ftp</b> '.T_('Désactivée, aucune mise à jour du logiciel ne sera possible (dé-commenter la ligne extension=php_ftp votre php.ini)').'.';
				echo "<br />";
				if ($xml=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_xml :</b> '.T_('Activée'); else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large red"></i> <b>php_xml</b> '.T_('Désactivée, le connecteur LDAP ne fonctionnera pas (apt-get install php7.0-xml)').'.';
				echo "<br />";
				if ($curl=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_curl :</b> '.T_('Activée'); else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>php_curl</b> '.T_('Le contrôle de sécurité sur le listing des repertoire ne fonctionnera pas. (apt-get install php7.0-curl)').'.';
				?>
			</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name">  <?php echo T_('Sécurité'); ?> : </div>
		<div class="profile-info-value">
			<span id="username">
				<?php
				if ($http=="https") 
				{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>HTTPS : </b>'.T_('Activée');}
				else 
				{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>HTTPS : </b>'.T_("Désactivé, les connexions vers le serveur ne sont pas chiffrées").' <a target="_blank" href="https://certbot.eff.org"> ('.T_("Installer un certificat Let's Encrypt").')</a>.';}
				echo "<br />";
				if ($web_server_name=='apache') 
				{
					if ($apache_display_version==0)
					{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>'.T_('Version Apache').' : </b>'.T_('Non affichée');}
					else
					{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>'.T_('Version Apache').' : </b>'.T_("Affichée, pour plus de sécurité masquer la version d'apache que vous utilisez. (Passer \"ServerTokens\" à \"Prod\" dans security.conf)").'.';}	
					echo "<br />";
				}
				
				if ($maxlifetime<=1440 && $rparameters['timeout']<=24) 
				{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>'.T_('Durée de la session').' : </b> PHP='.$maxlifetime.'s GestSup='.$rparameters['timeout'].'m';}
				else
				{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>'.T_('Durée de la session').' : </b> PHP='.$maxlifetime.'s GestSup='.$rparameters['timeout'].'m, '.T_("pour plus de sécurité diminuer la durée à 24m minimum, paramètre \"session.gc_maxlifetime\" du php.ini et paramètre GestSup.");}
				echo "<br />";
				if($_GET['subpage']=='system') //not display on installation page
				{
					if (!is_writable('./index.php'))
					{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>'.T_("Droits d'écriture").' : </b>'.T_('Verrouillés');}
					else
					{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>'.T_("Droits d'écriture").' : </b>'.T_('Non verrouillés').' (<a target="_blank" href="https://gestsup.fr/index.php?page=support&item1=install&item2=debian#43">'.T_('cf documentation').'</a>).';}  
					echo "<br />";
					$test_install_file=file_exists('./install/index.php' );
					if (!$test_install_file) 
					{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>'.T_("Répertoire installation").' : </b>'.T_('Non présent');}
					else 
					{echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>'.T_("Répertoire installation").' : </b>'.T_('Présent, supprimer le répertoire "./install" de votre serveur').'.';}
					echo "<br />";
				}
				//if curl extension is installed
				if($curl==1)
				{
					//test directory listing
					$url=$http.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
					$url=explode('/index.php', $url);
					$url=$url[0].'/upload/';
					$c = curl_init($url);
					curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
					$html = curl_exec($c);
					if (curl_error($c)) die(curl_error($c));
					$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
					curl_close($c);
					if($status=='403' || $status=='301')
					{
						echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>'.T_("Listing des répertoires").' : </b>'.T_('Désactivé').'<br />';
					} else {
						echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>'.T_("Listing des répertoires").' : </b>'.T_("Activé, vérifier l'option \"Indexes\" de votre serveur Apache").'.<br />';
					}
				}
				if($_GET['subpage']=='system')
				{
					//check secure SMTP
					if($rparameters['mail'])
					{
						if($rparameters['mail_port']=='587' || $rparameters['mail_port']=='465')
						{
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>SMTP : </b>'.T_('Sécurisé').'<br />';
						} else {
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>SMTP : </b>'.T_('Non sécurisé').' <i>('.T_('Régler le port 465 ou 587, dans la configuration du connecteur').').</i><br />';
						}
					}
					//check secure IMAP
					if($rparameters['imap'])
					{
						if($rparameters['imap_port']=='993/imap/ssl')
						{
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>IMAP : </b>'.T_('Sécurisé').'<br />';
						} else {
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>IMAP : </b>'.T_('Non sécurisé').' <i>('.T_('Régler le port 993, dans la configuration du connecteur').').</i><br />';
						}
					}
					//check secure LDAP
					if($rparameters['ldap'])
					{
						if($rparameters['ldap_port']=='636')
						{
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>LDAP : </b>'.T_('Sécurisé').'<br />';
						} else {
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>LDAP : </b>'.T_('Non sécurisé').' <i>('.T_('Régler le port 636, dans la configuration du connecteur').').</i><br />';
						}
					}
					//check password policy
					if($rparameters['ldap_auth'])
					{
						echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>Mots de passes : </b>'.T_('Gérer par le serveur LDAP').'<br />';
					} elseif($rparameters['user_password_policy'])
					{
						if($rparameters['user_password_policy_min_lenght']>=8)
						{
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>Mots de passes : </b>'.T_('Sécurisés').'<br />';
						} else {
							echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>Mots de passes : </b>'.T_('Longueur de mot de passe trop faible').' <i>('.T_('Définir la longueur minimal à 8 caractères').').</i><br />';
						}
					} else {
						echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>'.T_('Mots de passes').' : </b>'.T_('Aucune politique définie').' <i>('.T_('Définissez une politique de mot de passe dans Administration > Paramètres > Général > Utilisateur').').</i><br />';
					}
				}
				?>
			</span>
		</div>
	</div>
</div>