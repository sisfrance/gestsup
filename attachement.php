<?php 
################################################################################
# @Name : attachement.php
# @Desc : attach file to ticket
# @call : /ticket.php
# @paramters : 
# @Autor : Flox
# @Create : 
# @Update : 04/01/2014
# @Version : 3.0.3
################################################################################

//initialize variables 
if(!isset($_GET['delimg'])) $_GET['delimg']= ''; 
if(!isset($_GET['id'])) $_GET['id']= ''; 
if(!isset($file_size)) $file_size= ''; 
if(!isset($globalrow['id'])) $globalrow['id']= ''; 

//database delete
if ($_GET['delimg']!="" && $rright['img_suppr']!=0) {
$requete = "UPDATE `tincidents` SET `$_GET[delimg]` = '' WHERE `id` =$globalrow[id];";
$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());
	//redirection vers la page d'accueil
	$www = "./index.php?page=ticket&id=$globalrow[id]";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}

$query = mysql_query("SELECT img1,img2,img3,img4,img5 FROM tincidents WHERE id LIKE '$_GET[id]'");
$row=mysql_fetch_array($query);

// find the first freeslot else not display attach input
if ($row['img1']=="") {$freeslot="1";}
else if ($row['img2']=="") {$freeslot="2";}
else if ($row['img3']=="") {$freeslot="3";}
else if ($row['img4']=="") {$freeslot="4";}
else if ($row['img5']=="") {$freeslot="5";}
else {$freeslot="0";}

if ($freeslot!="0")
{
	echo "
	<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"100000000\" />
	<input style=\"display:inline;\" id=\"file$freeslot\" type=\"file\" name=\"file$freeslot\" /> &nbsp;
	<button class=\"btn btn-minier btn-success\" title=\"Charger le fichier\" name=\"upload\" value=\"upload\" type=\"submit\" id=\"upload\"><i class=\"icon-upload bigger-140\"></i></button>
	<br />
	";
}

if ($row['img1']!='')
{
	$ext = strrchr($row['img1'],'.' );
	$ext = substr($ext, 1);
	$ext = strtolower($ext);
    //	if (($ext=='png')||($ext=='jpg')||($ext=='gif')||($ext=='bmp')) $pic='rel="lightbox"'; else $pic='';
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  target=\"about_blank\" title=\"$row[img1]\" href=\"./upload/$_GET[id]/$row[img1]\" style=\"text-decoration:none\"><img border=\"0\" src=\"./images/icon_file/$ext.png\" /></a>&nbsp;<a target=\"about_blank\" title=\"$row[img1]\" href=\"./upload/$_GET[id]/$row[img1]\" >$row[img1]</a>" ;
	if ($_GET['page']!="ticket_u" && $rright['img_suppr']!=0) echo "<a title=\"Supprimer\" href=\"./index.php?page=ticket&amp;&amp;id=$globalrow[id]&amp;delimg=img1\"> <i class=\"icon-remove red bigger-140\"></i></a>";
	if (is_dir("./upload/$_GET[id]/")) $file_size = filesize("./upload/$_GET[id]/$row[img1]");
	$file_size=round($file_size/1024,0);
	echo " ($file_size Ko)<br />";
}

if ($row['img2']!='')
{
	$ext = strrchr($row['img2'],'.' );
	$ext = substr($ext, 1);
	$ext = strtolower($ext);
    //if (($ext=='png')||($ext=='jpg')||($ext=='gif')||($ext=='bmp')) $pic='rel="lightbox"'; else $pic='';
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$row[img2]\" title=\"$row[img2]\" style=\"text-decoration:none\"><img border=\"0\" src=\"./images/icon_file/$ext.png\" /></a>&nbsp;<a target=\"about_blank\" href=\"./upload/$_GET[id]/$row[img2]\" title=\"$row[img2]\">$row[img2]</a>";
	if ($_GET['page']!="ticket_u") echo "<a title=\"Supprimer\" href=\"./index.php?page=ticket&amp;&amp;id=$globalrow[id]&amp;delimg=img2\"> <i class=\"icon-remove red bigger-140\"></i></a>";
	if (is_dir("./upload/$_GET[id]/")) $file_size = filesize("./upload/$_GET[id]/$row[img2]");
	$file_size=round($file_size/1024,0);
	echo " ($file_size Ko)<br />";
		
}

if ($row['img3']!='')
{
	$ext = strrchr($row['img3'],'.' );
	$ext = substr($ext, 1);
	$ext = strtolower($ext);
//	if (($ext=='png')||($ext=='jpg')||($ext=='gif')||($ext=='bmp')) $pic='rel="lightbox"'; else $pic='';
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$row[img3]\" title=\"$row[img3]\" style=\"text-decoration:none\"><img border=\"0\" src=\"./images/icon_file/$ext.png\" /></a>&nbsp;<a target=\"about_blank\" href=\"./upload/$_GET[id]/$row[img3]\" title=\"$row[img3]\" >$row[img3]</a>";
	if ($_GET['page']!="ticket_u") echo " <a title=\"Supprimer\" href=\"./index.php?page=ticket&amp;&amp;id=$globalrow[id]&amp;delimg=img3\"> <i class=\"icon-remove red bigger-140\"></i></a>";	
	if (is_dir("./upload/$_GET[id]/")) $file_size = filesize("./upload/$_GET[id]/$row[img3]");
	$file_size=round($file_size/1024,0);
	echo " ($file_size Ko)<br />";
}

if ($row['img4']!='')
{
	$ext = strrchr($row['img4'],'.' );
	$ext = substr($ext, 1);
	$ext = strtolower($ext);
//	if (($ext=='png')||($ext=='jpg')||($ext=='gif')||($ext=='bmp')) $pic='rel="lightbox"'; else $pic='';
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$row[img4]\" title=\"$row[img4]\" style=\"text-decoration:none\"><img border=\"0\" src=\"./images/icon_file/$ext.png\" /></a>&nbsp;<a target=\"about_blank\" href=\"./upload/$_GET[id]/$row[img4]\" title=\"$row[img4]\" >$row[img4]</a>";
	if ($_GET['page']!="ticket_u") echo " <a title=\"Supprimer\" href=\"./index.php?page=ticket&amp;&amp;id=$globalrow[id]&amp;delimg=img4\"> <i class=\"icon-remove red bigger-140\"></i></a>";	
	if (is_dir("./upload/$_GET[id]/")) $file_size = filesize("./upload/$_GET[id]/$row[img4]");
	$file_size=round($file_size/1024,0);
	echo " ($file_size Ko)<br />";
}
if ($row['img5']!='')
{
	$ext = strrchr($row['img5'],'.' );
	$ext = substr($ext, 1);
	$ext = strtolower($ext);
//	if (($ext=='png')||($ext=='jpg')||($ext=='gif')||($ext=='bmp')) $pic='rel="lightbox"'; else $pic='';
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$row[img5]\" title=\"$row[img5]\" style=\"text-decoration:none\"><img border=\"0\" src=\"./images/icon_file/$ext.png\" /></a>&nbsp;<a target=\"about_blank\" href=\"./upload/$_GET[id]/$row[img5]\" title=\"$row[img5]\" >$row[img5]</a>";
	if ($_GET['page']!="ticket_u") echo " <a title=\"Supprimer\" href=\"./index.php?page=ticket&amp;&amp;id=$globalrow[id]&amp;delimg=img5\"> <i class=\"icon-remove red bigger-140\"></i></a>";	
	if (is_dir("./upload/$_GET[id]/")) $file_size = filesize("./upload/$_GET[id]/$row[img5]");
	$file_size=round($file_size/1024,0);
	echo " ($file_size Ko)<br />";
}
?>