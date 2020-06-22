<?php
session_start();
require "connect.php";
mysql_query("SET NAMES 'utf8'");
setlocale(LC_TIME, "fr_FR");

if(isset($_GET['id'])) {
	$query = mysql_query("SELECT * FROM tprocedures WHERE id=$_GET[id]");
	$row=mysql_fetch_array($query);

?>
<html>
<head>
	<title><?= $row['name'] ?></title>
	<style>
	body {
		margin-left:10%;
		width:80%;
	}
	</style>
</head>
<body>
	<h1><?= $row['name'] ?></h1>
	<hr>
	<?= $row['text'] ?>
</body>
</html>
<?php
} else {

echo 'Cette procédure n\'existe pas !';

}
