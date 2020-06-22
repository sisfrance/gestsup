<?php
################################################################################
# @Name : ticket.php 
# @Desc : page to display: create and edit ticket
# @call : /dashboard.php
# @Author : Flox
# @Version : 3.0.11
# @Update : 16/03/2015
################################################################################

//initialize variables 
if(!isset($userreg)) $userreg = ''; 
if(!isset($category)) $category = ''; 
if(!isset($subcat)) $subcat = ''; 
if(!isset($title)) $title = ''; 
if(!isset($date_hope)) $date_hope = ''; 
if(!isset($date_create)) $date_create = ''; 
if(!isset($state)) $state = ''; 
if(!isset($description)) $description = ''; 
///////////////////////////////////////////////////
if(!isset($materiel)) $materiel = '';
if(!isset($serialnumber)) $serialnumber = '';
if(!isset($traveltime)) $traveltime = '';
if(!isset($warranty)) $warranty = ''; 
if(!isset($mntcontract)) $mntcontract = ''; 
///////////////////////////////////////////////////
if(!isset($resolution)) $resolution = ''; 
if(!isset($priority)) $priority = '';
if(!isset($percentage)) $percentage = '';
if(!isset($id)) $id = '';
if(!isset($id_in)) $id_in = '';
if(!isset($save)) $save = '';
if(!isset($techread)) $techread = '';
if(!isset($next)) $next = '';
if(!isset($previous)) $previous = '';
if(!isset($user)) $user = '';
if(!isset($down)) $down = '';
if(!isset($u_group)) $u_group = '';
if(!isset($t_group)) $t_group = '';
if(!isset($globalrow['time'])) $globalrow['time'] = '';
if(!isset($userid)) $userid = '';
if(!isset($u_service)) $u_service = '';
if(!isset($date_hope_error)) $date_hope_error = '';

if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['upload'])) $_POST['upload'] = '';
if(!isset($_POST['title'])) $_POST['title'] = '';
if(!isset($_POST['description'])) $_POST['description'] = '';
///////////////////////////////////////////////////////////////////
if(!isset($_POST['materiel'])) $_POST['materiel'] = '';
if(!isset($_POST['serialnumber'])) $_POST['serialnumber'] = '';
if(!isset($_POST['traveltime'])) $_POST['traveltime'] = '';
if(!isset($_POST['warranty'])) $_POST['warranty'] = '';
if(!isset($_POST['mntcontract'])) $_POST['mntcontract'] = '';
if(!isset($_POST['stateimpr'])) $_POST['stateimpr'] = '';
///////////////////////////////////////////////////////////////////
if(!isset($_POST['resolution'])) $_POST['resolution'] = '';
if(!isset($_POST['Submit'])) $_POST['Submit'] = '';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';
if(!isset($_POST['user'])) $_POST['user'] = '';
if(!isset($_POST['type'])) $_POST['type'] = '';
if(!isset($_POST['modify'])) $_POST['modify'] = '';
if(!isset($_POST['quit'])) $_POST['quit'] = '';
if(!isset($_POST['date_create'])) $_POST['date_create'] = '';
if(!isset($_POST['date_hope'])) $_POST['date_hope'] = '';
if(!isset($_POST['date_res'])) $_POST['date_res'] = '';
if(!isset($_POST['priority'])) $_POST['priority'] = '';
if(!isset($_POST['criticality'])) $_POST['criticality'] = '';
if(!isset($_POST['category'])) $_POST['category'] = '';
if(!isset($_POST['time'])) $_POST['time'] = '';
if(!isset($_POST['time_hope'])) $_POST['time_hope'] = '';
if(!isset($_POST['state'])) $_POST['state'] = '';
if(!isset($_POST['cancel'])) $_POST['cancel'] = '';
if(!isset($_POST['technician'])) $_POST['technician'] = '';
if(!isset($_POST['ticket_places'])) $_POST['ticket_places'] = '';
if(!isset($_POST['text2'])) $_POST['text2'] = '';
if(!isset($_POST['start_availability_d'])) $_POST['start_availability_d'] = '';
if(!isset($_POST['end_availability_d'])) $_POST['end_availability_d'] = '';

if(!isset($_GET['id'])) $_GET['id'] = '';
if(!isset($_GET['action'])) $_GET['action'] = '';
if(!isset($_GET['threadedit'])) $_GET['threadedit'] = '';

if(!isset($globalrow['technician'])) $globalrow['technician'] = '';

//core ticket actions
include('./core/ticket.php');

//defaults values for new tickets
if(!isset($globalrow['creator'])) $globalrow['creator'] = '';
if(!isset($globalrow['t_group'])) $globalrow['t_group'] = '';
if(!isset($globalrow['t_group'])) $globalrow['t_group'] = '';
if(!isset($globalrow['u_group'])) $globalrow['u_group'] = '';  //// TEST
if(!isset($globalrow['category'])) $globalrow['category'] = '';
if(!isset($globalrow['subcat'])) $globalrow['subcat'] = '';
if(!isset($globalrow['title'])) $globalrow['title'] = '';
if(!isset($globalrow['description'])) $globalrow['description'] = '';
//////////////////////////////////////////////////////////////////////////////////
if(!isset($_POST['materiel'])) $_POST['materiel'] = '';
if(!isset($_POST['serialnumber'])) $_POST['serialnumber'] = '';
if(!isset($_POST['traveltime'])) $_POST['traveltime'] = '';
if(!isset($_POST['warranty'])) $_POST['warranty'] = 'Non';
if(!isset($_POST['mntcontract'])) $_POST['mntcontract'] = 'Non';
if(!isset($_POST['stateimpr'])) $_POST['stateimpr'] = 'Non';
//////////////////////////////////////////////////////////////////////////////////
if(!isset($globalrow['date_create'])) $globalrow['date_create'] = $datetime;
if(!isset($globalrow['date_hope'])) $globalrow['date_hope'] = '';
if(!isset($globalrow['date_res'])) $globalrow['date_res'] = '';
if(!isset($globalrow['time_hope'])) $globalrow['time_hope'] = '';
if(!isset($globalrow['time'])) $globalrow['time'] = '';
if(!isset($globalrow['priority'])) $globalrow['priority'] = ''; 
if(!isset($globalrow['criticality'])) $globalrow['criticality'] = '';
if(!isset($globalrow['state'])) $globalrow['state'] = '1';
if(!isset($globalrow['type'])) $globalrow['type'] = '1';
if(!isset($globalrow['start_availability'])) $globalrow['start_availability'] = date("Y-m-d").' '.date("H:i:s");
if(!isset($globalrow['end_availability'])) $globalrow['end_availability'] = date("Y-m-d").' '.date("H:i:s");
if(!isset($globalrow['availability_planned'])) $globalrow['availability_planned'] = 0;

//default values for tech and admin
if($_SESSION['profile_id']==4 || $_SESSION['profile_id']==0)
{
	if(!isset($globalrow['technician'])) $globalrow['technician'] = $_SESSION['user_id'];
	if(!isset($globalrow['user'])) $globalrow['user'] = '';
} else {
	if(!isset($globalrow['technician'])) $globalrow['technician'] = '';
	if(!isset($globalrow['user'])) $globalrow['user'] = $_SESSION['user_id'];
}
?>
<div id="row">
	<div class="col-xs-12">
		<div class="widget-box">
			<form class="form-horizontal" name="myform" id="myform" enctype="multipart/form-data" method="post" action="" onsubmit="loadVal();" >
				<div class="widget-header">
					<h4>
						<i class="icon-ticket"></i>
						<?php
    						//display widget title
    						if($_GET['action']=='new') echo 'Ouverture du ticket n° '.$_GET['id'].''; else echo 'Edition du ticket '.$_GET['id'].' '.$percentage.':  '.$title.'';
    						//display clock if alarm 
    						$query = mysql_query('SELECT * FROM tevents WHERE incident='.$_GET['id'].' and disable=0');
    						$alarm = mysql_fetch_array($query);
    						if($alarm) echo ' <i class="icon-bell-alt green" title="Alame activée le '.$alarm['date_start'].'" /></i>';
						?>
					</h4>
					<span class="widget-toolbar">
						<?php

                        if ($rright['ticket_next']!=0)
							{
								if($previous[0]!='') echo"<a href=\"./index.php?page=ticket&amp;id=$previous[0]&amp;state=$state&amp;userid=$userid\"><i title=\"Ticket précèdent dans cet état\" class=\"icon-circle-arrow-left bigger-130\"></i>&nbsp;"; 
								if($next[0]!='') echo"<a href=\"./index.php?page=ticket&amp;id=$next[0]&amp;state=$state&amp;userid=$userid \"><i title=\"Ticket suivant dans cet état\" class=\"icon-circle-arrow-right bigger-130\"></i></a>";
							}
							if ($rright['ticket_print']!=0)
							{
								echo "&nbsp;";
								echo '<a target="_blank" href="./ticket_print.php?id='.$_GET['id'].'"><i title="Imprimer ce ticket" class="icon-print green bigger-130"></i></a>';
								echo "&nbsp;";
								echo '<a target="_blank" href="./ticket_csv.php?id='.$_GET['id'].'"><i title="Mettre en tableau ce ticket" class="icon-print red bigger-130"></i></a>';
							}
							if ($rright['ticket_template']!=0 && $_GET['action']=='new')
							{
								echo "&nbsp;";
								echo '<a href="./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=template"><i title="Modèle de tickets" class="icon-tags pink bigger-130"></i></a>';
							}
							if ($rright['ticket_event']!=0)
							{
								echo "&nbsp;&nbsp;";
								echo'<i onclick="parent.location=\'./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=addevent&techncian='.$_SESSION['user_id'].'\'" title="Créer un rappel pour ce ticket" class="icon-bell-alt bigger-130 orange"></i>';
							}
							if (($rright['planning']!=0) && ($rparameters['planning']==1) && ($rright['ticket_calendar']!=0)) 
							{
								echo "&nbsp;&nbsp;";
								echo'<i onclick="parent.location=\'./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=addcalendar&techncian='.$_SESSION['user_id'].'\'" title="Créer un rappel pour ce ticket" class="icon-calendar bigger-130 purple"></i>';
							}
							if ($rright['ticket_delete']!=0 && $_GET['action']!='new')
							{
								echo "&nbsp;&nbsp;";
								echo '<a href="./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=delete"><i title="Supprimer ce ticket" class="icon-trash red bigger-130"></i></a>';
							}
							if ($rright['ticket_save']!=0)
							{
								echo "&nbsp;&nbsp;";
								echo '<button class="btn btn-minier btn-success" title="Sauvegarder" name="modify" value="Enregistrer" type="submit" id="modify"><i class="icon-save bigger-140"></i></button>';
                                echo "&nbsp;&nbsp;";
                                echo '<button class="btn btn-minier btn-purple" title="Sauvegarder et quitter" name="quit" value="Enregistrer" type="quit" id="modify"><i class="icon-save bigger-140"></i></button>';
							}
							?>
					</span>
				</div>
				<div class="widget-body">
					<div class="widget-main">
						<!-- START sender part -->	
						<div class="form-group <?php if(($rright['ticket_user_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_user_disp']==0 && $_GET['action']=='new')) echo 'hide';?>" >
							<label class="col-sm-2 control-label no-padding-right" for="user">
								<?php if (($_POST['user']==0) && ($globalrow['user']==0) && ($u_group =='')) echo '<i title="selectionner un demandeur." class="icon-warning-sign red bigger-130"></i>&nbsp;'; ?>
								Demandeur:
							</label>
							<div class="col-sm-9">
								<select id="user" name="user" onchange="loadVal(); submit();" <?php if(($rright['ticket_user']==0 && $_GET['action']!='new') || ($rright['ticket_new_user']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?> >
									<?php
									//diplay user list

//									$query = mysql_query("SELECT * FROM tgroups, tgroups_assoc,tusers WHERE tgroups.id=tgroups_assoc.group AND tgroups_assoc.user= tusers.id WHERE disable='0' ORDER BY lastname ASC, firstname ASC");
                                    $query = mysql_query("SELECT * FROM `tusers` WHERE disable='0' ORDER BY lastname ASC, firstname ASC");
									while ($row=mysql_fetch_array($query)) {
										$rower = $row[company];
										$queryx = mysql_query("
											SELECT *, tcompany.name AS tcname
											FROM tcompany
											INNER JOIN tusers ON tusers.company = tcompany.id
											WHERE tcompany.id='$rower'
										");
										$rowx=mysql_fetch_array($queryx);
										echo "<option value=\"$row[id]\">$row[lastname] $row[firstname]</option>";
									}
									///

                                    ///

									$query = mysql_query("SELECT * FROM `tgroups` WHERE disable='0' and type='0' ORDER BY name");
									while ($row=mysql_fetch_array($query)) echo "<option value=\"G_$row[id]\">[G] $row[name]</option>";

									//selection
									if (($globalrow['u_group']== '' && $u_group=='') || $_POST['user']!="")
									{
										if ($_POST['user']) {

										    $user=$_POST['user'];

										}	else {

										    $user=$globalrow['user'];}

										$query = mysql_query("SELECT * FROM tusers WHERE id LIKE $user");
										//

                                        //
										$row = mysql_fetch_array($query);
										$rower = $row[company];
										$queryx = mysql_query("
											SELECT *, tcompany.name AS tcname
											FROM tcompany
											INNER JOIN tusers ON tusers.company = tcompany.id
											WHERE tcompany.id='$rower'
										");
										$rowx=mysql_fetch_array($queryx);
										echo "<option selected value=\"$user\">$row[lastname] $row[firstname]</option>";
									} else {
										if (($globalrow['u_group']!=$u_group) && $u_group != ''){

										    $group=$u_group;
										}else {

										    $group=$globalrow['u_group'];}
										$query = mysql_query("SELECT * FROM `tgroups` WHERE id=$group");
										$row = mysql_fetch_array($query);
										echo "<option selected value=\"G_$u_group\">[G] $row[name]</option>";
									}
									?>
								</select>
								<?php if(($rright['ticket_user']==0 && $_GET['action']!='new') || ($rright['ticket_new_user']==0 && $_GET['action']=='new')) echo ' <input type="hidden" name="user" value='.$globalrow['user'].' /> '; //send data in disabled case?>
								
								<!-- START sender actions part -->
								<?php
								if ($rright['ticket_user_actions']!=0)
								{
								    echo'<input type="hidden" name="action" value="">';
								    echo'<input type="hidden" name="edituser" value="">';
									echo '&nbsp;&nbsp;<i class="icon-plus-sign green bigger-130" title="Ajouter un utilisateur" onclick="loadVal(); document.forms[\'myform\'].action.value=\'adduser\';document.forms[\'myform\'].submit();"></i>&nbsp;&nbsp;';
									if ($u_group!=0)
									{
									    echo "<i class=\"icon-pencil orange bigger-130\" title=\"Modifier le groupe\" value=\"useredit\" onClick=\"parent.location='./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]&action=edituser&edituser=$u_group'\" \"  /></i>&nbsp;&nbsp;";
									}
									else
									{
										if ($_POST['user']) $selecteduser=$_POST['user']; else $selecteduser=$globalrow['user'];
						                echo '<i class="icon-pencil orange bigger-130" title="Modifier un utilisateur" onclick="loadVal(); document.forms[\'myform\'].action.value=\'edituser\';document.forms[\'myform\'].edituser.value=\''.$selecteduser.'\';document.forms[\'myform\'].submit();"></i>&nbsp;&nbsp;';
									}
								}	
								?>
								<!-- END sender actions part -->
								<!-- START user info part -->
									<?php
									//Display tel fax departement if exist
									if ($u_group=='')
									{
										if ($_POST['user']) 
										{
											$query = mysql_query("SELECT * FROM `tusers` WHERE id LIKE '$_POST[user]'"); 
										}
										else
										{
											$query = mysql_query("SELECT * FROM `tusers` WHERE id LIKE '$globalrow[user]'"); 
										}
										
										$row=mysql_fetch_array($query);
										echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										if ($row['phone']!="") echo "&nbsp;&nbsp;&nbsp;<i title=\"Téléphone\" class=\"icon-phone-sign blue bigger-130\"></i> <b>$row[phone]</b>";
										
										if ($row['mail']!="") echo "&nbsp;&nbsp;&nbsp;<a href=\"mailto:$row[mail]\"><i title=\"$row[mail]\" class=\"icon-envelope blue bigger-130\"></i></a>";
										if ($row['function']!="") echo "&nbsp;&nbsp;&nbsp;<i title=\"Fonction\" class=\"icon-user blue bigger-130\"></i> $row[function]";
										if ($row['service']!=0) 
										{
											$q=mysql_query("SELECT name FROM tservices WHERE id='$row[service]'"); 
											$g_service_name=mysql_fetch_array($q);
											echo "&nbsp;&nbsp;&nbsp;<i title=\"Service\" class=\"icon-group blue bigger-130\"></i> $g_service_name[0]";
										}
//										// TEST
//										if ($row['u_group']!= 0){
//
//                                            $ug=mysql_query("SELECT * FROM tgroups_assoc,tusers WHERE tgroups_assoc.user= tusers.id");
//                                            $g_u_group_name=mysql_fetch_array($ug);
//                                            echo "&nbsp;&nbsp;&nbsp;<i title=\"Société: $g_u_group_name[user] \" class=\"icon-building blue bigger-130\"></i> $g_u_group_name[user]";
//
//                                        } else {
//
//                                        }
//										///////////


                                        //
										if ($row['company']!=0) 
										{
											$q=mysql_query("SELECT * FROM tcompany WHERE id='$row[company]'");
											$g_company_name=mysql_fetch_array($q);
											echo "&nbsp;&nbsp;&nbsp;<i title=\"Société: $g_company_name[name] $g_company_name[address] $g_company_name[zip] $g_company_name[city]\" class=\"icon-building blue bigger-130\"></i> $g_company_name[name]";
										}
									}
									//other demands for this user or group
									if ($u_group)
									{
										$umodif=$u_group;
										$usergroup="u_group";
									} else {
										if($_POST['user']) $umodif=$_POST['user']; else $umodif=$globalrow['user'];
										$usergroup="user";
									}
									if ($umodif!='') //case for new ticket without sender
									{
										$qn = mysql_query("SELECT count(*) FROM `tincidents` WHERE $usergroup LIKE '$umodif' and (state='1' OR state='2') and id NOT LIKE $_GET[id] and disable=0"); 
										while ($rn=mysql_fetch_array($qn))
										$rnn=$rn[0];
										if ($rnn!=0) echo "&nbsp;&nbsp; <i title=\"Autres tickets de cet utilisateur\" class=\"icon-ticket blue bigger-130\"></i> ";
										$c=0;
										$q = mysql_query("SELECT * FROM `tincidents` WHERE $usergroup LIKE '$umodif' and (state='1' OR state='2') and id NOT LIKE $_GET[id] and disable=0 ORDER BY id DESC"); 
										while (($r=mysql_fetch_array($q)) && ($c<2)) {	
											$c=$c+1;
											echo "<a title=\"$r[title]\" href=\"./index.php?page=ticket&amp;id=$r[id]\">#$r[id]</a>";
											if ($c<$rnn) echo ", ";
											if ($c==2) echo "...";
										}  
										if ($rnn!=0) echo "";
									}
									?>
								<!-- START user info part -->
							</div>
						</div>
						<!-- END sender part -->
				        <!-- START type part -->
				        <?php
				            if($rparameters['ticket_type']=='1')
				            {
				                echo'
				                <div class="form-group '; if(($rright['ticket_type_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_type_disp']==0 && $_GET['action']=='new')) {echo 'hide';} echo'">
        							<label class="col-sm-2 control-label no-padding-right" for="type">
        							    ';if (($_POST['type']==0) && ($globalrow['type']==0)) {echo '<i title="Selectionner un type" class="icon-warning-sign red bigger-130"></i>&nbsp;';} echo'
        							    Type:
        							</label>
        							<div class="col-sm-8">
        							    <select  id="type" name="type"'; if(($rright['ticket_type']==0 && $_GET['action']!='new') || ($rright['ticket_new_type']==0 && $_GET['action']=='new')) {echo 'onfocus="this.blur();"';} echo'>';
        									if ($_POST['type'])
        									{
        										$query = mysql_query("SELECT * FROM `ttypes` WHERE id='$_POST[type]'");
        										$row=mysql_fetch_array($query);
        										echo "<option value=\"$_POST[type]\" selected >$row[name]</option>";
        										$query = mysql_query("SELECT * FROM `ttypes` WHERE id!='$_POST[type]'");
        							    		while ($row=mysql_fetch_array($query)) echo "<option value=\"$row[id]\">$row[name]</option>";
        									}
        									else
        									{
        										$query = mysql_query("SELECT * FROM `ttypes` WHERE id='$globalrow[type]' ORDER BY id");
        										$row=mysql_fetch_array($query);
        										echo "<option value=\"$globalrow[type]\" selected >$row[name]</option>";
        										$query = mysql_query("SELECT * FROM `ttypes` WHERE id!='$globalrow[type]'");
        								    	while ($row=mysql_fetch_array($query)) echo "<option value=\"$row[id]\">$row[name]</option>";
        									}
        									echo'			
        								</select>
        							</div>
    					    	</div>
    					    	';
				            }
				        ?>
					    <!-- END type part -->	
						<!-- START tech part -->
						<div class="form-group <?php if(($rright['ticket_tech_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_tech_disp']==0 && $_GET['action']=='new')) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="technician">
							<?php if($globalrow['technician']==0 && $globalrow['t_group']==0 && $_POST['technician']==0 ) echo '<i title="Aucun technicien associé à ce ticket." class="icon-warning-sign red bigger-130"></i>&nbsp;'; ?>
							Technicien:
							</label>
							<div class="col-sm-8 ">
								<select  id="technician" name="technician" onchange="loadVal(); submit();" <?php if($rright['ticket_tech']==0) echo ' disabled="disabled" ';?> >
									<?php
									//tech user
									$query = mysql_query("SELECT * FROM `tusers` WHERE disable='0' and (profile='0' || profile='4') and id!='$globalrow[technician]' ORDER BY lastname ASC, firstname ASC") ;
									while ($row=mysql_fetch_array($query)) {echo "<option value=\"$row[id]\">$row[lastname] $row[firstname] </option>";} 
									//tech group
									$query = mysql_query("SELECT * FROM `tgroups` WHERE disable='0' and type='1' ORDER BY name");
									while ($row=mysql_fetch_array($query)) echo "<option value=\"G_$row[id]\">[G] $row[name]</option>";
									//selected value
									if ($t_group)
									{
										$query = mysql_query("SELECT * FROM `tgroups` WHERE id=$t_group");
										$row = mysql_fetch_array($query);
										echo "<option selected value=\"G_$t_group\">[G] $row[name]</option>";
									} else {
										if ($_POST['technician'])
										{
											$querytech = mysql_query("SELECT * FROM `tusers` WHERE disable='0' and id LIKE '$_POST[technician]' ");
										} else {
											$querytech = mysql_query("SELECT * FROM `tusers` WHERE disable='0' and id LIKE '$globalrow[technician]' ");		
										}
										$row=mysql_fetch_array($querytech);
										echo "<option value=\"$row[id]\" selected >$row[lastname] $row[firstname]</option>";
									}
									if ($_POST['technician']=='0') echo "<option value=\"0\" selected >Aucun</option>"; else echo "<option value=\"0\" >Aucun</option>";
									?>
								</select>
								<?php if($rright['ticket_tech']==0) echo '<input type="hidden" name="technician" value="'.$globalrow['technician'].'" />'; //send data in disabled case?>
								<?php
								//display open user
								if (($globalrow['creator']!=$globalrow['technician']) && ($globalrow['creator']!="0") && $_GET['action']!='new')
								{
									//select creator name
									$query = mysql_query("SELECT * FROM `tusers` WHERE id LIKE '$globalrow[creator]'");
									$row=mysql_fetch_array($query);
									echo "&nbsp;<i class=\"icon-user blue bigger-130\"></i>&nbsp;Ouvert par $row[firstname] $row[lastname]";
								}
								?>
							</div>
						</div>
						<!-- END tech part -->
						<!-- START cat part -->
						<div class="form-group <?php if(($rright['ticket_cat_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat_disp']==0 && $_GET['action']=='new')) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="category">
								<?php if(($globalrow['category']==0 || $globalrow['subcat']==0) && ($_POST['category']==0 || $_POST['subcat']==0)) echo '<i title="Aucune catégorie associé." class="icon-warning-sign red bigger-130"></i>&nbsp;'; ?>
								Catégorie:
							</label>
							<div class="col-sm-8">
								<select id="category" name="category" onchange="loadVal(); submit();" <?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?>>
								<?php
									$query= mysql_query("SELECT * FROM `tcategory` order by name ");
									while ($row=mysql_fetch_array($query)) 
									{
										if ($_POST['category'])
										{
											if ($_POST['category']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
										}
										else
										{
											if ($globalrow['category']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
										}
									}
									if ($globalrow['category']==0 && $_POST['category']==0) echo "<option value=\"\" selected></option>";
								?>
								</select>
								<?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new'))  echo '<input type="hidden" name="category" value="'.$globalrow['category'].'" />'; //send data in disabled case?>
								<select  id="subcat" name="subcat" onchange="loadVal(); submit();" <?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?> >
								<?php
									if ($_POST['category'])
									{$query= mysql_query("SELECT * FROM `tsubcat` WHERE cat LIKE '$_POST[category]' order by name ASC");}
									else
									{$query= mysql_query("SELECT * FROM `tsubcat` WHERE cat LIKE '$globalrow[category]' order by name ASC");}
									
									while ($row=mysql_fetch_array($query)) 
									{
										if ($_POST['subcat'])
										{
											if ($_POST['subcat']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
										}
										else
										{
											if ($globalrow['subcat']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
										}
									} 
									if ($globalrow['subcat']==0 && $_POST['subcat']==0) echo "<option value=\"\" selected></option>";
								?>
								</select>
								<?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new'))  echo '<input type="hidden" name="subcat" value="'.$globalrow['subcat'].'" />'; //send data in disabled case?>
								<?php
								if ($rright['ticket_cat_actions']!=0)
								{
									echo '
									&nbsp;&nbsp;<i class="icon-plus-sign green bigger-130" title="Ajouter une categorie" onclick="loadVal(); document.forms[\'myform\'].action.value=\'addcat\';document.forms[\'myform\'].submit();"></i>&nbsp;&nbsp;
									&nbsp;&nbsp;<i class="icon-pencil orange bigger-130" title="Modifier une categorie" onclick="loadVal(); document.forms[\'myform\'].action.value=\'editcat\';document.forms[\'myform\'].submit();"></i>&nbsp;&nbsp;
									';
								}
								?>
							</div>
						</div>
						<!-- END cat part -->
						<!-- START place part if parameter is on -->
						<?php
						if($rparameters['ticket_places']==1)
						{
							echo "
							<div class=\"form-group\">
								<label class=\"col-sm-2 control-label no-padding-right\" for=\"ticket_places\">Lieu:</label>
								<div class=\"col-sm-8\">
									<select class=\"textfield\" id=\"ticket_places\" name=\"ticket_places\" > 
										 ";
										if($_POST['ticket_places'])
										{
										    $query = mysql_query("SELECT * FROM `tplaces` ORDER BY name ASC");
    										while ($row=mysql_fetch_array($query)) 
    										{
    											if ($_POST['ticket_places']==$row['id']) echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>'; else echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    										}
										} else {
    										$query = mysql_query("SELECT * FROM `tplaces` ORDER BY name ASC");
    										while ($row=mysql_fetch_array($query)) 
    										{
    											if ($globalrow['place']==$row['id']) echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>'; else echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    										}
										}
									echo "
									</select>
								</div>
							</div>
							";
						}
						?>
						<!-- END place part -->
						<!-- START title part -->
						<div class="form-group <?php if($rright['ticket_title_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="title">Titre:</label>
							<div class="col-sm-8">
								<input  name="title" id="title" type="text" size="50"  value="<?php if ($_POST['title']) echo $_POST['title']; else echo $globalrow['title']; ?>" <?php if($rright['ticket_title']==0  && $_GET['action']!='new') echo 'readonly="readonly"';?> />
							</div>
						</div>
						<!-- END title part -->
						<!-- START description part -->
						<div class="form-group <?php if($rright['ticket_description_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="text">Description:</label>
							<div class="col-sm-8">
								<table border="1" width="732" style="border: 1px solid #D8D8D8;" <?php if ($rright['ticket_description']==0) echo 'cellpadding="10"'; ?> >
									<tr>
										<td>
											<?php
											if ($rright['ticket_description']!=0 || $_GET['action']=='new')
											{
												//detect <br> for wysiwyg transition from 2.9 to 3.0
												$findbr=stripos($globalrow['description'], '<br>');
												if ($findbr === false) {$desc=nl2br($globalrow['description']);} else {$desc=$globalrow['description'];}
												//display editor
												echo '
												<div id="editor" class="wysiwyg-editor" >';
											    	if ($_POST['text'] && $_POST['text']!='<br><br><br>') echo "$_POST[text]"; else echo $desc;
										            if ($_GET['action']=='new' && !$_POST['user']) echo '<br /><br /><br />'; echo'
												</div>
												<input type="hidden" id="text" name="text" />
												';
											} else {
												echo $globalrow['description'];
												echo '<input type="hidden" name="text" value="'.$globalrow['description'].'" />';
											}
											?>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<!-- END description part -->
<!-- ----------------------------------------------------------------------------- -->
						<!-- START serialnumber part -->
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="title">Numéro de série:</label>
							<div class="col-sm-8">
								<input  name="serialnumber" id="serialnumber" type="text" size="50"  value="<?php if ($_POST['serialnumber']) echo $_POST['serialnumber']; else echo $globalrow['serialnumber']; ?>" <?php if($rright['serialnumber']==0) echo 'readonly="readonly"';?> />
							</div>
						</div>
						<!-- END serialnumber part -->
						<!-- START materiel part -->
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="title">Matériel:</label>
							<div class="col-sm-8">
								<input  name="materiel" id="materiel" type="text" size="50"  value="<?php if ($_POST['materiel']) echo $_POST['materiel']; else echo $globalrow['materiel']; ?>" <?php if($rright['materiel']==0) echo 'readonly="readonly"';?> />
							</div>
						</div>
						<!-- END materiel part -->
						<!-- START timetravel part -->
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="title">Temps de trajet aller/retour:</label>
							<div class="col-sm-8">
								<input  name="timetravel" id="timetravel" type="text" size="50"  value="<?php if ($_POST['timetravel']) echo $_POST['timetravel']; else echo $globalrow['timetravel']; ?>" <?php if($rright['timetravel']==0) echo 'readonly="readonly"';?> />
							</div>
						</div>
						<!-- END timetravel part -->
						<!-- START warranty part -->
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="title">Garantie:</label>
							<div class="col-sm-8">
                                <select name="warranty" id="warranty" <?php if($rright['warranty']==0) echo 'disabled="true"';?> >
									<option <?php if(empty($_POST['warranty']) && empty($globalrow['warranty'])) { echo 'selected="selected"'; } ?> disabled>Option</option>
									<option <?php if($_POST['warranty'] == "Oui"){ echo 'selected="selected"'; } elseif($globalrow['warranty'] == "Oui"){ echo 'selected="selected"'; } else {}; ?> value="Oui">Oui</option>
									<option <?php if($_POST['warranty'] == "Non") { echo 'selected="selected"'; } elseif($globalrow['warranty'] == "Non") { echo 'selected="selected"'; } else {}; ?> value="Non">Non</option>
                                </select>
							</div>
						</div>
						<!-- END warranty part -->
						<!-- START mntcontract part -->
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="title">Contrat de maintenance:</label>
							<div class="col-sm-8">
                                <select name="mntcontract" id="mntcontract" <?php if($rright['mntcontract']==0) echo 'disabled="true"';?> >
									<option <?php if(empty($_POST['mntcontract']) && empty($globalrow['mntcontract'])) { echo 'selected="selected"'; } ?> disabled>Option</option>
									<option <?php if($_POST['mntcontract'] == "Oui"){ echo 'selected="selected"'; } elseif($globalrow['mntcontract'] == "Oui"){ echo 'selected="selected"'; } else {}; ?> value="Oui">Oui</option>
									<option <?php if($_POST['mntcontract'] == "Non") { echo 'selected="selected"'; } elseif($globalrow['mntcontract'] == "Non") { echo 'selected="selected"'; } else {}; ?> value="Non">Non</option>
                                </select>
							</div>
						</div>
						<!-- END mntcontract part -->
<!-- ----------------------------------------------------------------------------- -->
						<!-- START resolution part -->
						<div class="form-group <?php if(($rright['ticket_resolution_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_resolution_disp']==0 && $_GET['action']=='new')) echo 'hide';?>" >
							<label class="col-sm-2 control-label no-padding-right" for="resolution">Résolution:</label>
							<div class="col-sm-8">
							<?php include "./thread.php";?>	
							</div>
						</div>
						<a id="down"></a>
						<!-- END resolution part -->
						<!-- START attachement part -->
						<?php
						if ($rright['ticket_attachment']!=0)
						{
							echo '
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="attachment">Fichier joint:</label>
									<div class="col-sm-8">
										<table border="1" style="border: 1px solid #D8D8D8;" cellpadding="10" >
										<tr>
											<td>';
										include "./attachement.php";
										echo '
										</td>
										</tr>
									</table>
									</div>
							</div>';
						}
						?>
						<!-- END attachement part -->
						<!-- START create date part -->
						<div class="form-group  <?php if($rright['ticket_date_create_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="date_create">Date de la demande:</label>
							<div class="col-sm-8">
								<input type="hidden" name="hide" id="hide" value="1"/>
								<input type="text" name="date_create" id="date_create" value="<?php if ($_POST['date_create']) echo $_POST['date_create']; else echo $globalrow['date_create']; ?>" <?php if($rright['ticket_date_create']==0) echo 'readonly="readonly"';?> >
							</div> 
						</div>
						<!-- END create date part -->
						<!-- START hope date part -->
						<?php if($rright['ticket_date_hope_mandatory']!=0) { if(($_POST['date_hope']=="" && $_GET['action']=='new') || $_POST['date_hope']=="0000-00-00" ||  $globalrow['date_hope']=="0000-00-00") {$date_hope_error="has-error";}  else {$date_hope_error="";}} //check empty field?>
						<!--<div class="form-group <?php echo $date_hope_error; if($rright['ticket_date_hope_disp']==0) echo 'hide';?>">
							<label class=" col-sm-2 control-label no-padding-right" for="date_hope">
							    <?php if($rright['ticket_date_hope_mandatory']!=0) { if (($_POST['date_hope']==0) && ($globalrow['date_hope']==0)) {echo '<i title="La séléction d\'une date de résolution estimée est obligatoire." class="icon-warning-sign red bigger-130"></i>&nbsp;';}} ?>
						    	Date de résolution estimée:
							</label>
							<div class="col-sm-8">
								<input  type="text" id="date_hope" name="date_hope"  onchange="loadVal(); submit();" value="<?php  if ($_POST['date_hope']) echo $_POST['date_hope']; else echo $globalrow['date_hope']; ?>" <?php if($rright['ticket_date_hope']==0) echo 'readonly="readonly"';?>>
								<?php/*
									//display warning if hope date is passed
									$date_hope=$globalrow['date_hope'];
									$querydiff=mysql_query("SELECT DATEDIFF(NOW(), '$date_hope') "); 
									$resultdiff=mysql_fetch_array($querydiff);
									if ($resultdiff[0]>0 && ($globalrow['state']!="3" && $globalrow['state']!="4")) echo "<i title=\"Date de résolution dépassée de $resultdiff[0] jours de retard\" class=\"icon-warning-sign orange bigger-130\" ></i>";
								*/ ?>
							</div>
						</div>-->
						<!-- END hope date part -->
						<!-- START resolution date part -->
						<div class="form-group <?php if($rright['ticket_date_res_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="">Date de résolution:</label>
							<div class="col-sm-8">
								<input  type="text" id="date_res" name="date_res"  value="<?php  if ($_POST['date_res']) echo $_POST['date_res']; else echo $globalrow['date_res']; ?>" <?php if($rright['ticket_date_res']==0) echo 'readonly="readonly"';?>>
							</div>
						</div>
						<!-- END resolution date part -->
						<!-- START time part -->
						<div class="form-group <?php if($rright['ticket_time_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="time">Temps passé:</label>
							<div class="col-sm-8">
								<select  id="time" name="time" <?php if($rright['ticket_time']==0) echo 'onfocus="this.blur();"';?> >
								<?php
									$query = mysql_query("SELECT * FROM `ttime` order by min ASC");
									while ($row=mysql_fetch_array($query)) 
									{
										if (($_POST['time']==$row['min'])||($globalrow['time']==$row['min'])) echo '<option selected value="'.$row['min'].'">'.$row['name'].'</option>'; else echo '<option value="'.$row['min'].'">'.$row['name'].'</option>';
									}
								?>
								</select>
							</div>
						</div>
						<!-- END time part -->
						<!-- START time hope part -->
						<!--<div class="form-group <?php // if($rright['ticket_time_hope_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="time_hope">
							<?php // if (($globalrow['time_hope']<$globalrow['time']) && $globalrow['state']!='3') echo "<i class=\"icon-warning-sign red bigger-130\" title=\"Le temps est sous-estimé.\"></i>";//display error if time hope < time pass?>
							Temps estimé:
							</label>
							<div class="col-sm-8">
								<select  id="time_hope" name="time_hope" <?php // if($rright['ticket_time_hope']==0) echo 'onfocus="this.blur();"';?> >
									<?php /*
									$query = mysql_query("SELECT * FROM `ttime` order by min ASC");
									while ($row=mysql_fetch_array($query)) 
									{
										if (($_POST['time_hope']==$row['min'])||($globalrow['time_hope']==$row['min'])) echo '<option selected value="'.$row['min'].'">'.$row['name'].'</option>';  else echo '<option value="'.$row['min'].'">'.$row['name'].'</option>';
									}
									*/ ?>
								</select>
							</div>
						</div>-->
						<!-- END time hope part -->
						<!-- START priority part -->
						<?php if($rright['ticket_priority_mandatory']!=0) {if(($_POST['priority']=="" && $_GET['action']=='new') || ($globalrow['priority']=="" && $_GET['action']!='new') || ($globalrow['criticality']=="0")) {$priority_error="has-error";} else {$priority_error="";}}  else {$priority_error="";} ?>
						<div class="form-group <?php echo $priority_error; if($rright['ticket_priority_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="priority">
							    <?php if($rright['ticket_priority_mandatory']!=0) { if (($_POST['priority']==0) && ($globalrow['priority']==0)) {echo '<i title="La séléction d\'une priorité est obligatoire." class="icon-warning-sign red bigger-130"></i>&nbsp;';}} ?>
						    	Priorité:
							</label>
							<div class="col-sm-8">
								<select  id="priority" name="priority"  onchange="loadVal(); submit();" <?php if($rright['ticket_priority']==0) echo 'onfocus="this.blur();"';?>>
									<?php
									if ($_POST['priority']!='')
									{
										$query = mysql_query("SELECT * FROM `tpriority` WHERE id='$_POST[priority]'");
										$row=mysql_fetch_array($query);
										echo "<option value=\"$_POST[priority]\" selected >$row[name]</option>";
									}
									else
									{
										$query = mysql_query("SELECT * FROM `tpriority` WHERE id='$globalrow[priority]'");
										$row=mysql_fetch_array($query);
										echo "<option value=\"$globalrow[priority]\" selected >$row[name]</option>";
									}		
									$query = mysql_query("SELECT * FROM `tpriority` WHERE id!='$globalrow[priority]'");
									while ($row=mysql_fetch_array($query)) echo "<option value=\"$row[id]\">$row[name]</option>"; 
									?>			
								</select>
								<?php
								//display priority icon
								if($_POST['priority']) {$check_id=$_POST['priority'];} else { $check_id=$globalrow['priority'];}
								$query = mysql_query("SELECT * FROM `tpriority` WHERE id='$check_id'");
								$row=mysql_fetch_array($query);
								if ($row['name']) {echo "<i title=\"$row[name]\" class=\"icon-warning-sign bigger-130\" style=\"color:$row[color]\" ></i>";}

								?>
							</div>
						</div>
						<!-- END priority part -->
						<!-- START criticality part -->
						<?php if($rright['ticket_criticality_mandatory']!=0) {if(($_POST['criticality']=="" && $_GET['action']=='new') || ($globalrow['criticality']=="" && $_GET['action']!='new') || ($globalrow['criticality']=="0")) {$criticality_error="has-error";} else {$criticality_error="";}}  else {$criticality_error="";} ?>
						<div class="form-group <?php echo $criticality_error; if($rright['ticket_criticality_disp']==0) echo 'hide';?>">
							<label  class="col-sm-2 control-label no-padding-right" for="criticality" >
							    <?php if($rright['ticket_criticality_mandatory']!=0) { if (($_POST['criticality']==0) && ($globalrow['criticality']==0)) {echo '<i title="La séléction d\'une criticité est obligatoire." class="icon-warning-sign red bigger-130"></i>&nbsp;';}} ?>
						    	Criticité:
							</label>
							<div class="col-sm-8">
								<select  id="criticality" name="criticality" onchange="loadVal(); submit();" <?php if($rright['ticket_criticality']==0) echo 'onfocus="this.blur();"';?>>
									<?php
									if ($_POST['criticality'])
									{
										$query = mysql_query("SELECT * FROM `tcriticality` WHERE id='$_POST[criticality]'");
										$row=mysql_fetch_array($query);
										echo "<option value=\"$_POST[criticality]\" selected >$row[name]</option>";
									}
									else
									{
										$query = mysql_query("SELECT * FROM `tcriticality` WHERE id='$globalrow[criticality]'");
										$row=mysql_fetch_array($query);
										echo "<option value=\"$globalrow[criticality]\" selected >$row[name]</option>";
									}			
									$query = mysql_query("SELECT * FROM `tcriticality` WHERE id!='$globalrow[criticality]' ORDER BY number");
									while ($row=mysql_fetch_array($query)) echo "<option value=\"$row[id]\">$row[name]</option>"; 
									?>			
								</select>
								<?php
								//display criticality icon
								if($_POST['criticality']) {$check_id=$_POST['criticality'];} else { $check_id=$globalrow['criticality'];}
								$query = mysql_query("SELECT * FROM `tcriticality` WHERE id='$check_id'");
								$row=mysql_fetch_array($query);
								if ($row['name']) {echo "<i title=\"$row[name]\" class=\"icon-bullhorn bigger-130\" style=\"color:$row[color]\" ></i>";}
								?>
							</div>
						</div>
						<!-- START criticality part -->
						<!-- START state part -->
						<div class="form-group <?php if($rright['ticket_state_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="state">État:</label>
							<div class="col-sm-8">
								<select  id="state"  name="state" <?php if($rright['ticket_state']==0) echo 'onfocus="this.blur();"';?>>
									<?php
									//selected value
									if ($_POST['state'])
									{
										$query = mysql_query("SELECT * FROM `tstates` WHERE id='$_POST[state]'");
										$row=mysql_fetch_array($query);
										echo "<option value=\"$_POST[state]\" selected >$row[name]</option>";
									}
									else
									{
										$query = mysql_query("SELECT * FROM `tstates` WHERE id='$globalrow[state]'");
										$row=mysql_fetch_array($query);
										echo "<option value=\"$globalrow[state]\" selected >$row[name]</option>";
									}			
							    	$query = mysql_query("SELECT * FROM `tstates` WHERE id!='$_POST[state]' AND id!='$globalrow[state]' ORDER BY number");
								    while ($row=mysql_fetch_array($query)) echo "<option value=\"$row[id]\">$row[name]</option>"; 
									?>
								</select>
								<?php
								//display state icon
								$query = mysql_query("SELECT * FROM `tstates` WHERE id LIKE '$globalrow[state]'");
								$row=mysql_fetch_array($query);
								echo "<span class=\"$row[display]\" title=\"$row[description]\">&nbsp;</span>";
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="title">Reçu imprimé:</label>
							<div class="col-sm-8">
                                <select name="stateimpr" id="stateimpr" <?php if($rright['stateimpr']==0) echo 'disabled="true"';?> >
									<option <?php if(empty($_POST['stateimpr']) && empty($globalrow['stateimpr'])) { echo 'selected="selected"'; } ?> disabled>Option</option>
									<option <?php if($_POST['stateimpr'] == "Oui"){ echo 'selected="selected"'; } elseif($globalrow['stateimpr'] == "Oui"){ echo 'selected="selected"'; } else {}; ?> value="Oui">Oui</option>
									<option <?php if($_POST['stateimpr'] == "Non") { echo 'selected="selected"'; } elseif($globalrow['stateimpr'] == "Non") { echo 'selected="selected"'; } else {}; ?> value="Non">Non</option>
                                </select>
							</div>
						</div>
						<!-- END state part -->
						<!-- START availability part --> 
						<?php
						//check if the availability parameter is on and condition parameter
						if($rparameters['availability']==1)
						{
						        if($rparameters['availability_condition_type']=='criticality' && ($globalrow['criticality']==$rparameters['availability_condition_value'] || $_POST['criticality']==$rparameters['availability_condition_value']))
						        {    
						        	//calc time
        					    	if ($globalrow['start_availability']!='0000-00-00 00:00:00' && $globalrow['end_availability']!='0000-00-00 00:00:00')
        					    	{
        					    	    $t1 =strtotime($globalrow['start_availability']) ;
                                        $t2 =strtotime($globalrow['end_availability']) ;
                                       	$time=(($t2-$t1)/60)/60;
                                       	$time="soit $time h";
        					    	} else $time='';
        					    	//explode selected date and hour
        					    	if ($_POST['start_availability_d'])
        					    	{
        					    	    $start_availability_d=$_POST['start_availability_d'];
        					    	    $start_availability_h=$_POST['start_availability_h'];
        					    	} elseif ($globalrow['start_availability']!='0000-00-00 00:00:00') 
        					    	{
        					    	    $start_availability_d=date("d/m/Y",strtotime($globalrow['start_availability']));
        					    	    $start_availability_h=date("G:i:s",strtotime($globalrow['start_availability']));
        					    	} else {
        					    	    $start_availability_d=date("d/m/Y");
        					    	    $start_availability_h=date("H:i:s");
        					    	}
        					    	
        					    	if ($_POST['end_availability_d'])
        					    	{
        					    	    $end_availability_d=$_POST['end_availability_d'];
        					    	    $end_availability_h=$_POST['end_availability_h'];
        					    	} else
        					    	if ($globalrow['start_availability']!='0000-00-00 00:00:00') {
        					    	    $end_availability_d=date("d/m/Y",strtotime($globalrow['end_availability']));
        					    	    $end_availability_h=date("G:i:s",strtotime($globalrow['end_availability']));
        					    	} else {
        					    	    $end_availability_d=date("d/m/Y");
        					    	    $end_availability_h=date("H:i:s");
        					    	}
        						    echo'
        						   	<div class="form-group '; if($rright['ticket_availability_disp']==0) echo 'hide'; echo '">
        						    	<label class="col-sm-2 control-label no-padding-right" for="start_availability_d">Début de l\'indisponibilité:</label>
        						    	<div class="col-sm-8">
            						    	<input  type="text" id="start_availability_d" name="start_availability_d"  value="'.$start_availability_d.'"';                							    	    echo '"';
                							    	    if($rright['ticket_availability']==0) echo ' readonly="readonly" ';
                							echo '
                							>
        						    	    <div class="bootstrap-timepicker">
									        	<input id="start_availability_h" name="start_availability_h" value="'.$start_availability_h.'" type="text"  />
							    	        </div>	
        						    	</div>
        					    	</div>
        					    	<div class="form-group '; if($rright['ticket_availability_disp']==0) echo 'hide'; echo '">
        						    	<label class="col-sm-2 control-label no-padding-right" for="end_availability_d">Fin de l\'indisponibilité:</label>
        						    	<div class="col-sm-8">
        							    	<input  type="text" id="end_availability_d" name="end_availability_d"  value="'.$end_availability_d.'"';
        							    	    if($rright['ticket_availability']==0) echo ' readonly="readonly" ';
        							    	echo '
        							    	>
        							        <div class="bootstrap-timepicker">
									        	<input id="end_availability_h" name="end_availability_h" value="'.$end_availability_h.'" type="text"  />
							                </div>
							                '.$time.'
							             </div>
        						    </div>
        					    	<div class="form-group '; if($rright['ticket_availability_disp']==0) echo 'hide'; echo '">
        					    		<label class="col-sm-2 control-label no-padding-right" for="availability_planned">Indisponibilité planifiée:</label>
        					    		<div class="col-sm-8">
        					    			<input type="checkbox"'; if ($globalrow['availability_planned']==1) {echo "checked";} echo ' name="availability_planned" value="1" />
        					    		</div>
        					    	</div>
        					    	';
						        }
						}
						?>
						<!-- END availability part -->
						<div class="form-actions center">
							<?php
							if (($rright['ticket_save']!=0 && $_GET['action']!='new') || ($rright['ticket_new_save']!=0 && $_GET['action']=='new'))
							{
								echo '
								<button name="modify" id="modify" value="modify" type="submit" class="btn btn-sm btn-success">
									<i class="icon-save icon-on-right bigger-110"></i> 
									&nbsp;Enregistrer
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_save_close']!=0)
							{
								echo '
								<button name="quit" id="quit" value="quit" type="submit" class="btn btn-sm btn-purple">
									<i class="icon-save icon-on-right bigger-110"></i> 
									&nbsp;Enregistrer et Fermer
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_new_send']!=0 && $_GET['action']=='new')
							{
								echo '
								<button name="send" id="send" value="send" type="submit" class="btn btn-sm btn-success">
									Envoyer
									&nbsp;<i class="icon-arrow-right icon-on-right bigger-110"></i> 
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_close']!=0 && $_POST['state']!='3' && $globalrow['state']!='3' && $_GET['action']!='new')
							{
								echo '
								<button name="close" id="close" value="close" type="submit" class="btn btn-sm btn-purple">
									<i class="icon-ok icon-on-right bigger-110"></i> 
									&nbsp;Cloturer le ticket
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_send_mail']!=0)
							{
								echo '
								<button name="mail" id="mail" value="mail" type="submit" class="btn btn-sm btn-primary">
									<i class="icon-envelope icon-on-right bigger-110"></i> 
									&nbsp;Envoyer un mail
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_cancel']!=0)
							{
								echo '
								<button name="cancel" id="cancel" value="cancel" type="submit" class="btn btn-sm btn-danger">
									<i class="icon-remove icon-on-right bigger-110"></i> 
									&nbsp;Annuler
								</button>
								';
							}
							?>
						</div>
					</div>
				</div> <!-- div widget body -->
			</form>
		</div> <!-- div end sm -->
	</div> <!-- div end x12 -->
</div> <!-- div end row -->

<?php include ('./wysiwyg.php'); ?>

<!-- date picker script -->
<script type="text/javascript">
	window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
</script>
	<script src="template/assets/js/date-time/bootstrap-timepicker.min.js"></script>
<script type="text/javascript">
jQuery(function($) {
    
    	$('#start_availability_h').timepicker({
    	        minuteStep: 1,
				showSeconds: true,
				showMeridian: false
			});
		$('#end_availability_h').timepicker({
	        minuteStep: 1,
			showSeconds: true,
			showMeridian: false
		});

		$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
		jQuery(function($){
		   $.datepicker.regional['fr'] = {
			  closeText: 'Fermer',
			  prevText: '<Préc',
			  nextText: 'Suiv>',
			  currentText: 'Courant',
			  monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
			  'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
			  monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
			  'Jul','Aoû','Sep','Oct','Nov','Déc'],
			  dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
			  dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
			  dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
			  weekHeader: 'Sm',
			  dateFormat: 'dd/mm/yy',
			  firstDay: 1,
			  isRTL: false,
			  showMonthAfterYear: false,
			  yearSuffix: ''};
		   $.datepicker.setDefaults($.datepicker.regional['fr']);
		    });
		$( "#date_create" ).datepicker({ 
			dateFormat: 'yy-mm-dd'
		});
		$( "#date_res" ).datepicker({ 
			dateFormat: 'yy-mm-dd'
		});
		$( "#date_hope" ).datepicker({ 
			dateFormat: 'yy-mm-dd'
		});
		$( "#start_availability_d" ).datepicker({ 
			dateFormat: 'dd/mm/yy'
		});
		$( "#end_availability_d" ).datepicker({ 
			dateFormat: 'dd/mm/yy'
		});
	});		
</script>		