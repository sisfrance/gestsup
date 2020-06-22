<?php
################################################################################
# @Name : ./register.php 
# @Desc : create gestsup user
# @call : /index.php
# @Autor : Flox
# @Version : 3.0.8
# @Create : 20/03/2014
# @Update : 24/03/2014
################################################################################

//initialize variable
if(!isset($_POST['login'])) $_POST['login'] = '';
if(!isset($_POST['password'])) $_POST['password'] = '';
if(!isset($_POST['password2'])) $_POST['password2'] = '';
if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['firstname'])) $_POST['firstname'] = '';
if(!isset($_POST['lastname'])) $_POST['lastname'] = '';
if(!isset($_POST['company'])) $_POST['company'] = '';

//secure HMTL injection
$_POST['login']=strip_tags($_POST['login']);
$_POST['password']=strip_tags($_POST['password']);
$_POST['password2']=strip_tags($_POST['password2']);
$_POST['mail']=strip_tags($_POST['mail']);
$_POST['firstname']=strip_tags($_POST['firstname']);
$_POST['lastname']=strip_tags($_POST['lastname']);

//default values
$defaultprofile=1; //1 is poweruser, 2 is single user 

//connexion script with database parameters
require "connect.php";

//load parameters table
$qparameters = mysql_query("SELECT * FROM `tparameters`"); 
$rparameters= mysql_fetch_array($qparameters);


if ($rparameters['user_register']==1)
{
    //actions on submit
	if (isset($_POST['submit']))
	{
	    //check inputs
	    if($_POST['firstname']) {
    	    if($_POST['lastname']) {
        	    if($_POST['login']) {
        	        if($_POST['password']) {
        	             if($_POST['password2']) {
            	             if($_POST['mail']) {
            	                 if($_POST['password2']==$_POST['password']) {
                	                //crypt password md5 + salt
                                	$salt = substr(md5(uniqid(rand(), true)), 0, 5); // Generate a random key
                                	$_POST['password']=md5($salt . md5($_POST['password'])); // store in md5, md5 password + salt
                	                //query
                	                $query = "INSERT INTO tusers (firstname,lastname,password,salt,mail,profile,login,chgpwd,company) VALUES ('$_POST[firstname]','$_POST[lastname]','$_POST[password]','$salt','$_POST[mail]','$defaultprofile','$_POST[login]','0','$_POST[company]')";
                                	$axec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
                                	//message to display
                                	$message='<div class="alert alert-block alert-success"><center><i class="icon-ok green"></i> Votre compte à été crée avec succès.</center></div>';
            	                } else {$message='<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Vos mots de passes ne sont pas identiques.<br></div>';}
            	              } else {$message='<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Vous devez spécifier une adresse mail.<br></div>';}
        	             } else {$message='<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Vous devez spécifier un mot de passe.<br></div>';}
        	        } else {$message='<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Vous devez spécifier un mot de passe.<br></div>';}
        	    } else {$message='<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Vous devez spécifier un identifiant.<br></div>';}
        	} else {$message='<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Vous devez spécifier un nom.<br></div>';}
        } else {$message='<div class="alert alert-danger"><strong><i class="icon-remove"></i> Erreur:</strong> Vous devez spécifier un prénom.<br></div>';}
	}
    
    //display form
    echo'
    <!DOCTYPE html>
    <html lang="fr">
	<head>
		<meta charset="UTF-8" />
		<title>GestSup | Gestion de Support</title>
		<link rel="shortcut icon" type="image/ico" href="./images/favicon.ico" />
		<meta name="description" content="gestsup" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!-- basic styles -->
		<link href="./template/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="./template/assets/css/font-awesome.min.css" />
		<!--[if IE 7]>
		  <link rel="stylesheet" href="./template/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
		<!-- page specific plugin styles -->
		<!-- fonts -->
		<link rel="stylesheet" href="./template/assets/css/ace-fonts.css" />
		<link rel="stylesheet" href="./template/assets/css/jquery-ui-1.10.3.full.min.css" />
		<!-- ace styles -->
		<link rel="stylesheet" href="./template/assets/css/ace.min.css" />
		<link rel="stylesheet" href="./template/assets/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="./template/assets/css/ace-skins.min.css" />
		
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="./template/assets/css/ace-ie.min.css" />
		<![endif]-->
		<!-- inline styles related to this page -->
		<!-- ace settings handler -->
		<script src="./template/assets/js/ace-extra.min.js"></script>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="./template/assets/js/html5shiv.js"></script>
		<script src="./template/assets/js/respond.min.js"></script>
		<![endif]-->
	</head>
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
								<h1>
									<img style="border-style: none" alt="logo" src="./upload/logo/'; if ($rparameters['logo']=='') echo 'logo.png'; else echo $rparameters['logo'];  echo '" />
									<span class="white">Ticket</span>
								</h1>
							</div>
							<br />
							'.$message.'
							<div class="space-6"></div>
							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header green lighter bigger">
												<i class="icon-user blue"></i>
												Inscription
												'.$info.'
											</h4>
											
											<div class="space-6"></div>
											<form id="conn" method="post" action="">	
												<fieldset>
												    <label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input class="form-control" type="text" id="firstname" name="firstname" class="span12" placeholder="Prénom" />
															<i class="icon-user"></i>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input class="form-control" type="text" id="lastname" name="lastname" class="span12" placeholder="Nom" />
															<i class="icon-user"></i>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input class="form-control" type="text" id="login" name="login" class="span12" placeholder="Identifiant" />
															<i class="icon-user"></i>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input class="form-control" type="password" id="password" name="password" class="span12" placeholder="Mot de passe" />
															<i class="icon-lock"></i>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input class="form-control" type="password" id="password2" name="password2" class="span12" placeholder="Re-taper votre mot de passe" />
															<i class="icon-retweet"></i>
														</span>
													</label>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input class="form-control" type="text" id="mail" name="mail" class="span12" placeholder="Adresse Mail" />
															<i class="icon-envelope"></i>
														</span>
													</label>';
													//for advanced user paramter display company
                                            		if($rparameters['user_advanced']==0)
                                            		{
                                            		    echo '
                                                		<label class="block clearfix">
    														<span class="block input-icon input-icon-right">
    															<select class="form-control" type="text" id="company" name="company" class="span12" placeholder="Adresse Mail" />
    															    <option value="">Votre Société:</option>';
                                									$query = mysql_query("SELECT * FROM tcompany ORDER BY name");
                                									while ($row=mysql_fetch_array($query)) 
                                									{
                                										if ($user1['company']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
                                									} 
                                									echo '
    															</select>
    															<i class="icon-building"></i>
    														</span>
    													</label>
    													';
                                            		}
                                            		echo'
													<div class="space"></div>
													<div class="clearfix">
														<button onclick="submit()" type="submit" id="submit" name="submit" class="width-65 pull-right btn btn-sm btn-success">
															<i class="icon-ok"></i>
															S\'enregistrer
														</button>
													</div>
													<div class="space-4"></div>
												</fieldset>
											</form>
										</div><!--/widget-main-->
										<div class="toolbar clearfix">
    										   
    											<div>
    												<a href="./"  class="forgot-password-link">
    												<i class="icon-arrow-left"></i>
    												Retour
    												</a>
    											</div>
    											<div>
    												<a href="register.php"  class="user-signup-link">
    												
    												</a>
    											</div>
    										</div
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
		// Close database access
		mysql_close($connexion); 
        echo '
	</body>
</html>';

} else {
    echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>Erreur</strong>Cette fonction est désativé par votre administrateur.<br></div>';
}


?>