<?php
################################################################################
# @Name : procedure.php
# @Desc : display, edit and add procedure
# @call : /index.php
# @parameters : 
# @Autor : Flox
# @Create : 03/09/2013
# @Update : 13/08/2014
# @Version :3.0.10
################################################################################

//initialize variables 
if(!isset($_POST['addprocedure'])) $_POST['addprocedure'] = '';
if(!isset($_POST['save'])) $_POST['save'] = '';
if(!isset($_POST['modif'])) $_POST['modif'] = '';
if(!isset($_POST['return'])) $_POST['return'] = '';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';

if(!isset($_GET['procedure'])) $_GET['procedure'] = '';
if(!isset($_GET['edit'])) $_GET['edit'] = '';


//if delete procedure is submit
if ($_GET['action']=='delete')
{
	//disable ticket
	$query = "UPDATE tprocedures SET disable='1' WHERE id LIKE '$_GET[id]'";
	$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	//display delete message
	echo "<div id=\"erreur\"><img src=\"./images/delete_max2.png\" border=\"0\" /> Procédure $_GET[id] supprimée.</div>";
	 //redirect
	$www = "./index.php?page=procedure";
	echo "<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
		window.location='$www'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
	</SCRIPT>";
}

//if add procedure is submit
if ($_GET['action']=='add')
{
	//Database modification
	if($_POST['save'])
	{
		//modify special char for sql query
		$_POST['name'] = mysql_real_escape_string($_POST['name']);
		$_POST['text'] = mysql_real_escape_string($_POST['text']);
	
		$query= "INSERT INTO tprocedures (name,text,category,subcat) VALUES ('$_POST[name]','$_POST[text]','$_POST[category]','$_POST[subcat]')";
		$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
		
		//display action message
		echo '
			<div class="alert alert-block alert-success">
				<i class="icon-ok green"></i>
				La procédure à été sauvegardée.
			</div>
		';
		
		 //redirect
		$www = "./index.php?page=procedure";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	
	echo '
		<div class="page-header position-relative">
			<h1>
				<i class="icon-book"></i> Ajout d\'une procédure
			</h1>
		</div>
		<fieldset>
			<div class="col-xs-12">
				<form method="POST" name="myform" id="myform" action="" onsubmit="loadVal();" >
					<label for="name">Nom:</label>
					<input name="name" size="50px" type="text" value="">
						<br />
					<br />
					<label for="category">Catégorie:</label>
					<select name="category" onchange="submit();">
					    ';
					       
					    	$qcat = mysql_query("SELECT * FROM tcategory ORDER BY name"); 
        					while ($rcat=mysql_fetch_array($qcat))
        					{
        					    if($_POST['category'])
        					    {
        					        if($rcat['id']==$_POST['category']) {echo '<option selected value="'.$rcat['id'].'">'.$rcat['name'].'</option>';}
        					    } elseif ($row['category']==$rcat['id']) 
        					    {
        					        echo '<option selected value="'.$rcat['id'].'">'.$rcat['name'].'</option>';
        					    } 
        					    echo '<option value="'.$rcat['id'].'">'.$rcat['name'].'</option>';
        					}
					    echo '
					</select>
					<br />
					<br />
					<label for="subcat">Sous-catégorie:</label>
					<select name="subcat">
					   ';
					    	if ($_POST['category'])
							{$qsubcat= mysql_query("SELECT * FROM `tsubcat` WHERE cat LIKE '$_POST[category]' order by name ASC"); }
							else
							{$qsubcat= mysql_query("SELECT * FROM `tsubcat` WHERE cat LIKE '$row[category]' order by name ASC");}
        					while ($rsubcat=mysql_fetch_array($qsubcat))
        					{
        					    if($_POST['subcat'])
        					    {
            					    if ($rsubcat['id']==$_POST['subcat'])
            					    {
            					        echo '<option selected value="'.$rsubcat['id'].'">'.$rsubcat['name'].'</option>';
            					    }
        					    } elseif ($row['subcat']==$rsubcat['id']) {
        					        echo '<option selected value="'.$rsubcat['id'].'">'.$rsubcat['name'].'</option>';
        					    }
        					        echo '<option value="'.$rsubcat['id'].'">'.$rsubcat['name'].'</option>';
        					    
        					}
					    echo '
					</select>
					<br /><br />
					<div id="editor" class="wysiwyg-editor"></div>
					<input type="hidden" name="text" />
					<div class="form-actions align-right clearfix">
						<button name="return" value="return" id="return" type="submit" class="btn btn-danger">
							<i class="icon-undo bigger-110"></i>
							retour
						</button>
						&nbsp;&nbsp;&nbsp;
						<button name="save" value="save" id="save" type="submit" class="btn btn-primary">
							<i class="icon-save bigger-110"></i>
							Sauvegarder
						</button>
					</div>
				</form>
			</div>
		</fieldset>			
	';
	
}
//if modif procedure
else if ($_GET['action']=='edit')
{
//Database modification
	if($_POST['modif'])
	{
		
		//modify special char for sql query
		$_POST['name'] = mysql_real_escape_string($_POST['name']);
		$_POST['text'] = mysql_real_escape_string($_POST['text']);
	
		$query= "UPDATE tprocedures SET name='$_POST[name]', text='$_POST[text]', category='$_POST[category]', subcat='$_POST[subcat]' WHERE id='$_GET[id]'";
		$exec = mysql_query($query) or die('Erreur SQL !<br />'.mysql_error());
		
		//display action message
		echo '
			<div class="alert alert-block alert-success">
				<i class="icon-ok green"></i>
				La procédure
				<strong class="green">
					<small>'.$_GET['id'].'</small>
				</strong>
				à été sauvgardée.
			</div>
		';
		
		 //redirect
		$www = "./index.php?page=procedure&id=$_GET[id]&action=edit&";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	if($_POST['return'])
	{
		//redirect
		$www = "./index.php?page=procedure";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	//SQL query
	$query = mysql_query("SELECT * FROM tprocedures WHERE id=$_GET[id]"); 
	$row=mysql_fetch_array($query);
	
	//detect <br> for wysiwyg transition from 2.9 to 3.0
	$findbr=stripos($row['text'], '<br>');
	if ($findbr === false) {$text=nl2br($row['text']);} else {$text=$row['text'];}
	
	echo '
		<div class="page-header position-relative">
			<h1>
				<i class="icon-book"></i> Edition de la procédure n°'.$row['id'].': '.$row['name'].'
			</h1>
		</div>
		<fieldset>
			<div class="col-xs-12">
				<form method="POST" name="myform" id="myform" action="" onsubmit="loadVal();" >
					<label for="name">Nom:</label>
					<input name="name" size="50px" type="text" value="'.$row['name'].'">
					<br />
					<br />
					<label for="category">Catégorie:</label>
					<select name="category" onchange="submit();">
					    ';
					       
					    	$qcat = mysql_query("SELECT * FROM tcategory ORDER BY name"); 
        					while ($rcat=mysql_fetch_array($qcat))
        					{
        					    if($_POST['category'])
        					    {
        					        if($rcat['id']==$_POST['category']) {echo '<option selected value="'.$rcat['id'].'">'.$rcat['name'].'</option>';}
        					    } elseif ($row['category']==$rcat['id']) 
        					    {
        					        echo '<option selected value="'.$rcat['id'].'">'.$rcat['name'].'</option>';
        					    } 
        					    echo '<option value="'.$rcat['id'].'">'.$rcat['name'].'</option>';
        					}
					    echo '
					</select>
					<br />
					<br />
					<label for="subcat">Sous-catégorie:</label>
					<select name="subcat">
					   ';
					    	if ($_POST['category'])
							{$qsubcat= mysql_query("SELECT * FROM `tsubcat` WHERE cat LIKE '$_POST[category]' order by name ASC"); }
							else
							{$qsubcat= mysql_query("SELECT * FROM `tsubcat` WHERE cat LIKE '$row[category]' order by name ASC");}
        					while ($rsubcat=mysql_fetch_array($qsubcat))
        					{
        					    if($_POST['subcat'])
        					    {
            					    if ($rsubcat['id']==$_POST['subcat'])
            					    {
            					        echo '<option selected value="'.$rsubcat['id'].'">'.$rsubcat['name'].'</option>';
            					    }
        					    } elseif ($row['subcat']==$rsubcat['id']) {
        					        echo '<option selected value="'.$rsubcat['id'].'">'.$rsubcat['name'].'</option>';
        					    }
        					        echo '<option value="'.$rsubcat['id'].'">'.$rsubcat['name'].'</option>';
        					    
        					}
					    echo '
					</select>
					<br /><br />
					<div id="editor" class="wysiwyg-editor">'.$text.'</div>
					<input type="hidden" name="text" />
					<div class="form-actions align-right clearfix">
						<button name="return" value="return" id="return" type="submit" class="btn btn-danger">
							<i class="icon-undo bigger-110"></i>
							retour
						</button>
						&nbsp;&nbsp;&nbsp;
						<button name="modif" value="modif" id="modif" type="submit" class="btn btn-primary">
							<i class="icon-save bigger-110"></i>
							Sauvegarder
						</button>
					</div>
				</form>
			</div>
		</fieldset>			
	';
}
//display list of procedure
else
{
	//SQL query
	$query = mysql_query("SELECT count(*) FROM tprocedures WHERE disable=0"); 
	$row=mysql_fetch_array($query);
	echo '
		<div class="page-header position-relative">
			<h1>
				<i class="icon-book"></i> 
				Procédures
				<small>
					<i class="icon-double-angle-right"></i>
					&nbsp;Nombre: '.$row[0].' &nbsp;&nbsp;
				</small>
			</h1>
		</div>
		';

	//display add button
	echo '
	<form name="add" method="post" action="index.php?page=procedure&amp;action=add"  id="thisform">
		<button name= name="addprocedure" value="Ajouter" id="addprocedure" type="submit" class="btn btn-sm btn-success">
			<i class="icon-plus"></i> Ajouter une procédure
		</button>
	</form>
	<div class="space-4"></div>
	';
	//begin table
	echo '
	<table id="sample-table-1" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th><i class="icon-circle"></i> Numéro</th>
					<th><i class="icon-sign-blank"></i> Catégorie</th>
					<th><i class="icon-sitemap"></i> Sous-Catégorie</th>
					<th><i class="icon-tag"></i> Nom</th>
					<th><i class="icon-play"></i> Actions</th>
				</tr>
			</thead>
			<tbody>
				';
					$masterquery = mysql_query("SELECT * FROM tprocedures WHERE disable=0 ORDER by category,subcat ASC"); 
					while ($row=mysql_fetch_array($masterquery))
					{
					   //get cat name
					   	$qcat=mysql_query("SELECT name FROM tcategory WHERE id=$row[category]"); 
	                    $rcat=mysql_fetch_array($qcat);
	                    $qscat=mysql_query("SELECT name FROM tsubcat WHERE id=$row[subcat]"); 
	                    $rscat=mysql_fetch_array($qscat);
						echo "
						<tr >	
							<td onclick=\"document.location='./index.php?page=procedure&amp;id=$row[id]&amp;action=edit'\" >$row[id]</td>
							<td onclick=\"document.location='./index.php?page=procedure&amp;id=$row[id]&amp;action=edit'\" >$rcat[0]</td>
							<td onclick=\"document.location='./index.php?page=procedure&amp;id=$row[id]&amp;action=edit'\" >$rscat[0]</td>
							<td onclick=\"document.location='./index.php?page=procedure&amp;id=$row[id]&amp;action=edit'\" >$row[name]</td>
							<td>
								<div class=\"hidden-phone visible-desktop btn-group\">									
									<button title=\"Editer\" onclick='window.location.href=\"./index.php?page=procedure&amp;id=$row[id]&amp;action=edit\";' class=\"btn btn-xs btn-warning\">
										<i class=\"icon-edit bigger-120\"></i>
									</button>
									
									<button title=\"Supprimer\" onclick='window.location.href=\"./index.php?page=procedure&amp;id=$row[id]&amp;action=delete\";' class=\"btn btn-xs btn-danger\">
										<i class=\"icon-trash bigger-120\"></i>
									</button>
								</div>
								
							</td>
						</tr>
						";
					}
				echo '
			</tbody>
		</table>
	';
}
include ('./wysiwyg.php');
?>