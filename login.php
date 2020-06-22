<?php
################################################################################
# @Name : login.php
# @Desc : login page
# @call : index.php
# @paramters : 
# @Autor : Flox
# @Create : 07/03/2010
# @Update : 09/06/2015
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
if(!isset($message)) $message= '';
// ##########
if(!isset($u_group)) $u_group= '';
if(!isset($_SESSION['u_group'])) $_SESSION['u_group'] = '';
//
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($_SESSION['login'])) $_SESSION['login'] = '';

if(!isset($_GET['page'])) $_GET['page'] = ''; 
if(!isset($_GET['state'])) $_GET['state'] = ''; 
if(!isset($_GET['techread'])) $_GET['techread'] = ''; 
if(!isset($_GET['userid'])) $_GET['userid'] = ''; 
if(!isset($_GET['id'])) $_GET['id'] = '';

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
			//uppercase login converter
			$login = strtoupper($login);
			$nom = strtoupper($row['login']);
			
			//double (OR) test for crypted password transition
			if ($nom == $login && ($row['password']==$pass || $row['password']==md5($row['salt'] . md5($pass))) && $row['password']!='' && $row['disable']==0) 
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
			$_SESSION['company'] = $row['company'];
			$_SESSION['rank'] = $row['profile'];
			
			//update last time connection
			$query = "UPDATE tusers SET last_login='$datetime' WHERE id LIKE '$user_id'";
			$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
			echo "Chargement...";
			
			//user pref defaut redirection state
			$quser = mysql_query("SELECT * FROM `tusers` WHERE id=$_SESSION[user_id]"); 
	        $ruser= mysql_fetch_array($quser);
			if($ruser['default_ticket_state']) $redirectstate=$ruser['default_ticket_state']; else $redirectstate=1;
			
			//select page to redirect for email link case
			if($_GET['action'] == "print") {
				$www = './ticket_print.php?id='.$_GET['id'].'';
			} elseif($_GET['id']) {
			    $www = './index.php?page=ticket&id='.$_GET['id'].'';
			} else {
			    $www = "./index.php?page=dashboard&userid=$user_id&state=$redirectstate";
			}
			//web redirection
			echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='$www'
						}
						setTimeout('redirect()');
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
				@$ldapbind = ldap_bind($ldap, "$login@$domain", $pass);
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
				$_SESSION['rank'] = $row['profile'];
				$q = mysql_query("SELECT id FROM tusers where login='$login'");
				$r = mysql_fetch_array($q);
				$_SESSION['user_id'] = "$r[0]";
				if($r['0']=='')
				{
					//if error with login or password 
					$message= '<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">
							<i class="icon-remove"></i>
						</button>
						<strong>
							<i class="icon-remove"></i>
							Erreur
						</strong>
						Votre compte est inexistant dans ce logiciel.
						<br>
					</div>';
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
					//update last time connection
					$query = "UPDATE tusers SET last_login='$datetime' WHERE id LIKE '$r[0]'";
					$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
					
					//user pref defaut redirection state
					$quser = mysql_query("SELECT * FROM `tusers` WHERE id=$_SESSION[user_id]"); 
					$ruser= mysql_fetch_array($quser);
					if($ruser['default_ticket_state']) $redirectstate=$ruser['default_ticket_state']; else $redirectstate=1;
			
					//select page to redirect for email link case
					if($_GET['action'] == "print") {
						$www = './ticket_print.php?id='.$_GET['id'].'';
					} elseif($_GET['id']) {
						$www = './index.php?page=ticket&id='.$_GET['id'].'';
					} else {
						$www = "./index.php?page=dashboard&userid=$_SESSION[user_id]&state=$redirectstate";
					}
					//web redirection
					echo "<SCRIPT LANGUAGE='JavaScript'>
							<!--
							function redirect()
							{
							window.location='$www'
							}
							setTimeout('redirect()');
							-->
						</SCRIPT>";
				}
			} else {
				// if error with login or password 
				$message= '<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">
							<i class="icon-remove"></i>
						</button>
						<strong>
							<i class="icon-remove"></i>
							Erreur
						</strong>
						<br />
						Votre nom d\'utilisateur ou mot de passe, n\'est pas correct
					</div>';
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
			$message= '<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">
							<i class="icon-remove"></i>
						</button>
						<strong>
							<i class="icon-remove"></i>
							Erreur
						</strong>
						Votre nom d\'utilisateur ou mot de passe, n\'est pas correct.
						<br>
				</div>';
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
			$info='<i title="Vous pouvez utiliser votre identifiant et mot de passe '.$ldap_type.'" class="icon-question-sign smaller-80"></i>';
		} else { $info='';}
		echo '
		<body class="login-layout">
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">
							<div class="center">
								<h1>';
									if($_SERVER['SERVER_NAME'] == 'ticket.sisfrance.eu'){
										echo '<img style="border-style: none" alt="logo" src="./images/'; if ($rparameters['logo']=='') echo 'logo_sis.png'; else echo $rparameters['logo'];  echo '" />';
									}else{
										echo '<img style="border-style: none" alt="logo" src="./upload/logo/'; if ($rparameters['logo']=='') echo 'logo.png'; else echo $rparameters['logo'];  echo '" />';
									}	
								echo '<span class="white">&nbsp;&nbsp;Ticket</span>
								</h1>
							</div>
							<br />
							'.$message.'
							<div class="space-6"></div>
							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue lighter bigger">
												<i class="icon-lock green"></i>
												Saisissez vos identifiants
												'.$info.'
											</h4>
											
											<div class="space-6"></div>
											<form id="conn" method="post" action="">	
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input class="form-control" type="text" id="login" name="login" class="span12" placeholder="Nom d\'utilisateur" />
															<i class="icon-user"></i>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input class="form-control" type="password" id="pass" name="pass" class="span12" placeholder="Mot de passe" />
															<i class="icon-lock"></i>
														</span>
													</label>
													<div class="space"></div>
													<div class="clearfix">
														<button onclick="submit()" type="submit" id="submit" name="submit" class="pull-right btn btn-sm btn-primary">
															<i class="icon-ok"></i>
															Connexion
														</button>
													</div>
													<div class="space-4"></div>
												</fieldset>
											</form>
										</div><!--/widget-main-->
										';
										if ($rparameters['user_register']==1)
										{
    										echo '
    										<div class="toolbar clearfix">
    										   
    											<div>
    												<a href="#" onclick="show_box(\'forgot-box\'); return false;" class="forgot-password-link">
    												
    												</a>
    											</div>
    											<div>
    												<a href="register.php"  class="user-signup-link">
    													S\'enregistrer
    													<i class="icon-arrow-right"></i>
    												</a>
    											</div>
    										</div';
										}
									echo '	
									</div><!--/widget-body-->
								</div><!--/login-box-->
							</div><!--/position-relative-->
						</div>
					</div><!--/.span-->
				</div><!--/.row-fluid-->
			</div>
			<span style="position: absolute; bottom: 0; right: 0;"><a href="http://gestsup.fr">.</a><a href="http://klemg.fr">.</a></span>
		</div><!--/.main-container-->
		<script type="text/JavaScript">
			document.getElementById("login").focus();
		</script>
		';
	}
?>

		
		