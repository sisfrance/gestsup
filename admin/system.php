<?php
################################################################################
# @Name : system.php
# @Desc :  admin system
# @call : admin.php
# @parameters : 
# @Autor : Flox
# @Create : 12/01/2011
# @Update : 08/09/2013
# @Version : 3.0
################################################################################

?>
<div class="page-header position-relative">
	<h1>
		<i class="icon-desktop"></i>  Etat du système
	</h1>
</div>
<?php include('./system.php'); ?>
<hr />
<center>
	<button onclick='window.open("./admin/phpinfos.php")' class="btn btn-primary">
		<i class="icon-cogs bigger-140"></i>
		Tous les paramètres PHP
	</button>
</center>
