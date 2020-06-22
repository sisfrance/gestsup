<?php
################################################################################
# @Name : system.php
# @Desc :  admin system
# @call : ./admin.php, install/index.php
# @parameters : 
# @Autor : Flox
# @Create : 10/11/2013
# @Update :23/06/2014
# @Version : 3.0.9
################################################################################

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
if(!isset($phpinfo[$vphp]['max_execution_time'][0])) $phpinfo[$vphp]['max_execution_time'][0] = '';
if(!isset($phpinfo['date']['date.timezone'][0])) $phpinfo['date']['date.timezone'][0] = '';
if(!isset($i)) $i = '';
if(!isset($openssl)) $openssl = '';
if(!isset($mysql)) $mysql = '';
if(!isset($ldap)) $ldap = '';
if(!isset($zip)) $zip = '';
if(!isset($imap)) $imap = '';

//mySQL basedir 
$query = mysql_query("show variables");
while ($row=mysql_fetch_array($query)) {
if ($row[0]=="version") $mysql=$row[1];
}

//check OS
$OS=$phpinfo['phpinfo']['System'];
$OS= explode(" ",$OS);
$OS=$OS[0];

?>
<div class="profile-user-info profile-user-info-striped">
	<div class="profile-info-row">
		<div class="profile-info-name"> Serveur: </div>
		<div class="profile-info-value">
			<span id="username">
				&nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/<?php echo $OS; ?>.png" style="border-style: none" alt="img" /> <?php echo "<b> {$phpinfo['phpinfo']['System']}</b><br />\n"; ?>
				&nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/apache.png" style="border-style: none" alt="img" /> <?php $apache=$phpinfo['apache2handler']['Apache Version']; $apache=preg_split('[ ]', $apache); $apache=preg_split('[/]', $apache[0]); echo "<b>Apache $apache[1] </b><br />\n"; ?>
				&nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/mysql_min.png" style="border-style: none" alt="img" /> <?php echo "<b>Mysql $mysql</b><br />\n"; ?>
				&nbsp;&nbsp;&nbsp;&nbsp;<img src="./images/php.png" style="border-style: none" alt="img" /> <b>PHP <?php echo phpversion(); ?></b>
			</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> Paramètres PHP: </div>
		<div class="profile-info-value">
			<span id="username">
				<?php
				if ($phpinfo[$vphp]['file_uploads'][0]=="On") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>file_uploads</b>: Activée<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>file_uploads:</b> Désactivé <i>(Le chargement de fichiers sera impossible)</i><br />';
				if ($phpinfo[$vphp]['memory_limit'][0]!="") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>memory_limit:</b> '.$phpinfo[$vphp]['memory_limit'][0].'<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>memory_limit:</b> '.$phpinfo[$vphp]['memory_limit'][0].' Il est conseiller d\'allouer plus de mémoire pour PHP > 256MB (editer votre fichier php.ini).<br />';
				if ($phpinfo[$vphp]['upload_max_filesize'][0]!="2M") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>upload_max_filesize:</b> '.$phpinfo[$vphp]['upload_max_filesize'][0].'<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>upload_max_filesize: </b>'.$phpinfo[$vphp]['upload_max_filesize'][0].' <i> (Il est préconisé d\'avoir une valeur supérieur ou égale à 10Mo, afin d\'attacher des pièces jointes volumineuses)</i>.<br />';
				if ($phpinfo[$vphp]['post_max_size'][0]!="8M") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>post_max_size:</b> '.$phpinfo[$vphp]['post_max_size'][0].' <br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>post_max_size: </b>'.$phpinfo[$vphp]['post_max_size'][0].' <i> (Il est préconisé d\'avoir une valeur supérieur ou égale à 10Mo, afin d\'attacher des pièces jointes volumineuses)</i>.<br />';
				if ($phpinfo[$vphp]['max_execution_time'][0]>="240") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>max_execution_time:</b> '.$phpinfo[$vphp]['max_execution_time'][0].'s<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>max_execution_time: </b>'.$phpinfo[$vphp]['max_execution_time'][0].'s <i>(Il est préconisé d\'avoir une valeur supérieur ou égale à 240s pour les mises à jours.)</i><br />';
				if ($phpinfo['date']['date.timezone'][0]=="Europe/Paris") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>date.timezone:</b> '.$phpinfo['date']['date.timezone'][0].'<br />'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>date.timezone:</b> '.$phpinfo['date']['date.timezone'][0].' <i>(Il est préconisé de modifier la valeur date.timezone du fichier php.ini, et mettre "Europe/Paris" afin de ne pas avoir de problème d\'horloge.)</i><br />';
				?>
			</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> Extensions PHP: </div>
		<div class="profile-info-value">
			<span id="username">
				<?php
				$textension = get_loaded_extensions();
				$nblignes = count($textension);
				if(!isset($textension[$i])) $textension[$i] = '';
				for ($i;$i<$nblignes;$i++)
				{
					if ($textension[$i]=='mysql') $mysql="1";
					if ($textension[$i]=='openssl') $openssl="1";
					if ($textension[$i]=='ldap') $ldap="1";
					if ($textension[$i]=='zip') $zip="1";
					if ($textension[$i]=='imap') $imap="1";
				}
				if ($mysql=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_mysql:</b> Activée'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-remove-sign icon-large red"></i> <b>php_mysql</b> Désactivé, certaines fonctions sont indisponibles.';
				echo "<br />";
				if ($openssl=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_openssl:</b> Activée'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>php_openssl</b> Désactivé, si vous utilisé un serveur SMTP sécurisé les mails ne seront pas envoyés. (Installation Linux: apt-get install openssl).';
				echo "<br />";
				if ($ldap=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_ldap:</b> Activée'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>php_ldap</b> Désactivé, aucune synchronisation ni authentification via un serveur LDAP ne sera possible (Installation Linux: apt-get install php5-ldap).';
				echo "<br />";
				if ($zip=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_zip:</b> Activée'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>php_zip</b> Désactivé, la fonction de mise à jour automatique ne sera pas possible.';
				echo "<br />";
				if ($imap=="1") echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-ok-sign icon-large green"></i> <b>php_imap:</b> Activée'; else echo '&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-warning-sign icon-large orange"></i> <b>php_imap</b> Désactivé, la fonction Mail2Ticket ne fonctionnera pas (Installation Linux: apt-get install php5-imap).';
				?>
			</span>
		</div>
	</div>
</div>