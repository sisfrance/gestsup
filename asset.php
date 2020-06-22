<?php
################################################################################
# @Name : asset.php 
# @Desc : Display tickets list
# @Author : Flox
# @Create : 20/11/2014
# @Update : 20/11/2014
# @Version : 3.0.11
################################################################################

//initialize variables 
if(!isset($asc)) $asc = ''; 
if(!isset($img)) $img= ''; 
if(!isset($filter)) $filter=''; 
if(!isset($col)) $col=''; 

if(!isset($_GET['technician'])) $_GET['technician']= ''; 
if(!isset($_GET['order'])) $_GET['order']= ''; 
if(!isset($_GET['way'])) $_GET['way']= ''; 


//get value is for filter case
if(!isset($_POST['date'])) $_POST['date']= '';

//default values


// select auto order 
if ($filter=='on' || $_GET['order']==''){$_GET['order']=$rparameters['order'];}
elseif ($_GET['order']=='') $_GET['order']='priority';

$masterquery = mysql_query("SELECT * FROM tassets"); 
/*$masterquery = mysql_query("SELECT * FROM tassets ORDER BY $_GET[order] $_GET[way] LIMIT $_GET[cursor], $rparameters[maxline]"); */
?>

<div class="page-header position-relative">
	<h1>
        <icon-ticket"></i> Liste des Matériels
		<small>
			<i class="icon-double-angle-right"></i>
			&nbsp;Nombre: <?php echo 0; ?></i>
		</small>
	</h1>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="table-responsive">
			<table id="sample-table-1" class="table table-striped table-bordered table-hover">
				<?php 
				//*********************** FIRST LIGN *********************** 
				if($_GET['way']=='ASC') $arrow_way='DESC'; else $arrow_way='ASC';
				//build page url link
				$url="./index.php?page=assets&amp;
				";
				
				echo "
				<thead>
					<tr >
						<th "; if ($_GET['order']=='id') echo 'class="active"'; echo ">
							<center>
								<a title=\"Numéro\" href=\"$url&amp;order=id&amp;way=$arrow_way\">
									<i class=\"icon-tag\"></i><br />
									Numéro";
									//Display way arrows
									if ($_GET['order']=='id'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									echo "
								</a>
							</center>
						</th>
						<th ";  if ($_GET['order']=='ip') echo 'class="active"'; echo ">
							<center>
								<a title=\"Adresse IP\"  href=\"$url&amp;order=ip&amp;way=$arrow_way\">
									<i class=\"icon-exchange\"></i><br />
									Adresse IP";
									//Display arrows
									if ($_GET['order']=='ip'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									echo"
								</a>
							</center>
						</th>
						<th "; if ($_GET['order']=='netbios') echo 'class="active"'; echo ">
							<center>
								<a title=\"Nom\"  href=\"$url&amp;order=netbios&amp;way=$arrow_way\">
									<i class=\"icon-desktop\"></i><br />
									Nom Netbios";
									//Display arrows
									if ($_GET['order']=='netbios'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									echo"
								</a>
							</center>
						</th>
						";
						?>
						<th <?php if ($_GET['order']=='type') echo 'class="active"'; ?> >
							<center>
								<a title="Type"  href="<?php echo $url; ?>&amp;order=type&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-sign-blank"></i><br />
									Type
									<?php
									//Display arrows
									if ($_GET['order']=='type'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<th <?php if ($_GET['order']=='model') echo 'class="active"'; ?> >
							<center>
								<a title="Modèle"  href="<?php echo $url; ?>&amp;order=model&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-sitemap"></i>
									Modèle
									<?php
									//Display arrows
									if ($_GET['order']=='model'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<th <?php if ($_GET['order']=='description') echo 'class="active"'; ?> >
							<center>
								<a title="Description"  href="<?php echo $url; ?>&amp;order=description&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-file-text-alt"></i>
									Description
									<?php
									//Display arrows
									if ($_GET['order']=='description'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<th <?php if ($_GET['order']=='date_stock') echo 'class="active"'; ?> >
							<center>
								<a title="Date d\' achat"  href="<?php echo $url; ?>&amp;order=date_stock&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-calendar"></i><br />
									Date achat				
									<?php
									//Display arrows
									if ($_GET['order']=='date_stock'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<th <?php if ($_GET['order']=='state') echo 'class="active"'; ?> >
							<center>
								<a title="État" href="<?php echo $url; ?>&amp;order=state&amp;way=<?php echo $arrow_way; ?>">
								<i class="icon-adjust"></i><br />
								État
								<?php
								//Display arrows
								if ($_GET['order']=='state'){
									if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
									if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
								}
								?>
								</a>
							</center>
						</th>
						<th <?php if ($_GET['order']=='department') echo 'class="active"'; ?> >
							<center>
								<a title="Service"  href="<?php echo $url; ?>&amp;order=department&amp;way=<?php echo $arrow_way; ?>">
								<i class="icon-building"></i>
								Service
								<?php
								//Display arrows
								if ($_GET['order']=='department'){
									if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
									if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
								}
								?>
								</a>
							</center>
						</th>
						<th <?php if ($_GET['order']=='action') echo 'class="active"'; ?> >
							<center>
								<a title="Criticité"  href="<?php echo $url; ?>&amp;order=action&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-play"></i>
									Actions
									<?php
									//Display arrows
									if ($_GET['order']=='action'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
									if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
					</tr>
					<?php // *********************************** FILTER LIGN ************************************** ?>
					<form name="filter" method="POST">
						<tr>
							<td>
								<center>
									<input name="ticket" onchange="submit();" type="text" size="7" value="<?php if ($_POST['ticket']!='%')echo $_POST['ticket']; ?>" />
								</center>
							</td>			
							<?php
								//Display tech column if all demands view is selected
								if ($_SESSION['profile_id']!=0 || $_SESSION['profile_id']!=4 || $_GET['userid']=='%')
								{
									echo '
									<td align="center" >
										<select style="width:81px" name="technician" onchange="submit()" >
											<option value="%"></option>';
											//tech
											$query = mysql_query("SELECT * FROM tusers WHERE (profile='0' or profile='4') and disable='0' ORDER BY lastname");
											while ($row=mysql_fetch_array($query)) 
											{
												$cutfname=substr($row['firstname'], 0, 1);
												if ($_POST['technician']==$row['id']) echo "<option selected value=\"$row[id]\">$cutfname. $row[lastname]</option>"; else echo "<option value=\"$row[id]\">$cutfname. $row[lastname]</option>";
											} 
											//tech group
											$query = mysql_query("SELECT * FROM tgroups WHERE disable='0' AND type='1' ORDER BY name");
											while ($row=mysql_fetch_array($query)) 
											{
												if ($t_group==$row['id'] || $_GET['t_group']==$row['id']) echo "<option selected value=\"G_$row[id]\">$row[name]</option>"; else echo "<option value=\"G_$row[id]\">$row[name]</option>";
											} 
										echo "
										</select>
									</td>";
								} 
								if (($_SESSION['profile_id']==0 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==4) || ($rright['side_all']!=0 && ($_GET['userid']=='%'|| $keywords!=''))) 
								{
									echo '
									<td align="center" >
										<select style="width:92px" name="user" onchange="submit()">
											<option value="%"></option>';
											//user list
											$query = mysql_query("SELECT * FROM tusers WHERE disable='0' ORDER BY lastname");
											while ($row=mysql_fetch_array($query)) 
											{
												$cutfname=substr($row['firstname'], 0, 1);
												if ($_POST['user']==$row['id']) echo "<option selected value=\"$row[id]\">$cutfname. $row[lastname]</option>"; else echo "<option value=\"$row[id]\">$row[lastname] $cutfname. </option>";
											} 
											//user group list
											$query = mysql_query("SELECT * FROM tgroups WHERE disable='0' AND type='0' ORDER BY name");
											while ($row=mysql_fetch_array($query)) 
											{
												if ($u_group==$row['id'] || $_GET['u_group']==$row['id']) echo "<option selected value=\"G_$row[id]\">$row[name]</option>"; else echo "<option value=\"G_$row[id]\">[G] $row[name]</option>";
											} 
											echo '
										</select>
									</td>';
								}
							?>
							<td align="center">
								<select style="width:65px" name="category" onchange="submit()" >
									<option value="%"></option>
									<?php
									$query = mysql_query("SELECT * FROM tcategory ORDER BY name");
									while ($row=mysql_fetch_array($query)) 
									{
										if ($_POST['category']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
									} 
									?>
								</select>	
							</td>
							<td align="center">
								<select style="width:60px" name="subcat" onchange="submit()">
									<option value="%"></option>
									<?php
									if($_POST['category']!='%')
									{$query = mysql_query("SELECT * FROM tsubcat WHERE cat LIKE $_POST[category] ORDER BY name");}
									else
									{$query = mysql_query("SELECT * FROM tsubcat ORDER BY name");}
									while ($row=mysql_fetch_array($query))
									{
										if ($_POST['subcat']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
									} 
									?>
								</select>
							</td>
							<td>
								<input name="title" size="24" onchange="submit();" type="text"  value="<?php if ($_POST['title']!='%')echo $_POST['title']; ?>" />
							</td>
							<td>
								<input name="date" size="4" onchange="submit();" type="text"  value="<?php if ($_POST['date']!='%')echo $_POST['date']; ?>" />
							</td>
							<td align="center">
								<select style="width:50px" id="fstate" name="fstate" onchange="submit()" >	
									<option value=""></option>
									<?php
									$query = mysql_query("SELECT * FROM tstates ORDER BY name");
									while ($row=mysql_fetch_array($query))  {echo "<option value=\"$row[id]\">$row[name]</option>";} 
									?>
								</select>
							</td>
							<td align="center">
								<select style="width:45px" id="priority" name="priority" onchange="submit()">
									<option value=""></option>
									<?php
									$query = mysql_query("SELECT * FROM tpriority ORDER BY number");
									while ($row=mysql_fetch_array($query)){echo "<option value=\"$row[number]\">$row[name]</option>";} 
									?>
								</select>
							</td>
							<td align="center">
								<select style="width:50px" id="criticality" name="criticality" onchange="submit()">
									<option value=""></option>
									<?php
									$query = mysql_query("SELECT * FROM tcriticality ORDER BY number");
									while ($row=mysql_fetch_array($query))
									{
									echo "<option value=\"$row[id]\">$row[name]</option>";
									} 
									?>
								</select>
							</td>
						</tr>
						<input name="state" type="hidden" value="<?php echo $_GET['state']; ?>" />
						<input name="filter" type="hidden" value="on" />
					</form>
				</thead>
				<tbody>
				<form name="actionlist" method="POST">
					<?php
						
						while ($row=mysql_fetch_array($masterquery))
						{ 
							//get type name
							$querytype=mysql_query("SELECT * FROM tassets_type WHERE id=$row[type]"); 
							$rtype=mysql_fetch_array($querytype);
							
							//get model name
							$querymodel=mysql_query("SELECT * FROM tassets_model WHERE id=$row[model]"); 
							$rmodel=mysql_fetch_array($querymodel);

							//get state name
							$querystate=mysql_query("SELECT * FROM tassets_states WHERE id LIKE $row[state]"); 
							$rstate=mysql_fetch_array($querystate);
							
							//get department name
							$querydepartment=mysql_query("SELECT * FROM tservices WHERE id LIKE $row[department]"); 
							$rdepartment=mysql_fetch_array($querydepartment);
							
							//Select name of user
							$queryuser=mysql_query("SELECT * FROM tusers WHERE id LIKE '$row[user]'"); 
							$resultuser=mysql_fetch_array($queryuser);
							
							//cut first letter of firstame
							$Fname=substr($resultuser['firstname'], 0, 1);

							$rowdate= date_cnv($row['date_stock']);
							
							//date hope
							$img='';
							if(!isset($row['date_hope'])) $row['date_hope']= ''; 
							$date_hope=$row['date_hope'];
							$querydiff=mysql_query("SELECT DATEDIFF(NOW(), '$date_hope') "); 
							$resultdiff=mysql_fetch_array($querydiff);
							if ($resultdiff[0]>0 && ($row['state']=='1'|| $row['state']=='2')) $img = '<i title="'.$resultdiff[0].' jours de retard" class="icon-time"></i>';
							
							//if title is too long cut
							$title=$row['description']; 

							echo "
								<tr>
									<td>
										<center>
										&nbsp<a href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\"><span title=\"\" class=\"label\">$row[id]</span></a>
										$img
										</center>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\" >
										<center>
											<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">
												$row[ip]
											</a>
										</center>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\"><a class=\"td\" title=\"Tel: $resultuser[phone] \" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">
										$row[netbios]
										</a>
									</td>

									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]'\">
										<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">
											$rtype[name]
										</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">
											$rmodel[name]
										</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a class=\"td\" title=\" \" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">
											$row[description]
										</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">
											$rowdate
										</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\"> 
											$rstate[name]
										</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a title=\"Priorité \" class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\" > 
											$rdepartment[name]
										</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a title=\"Criticité \" class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\" > <center><i title=\"\" class=\"icon-bullhorn bigger-130\" style=\"color\" ></i></a></center>
									</td>
								</tr>
							";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
	<?php
	//display multicheck options
	if ($rright['task_checkbox']!=0 && $resultcount[0]>0)
	{
		echo '
			&nbsp;&nbsp;&nbsp;	<i class="icon-level-down icon-rotate-180 icon-2x"></i>&nbsp&nbsp&nbsp
			<select title="Effectue des actions pour les tickets selectionnés dans la liste des tâches." name="selectrow" onchange="submit()">
				<option selected>Pour la selection:</option>';
			if ($rright['ticket_delete']!=0){
				echo '<option value="delete">Supprimer</option>';
			}
			echo '<option value="unattrib">Marquer comme Non attribué</option>
				<option value="pec">Marquer comme Attente de PEC</option>
				<option value="current">Marquer comme En cours</option>
				<option value="return">Marquer comme En attente de retour</option>
				<option value="resolv">Marquer comme Résolu</option>
			</select>
		';
	}
	echo "</form>"; //end form for task_checkbox
	
	//Multi-pages link
	if  ($resultcount[0]>$rparameters['maxline'])
	{
		//count number of page
		$pagenum=ceil($resultcount[0]/$rparameters['maxline']);
		echo '
		<center>
			<ul class="pagination">';
				for ($i = 1; $i <= $pagenum; $i++) {
					if ($i==1) $cursor=0;
					$selectcursor=$rparameters['maxline']*($i-1);
					if ($_GET['cursor']==$selectcursor)
					{
						$active='class="active"';
					} else	$active='';
					if($_GET['searchengine']==1)
					{echo "<li > <a href=\"$url&amp;order=$_GET[order]&amp;way=$_GET[way]&amp;cursor=$cursor\">&nbsp;$i&nbsp;</a></li> ";}
					else
					{echo "<li $active><a href=\"$url&amp;order=$_GET[order]&amp;way=$_GET[way]&amp;cursor=$cursor\">&nbsp;$i&nbsp;</a></li> ";}
					$cursor=$i*$rparameters['maxline'];
				}
				echo '
			</ul>
		</center>
	';
	}
	
//////////////////////////////////////functions
//date conversion
function date_cnv ($date) 
{return substr($date,8,2) . "/" . substr($date,5,2) . "/" . substr($date,0,4);}

//play notify sound for tech and admin in new ticket case
if ($rparameters['notify']==1 && ($_SESSION['profile_id']==4 || $_SESSION['profile_id']==0))
{
	$query=mysql_query("SELECT id FROM `tincidents` WHERE technician='0' and t_group='0' and techread='0' and disable='0' and notify='0'");
	$row=mysql_fetch_array($query);
	if ($row[0]!=0) {
		echo'<audio hidden="false" autoplay="true" src="./sounds/notify.ogg" controls="controls"></audio>';
		$query = "UPDATE tincidents SET notify='1' WHERE id='$row[0]'";
		$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
		
	}
}
?>