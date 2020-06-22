<?php
################################################################################
# @Name : thread.php
# @Call: ticket.php
# @Desc : display tickets thread
# @Autor : Flox
# @Create : 27/01/2013
# @Update : 17/03/2015
# @Version : 3.0.11
################################################################################

// initialize variables 
if(!isset($_GET['threaddelete'])) $_GET['threaddelete'] = ''; 
if(!isset($_GET['threadedit'])) $_GET['threadedit'] = ''; 
if(!isset($rcreator['firstname'])) $rcreator['firstname']= ''; 
if(!isset($rcreator['lastname'])) $rcreator['lastname']= ''; 

///// actions for threads

//thread delete
if ($_GET['threaddelete']!='')
{
	$query = 'DELETE FROM tthreads WHERE id='.$_GET['threaddelete'].'';
	$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
} 

//call date conversion function from index
$date_start=date_convert($globalrow['date_create']);

//find firstname et lastname of creator
if ($globalrow['creator']!='')
{
    $qcreator = mysql_query('SELECT * FROM tusers WHERE id='.$globalrow['creator'].' AND disable=0');
    $rcreator=mysql_fetch_array($qcreator);
}

//display time line
if($_GET['action']!='new') //case for edit ticket not new ticket
{
	echo '
    <table border="1" style="border: 1px solid #D8D8D8;" CELLPADDING="15">
		<tr>
			<td>
				<div id="timeline-1">
					<div class="row">
							<div class="timeline-container">
								<div class="timeline-items">
									<div class="timeline-item clearfix">
											<div class="timeline-label">
												<span class="label label-primary arrowed-in-right label-lg">
													<i class="icon-circle"></i> '.$date_start.': <b>Ouverture</b> du ticket <font size="1px">(Effectué par '.$rcreator['firstname'].' '.$rcreator['lastname'].')</font>
												</span>
											</div>
										<div class="timeline-items">';
											$query = mysql_query("SELECT * FROM tthreads WHERE ticket='$_GET[id]' ORDER BY date");
											while ($row=mysql_fetch_array($query)) 
											{
												////foreach type of thread display line
												
												//call date conversion function
												$date_thread=date_convert($row['date']);
												
												//author name
												$quser = mysql_query("SELECT * FROM tusers WHERE id='$row[author]'");
												$ruser=mysql_fetch_array($quser);
												//find author profile
												$quserprofile = mysql_query("SELECT tprofiles.img FROM tprofiles,tusers WHERE tusers.profile=tprofiles.level and tusers.id=$row[author]");
												$ruserprofile=mysql_fetch_array($quserprofile);
												
												//if it's text message
												if ($row['type']==0)
												{
													echo '
													<div class="timeline-item clearfix">
														<div class="timeline-info">
															<img title="'.$ruser['firstname'].' '.$ruser['lastname'].'" alt="avatar" src="./images/avatar/'.$ruserprofile[0].'">
														</div>
														<div class="widget-box transparent">
															<div class="widget-header widget-header-small hidden"></div>
															<div class="widget-header widget-header-small">
																<h5 class="smaller">
																	<a href="#" class="blue"><i class="icon-user bigger-110"></i> '.$ruser['firstname'].' '.$ruser['lastname'].'</a>&nbsp;
																	<span class="grey"><i class="icon-time bigger-110"></i> '.$date_thread.'</span>
																</h5>
																<span class="widget-toolbar">
																	<a title="Réduire" href="#" data-action="collapse">
																		<i class="icon-chevron-up"></i>
																	</a>
																	&nbsp;
																	';
																	//check your own tickets
																	if($row['author']==$_SESSION['user_id']) 
																	{
																		if ($rright['ticket_thread_edit']!=0) echo '<a href="./index.php?page=ticket&id='.$_GET['id'].'&threadedit='.$row['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&category='.$_GET['category'].'&subcat='.$_GET['subcat'].'&viewid='.$_GET['viewid'].'#down"><i title="Modifier" class="icon-pencil orange bigger-130"></i></a>&nbsp;';
																	} else  {
																		if ($rright['ticket_thread_edit_all']!=0) echo '<a href="./index.php?page=ticket&id='.$_GET['id'].'&threadedit='.$row['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&category='.$_GET['category'].'&subcat='.$_GET['subcat'].'&viewid='.$_GET['viewid'].'#down"><i title="Modifier" class="icon-pencil orange bigger-130"></i></a>&nbsp;';
																	}
																	if ($rright['ticket_thread_delete']!=0) echo '<a href="./index.php?page=ticket&id='.$_GET['id'].'&threaddelete='.$row['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&category='.$_GET['category'].'&subcat='.$_GET['subcat'].'&viewid='.$_GET['viewid'].'#down"><i title="Supprimer" class="icon-remove red bigger-130"></i></a>';
																	echo '
																</span>
															</div>
															<div class="widget-body">
																<div class="widget-main">';
																//detect <br> for wysiwyg transition from 2.9 to 3.0
												                $findbr=stripos($row['text'], '<br>');
										                        if ($findbr === false) {$threadtext=nl2br($row['text']);} else {$threadtext=$row['text'];}
											                	echo '
																	'.$threadtext.'
																</div>
															</div>
														</div>
													</div>
													';
												}
												//if it's attribution type
												if ($row['type']==1)
												{
													if ($row['group1'])
													{
														//find group name 
														$qtgroup = mysql_query("SELECT * FROM tgroups WHERE id='$row[group1]'");
														$rgroup=mysql_fetch_array($qtgroup);
														$name="au groupe <b>$rgroup[name]</b>";
													} else {
														//find technician name 
														$qtech = mysql_query("SELECT * FROM tusers WHERE id='$row[tech1]'");
														$rtech=mysql_fetch_array($qtech);
														$name="à <b>$rtech[firstname] $rtech[lastname]</b>";
													}
													echo '
													<div class="timeline-label">
														<span class="label label-purple arrowed-in-right label-lg">
															<i class="icon-user"></i> '.$date_thread.': <b>Attribution</b> du ticket '.$name.'  <font size="1px">(Effectué par  '.$ruser['firstname'].' '.$ruser['lastname'].')</font>
														</span>
													</div>
													';
												}
												//if it's transfert type
												if ($row['type']==2)
												{
													//find technicians group name 
													$qgroup1 = mysql_query("SELECT * FROM tgroups WHERE id='$row[group1]'");
													$rgroup1=mysql_fetch_array($qgroup1);
													$qgroup2 = mysql_query("SELECT * FROM tgroups WHERE id='$row[group2]'");
													$rgroup2=mysql_fetch_array($qgroup2);
													//find technicians name
													$qtech1 = mysql_query("SELECT CONCAT_WS('. ', left(tusers.firstname, 1),  tusers.lastname) as name FROM tusers WHERE id='$row[tech1]'");
													$rtech1=mysql_fetch_array($qtech1);
													$qtech2 = mysql_query("SELECT CONCAT_WS('. ', left(tusers.firstname, 1),  tusers.lastname) as name FROM tusers WHERE id='$row[tech2]'");
													$rtech2=mysql_fetch_array($qtech2);
													$dispname="de <b>$rtech1[name]$rgroup1[name]</b> à <b>$rtech2[name]$rgroup2[name]</b>";
													echo '
													<div class="timeline-label">
														<span class="label label-yellow arrowed-in-right label-lg">
															<i class="icon-exchange"></i> '.$date_thread.': <b>Transfert</b> du ticket '.$dispname.'  <font size="1px">(Effectué par  '.$ruser['firstname'].' '.$ruser['lastname'].')</font>
														</span>
													</div>
													';
												}
												//if it's mails type
												if ($row['type']==3)
												{
													//find technicians name 
													$qtech1 = mysql_query("SELECT * FROM tusers WHERE id='$row[tech1]'");
													$rtech1=mysql_fetch_array($qtech1);
													$qtech2 = mysql_query("SELECT * FROM tusers WHERE id='$row[tech2]'");
													$rtech2=mysql_fetch_array($qtech2);
													echo '
													<div class="timeline-label">
														<span class="label label-grey arrowed-in-right label-lg">
															<i class="icon-envelope"></i> '.$date_thread.': <b>Envoi de mail</b> <font size="1px">(Effectué par  '.$ruser['firstname'].' '.$ruser['lastname'].')</font>
														</span>
													</div>
													';
												}
												//if it's close type
												if ($row['type']==4)
												{
													echo '
													<div class="timeline-label">
														<span class="label label-success arrowed-in-right label-lg">
															<i class="icon-ok"></i> '.$date_thread.': <b>Cloture</b> <font size="1px">(Effectué par  '.$ruser['firstname'].' '.$ruser['lastname'].')</font>
														</span>
													</div>
													';
												}
											}
											echo '
										</div>
										
									</div><!-- /.timeline-items -->	
								</div><!-- /.timeline-items -->
							</div><!-- /.timeline-container -->
					
					</div>
				</div>
				';
}
				if ($rright['ticket_thread_add']!=0)
				{
					//display text input
					$query = mysql_query("SELECT text FROM `tthreads` WHERE id LIKE '$_GET[threadedit]'");
					$row=mysql_fetch_array($query);
					
					//find name for submit button
					if($_GET['threadedit']) $button="Modifier"; else $button="Ajouter";
					
					//detect <br> for wysiwyg transition from 2.9 to 3.0
					$findbr=stripos($row[0], '<br>');
					if ($findbr === false) {$text=nl2br($row[0]);} else {$text=$row[0];}
					echo '
					<table border="0"  width="700" >
						<tr>
							<td>
								<table border="1" style="border: 1px solid #D8D8D8;" >
									<tr>
										<td>
											<div id="editor2" class="wysiwyg-editor" >';
										    	if($_POST['text2']) echo $_POST['text2']; elseif($text) echo $text; else echo "<br /><br /><br />";
											echo '</div>
											<input type="hidden" name="text2" />
										</td>
									</tr>
								</table>
							</td>
							<td>	
								';
								if($_GET['action']!='new') {echo '&nbsp;&nbsp;<button class="btn btn-sm btn-success" title="'.$button.'" name="modify" value="modify" type="submit" id="modify">'.$button.' <i class="icon-arrow-right icon-on-right"></i></i></button>';}
								echo '
							</td>
						</tr>
					</table>
					';
				}
if($_GET['action']!='new') 
{
			echo '
			</td>
		</tr>
	</table>
	';	
}
?>