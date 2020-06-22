<?php
################################################################################
# @Name : /plugin/availability/print.php
# @Desc : print this page
# @call : /plugin/availability/index.php
# @parameters : category
# @Author : Flox
# @Create : 26/05/2015
# @Update : 02/06/2015
# @Version : 3.0.11
################################################################################
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
	    
		<meta charset="UTF-8" />
		<title>GestSup | Gestion de Support</title>
		<link rel="shortcut icon" type="image/ico" href="../..//images/favicon.ico" />
		<meta name="description" content="gestsup" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	</head>
    <body onload="window.print();">
        <?php
            include("../../connect.php");
			mysql_query("SET NAMES 'utf8'"); 
			$year=$_GET['year'];
			//load parameters table
			$qparameters = mysql_query("SELECT * FROM `tparameters`"); 
			$rparameters= mysql_fetch_array($qparameters);
			//modify database encoding			
			include("index.php");
			mysql_close($connexion);
        ?>
    </body>
</html>