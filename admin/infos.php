<?php
################################################################################
# @Name : infos.php
# @Desc :  admin infos
# @call : admin.php
# @parameters : 
# @Autor : Flox
# @Create : 12/01/2011
# @Update : 02/01/2014
# @Version : 3.0.3
################################################################################

//generate name of current version
$vactuname=explode('.',$rparameters['version']);
if($vactuname[2]==0) $vactuname=''; else $vactuname="($vactuname[0].$vactuname[1] patch $vactuname[2])";
?>
<div class="page-header position-relative">
	<h1>
		<i class="icon-info-sign"></i>  Informations sur Fork GestSup
	</h1>
</div>
<div class="profile-user-info profile-user-info-striped">
	<div class="profile-info-row">
		<div class="profile-info-name"> Version: </div>
		<div class="profile-info-value">
			<span id="username"><a href="./index.php?page=changelog"><?php echo ''.$rparameters['version'].' <font size=1">'.$vactuname.'</font>';?></a>, fork 1.7.3</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> Licence: </div>

		<div class="profile-info-value">
			<span id="username"><a target="_blank" href="http://fr.wikipedia.org/wiki/Licence_publique_g%C3%A9n%C3%A9rale_GNU">GNU GPL</a>, fork GNU GPL</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> Site Officiel: </div>

		<div class="profile-info-value">
			<span id="username"><a target="_blank" href="http://gestsup.fr">GestSup.fr</a>, fork sur demande par email</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> Contact: </div>
		<div class="profile-info-value">
			<span id="username"><a target="_blank" href="">KlemG</a> & GestSup</span>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> Communauté: </div>

		<div class="profile-info-value">
			<span id="username"><a target="_blank" href="http://gestsup.fr/index.php?page=forum">Forum</a> & Gestsup</span>
		</div>
	</div>
</div>