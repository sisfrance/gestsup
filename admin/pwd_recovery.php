<?php
################################################################################
# @Name : pwd_recovery.php
# @Description : recover pwd
# @Call : 
# @Parameters : 
# @Author : Flox
# @Create : 19/03/2019
# @Update : 19/03/2019
# @Version : 3.1.40
################################################################################
?>
<h1>GestSup password recovery</h1>
<form method="POST" action="">
	<label for="password">Enter password :</label>
	<input autocomplete="off" type="password" name="password" />
	<input type="submit" />
</form>
<?php
if(isset($_POST['password']))
{
	$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
	echo '
		Follow this steps :
		<ul>
			<li>STEP 1 : Connect to database</li>
			<li>STEP 2 : Select GestSup database</li>
			<li>STEP 3 : Select tusers table</li>
			<li>STEP 4 : Edit the line with your login</li>
			<li>STEP 5 : Paste this string '.$hash.' in password field</li>
		</ul>
	';
}
?>