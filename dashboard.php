<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
################################################################################
# @Name : dashboard.php 
# @Desc : Display tickets list
# @Author : Flox
# @Create : 17/07/2009
# @Update : 19/06/2015
# @Version : 3.0.11
################################################################################

//initialize variables 
if(!isset($asc)) $asc = ''; 
if(!isset($img)) $img= ''; 
if(!isset($date)) $date= '';  
if(!isset($from)) $from=''; 
if(!isset($filter)) $filter=''; 
if(!isset($col)) $col=''; 
if(!isset($view)) $view=''; 
if(!isset($nkeyword)) $nkeyword=''; 
if(!isset($rowlastname)) $rowlastname=''; 
if(!isset($resultcriticality['color'])) $resultcriticality['color']= ''; 
if(!isset($displayusername)) $displayusername= ''; 
if(!isset($displaytechname)) $displaytechname= ''; 
if(!isset($u_group)) $u_group= ''; 
if(!isset($t_group)) $t_group= ''; 
if(!isset($techread)) $techread= '';  

if(!isset($_GET['technician'])) $_GET['technician']= ''; 
if(!isset($_GET['u_group'])) $_GET['u_group']= ''; 
if(!isset($_GET['t_group'])) $_GET['t_group']= ''; 
if(!isset($_GET['category'])) $_GET['category']= ''; 
if(!isset($_GET['subcat'])) $_GET['subcat']= ''; 
if(!isset($_GET['lieu'])) $_GET['lieu']= ''; 
if(!isset($_GET['cursor'])) $_GET['cursor']= ''; 
if(!isset($_GET['searchengine'])) $_GET['searchengine'] = ''; 
if(!isset($_GET['date_create'])) $_GET['date_create'] = ''; 
if(!isset($_GET['user'])) $_GET['user'] = ''; 
if(!isset($_GET['date'])) $_GET['date'] = ''; 
if(!isset($_GET['state'])) $_GET['state'] = ''; 
if(!isset($_GET['priority'])) $_GET['priority'] = ''; 
if(!isset($_GET['title'])) $_GET['title'] = ''; 
if(!isset($_GET['criticality'])) $_GET['criticality'] = ''; 
if(!isset($_GET['way'])) $_GET['way'] = ''; 
if(!isset($_GET['order'])) $_GET['order'] = ''; 
if(!isset($_GET['techread'])) $_GET['techread'] = ''; 

//get value is for filter case
if(!isset($_POST['date'])) $_POST['date']= '';
if(!isset($_POST['selectrow'])) $_POST['selectrow']= '';
if(!isset($_POST['technician'])) $_POST['technician']= $_GET['technician'];
if(!isset($_POST['title'])) $_POST['title']= $_GET['title'];
if(!isset($_POST['ticket'])) $_POST['ticket']= '';
if(!isset($_POST['userid'])) $_POST['userid']= '';	
if(!isset($_POST['user'])) $_POST['user']= $_GET['user'];
if(!isset($_POST['category'])) $_POST['category']= $_GET['category'];
if(!isset($_POST['subcat'])) $_POST['subcat']= $_GET['subcat'];
if(!isset($_POST['lieu'])) $_POST['lieu']= $_GET['lieu'];
if(!isset($_POST['fstate']) || $_GET['state']!='') $_POST['fstate']= $_GET['state'];
if(!isset($_POST['priority'])) $_POST['priority']=$_GET['priority'];
if(!isset($_POST['criticality'])) $_POST['criticality']=$_GET['criticality']; 
if(!isset($_POST['u_group'])) $_POST['u_group']=$_GET['u_group']; 
if(!isset($_POST['t_group'])) $_POST['t_group']=$_GET['t_group']; 

//escape special char to sql query
$_POST['title']=mysql_real_escape_string($_POST['title']);

//default values
if ($techread=='') $techread='%';
if ($state=='')$state='%';
if($_GET['category']=='') $_GET['category']= '%'; 
if($_GET['t_group']=='') $_GET['t_group']= '%'; 
if($_GET['u_group']=='') $_GET['u_group']= '%'; 
if($_GET['subcat']=='') $_GET['subcat']= '%';
if($_GET['lieu']=='') $_GET['lieu']= '%';
if($_GET['cursor']=='') $_GET['cursor']='0'; 
if($_GET['techread']=='') $_GET['techread']='%';
if($_POST['criticality']=='') $_POST['criticality']= '%'; 
if($_POST['priority']=='') $_POST['priority']='%';
if($_POST['fstate']=='' ) {$_POST['fstate']='%'; }

//default values check user profil parameters

//if admin user
if($_SESSION['profile_id']==0 || $_SESSION['profile_id']==4)
{
	if($_POST['technician']=='') $_POST['technician']= $_GET['userid'];
	if($_POST['user']=='') $_POST['user']= '%'; 	
} else {
	if($_POST['user']=='') $_POST['user']= $_GET['userid'];
	if($_POST['technician']=='') $_POST['technician']= '%';
}

//convert post date to sql query
if($_POST['date']=='') 
{
	if ($_GET['date_create']=='current') 
	{
		$_POST['date']=date("Y-m-d") ;
	} else {
		$_POST['date']= '%'; 
	}
}


if($_POST['title']=='') $_POST['title']= '%'; 
if($_POST['ticket']=='') $_POST['ticket']= '%'; 
if($_POST['userid']=='') $_POST['userid']= '%'; 
if($_POST['category']=='') $_POST['category']= '%'; 
if($_POST['subcat']=='') $_POST['subcat']= '%';
if($_POST['lieu']=='') $_POST['lieu']= '%';


//tech and techgroup separate
if(substr($_POST['technician'], 0, 1) =='G') 
{
 	$t_group = explode("_", $_POST['technician']);
	$t_group=$t_group[1];
	$_GET['t_group']=$t_group;
	$_POST['technician']='%';
}
//user and usergroup separate
if(substr($_POST['user'], 0, 1) =='G') 
{
 	$u_group = explode("_", $_POST['user']);
	$u_group=$u_group[1];
	$_GET['u_group']=$u_group;
	$_POST['user']='%';
}

//select order 
if ($filter=='on' || $_GET['order']==''){
    if($ruser['dashboard_ticket_order']) {$_GET['order']=$ruser['dashboard_ticket_order'];} else {$_GET['order']=$rparameters['order'];}
}
elseif ($_GET['order']=='') $_GET['order']='priority';

//meta state generation
if($_GET['state']=='meta')
{
    $state="AND	(tincidents.state LIKE 1 OR tincidents.state LIKE 2 OR tincidents.state LIKE 6)";  
    //change order in this case
    if ($_GET['order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create') {$_GET['order']='tincidents.priority, tincidents.criticality, tincidents.date_create';}
    if ($_GET['order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope') {$_GET['order']='tincidents.priority, tincidents.criticality, tincidents.date_hope';}
    if ($_GET['order']=='tstates.number, tincidents.date_hope, tincidents.priority, tincidents.criticality') {$_GET['order']='tincidents.date_hope, tincidents.priority, tincidents.criticality';}
    if ($_GET['order']=='tstates.number, tincidents.date_hope, tincidents.criticality, tincidents.priority') {$_GET['order']='tincidents.date_hope, tincidents.criticality, tincidents.priority';}
    if ($_GET['order']=='tstates.number, tincidents.criticality, tincidents.date_hope, tincidents.priority') {$_GET['order']='tincidents.criticality, tincidents.date_hope, tincidents.priority';}
} else {
    $state='AND	tincidents.state LIKE \''.$_POST['fstate'].'\'';
}

//echo $_GET['order'];
///// SQL QUERY
		//Date conversion for filter line
		if ($_POST['date']!='%')
		{
			$date=$_POST['date'];
			$find='/';
			$find= strpos($date, $find);
			if ($find!=false)
			{			
				$date=explode("/",$date);
				$_POST['date']="$date[2]-$date[1]-$date[0]";
			}
		}
		if ($keywords)
		{
			include "./searchengine.php";
		} else {
			$from="
			FROM tincidents, tstates ,tgroups_assoc,tusers";
			if($rparameters['ticket_places']==1){
				$from.= ", tplaces ";
			}
            /////// Changement d'affichage en fonction du role et de son groupe d'utilisateur

                    if($_SESSION['profile_id']==4 || $_SESSION['profile_id']==0){

                        $from.="
                        WHERE 
                        tincidents.state=tstates.id ";

                    }else {

                            $from.="
                         WHERE 
                        tincidents.state=tstates.id 
                        AND tincidents.u_group=tgroups_assoc.group 
                        AND tgroups_assoc.user=tusers.id  
                        AND tincidents.user=tgroups_assoc.user  ";
                    }
                    /////////////


			if($rparameters['ticket_places']==1){
				$from.= " AND tincidents.place=tplaces.id
				AND tplaces.id LIKE '$_POST[lieu]'";
			}


			$from.="
			AND	tincidents.user LIKE '$_POST[user]'
			AND	tincidents.u_group LIKE '$_GET[u_group]'
			AND	tincidents.technician LIKE '$_POST[technician]'
			AND	tincidents.t_group LIKE '$_GET[t_group]'
			AND	tincidents.techread LIKE '$_GET[techread]'
			AND	tincidents.disable='0'
			AND	(tincidents.category LIKE '$_POST[category]')
			AND	tincidents.subcat LIKE '$_POST[subcat]'
			AND	tincidents.id LIKE '$_POST[ticket]'
			AND	tincidents.user LIKE '$_POST[userid]'
			AND	tincidents.date_create LIKE '$_POST[date]%'
			$state
			AND	tincidents.priority LIKE '$_POST[priority]'
			AND	tincidents.criticality LIKE '$_POST[criticality]'
			AND	tincidents.title LIKE '%$_POST[title]%'
			";
		}
		$mastercount = mysql_query("SELECT COUNT(DISTINCT tincidents.id) $from") or die (mysql_error());
		$resultcount=mysql_fetch_array($mastercount);
		$addSelect ="";
		if($rparameters['ticket_places']==1){
//            $addSelect = ", tgroups_assoc.group";
		$addSelect = ", tplaces.name";
		}
		$masterquery = mysql_query("
		SELECT DISTINCT tincidents.* ".$addSelect."
		$from
		ORDER BY $_GET[order] $_GET[way]
		LIMIT $_GET[cursor],
		$rparameters[maxline]
		"); 
		
		if ($rparameters['debug']==1)
		{
			echo "
			SELECT DISTINCT tincidents.*<br />
			$from<br />
			ORDER BY $_GET[order] $_GET[way]<br />
			LIMIT $_GET[cursor],
			$rparameters[maxline]<br />
			";
		}
		
//checkbox selection
if($_POST['selectrow'])
{
	while ($row=mysql_fetch_array($masterquery))
	{
		//initialize variables 
		if(!isset($_POST['checkbox'.$row["id"]])) $_POST['checkbox'.$row["id"]] = ''; 
		if ($_POST['checkbox'.$row['id']]!='') 
		{
			//disable ticket
			if($_POST['selectrow']=="delete")
			{
				$query = "UPDATE tincidents SET disable='1' WHERE id LIKE '$row[id]'";
				$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
				echo '<div class="alert alert-block alert-success"><i class="icon-remove"></i> Ticket '.$row['id'].' supprimé.</div>';
			}
			//move ticket in unattrib state
			if($_POST['selectrow']=="unattrib")
			{
				$query = "UPDATE tincidents SET state='5' WHERE id LIKE '$row[id]'";
				$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());;
				echo '<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Ticket '.$row['id'].' marqué en non attribué.</div>';
			}
			//move ticket in pec state
			if($_POST['selectrow']=="pec")
			{
				$query = "UPDATE tincidents SET state='1' WHERE id LIKE '$row[id]'";
				$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
				echo '<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Ticket '.$row['id'].' marqué en attente de prise en charge.</div>';
			}
			//move ticket in current state
			if($_POST['selectrow']=="current")
			{
				$query = "UPDATE tincidents SET state='2' WHERE id LIKE '$row[id]'";
				$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
				echo '<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Ticket '.$row['id'].' marqué en cours.</div>';
			}
			//move ticket in return state
			if($_POST['selectrow']=="resolv")
			{
				$query = "UPDATE tincidents SET state='6' WHERE id LIKE '$row[id]'";
				$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
				echo '<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Ticket '.$row['id'].' marqué en attente de retour.</div>';
			}
			//move ticket in resolv state
			if($_POST['selectrow']=="cloturer")
			{
				$query = "UPDATE tincidents SET state='3' WHERE id LIKE '$row[id]'";
				$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
				echo '<div class="alert alert-block alert-success"><i class="icon-ok green"></i> Ticket '.$row['id'].' marqué en résolu.</div>';
			}
		}
	}
	
	//redirect
	echo "<SCRIPT LANGUAGE='JavaScript'>
				<!--
				function redirect()
				{
				window.location='./index.php?page=dashboard&state=$_GET[state]&userid=$_GET[userid]'
				}
				setTimeout('redirect()',$rparameters[time_display_msg]);
				-->
		</SCRIPT>";
}
?>

<div class="page-header position-relative">
	<h1>
		<?php
		if ($keywords)
		{
			echo '<i class="icon-search"></i> Recherche: '.$keywords.' ';
		} 
		else 
		{
		    //find state name for display in title
            $qstate = mysql_query("SELECT * FROM tstates WHERE id='$_GET[state]'");
            $rstate=mysql_fetch_array($qstate);
            if (!$rstate && !$_GET['viewid']) $rstate['description']='tickets non lus'; //case not read
            if ($_GET['state']=='meta') $rstate['description']='tickets à traiter'; //case not read
            //find view name to display in title
            if ($_GET['viewid']) 
            {
                $qview = mysql_query("SELECT name FROM tviews WHERE id='$_GET[viewid]'");
                $rview=mysql_fetch_array($qview);
                $rstate['description']='tickets de la vue '.$rview['name'].'';
            }
            
			if($_GET['userid']=='%')
			{
			    if ($_GET['state']=='%') {echo '<i class="icon-ticket"></i> Tous les tickets';} else {echo '<i class="icon-ticket"></i> Tous les '.$rstate['description'].'';}
			}
			else if ($_GET['userid']!='0')
			{
			    if ($_GET['state']=='%') {echo '<i class="icon-ticket"></i> Tous vos tickets';} else {echo '<i class="icon-ticket"></i> Vos '.$rstate['description'].'';}
			}
			if($_GET['state']=='%' && $_GET['userid']==0 && $_GET['userid']!='%') echo '<i class="icon-ticket"></i> Tous les tickets non attribués';; //case not read
			if($_GET['date_create']=='current') echo ' du jour'; //case for today link is selected
		}
		?>
		<small>
			<i class="icon-double-angle-right"></i>
			&nbsp;Nombre: <?php echo $resultcount[0]; ?></i>

		</small>
	</h1>
</div>
<?php
	//display message if search result is null
	if($resultcount[0]==0 && $keywords!="") echo '<div class="alert alert-danger"><i class="icon-remove"></i> Aucun ticket trouvé pour la recherche: <strong>'.$keywords.'</strong></div>';
?>
<div class="row">
	<div class="col-xs-12">
		<div class="table-responsive">
			<table id="sample-table-1" class="table  table-striped table-bordered table-hover">
				<?php 
				//*********************** FIRST LIGN *********************** 
				if($_GET['way']=='ASC') $arrow_way='DESC'; else $arrow_way='ASC';
				//build page url link
				$url="./index.php?page=dashboard&amp;
				userid=$_GET[userid]&amp;
				user=$_POST[user]&amp;
				u_group=$_GET[u_group]&amp;
				t_group=$_GET[t_group]&amp;
				technician=$_POST[technician]&amp;
				keywords=$keywords&amp;
				viewid=$_GET[viewid]&amp;
				title=$_POST[title]&amp;
				category=$_POST[category]&amp;
				subcat=$_POST[subcat]&amp;
				date=$_POST[date]&amp;
				state=$_POST[fstate]&amp;
				date_create=$_GET[date_create]&amp;
				priority=$_POST[priority]&amp;
				criticality=$_POST[criticality]
				";
				
				echo "
				<thead>
					<tr >
						<th "; if ($_GET['order']=='id') echo 'class="active"'; echo ">
							<center>
								<a title=\"Numéro du ticket\" href=\"$url&amp;order=id&amp;way=$arrow_way\">
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
						";
						// do not diplay TECH column if technician is connected
						if ($_SESSION['profile_id']!=0 || $_SESSION['profile_id']!=4 || $_GET['userid']=='%')
						{
						echo "
						<th ";  if ($_GET['order']=='technician') echo 'class="active"'; echo ">
							<center>
								<a title=\"Technicien en charge du ticket\"  href=\"$url&amp;order=technician&amp;way=$arrow_way\">
									<i class=\"icon-user\"></i><br />
									Technicien";
									//Display arrows
									if ($_GET['order']=='technician'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									echo"
								</a>
							</center>
						</th>
						";
						} 
						if (($_SESSION['profile_id']==0 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==4) || ($rright['side_all']!=0 && ($_GET['userid']=='%'|| $keywords!=''))) 
						{
						echo "
						<th "; if ($_GET['order']=='user') echo 'class="active  "'; echo ">
							<center>
								<a title=\"Demandeur\"  href=\"$url&amp;order=user&amp;way=$arrow_way\">
									<i class=\"icon-male\"></i><br />
									Demandeur";
									//Display arrows
									if ($_GET['order']=='user'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									echo"
								</a>
							</center>
						</th>
						";
						}
						?>
						<th <?php if ($_GET['order']=='category') echo 'class="active"'; ?> >
							<center>
								<a title="Catégorie"  href="<?php echo $url; ?>&amp;order=category&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-sign-blank "></i><br />
									Catégorie
									<?php
									//Display arrows
									if ($_GET['order']=='category'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<th <?php if ($_GET['order']=='subcat') echo 'class="active"'; ?> >
							<center>
								<a title="Sous-Catégorie"  href="<?php echo $url; ?>&amp;order=subcat&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-sitemap"></i>
									Sous Catégorie
									<?php
									//Display arrows
									if ($_GET['order']=='subcat'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<?php if($rparameters['ticket_places']==1){ ?>
						<th <?php if ($_GET['order']=='name') echo 'class="active"'; ?> >
							<center>
								<a title="Lieu"  href="<?php echo $url; ?>&amp;order=name&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-building"></i>
									Lieu
									<?php
									//Display arrows
									if ($_GET['order']=='name'){
										if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
										if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<?php } ?>
						<th <?php if ($_GET['order']=='description') echo 'class="active"'; ?> >
							<center>
								<a title="Titre de la demande"  href="<?php echo $url; ?>&amp;order=description&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-file-text-alt"></i>
									Titre
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
						<th <?php if ($_GET['order']==$rparameters['dash_date']) echo 'class="active"'; ?> >
						    <?php
						        if($rparameters['dash_date']=="date_create") {$date_title='Date de création du ticket';} 
						        if($rparameters['dash_date']=="date_hope") {$date_title='Date de résolution estimé';}
						    ?>
							<center>
								<a title="<?php echo $date_title; ?>"  href="<?php echo $url; ?>&amp;order=<?php echo $rparameters['dash_date']; ?>&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-calendar"></i><br />
									Date
									<?php
									//Display arrows
									if ($_GET['order']==$rparameters['dash_date']){
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
						<th <?php if ($_GET['order']=='priority') echo 'class="active"'; ?> >
							<center>
								<a title="Priorité 0=Urgent et 5=Très basse"  href="<?php echo $url; ?>&amp;order=priority&amp;way=<?php echo $arrow_way; ?>">
								<i class="icon-sort-by-attributes"></i>
								Priorité
								<?php
								//Display arrows
								if ($_GET['order']=='priority'){
									if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
									if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
								}
								?>
								</a>
							</center>
						</th>
						<th <?php if ($_GET['order']=='criticality') echo 'class="active"'; ?> >
							<center>
								<a title="Criticité"  href="<?php echo $url; ?>&amp;order=criticality&amp;way=<?php echo $arrow_way; ?>">
									<i class="icon-bullhorn"></i>
									Criticité
									<?php
									//Display arrows
									if ($_GET['order']=='criticality'){
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
												//$cutfname = substr($row['firstname'], 0, 1);
												$cutfname = $row['name'];
												$rower = $row[company];
												$queryx = mysql_query("
													SELECT *, tcompany.name AS tcname
													FROM tcompany
													INNER JOIN tusers ON tusers.company = tcompany.id
													WHERE tcompany.id='$rower'
												");
												$rowx=mysql_fetch_array($queryx);
												if ($_POST['user']==$row['id']) echo "<option selected value=\"$row[id]\">$cutfname $row[lastname]</option>"; else echo "<option value=\"$row[id]\">$row[lastname] $cutfname </option>";
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
							<?php if($rparameters['ticket_places']==1){ ?>
								<td align="center">
									<select style="width:65px" name="lieu" onchange="submit()" >
										<option value="%"></option>
										<?php
										$query = mysql_query("SELECT * FROM tplaces ORDER BY name");
										while ($row=mysql_fetch_array($query)) 
										{
											if ($_POST['lieu']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
										} 
										?>
									</select>	
								</td>
							<?php } ?>
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
							//Select name of states
							$querystate=mysql_query("SELECT * FROM tstates WHERE id LIKE $row[state]"); 
							$resultstate=mysql_fetch_array($querystate);
							//Select name of priority
							$querypriority=mysql_query("SELECT * FROM tpriority WHERE id LIKE $row[priority]"); 
							$resultpriority=mysql_fetch_array($querypriority);
							//Select name of criticality
							$querycriticality=mysql_query("SELECT * FROM tcriticality WHERE id LIKE $row[criticality]"); 
							$resultcriticality=mysql_fetch_array($querycriticality);
							//Select name of user
							$queryuser=mysql_query("SELECT * FROM tusers WHERE id LIKE '$row[user]'"); 
							$resultuser=mysql_fetch_array($queryuser);
							//Select name of usergroup
							$queryusergroup=mysql_query("SELECT * FROM tgroups WHERE id LIKE '$row[u_group]'"); 
							$resultusergroup=mysql_fetch_array($queryusergroup);
							//Select name of technician
							$querytech=mysql_query("SELECT * FROM tusers WHERE id LIKE '$row[technician]'"); 
							$resulttech=mysql_fetch_array($querytech);
							//Select name of techniciangroup
							$querytechgroup=mysql_query("SELECT * FROM tgroups WHERE id LIKE '$row[t_group]'"); 
							$resulttechgroup=mysql_fetch_array($querytechgroup);
							//Select name of category
							$querycat=mysql_query("SELECT * FROM tcategory WHERE id LIKE '$row[category]'"); 
							$resultcat=mysql_fetch_array($querycat);
							//Select name of subcategory
							$queryscat=mysql_query("SELECT * FROM tsubcat WHERE id LIKE '$row[subcat]'"); 
							$resultscat=mysql_fetch_array($queryscat);
							
							if($rparameters['ticket_places']==1) {$nameLieu = $row["name"];}

                            //cut first letter of firstame
							$Fname=substr($resultuser['firstname'], 0, 1);
							$Ftname=substr($resulttech['firstname'], 0, 1);
							
							//display username or groupname
							if ($resultusergroup[0]!=0) {$displayusername="$resultuser[lastname] / $resultusergroup[name]";

							} else {$displayusername="$Fname. $resultuser[lastname]" ;}

							if ($resulttechgroup[0]!=0) {$displaytechname="[G] $resulttechgroup[name]";

							} else {$displaytechname="$Ftname. $resulttech[lastname]" ;}
								
							$rowdate= date_cnv($row[$rparameters['dash_date']]);
							
							//date hope
							$img='';
							if(!isset($row['date_hope'])) $row['date_hope']= ''; 
							$date_hope=$row['date_hope'];
							$querydiff=mysql_query("SELECT DATEDIFF(NOW(), '$date_hope') "); 
							$resultdiff=mysql_fetch_array($querydiff);
							if ($resultdiff[0]>0) $img = '<i title="'.$resultdiff[0].' jours de retard" class="icon-time red"></i>';
							
							// Display line color
							$bgcolor="";
							
							/* too long to execute temp remove
							//query 30 days
							$query15=mysql_query("SELECT count(*) FROM `tincidents` WHERE TO_DAYS(NOW()) - TO_DAYS(date_create) >= $rparameters[lign_yellow] and TO_DAYS(NOW()) - TO_DAYS(date_create) <= 45 and (state LIKE '2' or state LIKE '1') and date_create LIKE '$row[date_create]'"); 
							$result15=mysql_fetch_array($query15);
							if ($result15[0]!=0 && ($row['state'] == '1' || $row['state'] == '2')) {$bgcolor="label-yellow"; $comment="Ticket crée il y à plus de $rparameters[lign_yellow] jours";}
										
							//query 45 days and more
							$query15=mysql_query("SELECT count(*) FROM `tincidents` WHERE TO_DAYS(NOW()) - TO_DAYS(date_create) > $rparameters[lign_orange] and (state LIKE '2' or state LIKE '1') and date_create LIKE '$row[date_create]'"); 
							$result15=mysql_fetch_array($query15);
							if ($result15[0]!=0 && ($row['state'] == '1' || $row['state'] == '2')) {$bgcolor="label-important"; $comment="Ticket crée il y à plus de $rparameters[lign_orange] jours";}
							*/
							
							//query date is today display green
							if (date('Y-m-d')==date('Y-m-d',strtotime($row['date_create']))) {$bgcolor="label-success"; $comment="Ticket crée ce jour";}
							
							//if techncian unread
							if ($row['techread']==0) {$bgcolor="label-info"; $comment="Ticket non lu par le technicien en charge";}
							
							// default bg color
							if ($bgcolor=="") {$bgcolor=""; $comment="";}
							
							//if title is too long cut
							$title=$row['title']; 
					
							//attach file
							$attach='';
							if(!isset($row['img1'])) $row['img1']= ''; 
								
							if($row['img1']!='') $attach= "<i title=\"$row[img1]\" class=\"icon-paper-clip\"></i> ";
							if($row['stateimpr']=='Oui') $attach= "<i title=\"Ticket Reçu\" class=\"icon-print\"></i> ";
							
							echo "
								<tr>
									<td>
										<center>
										";if ($rright['task_checkbox']!=0) echo "<input style=\"float:left;\" type=\"checkbox\"  name=\"checkbox$row[id]\" value=\"$row[id]\"/>"; echo"
										&nbsp<a href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\"><span title=\"$comment\" class=\"label $bgcolor\">$row[id]</span></a>
										$img
										</center>
									</td>
								 "; 
								 if ($_SESSION['profile_id']!=0 || $_SESSION['profile_id']!=4 || $_GET['userid']=='%') 
								 {
									echo "<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\" ><center><a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">$displaytechname</a></center></td>";
								 } 
								 if (($_SESSION['profile_id']==0 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==4) || ($rright['side_all']!=0 && ($_GET['userid']=='%'|| $keywords!='')))
								 {
									echo "<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\"><a class=\"td\" title=\"Tel: $resultuser[phone] \" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">$displayusername</a></td>";
								 }
								 echo "
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]'\">
										<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">$resultcat[name]</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">$resultscat[name]</a>
									</td>
									";
									if($rparameters['ticket_places']==1){ 
										echo "<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
											<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">$nameLieu</a>
										</td>";
									}
									echo "<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a class=\"td\" title=\"$row[title] \" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">$title $attach</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\">$rowdate</a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\"> <span class=\"$resultstate[display]\" title=\"$resultstate[description]\"> <font size=\"1\">$resultstate[name]</font></span></a>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<center><a title=\"Priorité $resultpriority[name]\" class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\" > <i title=\"$resultpriority[name]\" class=\"icon-warning-sign bigger-130\" style=\"color:$resultpriority[color]\"></i></a></center>
									</td>
									<td onclick=\"document.location='./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]'\">
										<a title=\"Criticité $resultcriticality[name]\" class=\"td\" href=\"./index.php?page=ticket&amp;id=$row[id]&amp;state=$_GET[state]&amp;userid=$_GET[userid]&amp;category=$_GET[category]&amp;subcat=$_GET[subcat]&amp;viewid=$_GET[viewid]\" > <center><i title=\"$resultcriticality[name]\" class=\"icon-bullhorn bigger-130\" style=\"color:$resultcriticality[color]\" ></i></a></center>
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
				<option value="resolv">Marquer comme Résolu</option>
				<option value="cloturer">Marquer comme Cloturer</option>
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
