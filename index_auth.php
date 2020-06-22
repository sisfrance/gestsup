<?php 
################################################################################
# @Name : index_auth.php
# @Desc : right check and authentication
# @call : /index.php
# @paramters : 
# @Autor : Flox
# @Create : 17/07/2009
# @Update : 10/03/2015
# @Version : 3.0.11
################################################################################

//initialize variables 
if(!isset($state)) $state = ''; 
if(!isset($userid)) $userid = ''; 
if(!isset($techread)) $techread = '';
if(!isset($findnom)) $findnom = '';
if(!isset($profile)) $profile = '';
if(!isset($newpassword)) $newpassword = '';
if(!isset($salt)) $salt= '';
if(!isset($dcgen)) $dcgen= '';
if(!isset($ldap_type)) $ldap_type= '';
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($_SESSION['login'])) $_SESSION['login'] = ''; 
if(!isset($_GET['page'])) $_GET['page'] = ''; 
if(!isset($_GET['state'])) $_GET['state'] = ''; 
if(!isset($_GET['techread'])) $_GET['techread'] = ''; 
if(!isset($_GET['userid'])) $_GET['userid'] = ''; 
if(!isset($_GET['viewid'])) $_GET['viewid'] = '';

//default values
if($_GET['state']=='') $_GET['state'] = '%';

	//actions on submit
	if (isset($_POST['submit']))
	{
		$login = (isset($_POST['login'])) ? $_POST['login'] : '';
		$pass =  (isset($_POST['pass']))  ? $_POST['pass']  : '';

		$qusr = mysql_query("SELECT * FROM `tusers` WHERE 1");
		while ($row=mysql_fetch_array($qusr)) 
		{
			////Uppercase login converter
			$login = strtoupper($login);
			$nom = strtoupper($row['login']);
			
			//double (OR) test for crypted password transition
			if ($nom == $login && ($row['password']==$pass || $row['password']==md5($row['salt'] . md5($pass))) && $row['disable']==0) 
			{
				$findnom=$row['login'];
				$findpwd=$row['password'];
				$user_id=$row['id'];
				$profile=$row['profile'];
				$findsalt=$row['salt'];
				
				//update no crypted password to crypted password
				if($row['password']==$pass)
				{
					//password conversion
					$salt = substr(md5(uniqid(rand(), true)), 0, 5); // Generate a random key
					$newpassword=md5($salt . md5($row['password'])); // store in md5, md5 password + salt
					//update query
					$query = "UPDATE tusers SET password='$newpassword', salt='$salt' WHERE id LIKE '$user_id'";
					$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
				}
			}	
		}
		if ($findnom != "") 
		{	
			$_SESSION['login'] = "$findnom";
			$_SESSION['user_id'] = "$user_id";
			
			//update last time connection
			$query = "UPDATE tusers SET last_login='$datetime' WHERE id LIKE '$user_id'";
			$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
			
			echo '<div id="valide"><img alt="logo" src="./images/valide.png" style="border-style: none" alt="img" /> Vos identifiants sont corrects.</div>';
			
			$www = "./index.php?page=dashboard&userid=$user_id&state=1";
			//web redirection
			echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='$www'
						}
						setTimeout('redirect()',$rparameters[time_display_msg]);
						-->
					</SCRIPT>";
		}
		else if (($rparameters['ldap'])=='1' && ($rparameters['ldap_auth']=='1'))
		{
			/////////// if Gestsup user is not found and LDAP is enable search in LDAP///////////
			// LDAP connect
			$ldap=ldap_connect($rparameters['ldap_server'],$rparameters['ldap_port']) or die("Impossible de se connecter au serveur LDAP.");
			ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			$domain=$rparameters['ldap_domain'];
			if ($rparameters['ldap_type']==0) 
			{
				$ldapbind = ldap_bind($ldap, "$login@$domain", $pass);
			} else {
				//Generate DC Chain from domain parameter
				$dcpart=explode(".",$domain);
				$i=0;
				while($i<count($dcpart)) {
					$dcgen="$dcgen,dc=$dcpart[$i]";
					$i++;
				}
				$ldapbind = ldap_bind($ldap, "uid=$login,$rparameters[ldap_url]$dcgen", $pass);	
			}

			if ($ldapbind && $pass!='') 
			{
				$_SESSION['login'] = "$login";
				$q = mysql_query("SELECT id FROM tusers where login='$login'");
				$r = mysql_fetch_array($q);
				$_SESSION['user_id'] = "$r[0]";
				if($r['0']=='')
				{
					// if error with login or password 
					echo '<div id="erreur"><img src="./images/access.png" alt="erreur" style="border-style: none" alt="img" /> Votre compte est inexistant dans ce logiciel.</div>';
					$www = "./index.php";
					session_destroy();
					//web redirection to login page
					echo "<SCRIPT LANGUAGE='JavaScript'>
							<!--
							function redirect()
							{
							window.location='$www'
							}
							setTimeout('redirect()',$rparameters[time_display_msg]);
							-->
						</SCRIPT>";
				} else {
					echo '<div id="valide"><img alt="logo" src="./images/valide.png" style="border-style: none" alt="img" /> Vos identifiants sont corrects.</div>';
					
					//update last time connection
					$query = "UPDATE tusers SET last_login='$datetime' WHERE id LIKE '$r[0]'";
					$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
			
					$www = "./index.php?page=dashboard&userid=$r[0]&state=1";
					//web redirection
					echo "<SCRIPT LANGUAGE='JavaScript'>
							<!--
							function redirect()
							{
							window.location='$www'
							}
							setTimeout('redirect()',$rparameters[time_display_msg]);
							-->
						</SCRIPT>";
				}
			} else {
				// if error with login or password 
				echo '<div id="erreur"><img src="./images/access.png" alt="erreur" style="border-style: none" alt="img" /> Votre nom d\'utilisateur ou mot de passe, n\'est pas correct.</div>';
				$www = "./index.php";
				session_destroy();
				//web redirection to login page
				echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='$www'
						}
						setTimeout('redirect()',$rparameters[time_display_msg]);
						-->
					</SCRIPT>";
			}
		}
		else
		{
			// if error with login or password 
			echo '<div id="erreur"><img src="./images/access.png" alt="erreur" style="border-style: none" alt="img" /> Votre nom d\'utilisateur ou mot de passe, n\'est pas correct.</div>';
			$www = "./index.php";
			session_destroy();
			//web redirection to login page
			echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='$www'
						}
						setTimeout('redirect()',$rparameters[time_display_msg]);
						-->
					</SCRIPT>";
		}
	}; 
	// if user isn't connected then display authentication else display dashboard
	if ($_SESSION['login'] == '') 
	{
		if($rparameters['ldap_auth']==1) 
		{
			if ($rparameters['ldap_type']==0) $ldap_type='Windows'; else $ldap_type='OpenLDAP';
			$info="&nbsp;<img title=\"Vous pouvez utiliser votre identifiant et mot de passe $ldap_type.\" src=\"./images/info_icn.png\" border=\"0\" />";
		} else { $info='';}
		echo '
		<br /><br /><br /><br /><br /><br /><br /><br />
		<center>
		<div style="width:300px" id="catalogue">
		<table style="height:300; valign:middle; width:300px; text-align:center;"   style="border-style: none" alt="img" cellpadding="0" cellspacing="0"> 
		<tr> 
		<td> 
			<center>
				<fieldset >
					<legend class="h2"><img alt="authentification" src="./images/auth.png" style="border-style: none" alt="img" />&nbsp;Authentification</legend>
					
					<br />
					<form id="conn" method="post" action="">	
						<table>
							<tr>
								<td align="left"><b>Utilisateur:</b></td>
								<td><input type="text" class="textbox" id="login" name="login" /> </td>
								<td>'.$info.'</td>
							</tr>
							<tr>
								<td align="left"><b>Mot de passe:</b>&nbsp;</td>
								<td><input type="password" class="textbox" id="pass" name="pass" /></td>
								<td></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td colspan="3">
									<div class="buttons1">
										<button name="submit" value="Enregistrer" type="submit"  class="positive"  id="submit">
											<img src="images/apply2.png" alt=""/>
											Valider
										</button>
									</div>
								</td>
							</tr>					
						</table>
					</form>
					<script type="text/JavaScript">
							document.getElementById("login").focus();
					</script>
					<br />
				</fieldset>
			</center>
		</td> 
		</tr> 
		</table>
		
		</div>
		</center>
		<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
		';
	}
	else if ($profile != '' && isset($_POST['submit']) != "Enregistrer")
	{
		//queries for count
		$uid= $_SESSION['user_id'];
		$cnt1 = mysql_query("SELECT count(*) FROM `tincidents` WHERE $profile='$uid' and state LIKE '%' and disable='0'"); 
		$cnt1= mysql_fetch_array($cnt1);
		$cnt2 = mysql_query("SELECT count(*) FROM `tincidents` WHERE state LIKE '%' and disable='0'"); 
		$cnt2= mysql_fetch_array($cnt2);
		$cnt3 = mysql_query("SELECT count(*) FROM `tincidents` WHERE $profile='$uid' and techread='0' and disable='0'"); 
		$cnt3= mysql_fetch_array($cnt3);
		$cnt4 = mysql_query("SELECT count(*) FROM `tincidents` WHERE techread='0' and disable='0'"); 
		$cnt4= mysql_fetch_array($cnt4);
		$cnt5 = mysql_query("SELECT count(*) FROM `tincidents` WHERE technician='0' and t_group='0' and disable='0'"); 
		$cnt5= mysql_fetch_array($cnt5);
		$cnt6 = mysql_query("SELECT count(*) FROM `tincidents` WHERE state='5' and disable='0'"); 
		$cnt6= mysql_fetch_array($cnt6);
			
			// table resize if no right on left menu
			if ($rright['side']!=0) 
			echo'<div id="right" style="width:860px">'; 
			else 
			echo'<div id="right" style="width:1100px">';

				// Display page
				if ($_GET['page']=="") {$_GET['page']="dashboard";}
				include("$_GET[page].php");
				echo "
			</div>";
			//Display left sidebar
			if ($rright['side']!=0) echo '
				<div id="sidebar">
					<div id="sidebartop"></div>'; 
	
				if ($rright['side_open_ticket']!=0)
				{
					if ($profile=='user') $new="newticket_u"; else $new="newticket";
					echo '
					<h2>Actions</h2>
					<ul>
						  <li '; if (($_GET['page']=='newticket') || $_GET['page']=='newticket_u') echo "class=\"active\""; echo '><a href="./index.php?page='.$new.'&userid='.$_SESSION['user_id'].'">Nouveau ticket</a></li>
					</ul>
					';
				}
				if ($rright['side_your']!=0)
				{
					echo '
					<h2>Vos demandes</h2>
					<ul>
						';
						//unread
						echo "<li "; if (($_GET['state']=='%') && ($_GET['userid']==$_SESSION['user_id']) && ($_GET['techread']!='0') && $_GET['page']!='searchengine') echo "class=\"active\""; echo '><a title="Tous les tickets vous étant attribués" href="./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state=%">Toutes vos demandes ('.$cnt1[0].')</a></li>';
						if ($rright['side_your_not_read']!=0) {
							echo "<li "; 
							if (($_GET['techread']=='0') && ($_GET['userid']==$_SESSION['user_id'])) echo "class=\"active\""; echo '><a title="Vos tickets non lus" href="./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;techread=0">Non lu ('.$cnt3[0].')'; if ($cnt3[0]!=0) {echo '&nbsp<img style="border-style: none" alt="img" src="./images/wait_min.png" />';} echo'</a>';
							echo '</li> ';
						}
						//not attribute
						if ($rright['side_your_not_attribute']!=0) {echo "<li "; if (($_GET['userid']=='0') && ($_GET['state']=='5')) echo "class=\"active\""; echo '><a href="./index.php?page=dashboard&amp;userid=0&amp;state=5">Non Attribué ('.$cnt6[0].')</a></li>';}
						//all other states
						$reqstate = mysql_query("SELECT * FROM `tstates` WHERE id not like 5 ORDER BY number"); 
						while ($row=mysql_fetch_array($reqstate))
						{
							$cnt= mysql_query("SELECT count(*) FROM `tincidents` WHERE $profile='$uid' and state LIKE '$row[id]' and disable='0'"); 
							$cnt= mysql_fetch_array($cnt);
							echo "<li "; if (($_GET['state']==$row['id']) && ($_GET['userid']==$_SESSION['user_id'])) echo "class=\"active\""; echo '><a title="'.$row['description'].'" href="./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state='.$row['id'].'">'.$row['name'].' ('.$cnt[0].')</a></li>';
						}
						echo "
					</ul>";
				}
				//display personal view
				if ($rright['side_view']!=0)
				{
					//if exist view for connected user then diplay link view
					$query = mysql_query("SELECT * FROM `tviews` WHERE uid='$uid' ORDER BY 'name' ");
					$row= mysql_fetch_array($query);
					if ($row[0]!='')
					{
						echo '
						<h2>Vos vues</h2>
						<ul>';
							//get view of connected user
							$query = mysql_query("SELECT * FROM `tviews` WHERE uid='$uid' ORDER BY 'name' ");
							while ($row=mysql_fetch_array($query))
							{
								//case for no sub categories
								if ($row['subcat']==0) $subcat='%'; else $subcat=$row['subcat']; 
								//count entries
								$q= mysql_query("SELECT COUNT(*) FROM `tincidents` WHERE category='$row[category]' AND subcat LIKE'$subcat' AND (state='1' OR state='2' OR state='6') AND disable='0'");
								$n= mysql_fetch_array($q);
								echo '<li '; if ($_GET['viewid']==$row['id'])  echo'class="active"'; echo '><a href="./index.php?page=dashboard&amp;userid=%&amp;category='.$row['category'].'&amp;subcat='.$subcat.'&amp;viewid='.$row['id'].'">Vue '.$row['name'].' ('.$n[0].')</a></li>';
							}
							echo '
						</ul>';
					}
				}
				//display all demands
				if ($rright['side_all']!=0)
				{
					echo "
					<h2>Toutes les demandes</h2>
					<ul>
						";
						if ($rright['side_all_wait']!=0) {echo "<li "; if (($_GET['state']=='%') && ($_GET['userid']!=$_SESSION['user_id']) && ($_GET['techread']!='0') && ($_GET['userid']=='0')) echo "class=\"active\""; echo '><a href="./index.php?page=dashboard&amp;userid=0&amp;t_group=0&amp;state=%">Nouvelles demandes ('.$cnt5[0].') '; if ($cnt5[0]!='0') echo '<img style="border-style: none" alt="img" src="./images/wait_min.png" />'; echo '</a></li>';}
						echo "<li "; if (($_GET['state']=='%') && ($_GET['userid']!=$_SESSION['user_id']) && ($_GET['techread']!='0')  && ($_GET['userid']!='0') && ($_GET['viewid']=='')) echo "class=\"active\""; echo '><a href="./index.php?page=dashboard&amp;userid=%&amp;state=%">Toutes les demandes ('.$cnt2[0].')</a></li>';
						if ($rright['side_your_not_read']!=0) { echo "<li "; if (($_GET['techread']=='0') && ($_GET['userid']!=$_SESSION['user_id']) && ($_GET['userid']!=$_SESSION['user_id'])) echo "class=\"active\""; echo '><a href="./index.php?page=dashboard&amp;userid=%&amp;techread=0">Non lu ('.$cnt4[0].')</a></li>';}
						$reqstate = mysql_query("SELECT * FROM `tstates` ORDER BY number"); 
						while ($row=mysql_fetch_array($reqstate))
						{
							$cnt= mysql_query("SELECT count(*) FROM `tincidents` WHERE state LIKE '$row[id]' and disable='0'"); 
							$cnt= mysql_fetch_array($cnt);
							echo "<li "; if (($_GET['state']==$row['id']) && ($_GET['userid']!=$_SESSION['user_id'])) echo "class=\"active\""; echo '><a title="'.$row['description'].'" href="./index.php?page=dashboard&amp;userid=%&amp;state='.$row['id'].'">'.$row['name'].' ('.$cnt[0].')</a></li>';
						}
						echo "
					</ul>";
				}
				if ($rright['side']!=0) echo '
					<div id=\"sidebarbtm\"></div>
				</div>
				';

	}
?>