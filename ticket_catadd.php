<?php
################################################################################
# @Name : edit_categories.php
# @Desc : add and modify categories
# @call : ./ticket.php
# @parameters :  
# @Author : Flox
# @Update : 01/11/2013
# @Version : 3.0
################################################################################

//initialize variables 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_GET['subcat'])) $_GET['subcat'] = ''; 
if(!isset($_GET['cat'])) $_GET['cat'] = ''; 

if(!isset($_POST['addsubcat'])) $_POST['addsubcat'] = ''; 
if(!isset($_POST['modifysubcat'])) $_POST['modifysubcat'] = ''; 
if(!isset($_POST['subcatname'])) $_POST['subcatname'] = '';
 
if(!isset($subcat)) $subcat = '';
if(!isset($subcatname)) $subcatname = '';
if(!isset($name)) $name = '';

//special char rename
$_POST['subcatname'] = mysql_real_escape_string($_POST['subcatname']);
$name = mysql_real_escape_string($name);

if($_POST['addsubcat']){
	$requete = "INSERT INTO tsubcat (cat,name) VALUES ('$_GET[cat]','$_POST[subcatname]')";
	$execution = mysql_query($requete);
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
if($_POST['modifysubcat']){
	$requete = "UPDATE tsubcat SET name='$_POST[name]' where id like '$_GET[subcat]'";
	$execution = mysql_query($requete);
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
// new subcat
if ($_GET['action']=="addcat")
{
	$boxtitle="<i class='icon-user blue bigger-120'></i> Ajout d'une sous-catégorie";
	$boxtext= "
	<form name=\"form\" method=\"POST\" action=\"\" id=\"form\">
		<input  name=\"addsubcat\" type=\"hidden\" value=\"1\">
		<label for=\"cat\">Catégorie:</label>
		<br />
		<select id=\"cat\" name=\"cat\">
			";
			$qcat= mysql_query("SELECT * FROM `tcategory` order by name ASC");
			while ($rcat=mysql_fetch_array($qcat)) {$boxtext= $boxtext.'<option value="'.$rcat['id'].'">'.$rcat['name'].'</option>';} 
			$query= mysql_query("SELECT * FROM `tcategory` WHERE id like '$_GET[cat]'");
			$row=mysql_fetch_array($query);	
			$boxtext= $boxtext."<option value=\"$row[id]\" selected>$row[name]</option>";
	$boxtext= $boxtext."				
		</select>
		<br />
		<label for=\"subcat\"> Sous-catégorie:</label>
		<input  name=\"subcatname\" type=\"text\" value=\"$subcatname\" size=\"26\">
	</form>
	";
	$valid="Ajouter";
	$action1="$('form#form').submit();";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
}
//edit subcat
else
{
	$boxtitle="<i class='icon-user blue bigger-120'></i>Modification de sous-catégorie";
	$boxtext= "
	<form name=\"form\" method=\"POST\" action=\"\" id=\"form\">
		<input  name=\"modifysubcat\" type=\"hidden\" value=\"1\">
		Catégorie:
		<select  id=\"cat\" name=\"cat\">
		";
		$query= mysql_query("SELECT * FROM `tcategory` order by name ASC");
		while ($row=mysql_fetch_array($query)) 	$boxtext=$boxtext."<option value=\"$row[id]\">$row[name]</option>";
		
		$query= mysql_query("SELECT * FROM `tcategory` WHERE id like '$_GET[cat]'");
		$row=mysql_fetch_array($query);	
		$boxtext=$boxtext."<option value=\"$row[id]\" selected>$row[name]</option>";
		
		$query = mysql_query("SELECT * FROM tsubcat WHERE id LIKE '$_GET[subcat]'");
		$row=mysql_fetch_array($query);
		$boxtext=$boxtext."
		</select>
		<br />
		Sous-Catégorie:
		<input  name=\"name\" type=\"text\" size=\"26\" value=\"$row[name]\">
		<br /><br />
	</form>
	";
	$valid="Modifier";
	$action1="$('form#form').submit();";
	$cancel="Fermer";
	$action2="$( this ).dialog( \"close\" ); ";
}
include "./modalbox.php"; 
?>