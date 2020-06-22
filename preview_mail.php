<?php
################################################################################
# @Name : preview_mail.php
# @Desc : page to preview mail
# @call: ticket.php
# @Author : Flox
# @Update : 01/10/2014
# @Version : 3.0.10
################################################################################

//initialize variables 
if(!isset($send)) $send = ''; 
if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['return'])) $_POST['return'] = '';

//send message and trace in thread
if ($_POST['mail'])
{
	//trace mail in thread
	$query = "INSERT INTO tthreads (ticket,date,author,text,type) VALUES ('$_GET[id]','$datetime', '$_SESSION[user_id]', '','3')";
	$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
	//send
	$send=1;
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
else {
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
							Paramètres du message
						</h4>
					</div>
					<div class="widget-body">
						<div class="widget-main">
							<div class="profile-user-info profile-user-info-striped">
								<div class="profile-info-row">
									<div class="profile-info-name"> Emetteur: </div>
									<div class="profile-info-value">
										<span id="username">'.$emetteur.'</span>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name"> Destinataire: </div>
									<div class="profile-info-value">
										<span id="username">';
											if($globalrow['u_group']!=0)
											{
												echo '	
												<select id="receiver" name="receiver" >';
													$qgroup = mysql_query("SELECT * FROM `tgroups` WHERE tgroups.id=$globalrow[u_group] AND tgroups.disable='0'");
													$rgroup=mysql_fetch_array($qgroup);
													echo '<option selected value="group" > Groupe '.$rgroup['name'].'</option>
													<option value="none">Aucun</option>
												</select>
												';
												$qgroup = mysql_query("SELECT mail FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=$globalrow[u_group] AND tusers.disable=0");
												while ($row=mysql_fetch_array($qgroup)) echo $row[0].' ';
											} else {
												echo '
													<select id="receiver" name="receiver" >
														<option selected value="'.$userrow['mail'].'">'.$userrow['lastname'].' '.$userrow['firstname'].' ('.$userrow['mail'].')</option>
														<option value="none">Aucun</option>
													</select>
												';
												if ($userrow['mail']=='') echo '&nbsp;<i title="Le destinataire ne possède pas d\'adresse mail." class="icon-warning-sign red bigger-130"></i>';
											}
										echo '
										</span>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name"> Copie: </div>
									<div class="profile-info-value">
										<span id="username">
											'.$rparameters['mail_cc'].',&nbsp;  
											<select id="usercopy" name="usercopy">
												<option value="">Aucun</option>';
												//display users
												$quser = mysql_query("SELECT * FROM `tusers`  WHERE mail NOT LIKE '' AND disable=0 ORDER BY lastname ASC, firstname ASC");
												while ($row=mysql_fetch_array($quser)) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												//display groups
												$qgroup = mysql_query("SELECT * FROM `tgroups` WHERE tgroups.disable='0'");
												while ($row=mysql_fetch_array($qgroup)) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												//auto select tech if it's not the current tech
												if ($creatorrow['mail']!=$techrow['mail']) echo '<option selected value="'.$techrow['mail'].'">'.$techrow['lastname'].' '.$techrow['firstname'].'</option>'; else echo '<option selected value=""></option>'; 
												echo '
											</select>
											,&nbsp; 
											<select id="usercopy2" name="usercopy2" >
												';
												//display users
												$quser = mysql_query("SELECT * FROM `tusers`  WHERE mail NOT LIKE '' AND disable=0 ORDER BY lastname ASC, firstname ASC");
												while ($row=mysql_fetch_array($quser)) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												//display groups
												$qgroup = mysql_query("SELECT * FROM `tgroups` WHERE tgroups.disable='0'");
												while ($row=mysql_fetch_array($qgroup)) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												echo '
												<option selected value=""></option>
											</select>
											,&nbsp; 
											<select id="usercopy3" name="usercopy3" >
												';
												//display users
												$quser = mysql_query("SELECT * FROM `tusers`  WHERE mail NOT LIKE '' AND disable=0 ORDER BY lastname ASC, firstname ASC");
												while ($row=mysql_fetch_array($quser)) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												//display groups
												$qgroup = mysql_query("SELECT * FROM `tgroups` WHERE tgroups.disable='0'");
												while ($row=mysql_fetch_array($qgroup)) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												echo '
												<option selected value=""></option>
											</select>
											,&nbsp; 
											<select id="usercopy4" name="usercopy4" >
												';
												//display users
												$quser = mysql_query("SELECT * FROM `tusers`  WHERE mail NOT LIKE '' AND disable=0 ORDER BY lastname ASC, firstname ASC");
												while ($row=mysql_fetch_array($quser)) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												//display groups
												$qgroup = mysql_query("SELECT * FROM `tgroups` WHERE tgroups.disable='0'");
												while ($row=mysql_fetch_array($qgroup)) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												echo '
												<option selected value=""></option>
											</select>
											,&nbsp; 
											<select id="usercopy5" name="usercopy5" >
												';
												//display users
												$quser = mysql_query("SELECT * FROM `tusers`  WHERE mail NOT LIKE '' AND disable=0 ORDER BY lastname ASC, firstname ASC");
												while ($row=mysql_fetch_array($quser)) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												//display groups
												$qgroup = mysql_query("SELECT * FROM `tgroups` WHERE tgroups.disable='0'");
												while ($row=mysql_fetch_array($qgroup)) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												echo '
												<option selected value=""></option>
											</select>
											,&nbsp; 
											<select id="usercopy6" name="usercopy6" >
												';
												//display users
												$quser = mysql_query("SELECT * FROM `tusers`  WHERE mail NOT LIKE '' AND disable=0 ORDER BY lastname ASC, firstname ASC");
												while ($row=mysql_fetch_array($quser)) {echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' </option>';}
												//display groups
												$qgroup = mysql_query("SELECT * FROM `tgroups` WHERE tgroups.disable='0'");
												while ($row=mysql_fetch_array($qgroup)) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												echo '
												<option selected value=""></option>
											</select>
											
											';
										echo '	
										</span>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name"> Objet: </div>
									<div class="profile-info-value">
										<span id="username">'.$objet.'</span>
									</div>
								</div>
								<div class="profile-info-row">
									<div class="profile-info-name"> Message: </div>
									<div class="profile-info-value">
										<span id="username">'.$msg.'</span>
									</div>
								</div>';
								if (($globalrow['img1']!='')||($globalrow['img2']!='')||($globalrow['img3']!='')||($globalrow['img4']!='')||($globalrow['img5']!='')) // if attachment exist display it
								{
									echo '
									<div class="profile-info-row">
										<div class="profile-info-name"> Pièce jointe: </div>
										<div class="profile-info-value">
											<span id="username">
											    <select id="withattachment" name="withattachment" >
													<option selected value="1">Avec Pièces jointes</option>
													<option value="0">Sans Pièces jointes</option>
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
    													<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img1]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img1]\" >$globalrow[img1]</a>
    												";
    											}
    											if ($globalrow['img2']!='')
    											{	
    												$ext = explode('.', $globalrow['img2']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img2]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img2]\" >$globalrow[img2]</a>
    												";
    											}
    											if ($globalrow['img3']!='')
    											{	
    												$ext = explode('.', $globalrow['img3']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img3]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img3]\" >$globalrow[img3]</a>
    												";
    											}
    											if ($globalrow['img4']!='')
    											{	
    												$ext = explode('.', $globalrow['img4']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img4]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img4]\" >$globalrow[img4]</a>
    												";
    											}
    											if ($globalrow['img5']!='')
    											{	
    												$ext = explode('.', $globalrow['img5']);
    												$ext=$ext[1];
    												$ext = strtolower($ext);
    												echo "
    													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    													<a  target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img5]\" style=\"text-decoration:none\"> <img border=\"0\" src=\"./images/icon_file/$ext.png\" /> </a>&nbsp;
    													<a target=\"about_blank\" href=\"./upload/$_GET[id]/$globalrow[img5]\" >$globalrow[img5]</a>
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
								<button name="mail" id="mail" value="mail" type="submit" class="btn btn-sm btn-primary">
									<i class="icon-envelope icon-on-right bigger-110"></i> 
									&nbsp;Envoyer le mail
								</button>
								&nbsp;
								<button name="return" id="return" value="return" type="submit" class="btn btn-sm btn-danger">
									<i class="icon-reply icon-on-right bigger-110"></i> 
									&nbsp;retour
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