<?php
################################################################################
# @Name : list.php
# @Desc : administration of table
# @call : /admin/admin.php
# @parameters : 
# @Author : Flox
# @Create : 15/03/2011
# @Update : 24/03/2014
# @Version : 3.0.8
################################################################################

//initialize variables 
if(!isset($_POST['Modifier'])) $_POST['Modifier'] = '';
if(!isset($_POST['Ajouter'])) $_POST['Ajouter'] = '';
if(!isset($_POST['cat'])) $_POST['cat'] = '';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';
if(!isset($_GET['table'])) $_GET['table'] = '';
if(!isset($_GET['id'])) $_GET['id'] = '';
if(!isset($_GET['action'])) $_GET['action'] = '';
if(!isset($nbchamp)) $nbchamp = '';
if(!isset($champ0)) $champ0 = '';
if(!isset($champ1)) $champ1 = '';
if(!isset($champ3)) $champ3 = '';
if(!isset($reqchamp)) $reqchamp = '';
if(!isset($set)) $set = '';
if(!isset($i)) $i = '';

//default table
if ($_GET['table']=='') $_GET['table']='tcategory';

//default page
if ($_GET['action']=='') $_GET['action']='disp_list';

//special char rename
$champ0 = mysql_real_escape_string($champ0); 
$champ1 = mysql_real_escape_string($champ1); 
$champ3 = mysql_real_escape_string($champ3); 
$_POST['cat'] = mysql_real_escape_string($_POST['cat']); 
$_POST['subcat'] = mysql_real_escape_string($_POST['subcat']); 

// Retreive selected table description
$query = mysql_query("DESC $_GET[table]");
while ($row=mysql_fetch_array($query)) 
{
	${'champ' . $nbchamp} =$row[0];
	$nbchamp++;
}
$nbchamp1=$nbchamp;
$nbchamp=$nbchamp-1;

if ($_GET['action']=="delete") 
{
	$requete = "DELETE FROM $_GET[table] WHERE id = '$_GET[id]'";
	$execution = mysql_query($requete);
	
	$www = "./index.php?page=admin&subpage=list&table=$_GET[table]&action=disp_list";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}

if ($_GET['action']=="update") 
{
	//special case subcat
	if($_GET['table']=='tsubcat')
	{
		$requete =  "UPDATE tsubcat SET cat='$_POST[cat]', name='$_POST[subcat]' WHERE id LIKE '$_GET[id]'";
		$execution = mysql_query($requete);
	}
	else
	{
		for ($i=0; $i <= $nbchamp; $i++)
		{
		    //init
			if(!isset($_POST[$reqchamp])) $_POST[$reqchamp] = '';
			$reqchamp="${'champ' . $i}";
			$_POST[$reqchamp] = mysql_real_escape_string($_POST[$reqchamp]); 
			if ($i=='1') $set="$reqchamp='$_POST[$reqchamp]'"; else $set="$set, $reqchamp='$_POST[$reqchamp]'";
		}
		$requete =  "UPDATE $_GET[table] SET $set WHERE id LIKE '$_GET[id]'";
		$execution = mysql_query($requete);
	}
	
	$www = "./index.php?page=admin&subpage=list&table=$_GET[table]&action=disp_list";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}

if ($_GET['action']=="add")
{
	//special case subcat
	if($_GET['table']=='tsubcat')
	{
		$requete =  "INSERT INTO tsubcat (cat,name) VALUES ('$_POST[cat]','$_POST[subcat]')";
		$execution = mysql_query($requete);
	}
	else
	{
		// on genere le champ champ de la requete update en conction de la table selectionner
		for ($i=1; $i <= $nbchamp; $i++)
		{
			if ($i!="1") {$reqchamp="$reqchamp,${'champ' . $i}";} else {$reqchamp="${'champ' . $i}";}
		}
		// on genere le champ values de la requete update en conction de la table selectionner
		for ($i=1; $i <= $nbchamp; $i++)
		{
			$nomchamp="${'champ' . $i}";
			$_POST[$nomchamp] = mysql_real_escape_string($_POST[$nomchamp]); 
			if ($i!="1") {$reqvalue="$reqvalue,'$_POST[$nomchamp]'";} else {$reqvalue="'$_POST[$nomchamp]'";}
		}
		$query = "INSERT INTO $_GET[table] ($reqchamp) VALUES ($reqvalue)";
		$exec = mysql_query($query);
	}
	$www = "./index.php?page=admin&subpage=list&table=$_GET[table]&action=disp_list";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
?>
<div class="page-header position-relative">
	<h1>
		<i class="icon-list"></i>  Gestion des listes
	</h1>
</div>
<div class="tabbable tabs-left">
	<ul class="nav nav-tabs" id="myTab3">
		<li <?php if($_GET['table']=='tcategory') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=tcategory">
				<i class="blue icon-table bigger-110"></i>
				Catégories
			</a>
		</li>
		<li <?php if($_GET['table']=='tsubcat') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=tsubcat">
				<i class="blue icon-table bigger-110"></i>
				Sous-Catégorie
			</a>
		</li>
		<li <?php if($_GET['table']=='tcriticality') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=tcriticality">
				<i class="blue icon-table bigger-110"></i>
				Criticité
			</a>
		</li>
		<li <?php if($_GET['table']=='tstates') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=tstates">
				<i class="blue icon-table bigger-110"></i>
				Etats
			</a>
		</li>
		<li <?php if($_GET['table']=='tplaces') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=tplaces">
				<i class="blue icon-table bigger-110"></i>
				Lieux
			</a>
		</li>
		<li <?php if($_GET['table']=='ttemplates') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=ttemplates">
				<i class="blue icon-table bigger-110"></i>
				Modèles
			</a>
		</li>
		<li <?php if($_GET['table']=='tpriority') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=tpriority">
				<i class="blue icon-table bigger-110"></i>
				Priorité
			</a>
		</li>
		<li <?php if($_GET['table']=='tservices') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=tservices">
				<i class="blue icon-table bigger-110"></i>
				Services
			</a>
		</li>
		<?php
		//for advanced user paramter
		if($rparameters['user_advanced']==1)
		{
		echo '
    		 <li '; if($_GET['table']=='tcompany') echo 'class="active"'; echo '>
    			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=tcompany">
    				<i class="blue icon-table bigger-110"></i>
    				Sociétés
    			</a>
	    	</li>
		';
		}
		?>
		<li <?php if($_GET['table']=='ttime') echo 'class="active";' ?>>
			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=ttime">
				<i class="blue icon-table bigger-110"></i>
				Temps
			</a>
		</li>
		<?php
		    if($rparameters['ticket_type']=='1')
		    {
		        echo '
    		        <li'; if($_GET['table']=='ttype') {echo 'class="active"';} echo'>
            			<a  href="./index.php?page=admin&amp;subpage=list&amp;table=ttypes">
            				<i class="blue icon-table bigger-110"></i>
            				Types
            			</a>
            		</li>
        		';
		    }
		?>
	</ul>
	<div class="tab-content">
		<?php
		//Display 
		if ($_GET['action']=="disp_edit")
		{
			echo '
				<div class="col-sm-5">
					<div class="widget-box">
						<div class="widget-header">
							<h4>Edition d\'une entrée:</h4>
						</div>
						<div class="widget-body">
							<div class="widget-main no-padding">
								<form method="post" action="./index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=update&id='.$_GET['id'].'" >
								';
									//specific view to subcat 
									if($_GET['table']=='tsubcat')
									{
											//find value
											$req = mysql_query("SELECT * FROM `$_GET[table]` WHERE id LIKE '$_GET[id]'"); 
											$req = mysql_fetch_array($req);
											
											//find category name
											$query = mysql_query("SELECT * FROM `tcategory` WHERE id LIKE '$req[cat]'"); 
											$row = mysql_fetch_array($query);
											
											echo "
												<fieldset>
													<label for=\"cat\">Catégorie</label>
													<select name=\"cat\" id=\"form-field-select-1\">
													";
														$qcat = mysql_query("SELECT * FROM `tcategory`"); 
														while ($rcat=mysql_fetch_array($qcat)) 
														{
															echo '
															<option '; if ($row['id']==$rcat['id']) {echo 'selected';} echo ' value="'.$rcat['id'].'">
																'.$rcat['name'].'
															</option>';
														}
													echo "
													</select>
													<div class=\"space-4\"></div>
													<label for=\"subcat\">Sous-Catégorie</label>
													<input name=\"subcat\" type=\"text\" value=\"$req[name]\" />
												</fieldset>
											";
									} 
									else 
									{
										for ($i=1; $i <= $nbchamp; $i++)
										{
											$req = mysql_query("SELECT ${'champ' . $i} FROM `$_GET[table]` WHERE id LIKE '$_GET[id]'"); 
											$req = mysql_fetch_array($req);
											echo "
											<fieldset>
												<label for=\"${'champ' . $i}\">${'champ' . $i}</label>
													<input name=\"${'champ' . $i}\" type=\"text\" value=\"$req[0]\" />
											</fieldset>
											<div class=\"space-4\"></div>
											";
										}
									}
									echo '
									<div class="form-actions center">
										<button type="submit" class="btn btn-sm btn-success">
											Modifier
											<i class="icon-arrow-right icon-on-right bigger-110"></i>
										</button>
									</div>
								</form>
							</div>
						</div>
							
					</div>
				</div>
			';
		}
		if ($_GET['action']=="disp_add")
		{
			echo '
				<div class="col-sm-5">
					<div class="widget-box">
						<div class="widget-header">
							<h4>Ajout d\'une entrée:</h4>
						</div>
						<div class="widget-body">
							<div class="widget-main no-padding">
								<form method="post" action="./index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=add" >';
									//special case subcat
									if($_GET['table']=='tsubcat')
									{
										echo "
										<fieldset>
											<label for=\"cat\">Catégorie</label>
											<select name=\"cat\" id=\"form-field-select-1\">
											";
												$qcat = mysql_query("SELECT * FROM `tcategory`"); 
												while ($rcat=mysql_fetch_array($qcat)) 
												{
													echo '<option value="'.$rcat['id'].'">'.$rcat['name'].'</option>';
												}	
												echo "
											</select>
											<div class=\"space-4\"></div>
											<label for=\"subcat\">Sous-Catégorie</label>
											<input name=\"subcat\" type=\"text\" value=\"\" />
										</fieldset>
											";
									}
									else
									{
										echo "<fieldset>";
										for ($i=1; $i <= $nbchamp; $i++)
										{
											echo "
											<label for=\"${'champ' . $i}\">${'champ' . $i}</label>
											<input name=\"${'champ' . $i}\" type=\"text\" value=\"\" />
											<div class=\"space-4\"></div>
										
											";
										}
										echo '</fieldset>';
									}
									echo '
									<div class="form-actions center">
										<button type="submit" class="btn btn-sm btn-success">
											Ajouter
											<i class="icon-arrow-right icon-on-right bigger-110"></i>
										</button>
									</div>
								</form>
							</div>
						</div>
							
					</div>
				</div>
			';
		}
		
		if ($_GET['action']=="disp_list")
		{
			echo '
			<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
				<p>
					<button onclick=\'window.location.href="./index.php?page=admin&amp;subpage=list&amp;table='.$_GET['table'].'&amp;action=disp_add";\' class="btn btn-sm btn-success">
						<i class="icon-plus"></i> Ajouter une entrée
					</button>
				</p>
			</div>
			<table id="sample-table-1" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>';
						//build title line
							$query = mysql_query("DESC $_GET[table]");
							while ($row=mysql_fetch_array($query)) 
							echo "<th>$row[Field]</th>";
						echo '
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>';
				//build each line
				$query = mysql_query("SELECT * FROM `$_GET[table]`");
				while ($row=mysql_fetch_array($query)) 
				{
					echo "
					<tr>
					";
					for($i=0; $i < $nbchamp1; ++$i)
					{
						//special case for tsubcat
						if($_GET['table']=='tsubcat' && $i==1)
						{
							$qcat = mysql_query("SELECT * FROM `tcategory` WHERE id LIKE '$row[$i]'"); 
							$rcat = mysql_fetch_array($qcat);
							echo "<td>$rcat[name]</td>";
						} else {echo "<td>$row[$i]</td>";}
					}
					echo '
						<td>
							<button title="Editer" onclick=\'window.location.href="./index.php?page=admin&amp;subpage=list&amp;table='.$_GET['table'].'&amp;action=disp_edit&amp;id='.$row['id'].'";\'class="btn btn-xs btn-warning">
								<i class="icon-pencil bigger-120"></i>
							</button>
							';
							if($_GET['table']!='tstates' || $row['id']>6 AND $row['id'] != '9' AND $row['id'] != '5' AND $row['id'] != '6') 
							{
								echo '
								<button title="Supprimer" onclick=\'window.location.href="./index.php?page=admin&amp;subpage=list&amp;table='.$_GET['table'].'&amp;id='.$row['id'].'&amp;action=delete";\' class="btn btn-xs btn-danger">
									<i class="icon-trash bigger-120"></i>
								</button>
								';
							}							
							 echo "
						</td>
					</tr>";
				}
				echo '
				</tbody>
			</table>
			<br /><br /><br /><br /><br /><br /><br /><br /><br />
			';
		}
		?>
	</div>
</div>