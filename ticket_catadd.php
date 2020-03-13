<?php
################################################################################
# @Name : edit_categories.php
# @Description : add and modify categories
# @Call : ./ticket.php
# @Parameters :  
# @Author : Flox
# @Create : 07/01/2014
# @Update : 13/04/2018
# @Version : 3.1.32
################################################################################

//initialize variables 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_GET['subcat'])) $_GET['subcat'] = ''; 
if(!isset($_GET['cat'])) $_GET['cat'] = ''; 

if(!isset($_POST['addsubcat'])) $_POST['addsubcat'] = ''; 
if(!isset($_POST['modifysubcat'])) $_POST['modifysubcat'] = ''; 
if(!isset($_POST['subcatname'])) $_POST['subcatname'] = '';
if(!isset($_POST['name'])) $_POST['name'] = '';
 
if(!isset($subcat)) $subcat = '';
if(!isset($subcatname)) $subcatname = '';
if(!isset($name)) $name = '';

$_GET['cat']=strip_tags($_GET['cat']);
$_GET['editcat']=strip_tags($_GET['editcat']);
$_POST['subcatname']=strip_tags($_POST['subcatname']);
$_POST['name']=strip_tags($_POST['name']);

$db_cat=strip_tags($db->quote($_GET['cat']));
$db_editcat=strip_tags($db->quote($_GET['editcat']));

if($_POST['addsubcat']){
	$qry=$db->prepare("INSERT INTO `tsubcat` (`cat`,`name`) VALUES (:cat,:name)");
	$qry->execute(array('cat' => $_GET['cat'],'name' => $_POST['subcatname']));
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
if($_POST['modifysubcat']){
	$qry=$db->prepare("UPDATE `tsubcat` SET name=:name WHERE id=:id");
	$qry->execute(array('name' => $_POST['name'],'id' => $_GET['editcat']));
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
	$boxtitle='<i class=\'icon-sitemap blue bigger-120\'></i> '.T_('Ajout d\'une sous-catégorie');
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input name="addsubcat" type="hidden" value="1">
		<label for="cat">'.T_('Catégorie').':</label>
		<br />
		<select id="cat" name="cat">
			';
			$qry=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY name ASC");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				if($row['id']==0) //special case to translate none value
				{$boxtext= $boxtext.'<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
				else
				{$boxtext= $boxtext.'<option value="'.$row['id'].'">'.$row['name'].'</option>';}
			} 
			$qry->closeCursor(); 
			
			$qry=$db->prepare("SELECT `id`,`name` FROM `tcategory` WHERE id=:id");
			$qry->execute(array('id' => $_GET['cat']));
			$row=$qry->fetch();
			$qry->closeCursor();

			$boxtext= $boxtext.'<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
        	$boxtext= $boxtext.'				
		</select>
		<br />
		<label for="subcat"> '.T_('Sous-catégorie').':</label>
		<input  name="subcatname" type="text" value="'.$subcatname.'" size="26">
	</form>
	';
	$valid=T_('Ajouter');
	$action1="$('form#form').submit();";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
//edit subcat
else
{
	$boxtitle='<i class=\'icon-user blue bigger-120\'></i>'.T_('Modification sous-catégorie');
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input  name="modifysubcat" type="hidden" value="1">
		'.T_('Catégorie').':
		<br />
		<select id="cat" name="cat">
		';
		$qry=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY name ASC");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			$qry2=$db->prepare("SELECT `id` FROM `tcategory` WHERE id=:id");
			$qry2->execute(array('id' => $_GET['cat']));
			$row2=$qry2->fetch();
			$qry2->closeCursor();
			
			if($row2['id']==$row['id']) {$selected='selected';} else {$selected='';}
			if($row['id']==0) //case translate none value
			{$boxtext= $boxtext.'<option value="'.$row['id'].'" '.$selected.'>'.T_($row['name']).'</option>';}
			else
			{$boxtext= $boxtext.'<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';}
		}
		$qry->closeCursor(); 
		
		$qry=$db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
		$qry->execute(array('id' => $_GET['editcat']));
		$row=$qry->fetch();
		$qry->closeCursor();
		
		$boxtext=$boxtext.'
		</select>
		<br />
		'.T_('Sous-catégorie').':
		<input  name="name" type="text" size="26" value="'.$row['name'].'">
		<br /><br />
	</form>
	';
	$valid=T_('Modifier');
	$action1="$('form#form').submit();";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
include "./modalbox.php"; 
?>