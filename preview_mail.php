<?php
################################################################################
# @Name : preview_mail.php
# @Description : page to preview mail
# @Call: ticket.php
# @Parameters: mail.php
# @Author : Flox
# @Create : 01/10/2014
# @Update : 16/05/2019
# @Version : 3.1.42
################################################################################

//initialize variables 
if(!isset($send)) $send = ''; 
if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['return'])) $_POST['return'] = '';

//send message and trace in thread
if ($_POST['mail'])
{
	//send
	$send=1;
	$mail_auto=false;
	require('./core/mail.php');
}
//return to previous page
elseif ($_POST['return'])
{
	$send=0;
	echo "
	<SCRIPT LANGUAGE='JavaScript'>
	<!--
	function redirect()
	{
	window.location='./index.php?page=ticket&id=$_GET[id]&state=$_GET[state]&userid=$_GET[userid]'
	}
	setTimeout('redirect()',0);
	-->
	</SCRIPT>
	";
}
//display preview mail and parameters
else
{
	$send=0;
	include('./core/mail.php');	
	echo '
	<div id="row">
		<div class="col-xs-12">
			<div class="widget-box">
				<form name="mail" method="post" action="">
					<div class="widget-header">
						<h4>
							<i class="icon-envelope"></i>
							'.T_('Paramètres du mail').'
						</h4>
						<span class="widget-toolbar">
							&nbsp;&nbsp;<button class="btn btn-minier btn-success" title="'.T_('Envoyer le mail').'" name="mail" value="Enregistrer" type="submit" id="mail"><i class="icon-envelope bigger-140"></i></button>
						</span>
					</div>
					<div class="widget-body">
						<div class="widget-main">
							<div class="profile-user-info profile-user-info-striped">
								<div class="profile-info-row">
									<div class="profile-info-name"> '.T_('Émetteur').' : </div>
									<div class="profile-info-value">
										<span id="username">'.$sender.'</span>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name"> '.T_('Destinataire').' : </div>
									<div class="profile-info-value">
										<span id="username">';
											if($globalrow['u_group']!=0)
											{
												echo '	
												<select id="receiver" name="receiver" >';
													$qry=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id  AND disable='0'");
													$qry->execute(array('id' => $globalrow['u_group']));
													$rgroup=$qry->fetch();
													$qry->closeCursor();
													echo '<option selected value="group" > Groupe '.$rgroup['name'].'</option>
													<option value="none">'.T_('Aucun').'</option>
												</select>
												';
												$qry=$db->prepare("SELECT `tusers`.`mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
												$qry->execute(array('group' => $globalrow['u_group']));
												while($row=$qry->fetch()) {echo $row['mail'].' ';}
												$qry->closeCursor();
											} else {
												echo '
													<select id="receiver" name="receiver" >
														<option selected value="'.$userrow['mail'].'">'.$userrow['lastname'].' '.$userrow['firstname'].' ('.$userrow['mail'].')</option>
														<option value="none">'.T_('Aucun').'</option>
													</select>
												';
												if ($userrow['mail']=='') echo '&nbsp;<i title="'.T_('Le destinataire ne possède pas d\'adresse mail').'." class="icon-warning-sign red bigger-130"></i>';
											}
										echo '
										</span>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name"> '.T_('Copie').' : </div>
									<div class="profile-info-value">
										<span id="username">';
											if($rparameters['mail_cc']) {echo $rparameters['mail_cc'].',&nbsp;';}
											echo'
											<select id="usercopy" name="usercopy">
												';
												//display users
												$qry=$db->prepare("SELECT `mail`,`firstname`,`lastname` FROM `tusers` WHERE (mail!='' AND disable='0') OR id='0' ORDER BY id!='0', lastname ASC, firstname ASC");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												$qry->closeCursor();
												
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) 
												{
													//auto select technician group if ticket is assigned to technician group
													if($globalrow['t_group']==$row['id']) 
													{echo '<option selected value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';} 
													else 
													{echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												}
												$qry->closeCursor();
												
												//auto select tech if it's not the current tech
												$different_tech=0;
												if (($_SESSION['user_id']!=$techrow['id']) && ($globalrow['t_group']==0)) {echo '<option selected value="'.$techrow['mail'].'">'.$techrow['lastname'].' '.$techrow['firstname'].'</option>'; $different_tech=1;}
												
												//auto select mail agency if parameters is enable and if agency have mail and user have no mail
												if ($rparameters['user_agency']==1 && $different_tech==0) {
													//get agency mail
													$qry=$db->prepare("SELECT `mail`,`name` FROM `tagencies` WHERE id IN (SELECT `u_agency` FROM `tincidents` WHERE id=:id)");
													$qry->execute(array('id' => $_GET['id']));
													$row=$qry->fetch();
													$qry->closeCursor();
													
													if($row['mail']) {echo '<option selected value="'.$row['mail'].'">'.T_('Agence').' '.$row['name'].' ('.$row['mail'].')</option>';}
												}
												echo '
											</select>
											,&nbsp; 
											<select id="usercopy2" name="usercopy2" >
												';
												//display users
												$qry=$db->prepare("SELECT `mail`,`firstname`,`lastname` FROM `tusers` WHERE (mail!='' AND disable='0') OR id='0' ORDER BY id!='0', lastname ASC, firstname ASC");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												$qry->closeCursor();
												
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
												
												//auto select mail agency if parameters is enable and if agency have mail and user have no mail
												if ($rparameters['user_agency']==1 && $different_tech==1) {
													//get agency mail
													$qry=$db->prepare("SELECT `mail`,`name` FROM `tagencies` WHERE id IN (SELECT `u_agency` FROM `tincidents` WHERE id=:id)");
													$qry->execute(array('id' => $_GET['id']));
													$row=$qry->fetch();
													$qry->closeCursor();
													if($row['mail']) {echo '<option selected value="'.$row['mail'].'">'.T_('Agence').' '.$row['name'].' ('.$row['mail'].')</option>';}
													
												} else {
													echo '<option selected value=""></option>';
												}
												echo '
												
											</select>
											,&nbsp; 
											<select id="usercopy3" name="usercopy3" >
												';
												//display users
												$qry=$db->prepare("SELECT `mail`,`firstname`,`lastname` FROM `tusers` WHERE (mail!='' AND disable='0') OR id='0' ORDER BY id!='0', lastname ASC, firstname ASC");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												$qry->closeCursor();
												
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
												echo '
												<option selected value=""></option>
											</select>
											,&nbsp; 
											<select id="usercopy4" name="usercopy4" >
												';
												//display users
												$qry=$db->prepare("SELECT `mail`,`firstname`,`lastname` FROM `tusers` WHERE (mail!='' AND disable='0') OR id='0' ORDER BY id!='0', lastname ASC, firstname ASC");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
												echo '
												<option selected value=""></option>
											</select>
											,&nbsp; 
											<select id="usercopy5" name="usercopy5" >
												';
												//display users
												$qry=$db->prepare("SELECT `mail`,`firstname`,`lastname` FROM `tusers` WHERE (mail!='' AND disable='0') OR id='0' ORDER BY id!='0', lastname ASC, firstname ASC");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
												echo '
												<option selected value=""></option>
											</select>
											,&nbsp; 
											<select id="usercopy6" name="usercopy6" >
												';
												//display users
												$qry=$db->prepare("SELECT `mail`,`firstname`,`lastname` FROM `tusers` WHERE (mail!='' AND disable='0') OR id='0' ORDER BY id!='0', lastname ASC, firstname ASC");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
												echo '
												<option selected value=""></option>
											</select>
											<div class="space-4"></div>
											<input placeholder="'.T_('Autre adresse mail').'" size="40" type="text" name="manual_address" />
										</span>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name"> '.T_('Objet').' : </div>
									<div class="profile-info-value">
										<span id="username">'.$object.'</span>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name"> '.T_('Corp').' : </div>
									<div class="profile-info-value">
										<span id="username">'.$msg.'</span>
									</div>
								</div>';
								if (($globalrow['img1']!='')||($globalrow['img2']!='')||($globalrow['img3']!='')||($globalrow['img4']!='')||($globalrow['img5']!='')) // if attachment exist display it
								{
									echo '
									<div class="profile-info-row">
										<div class="profile-info-name"> '.T_('Pièce jointe').' : </div>
										<div class="profile-info-value">
											<span id="username">
											    <select id="withattachment" name="withattachment" >
													<option selected value="1">'.T_('Avec Pièces jointes').'</option>
													<option value="0">'.T_('Sans Pièces jointes').'</option>
												</select>
												<br />
												<br />
    											';
    											if ($globalrow['img1']!='')
    											{	
    												$ext = explode('.', $globalrow['img1']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img1]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img1]\" >$globalrow[img1]</a>
    												";
    											}
    											if ($globalrow['img2']!='')
    											{	
    												$ext = explode('.', $globalrow['img2']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img2]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img2]\" >$globalrow[img2]</a>
    												";
    											}
    											if ($globalrow['img3']!='')
    											{	
    												$ext = explode('.', $globalrow['img3']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img3]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img3]\" >$globalrow[img3]</a>
    												";
    											}
    											if ($globalrow['img4']!='')
    											{	
    												$ext = explode('.', $globalrow['img4']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img4]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img4]\" >$globalrow[img4]</a>
    												";
    											}
    											if ($globalrow['img5']!='')
    											{	
    												$ext = explode('.', $globalrow['img5']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img5]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"_blank\" href=\"./upload/$_GET[id]/$globalrow[img5]\" >$globalrow[img5]</a>
    												";
    											}
    											echo '
											</span>
										</div>
									</div>
									';
								}
							echo '
							</div>
							<div class="form-actions center">
								<button name="mail" id="mail" value="mail" type="submit" class="btn btn-sm btn-success">
									<i class="icon-envelope icon-on-right bigger-110"></i> 
									&nbsp;'.T_('Envoyer le mail').'
								</button>
								&nbsp;
								<button name="return" id="return" value="return" type="submit" class="btn btn-sm btn-danger">
									<i class="icon-reply icon-on-right bigger-110"></i> 
									&nbsp;'.T_('Retour').'
								</button>
							</div>
						</div> <!-- end widget main -->
					</div> <!-- end widget body -->
				</form>
			</div>
		</div>
	</div>
	';
}
?>