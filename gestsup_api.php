<?php
################################################################################
# @Name : gestsup_api.php 
# @Desc : Display short ticket declaration interface to integer in other website (ex: intranet)
# @Autor : Flox
# @Create : 29/10/2013
# @Update : 29/10/2013
# @Version : 1.3 beta
################################################################################

############################## START EDITABLE PART #############################
$server="localhost"; //gestsup server name
$user="root";	//gestsup database username
$password=""; //gestsup database password
$db="gestsup"; //gestsup database name 
############################## END EDITABLE PART #############################

//database connection
$connect = mysql_connect($server,$user,$password) or die("impossible de se connecter : ". mysql_error());
$db = mysql_select_db($db, $connect)  or die("impossible de sélectionner la base : ". mysql_error());
 
//initialize variables
if(!isset($_POST['send'])) $_POST['send']= '';

if ($_POST['send']) //database input
{
	$date=date('Y-m-d H:m:s');
	
	//escape special char in sql query 
	$_POST['description'] = mysql_real_escape_string($_POST['description']);
	$_POST['title'] = mysql_real_escape_string($_POST['title']);
	
	$query= "INSERT INTO tincidents (user,title,description,state,date_create,creator,criticality,techread) VALUES ('$_POST[user]','$_POST[title]','$_POST[description]','5','$date','$_POST[user]','4','0')";
	$exec = mysql_query($query);
	
	//load parameters table
	$qparameters = mysql_query("SELECT * FROM `tparameters`"); 
	$rparameters= mysql_fetch_array($qparameters);
	
	//find incident number  
	$query = mysql_query("SELECT MAX(id) FROM tincidents");
	$row=mysql_fetch_array($query);
	$number =$row[0];
	echo '
	<font color="green">
		La demande <b>#'.$number.'</b> à bien été prise en compte.<br />
	</font>
	Pour suivre vos demandes vous pouvez vous rendre sur la page <a target="about_blank" href="'.$rparameters['server_url'].'">'.$rparameters['server_url'].'</a>
	';
}
else //display form
{
	echo '
	<form method="POST" action="" id="myform">
		<table border="0">
			<tr>
				<td><label for="user">Nom:</label></td>
				<td>
					<select name="user" />
						';
						$q = mysql_query("SELECT * FROM `tusers` WHERE disable='0' ORDER BY lastname"); 
						while ($row=mysql_fetch_array($q))
						{
							echo '<option value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';
						}
						echo '
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="title">Titre:</label>
				</td>
				<td>
					<input name="title" type="text" size="30px" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<label for="description">Demande:</label>
				<br />
				<textarea name="description" cols="50" rows="10" ></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value=" Envoyer votre demande " id="send" name="send" />
				</td>
			</tr>
		</table>
	</form>';
}
//close database access
mysql_close($connect);?>