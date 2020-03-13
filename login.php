<?php
################################################################################
# @Name : login.php
# @Description : Login page for enter credentials and redirect to register page
# @Call : index.php
# @Parameters : 
# @Author : Flox
# @Create : 07/03/2010
# @Update : 01/10/2019
# @Version : 3.1.44
################################################################################

//initialize variables 
if(!isset($state)) $state = ''; 
if(!isset($userid)) $userid = ''; 
if(!isset($techread)) $techread = '';
if(!isset($find_login)) $find_login = '';
if(!isset($profile)) $profile = '';
if(!isset($newpassword)) $newpassword = '';
if(!isset($salt)) $salt= '';
if(!isset($dcgen)) $dcgen= '';
if(!isset($ldap_type)) $ldap_type= '';
if(!isset($message)) $message= '';
if(!isset($login_error)) $login_error= '';
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($_SESSION['login'])) $_SESSION['login'] = ''; 
if(!isset($_GET['page'])) $_GET['page'] = ''; 
if(!isset($_GET['state'])) $_GET['state'] = ''; 
if(!isset($_GET['techread'])) $_GET['techread'] = ''; 
if(!isset($_GET['userid'])) $_GET['userid'] = ''; 
if(!isset($_GET['id'])) $_GET['id'] = '';

if($_GET['state']=='') $_GET['state'] = '%';
	//actions on submit
	if (isset($_POST['submit']))
	{
		$login=(isset($_POST['login'])) ? $_POST['login'] : '';
		$pass= (isset($_POST['pass'])) ? $_POST['pass']  : '';
		
		$qry=$db->prepare("SELECT `id`,`login`,`password`,`salt`,`profile`,`disable` FROM `tusers`");
		$qry->execute();
		while ($row = $qry->fetch()) 
		{
			//uppercase login converter to compare
			$login=strtoupper($login);
			$db_login=strtoupper($row['login']);
			
			if($login && $pass && ($db_login == $login) && $row['password']!='' && $row['disable']==0) //check existing login
			{
				if(strlen($row['password'])>33) //hash detection
				{
					if(password_verify($pass, $row['password'])) {
						$find_login=$row['login'];
						$user_id=$row['id'];
						$profile=$row['profile'];
					}
				}elseif($row['password']==md5($row['salt'] . md5($pass))) //md5 has detect allow and convert hash, using for hash transition
				{ 
					$find_login=$row['login'];
					$user_id=$row['id'];
					$profile=$row['profile'];
					//update hash
					$hash=password_hash($pass, PASSWORD_DEFAULT);
					$qry2=$db->prepare("UPDATE `tusers` SET `password`=:password WHERE `id`=:id");
					$qry2->execute(array('password' => $hash,'id' => $row['id']));
				}
			}	
		}
		$qry->closeCursor();
		if($find_login) 
		{	
			$_SESSION['login']=$find_login;
			$_SESSION['user_id']=$user_id;
			//reset attempt counter
			if($rparameters['user_disable_attempt'])
			{
				$qry=$db->prepare("UPDATE `tusers` SET `auth_attempt`=0 WHERE `id`=:id");
				$qry->execute(array('id' => $_SESSION['user_id']));
			}
			//update last time connection
			$qry=$db->prepare("UPDATE `tusers` SET `last_login`=:last_login,`ip`=:ip WHERE `id`=:id");
			$qry->execute(array('last_login' => $datetime,'ip' => $_SERVER['REMOTE_ADDR'],'id' => $user_id));
			//display loading 
			echo '<i class="icon-spinner icon-spin"></i>&nbsp;Chargement...';
			//user pref default redirection state
			$qry = $db->prepare("SELECT * FROM `tusers` WHERE id=:id");
			$qry->execute(array('id' => $_SESSION['user_id']));
			$ruser=$qry->fetch();
			$qry->closeCursor();
			if($ruser['default_ticket_state']) $redirectstate=$ruser['default_ticket_state']; else $redirectstate=1;
			//select page to redirect for email link case
			if($_GET['id']) {
			    $www='./index.php?page=ticket&id='.$_GET['id'].'';
			} else {
				if($redirectstate=='meta_all')
				{
					$www="./index.php?page=dashboard&userid=%25&state=meta";
				} else {
					$www="./index.php?page=dashboard&userid=$user_id&state=$redirectstate";
				}
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
		elseif(($rparameters['ldap'])=='1' && ($rparameters['ldap_auth']=='1'))
		{
			/////////// if Gestsup user is not found and LDAP is enable search in LDAP///////////
			// LDAP connect
			if($rparameters['ldap_port']==636) {$hostname='ldaps://'.$rparameters['ldap_server'];} else {$hostname=$rparameters['ldap_server'];}
			$ldap=ldap_connect($hostname,$rparameters['ldap_port']) or die("Impossible de se connecter au serveur LDAP.");
			ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			$domain=$rparameters['ldap_domain'];
			if ($rparameters['ldap_type']==0 || $rparameters['ldap_type']==3) 
			{
				//if UPN not add domain suffix
				if($rparameters['ldap_login_field']=='UserPrincipalName')
				{
					$ldapbind = @ldap_bind($ldap, $login, $pass);
				} else { 
					$ldapbind = @ldap_bind($ldap, "$login@$domain", $pass);
				}
			} else { //open LDAP case
				//generate DC Chain from domain parameter
				$dcpart=explode(".",$domain);
				$i=0;
				while($i<count($dcpart)) {
					$dcgen="$dcgen,dc=$dcpart[$i]";
					$i++;
				}
				require_once('./core/functions.php');
				if(preg_match('/gs_en/',$rparameters['ldap_password'])) {$rparameters['ldap_password']=gs_crypt($rparameters['ldap_password'], 'd' , $rparameters['server_private_key']);}
				$dn='cn='.$rparameters['ldap_user'].$dcgen;
				$ldapbind_as_admin = @ldap_bind($ldap, $dn, $rparameters['ldap_password']);
				if($ldapbind_as_admin)
				{
					$dn='cn='.$login.','.$rparameters['ldap_url'].$dcgen;
					$ldapbind = @ldap_bind($ldap, $dn, $pass);
				} else {
					if($rparameters['debug']==1) {echo 'ERROR: unable to bind as admin on OpenLDAP Server';}
					$ldapbind=0;
				}
			}

			if ($ldapbind && $pass!='') 
			{
				$_SESSION['login'] = "$login";
				$qry = $db->prepare("SELECT `id`,`password` FROM `tusers` WHERE `login`=:login AND `disable`=:disable");
				$qry->execute(array('login' => $login,'disable' => 0));
				$r=$qry->fetch();
				$qry->closeCursor();
				$_SESSION['user_id'] = "$r[id]";
				if($r['id']=='')
				{
					//if error with login or password 
					$message= '<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">
							<i class="icon-remove"></i>
						</button>
						<strong>
							<i class="icon-remove"></i>
							'.T_('Erreur').'
						</strong>
						'.T_('Votre compte est inexistant dans ce logiciel.').'
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
							setTimeout('redirect()',$rparameters[time_display_msg]+1000);
							-->
						</SCRIPT>";
				} else {
					//update last time connection
					$qry=$db->prepare("UPDATE `tusers` SET `last_login`=:last_login,`ip`=:ip WHERE `id`=:id");
					$qry->execute(array('last_login' => $datetime,'ip' => $_SERVER['REMOTE_ADDR'],'id' => $r['id']));
					
					//update GS db pwd
					$newpassword = password_hash($pass, PASSWORD_DEFAULT);
					//update password
					$qry=$db->prepare("UPDATE `tusers` SET `password`=:password WHERE `id`=:id");
					$qry->execute(array('password' => $newpassword,	'id' => $r['id']));
					
					//user pref default redirection state
					$qry=$db->prepare("SELECT * FROM `tusers` WHERE `id`=:id");
					$qry->execute(array('id' => $_SESSION['user_id']));
					$ruser=$qry->fetch();
					$qry->closeCursor();
					
					//modify redirection state to personal user state if it's define else using admin parameter
					if($ruser['default_ticket_state']) {$redirectstate=$ruser['default_ticket_state'];} else {$redirectstate=$rparameters['login_state'];}
			
					//select page to redirect for email link case
					if($_GET['id']) {
						$www = './index.php?page=ticket&id='.$_GET['id'].'&userid='.$_SESSION['user_id'];
					} else {
						if($redirectstate=='meta_all')
						{
							//URL
							$www = "./index.php?page=dashboard&userid=%&state=meta";
						} elseif($redirectstate=='all')
						{
							//URL
							$www = "./index.php?page=dashboard&userid=%25&state=%25";
						} else {
							//URL
							$www = './index.php?page=dashboard&userid='.$_SESSION['user_id'].'&state='.$redirectstate;
						}
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
							'.T_('Erreur').':
						</strong>
						'.T_('Votre nom d\'utilisateur ou mot de passe, n\'est pas correct.').'
					</div>';
				$login_error=1;	
				$www = "./index.php";
				session_destroy();
				//web redirection to login page
				echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='$www'
						}
						setTimeout('redirect()',$rparameters[time_display_msg]+1500);
						-->
					</SCRIPT>";
			}
		}
		else
		{
			//secure check user attempt
			if($rparameters['user_disable_attempt'])
			{
				//check if user exist
				$qry=$db->prepare("SELECT `id`,`auth_attempt`,`disable` FROM `tusers` WHERE login=:login");
				$qry->execute(array('login' => $_POST['login']));
				$row=$qry->fetch();
				$qry->closeCursor();
				if($row)
				{
					$attempt=$row['auth_attempt']+1;
					$qry=$db->prepare("UPDATE `tusers` SET `auth_attempt`=:auth_attempt WHERE `id`=:id");
					$qry->execute(array('auth_attempt' => $attempt,'id' => $row['id']));
					$attempt_remaing=$rparameters['user_disable_attempt_number']-$attempt;
					if($attempt_remaing>0)
					{
						$attempt_remaing=T_('Il reste').' '.$attempt_remaing.' '.T_('tentatives avant la désactivation de votre compte');
					} else {
						if($row['disable'])
						{
							$attempt_remaing=T_('Votre compte est désactivé, contacter votre administrateur');
						} else {
							$qry=$db->prepare("UPDATE `tusers` SET `disable`=1 WHERE `id`=:id");
							$qry->execute(array('id' => $row['id']));
							$attempt_remaing=T_('Votre compte a été désactivé, suite à').' '.$rparameters['user_disable_attempt_number'].' '.T_('tentatives de connexion infructueuses');
						}
					}
				} else {$attempt_remaing='';}
			} else {$attempt_remaing='';}
			// if error with login or password 
			$message= '
			<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">
				<i class="icon-remove"></i>
			</button>
			<strong>
				<i class="icon-remove"></i>
				'.T_('Erreur').' : '.T_('Identifiant ou mot de passe invalide').'
			</strong>
			';
			if($attempt_remaing) {$message.='<br />'.$attempt_remaing;} else {$message.=T_("Votre nom d'utilisateur ou mot de passe, n'est pas correct.");}
			echo '
			<br>
			</div>';
			$www = "./index.php";
			$login_error=1;
			session_destroy();
			//web redirection to login page
			echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='$www'
						}
						setTimeout('redirect()',$rparameters[time_display_msg]+1500);
						-->
					</SCRIPT>";
		}
	}; 
	// if user isn't connected then display authentication else display dashboard
	if ($_SESSION['login']=='') 
	{
		if($rparameters['ldap_auth']==1) 
		{
			if ($rparameters['ldap_type']==0) {$ldap_type='Windows';} elseif($rparameters['ldap_type']==1) {$ldap_type='OpenLDAP';} elseif($rparameters['ldap_type']==3) {$ldap_type='Samba4';}
			$info='<i title="'.T_('Vous pouvez utiliser votre identifiant et mot de passe').' '.$ldap_type.'" class="icon-question-sign smaller-80"></i>';
		} else { $info='';}
		echo '
		<body class="login-layout">
		<br />
		<br />
		<br />
		<div class="main-container">
			<div class="main-content" >
				<div class="row"  >
					<div class="col-sm-10 col-sm-offset-1" >
						<div class="login-container">
							<div class="center">
								<h1>
									<i class="icon-ticket green"></i>
									<span class="white">GestSup</span>
								</h1>
								<h4 class="blue">';if (isset($rparameters['company'])) echo $rparameters['company']; echo' </h4>
								';
								//re-size logo if height superior 40px
								if ($rparameters['logo']!='' && file_exists("./upload/logo/$rparameters[logo]")) 
								{
									$size = getimagesize("./upload/logo/$rparameters[logo]");
									$width=$size[0];
									if ($width>300) {$logo_width='width="300"';} else {$logo_width='';}
								} else {$logo_width=''; }
								//display logo if image file exist
								if (file_exists("./upload/logo/$rparameters[logo]"))
								{
									echo '<img style="border-style: none" alt="logo" '.$logo_width.' src="./upload/logo/'; if ($rparameters['logo']=='') echo 'logo.png'; else echo $rparameters['logo'];  echo '" />';
								}
								echo '
							</div>
							<br />
							'.$message;
							//display login if no error
							if($login_error==0)
							{
								echo '
								<div class="space-6"></div>
								<div class="position-relative">
									<div id="login-box" class="login-box visible widget-box no-border">
										<div class="widget-body">
											<div class="widget-main">
												<h4 class="header blue lighter bigger">
													<i class="icon-lock green"></i>
													'.T_('Saisissez vos identifiants').'
													'.$info.'
												</h4>
												
												<div class="space-6"></div>
												<form id="conn" method="post" action="">	
													<fieldset>
														<label class="block clearfix">
															<span class="block input-icon input-icon-right">
																<input class="form-control" type="text" id="login" name="login" class="span12" placeholder="'.T_('Nom d\'utilisateur').'" />
																<i class="icon-user"></i>
															</span>
														</label>
														<label class="block clearfix">
															<span class="block input-icon input-icon-right">
																<input class="form-control" type="password" id="pass" name="pass" class="span12" placeholder="'.T_('Mot de passe').'" />
																<i class="icon-lock"></i>
															</span>
														</label>
														<div class="space"></div>
														<div class="clearfix">
															<button onclick="submit()" type="submit" id="submit" name="submit" class="pull-right btn btn-sm btn-primary">
																<i class="icon-ok"></i>
																'.T_('Connexion').'
															</button>
														</div>
														<div class="space-4"></div>
													</fieldset>
												</form>
												<br />
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
														<a href="./register.php"  class="user-signup-link">
															'.T_('S\'enregistrer').'
															<i class="icon-arrow-right"></i>
														</a>
													</div>
												</div';
											}
										echo '	
										</div><!--/widget-body-->
									</div><!--/login-box-->
								</div><!--/position-relative-->
								';
							}
							echo '
						</div>
					</div><!--/.span-->
				</div><!--/.row-fluid-->
			</div>
			<!-- DO NOT DELETE OR MODIFY THIS LINE THANKS -->
				<span style="position: fixed; bottom: 0px; right: 0px;"><a title="'.T_('Ouvre un nouvel onglet vers le site gestsup.fr').'" target="_blank" href="https://gestsup.fr">GestSup.fr</a></span>
			<!-- DO NOT DELETE OR MODIFY THIS LINE THANKS -->
		</div><!--/.main-container-->
		<script type="text/JavaScript">
			document.getElementById("login").focus();
		</script>
		';
	}
?>