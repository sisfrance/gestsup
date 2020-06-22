<?php
################################################################################
# @Name : /core/ldap.php
# @Desc : page to synchronize users from LDAP to GestSup
# @call : /admin/user.php
# @Autor : Flox
# @Create : 15/10/2012
# @Update : 06/04/2014
# @Version : 3.0.8
################################################################################

//initialize variables
if(!isset($_POST['test_ldap'])) $_POST['test_ldap'] = '';
if(!isset($_GET['action'])) $_GET['action'] = '';
if(!isset($_GET['subpage'])) $_GET['subpage'] = '';
if(!isset($_GET['ldaptest'])) $_GET['ldaptest'] = '';

if(!isset($ldap_query)) $ldap_query = '';
if(!isset($find)) $find = '';
if(!isset($dcgen)) $dcgen = '';
if(!isset($find2_login)) $find2_login= '';
if(!isset($update)) $update= '';
if(!isset($find_dpt)) $find_dpt= '';
if(!isset($find_company)) $find_company= '';
if(!isset($samaccountname)) $samaccountname= '';
if(!isset($ldap_type)) $ldap_type= '';
if(!isset($ldap_auth)) $ldap_auth= '';

//LDAP connection parameters
$user=$rparameters['ldap_user']; 
$password=$rparameters['ldap_password']; 
$hostname=$rparameters['ldap_server'];
$domain=$rparameters['ldap_domain'];

//Generate DC Chain from domain parameter
$dcpart=explode(".",$domain);
$i=0;
while($i<count($dcpart)) {
	$dcgen="$dcgen,dc=$dcpart[$i]";
	$i++;
}
	
//LDAP URL for users emplacement
$ldap_url="$rparameters[ldap_url]$dcgen";

//display head title
if ($rparameters['ldap_type']==0) $ldap_type='Active Directory'; else $ldap_type='OpenLDAP';
if ($_GET['subpage']=='user')
{
	echo '
	<div class="page-header position-relative">
		<h1>
			<i class="icon-refresh"></i>   
			Synchronisation: '.$ldap_type.' > GestSup 
		</h1>
	</div>';
}
if(($_GET['action']=='simul') || ($_GET['action']=='run') || ($_GET['ldaptest']==1) || ($_GET['ldap']==1) || ($ldap_auth==1))
{
	//LDAP connect
	$ldap = ldap_connect($hostname,$rparameters['ldap_port']) or die("Impossible de se connecter au serveur LDAP.");
	ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
	if ($rparameters['ldap_type']==1) {ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);}
	//check LDAP type for bind
	if ($rparameters['ldap_type']==0) $ldapbind = ldap_bind($ldap, "$user@$domain", $password); else $ldapbind = ldap_bind($ldap, "cn=$user$dcgen", $password);	
	//check ldap authentication
	if ($ldapbind) {$ldap_connection="<i title=\"Connecteur opérationnel\" class=\"icon-ok-sign icon-large green\"></i> Connecteur opérationnel.";} else {$ldap_connection="<i title=\"Le connecteur ne fonctionne pas vérifier vos paramètres\" class=\"icon-remove-sign icon-large red\"></i> Le connecteur ne fonctionne pas vérifier vos paramètres";}
	
	if ($ldapbind) 
	{
		if(($_GET['action']=='simul') || ($_GET['action']=='run')) 
		{
				//change query filter for OpenLDAP or AD
				if ($rparameters['ldap_type']==0) {$filter="(&(objectClass=user)(objectCategory=person)(cn=*))";} else {$filter="(uid=*)";}		
				$query = @ldap_search($ldap, $ldap_url, $filter);
				//put all data to $data
				$data = @ldap_get_entries($ldap, $query);
				//count LDAP number of users
				$cnt_ldap = @ldap_count_entries($ldap, $query);
				//count GESTSUP number of users
				$q=mysql_query("SELECT count(*) FROM tusers WHERE disable='0'"); 
				$cnt_gestsup=mysql_fetch_array($q);
				
				echo "<i class=\"icon-book green\"></i> <b><u>Vérification des Annuaires</u></b><br />";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;Nombre d'utilisateurs trouvés dans l'annuaire $ldap_type: $cnt_ldap<br />";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;Nombre d'utilisateurs actif trouvés dans GestSup: $cnt_gestsup[0]<br /><br />";
				echo "<i class=\"icon-edit-sign red\"></i> <b><u>Modifications à apporter dans GestSup:</u></b><br /><br />";
				//Initialize counter
				$cnt_maj=0;
				$cnt_create=0;
				$cnt_disable=0;
				$cnt_enable=0;
				
				//display all data for debug
				//print_r($data);
				
				//for each LDAP user 
				for ($i=0; $i < $cnt_ldap; $i++) 
				{
					//Initialize variable for empty data
					if(!isset($data[$i]['samaccountname'][0])) $data[$i]['samaccountname'][0] = '';
					if(!isset($data[$i]['useraccountcontrol'][0])) $data[$i]['useraccountcontrol'][0] = '';
					if(!isset($data[$i]['givenname'][0])) $data[$i]['givenname'][0] = '';
					if(!isset($data[$i]['sn'][0])) $data[$i]['sn'][0] = '';
					if(!isset($data[$i]['telephonenumber'][0])) $data[$i]['telephonenumber'][0] = '';
					if(!isset($data[$i]['streetaddress'][0])) $data[$i]['streetaddress'][0] = '';
					if(!isset($data[$i]['postalcode'][0])) $data[$i]['postalcode'][0] = '';
					if(!isset($data[$i]['l'][0])) $data[$i]['l'][0] = '';
					if(!isset($data[$i]['mail'][0])) $data[$i]['mail'][0] = '';
					if(!isset($data[$i]['company'][0])) $data[$i]['company'][0] = '';
					if(!isset($data[$i]['facsimiletelephonenumber'][0])) $data[$i]['facsimiletelephonenumber'][0] = '';
					if(!isset($data[$i]['userAccountControl'][0])) $data[$i]['userAccountControl'][0] = '';
					if(!isset($data[$i]['title'][0])) $data[$i]['title'][0] = '';
					if(!isset($data[$i]['department'][0])) $data[$i]['department'][0] = '';					
					if(!isset($data[$i]['uid'][0])) $data[$i]['uid'][0] = '';
					
					//get user data from Windows AD or OpenLDAP & transform in UTF-8
					if ($rparameters['ldap_type']==0) $samaccountname=utf8_encode($data[$i]['samaccountname'][0]);  else $samaccountname=utf8_encode($data[$i]['uid'][0]);
					$UAC=$data[$i]['useraccountcontrol'][0];
					$givenname=utf8_encode($data[$i]['givenname'][0]);
					$sn=utf8_encode($data[$i]['sn'][0]);
					$mail=$data[$i]['mail'][0];
					$telephonenumber=$data[$i]['telephonenumber'][0];  
					$streetaddress=utf8_encode($data[$i]['streetaddress'][0]);  
					$postalcode=$data[$i]['postalcode'][0]; 
					$l=utf8_encode($data[$i]['l'][0]); 
					$company=utf8_encode($data[$i]['company'][0]); 
					$fax=$data[$i]['facsimiletelephonenumber'][0]; 
					$title=utf8_encode($data[$i]['title'][0]); 
					$department=utf8_encode($data[$i]['department'][0]); 
					
					if($rparameters['debug']==1) echo "- SamAccountName=$samaccountname UAC=$UAC company=$company<br>";
					
					////check if account not exist in GestSup user database
					//1st Check login
					$find_login=0;
					$q = mysql_query("SELECT * FROM `tusers`");
					while ($row=mysql_fetch_array($q))
					{
						if($samaccountname==$row['login']) {
						$find_login=$row['login'];
						$g_firstname=$row['firstname'];
						$g_lastname=$row['lastname'];
						$g_disable=$row['disable'];
						$g_mail=$row['mail'];
						$g_telephonenumber=$row['phone'];
						$g_streetaddress=$row['address1'];
						$g_postalcode=$row['zip'];
						$g_l=$row['city'];
						$g_company=$row['company'];
						$g_fax=$row['fax'];
						$g_title=$row['function'];
						$g_service= $row['service'];
						}
					}
					if ($find_login!='')
					{	
						////update exist account
						if (($UAC=='66050' || $UAC=='514') && ($g_disable==0)) 
						{
							//disable GestSup account
							$cnt_disable=$cnt_disable+1;
							if($_GET['action']=='run') {
								echo "<i class=\"icon-remove-sign icon-large red\"></i><font color=\"red\"> Utilisateur <b>$givenname $sn</b> ($samaccountname), désactivé.</font><br />";
								$query= "UPDATE tusers SET disable='1' WHERE login='$find_login'";		
								$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
							} else {
								echo "<i class=\"icon-remove-sign icon-large red\"></i><font color=\"red\"> Désactivation de l'utilisateur <b>$givenname $sn</b> ($samaccountname). <font size=\"1\" >Raison: Utilisateur désactivé dans l'annuaire LDAP.</font></font><br />";
							}
						} else {
							//enable gestsup account if LDAP user is re-activate
							if(($g_disable=='1') && ($UAC!='66050' && $UAC!='514' && $UAC!='66082' && $UAC!='546')) // 546 et 66082 special detect for invité
							{
								$cnt_enable=$cnt_enable+1;
								if($_GET['action']=='run') {
								echo "<i class=\"icon-ok-sign icon-large green\"></i><font color=\"green\"> Utilisateur <b>$givenname $sn</b> ($samaccountname), activé.</font><br />";
								$query= "UPDATE tusers SET disable='0' WHERE login='$samaccountname'";
								$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
								} else {
									echo "<i class=\"icon-ok-sign icon-large green\"></i><font color=\"green\"> Activation de l'utilisateur <b>$givenname $sn</b> ($samaccountname).</font><br />";
								}
							//update GestSup account if LDAP have informations
							} else if($UAC=='66050' || $UAC=='514' || $UAC=='512' || $UAC=='66048'){
								//compare data 
								$update=0;
								if($g_firstname!=$givenname) 
								{
									$update="du prénom \"$givenname\"";
									if($_GET['action']=='run') {
									$givenname = mysql_real_escape_string($givenname); 
									$query= "UPDATE tusers SET firstname='$givenname' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								if($g_lastname!=$sn) 
								{
									$update="du nom \"$sn\"";
									if($_GET['action']=='run') {
									$sn = mysql_real_escape_string($sn); 
									$query= "UPDATE tusers SET lastname='$sn' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								if($g_mail!=$mail) 
								{
									$update="de l'adresse mail \"$mail\"";
									if($_GET['action']=='run') {
									$query= "UPDATE tusers SET mail='$mail' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								if(($g_telephonenumber=='') && ($telephonenumber!='')) //special case for no tel number in AD
								{
									$update="du numéro de téléphone \"$telephonenumber\" ";
									if($_GET['action']=='run') {
									$query= "UPDATE tusers SET phone='$telephonenumber' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								if($g_streetaddress!=$streetaddress) 
								{
									$update="de l'adresse \"$streetaddress\" ";
									if($_GET['action']=='run') {
									$streetaddress = mysql_real_escape_string($streetaddress);
									$query= "UPDATE tusers SET address1='$streetaddress' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								if($g_postalcode!=$postalcode) 
								{
									$update="du code postal \"$zip\" ";
									if($_GET['action']=='run') {
									$query= "UPDATE tusers SET zip='$postalcode' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								if($g_l!=$l) 
								{
									$update="de la ville \"$l\" ";
									if($_GET['action']=='run') {
									$l = mysql_real_escape_string($l);
									$query= "UPDATE tusers SET city='$l' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								if($g_fax!=$fax) 
								{
									$update="du FAX \"$fax\"";
									if($_GET['action']=='run') {
									$query= "UPDATE tusers SET fax='$fax' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								if($g_title!=$title) 
								{
									$update="de la fonction \"$title\"";
									if($_GET['action']=='run') {
									$title = mysql_real_escape_string($title);
									$query= "UPDATE tusers SET function='$title' WHERE login='$samaccountname'";
									$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
									}
								}
								
								//get gestsup company name
								$q=mysql_query("SELECT name FROM tcompany WHERE id='$g_company'"); 
								$g_company_name=mysql_fetch_array($q);
								if($company!=$g_company_name[0]) 
								{
									$update="de la Société \"$company\" ";
									if($_GET['action']=='run') 
									{
										//find company in GestSup database
										$q = mysql_query("SELECT * FROM `tcompany`");
										while ($row=mysql_fetch_array($q))
										{
											if ($company==$row['name']) $find_company=$row['id']; else $find_company='';
										}
										//if company is find update company id else create service in gestsup
										if ($find_company!='')
										{
											$query= "UPDATE tusers SET service='$find_company' WHERE login='$samaccountname'";
											$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
										} 
										elseif ($company!='')
										{
											$company = mysql_real_escape_string($company); 
											$query= "INSERT INTO tcompany (name) VALUES ('$company')";
											$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
											echo "<i class=\"icon-plus-sign green bigger-130\"></i><font color=\"green\"> Société $company crée.</font><br />";
											//get GestSup company table
											$q = mysql_query("SELECT * FROM `tcompany`");
											while ($row=mysql_fetch_array($q))
											{
												if ($company==$row['name']) $find_company=$row['id']; 
											}
											// if company is find update company id else create company in gestsup
											if ($find_company!='')
											{
												$query= "UPDATE tusers SET company='$find_company' WHERE login='$samaccountname'";
												$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
											}
										}											
									} 
									else
									{
										//get gestsup service table
										$q = mysql_query("SELECT * FROM `tcompany`");
										while ($row=mysql_fetch_array($q))
										{
											if ($company==$row['name']) $find_company=$row['id']; else $find_company='';
										}
										// if dept is find update service id else create service in gestsup
										if ($find_company=='')	echo "<i class=\"icon-plus-sign green bigger-130\"></i><font color=\"green\"> Création de la Société $company.</font><br />";
									}
								}
								
								//get gestsup service name
								$q=mysql_query("SELECT name FROM tservices WHERE id='$g_service'"); 
								$g_service_name=mysql_fetch_array($q);
								if($department!=$g_service_name[0]) 
								{
									$update="du Service \"$department\"";
									if($_GET['action']=='run') 
									{
										//get gestsup service table
										$q = mysql_query("SELECT * FROM `tservices`");
										while ($row=mysql_fetch_array($q))
										{
											if ($department==$row['name']) $find_dpt=$row['id']; 
										}
										// if dept is find update service id else create service in gestsup
										if ($find_dpt!='')
										{
											$query= "UPDATE tusers SET service='$find_dpt' WHERE login='$samaccountname'";
											$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
										} 
										elseif ($department!='') 
										{
											$department = mysql_real_escape_string($department); 
											$query= "INSERT INTO tservices (name) VALUES ('$department')";
											$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
											echo "<i class=\"icon-plus-sign green bigger-130\"></i><font color=\"green\"> Service $department crée.</font><br />";
											//get gestsup service table
											$q = mysql_query("SELECT * FROM `tservices`");
											while ($row=mysql_fetch_array($q))
											{
												if ($department==$row['name']) $find_dpt=$row['id']; 
											}
											// if dept is find update service id else create service in gestsup
											if ($find_dpt!='')
											{
												$query= "UPDATE tusers SET service='$find_dpt' WHERE login='$samaccountname'";
												$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
											}
										}
									} else {
										//get gestsup service table
										$q = mysql_query("SELECT * FROM `tservices`");
										while ($row=mysql_fetch_array($q))
										{
											if ($department==$row['name']) $find_dpt=$row['id']; 
										}
										// if dept is find update service id else create service in gestsup
										if ($find_dpt=='')	echo "<i class=\"icon-plus-sign green bigger-130\"></i><font color=\"green\"> Création du service $department.</font><br />";
										
									}
								}
								
								if($update)
								{
									$cnt_maj=$cnt_maj+1;
									if($_GET['action']=='run') {
										echo "<i class=\"icon-refresh orange\"></i><font color=\"orange\"> Utilisateur <b>$givenname $sn</b> ($samaccountname), mis à jour.</font><br />";
									} else {
										echo "<i class=\"icon-refresh orange\"></i><font color=\"orange\"> Mise à jour $update pour <b>$givenname $sn</b> ($samaccountname).</font><br />";
									}
								}
							}
						}

					} else {
						//create GestSup account
							//escape special char for SQL query
							$givenname = mysql_real_escape_string($givenname); 
							$sn = mysql_real_escape_string($sn); 
							$streetaddress = mysql_real_escape_string($streetaddress); 
							$company= mysql_real_escape_string($company); 
							$title= mysql_real_escape_string($title); 
						$cnt_create=$cnt_create+1;
						if($_GET['action']=='run') {
							echo "<i class=\"icon-plus-sign green bigger-130\"></i><font color=\"green\"> Utilisateur <b>$givenname $sn</b> ($samaccountname) à été crée.</font><br />";
							$query= "INSERT INTO tusers (login,firstname,lastname,profile,mail,phone,address1,zip,city,company,fax) VALUES ('$samaccountname','$givenname','$sn','2','$mail','$telephonenumber','$streetaddress','$postalcode','$l','$company','$fax')";
							$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
						} else {
							echo "<i class=\"icon-plus-sign green bigger-130\"></i><font color=\"green\"> Création de l'utilisateur <b>$givenname $sn</b> ($samaccountname).</font><br />";
						}
					}
				}
				//For each Gestsup USER (find user not present in LDAP for disable in GestSup)
				$q = mysql_query("SELECT * FROM `tusers`");
				while ($row=mysql_fetch_array($q))	
				{
					$find2_login='';
					for ($i=0; $i < $cnt_ldap; $i++) 
					{
						if ($rparameters['ldap_type']==0) $samaccountname=$data[$i]['samaccountname'][0];  else $samaccountname=$data[$i]['uid'][0];
						if ($samaccountname==$row['login']) $find2_login=$row['login'];
					}
					if (($find2_login=='') && ($row['disable']=='0') && ($row['login']!='') && $row['login']!=' ')
					{
						$cnt_disable=$cnt_disable+1;
						if($_GET['action']=='run')
						{
							echo "<i class=\"icon-remove-sign icon-large red\"></i><font color=\"red\"> Utilisateur <b>$row[firstname] $row[lastname]</b> ($row[login]), désactivé.</font><br />";
							$query= "UPDATE tusers SET disable='1' WHERE login='$row[login]'";
							
							$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
						} else {
							echo "<i class=\"icon-remove-sign icon-large red\"></i><font color=\"red\"> Désactivation de l'utilisateur <b>$row[firstname] $row[lastname]</b> ($row[login]). <font size=\"1\" >Raison: Utilisateur non présent dans l'annuaire LDAP.</font></font><br />";
						}
					}
						
				}
				
				if (($cnt_create=='0') && ($cnt_disable=='0') && ($cnt_maj=='0') && ($cnt_enable=='0')) echo "&nbsp;&nbsp;&nbsp;&nbsp;<i class=\"icon-ok-sign icon-large green\"></i><font color=\"green\"> Aucune modification à apporter, les annuaires sont à jour.</font><br />";
				echo'
				<br />
				&nbsp;&nbsp;&nbsp;&nbsp;Nombre de d\'utilisateurs à créer dans GestSup: '.$cnt_create.' <br />
				&nbsp;&nbsp;&nbsp;&nbsp;Nombre de d\'utilisateurs à mettre à jour dans GestSup: '.$cnt_maj.' <br />
				&nbsp;&nbsp;&nbsp;&nbsp;Nombre de d\'utilisateurs à désactiver dans GestSup: '.$cnt_disable.' <br />
				&nbsp;&nbsp;&nbsp;&nbsp;Nombre de d\'utilisateurs à activer dans GestSup: '.$cnt_enable.' <br />
				<br />
				<i class="icon-info-sign blue"></i> <b><u>Informations de Synchronisation:</u></b><br />
				&nbsp;&nbsp;&nbsp;&nbsp;La jointure inter-annuaires est réalisée sur le login, les comptes existant dans GestSup qui possèdent un login doivent être existant dans l\'annuaire LDAP.<br />
				';
		}
		if(($_GET['action']=='simul') || ($_GET['action']=='run') || ($_GET['ldap']=='1')) 
		{
			echo'
				<br />
				<button onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;ldap=1&amp;action=simul"\' type="submit" class="btn btn-primary">
					<i class="icon-beaker bigger-120"></i>
					Lancer une simulation
				</button>
				<button onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;ldap=1&amp;action=run"\' type="submit" class="btn btn-primary">
					<i class="icon-bolt bigger-120"></i>
					Lancer la synchronisation
				</button>
				<button onclick=\'window.location.href="index.php?page=admin&subpage=user"\' type="submit" class="btn btn-primary">
					<i class="icon-reply bigger-120"></i>
					Retour
				</button>					
			';
		}
	} else if($_GET['subpage']=='user')
	{
		echo '
		<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">
					<i class="icon-remove"></i>
				</button>
				<strong>
					<i class="icon-remove"></i>
					Erreur
				</strong>
				La connection LDAP n\'est pas disponible, vérifier si votre serveur LDAP est joignable ou vérifier vos paramètres de connection.
				<br>
		</div>';
	}
} 
?>