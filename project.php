<?php
################################################################################
# @Name : project.php
# @Description : display project
# @Call : /index.php
# @Parameters : 
# @Author : Flox
# @Create : 26/01/2019
# @Update : 18/10/2019
# @Version : 3.1.45
################################################################################

//initialize variables 
if(!isset($_GET['action'])) $_GET['action'] = '';
if(!isset($_GET['task_action'])) $_GET['task_action'] = '';
if(!isset($_GET['id'])) $_GET['id'] = '';
if(!isset($_GET['task_id'])) $_GET['task_id'] = '';

if(!isset($_POST['name'])) $_POST['name'] = '';
if(!isset($_POST['save'])) $_POST['save'] = '';
if(!isset($_POST['return'])) $_POST['return'] = '';
if(!isset($_POST['task_add'])) $_POST['task_add'] = '';

if($rright['project'])
{
	if($_POST['return'])
	{
		//redirect
		$www = "./index.php?page=project";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()');
			-->
			</SCRIPT>";
	}
	//delete project
	if($_GET['action']=='delete' && $_GET['id']) 
	{
		$qry=$db->prepare("UPDATE `tprojects` SET `disable`=1 WHERE `id`=:id");
		$qry->execute(array('id' => $_GET['id']));
	}
	//delete task
	if($_GET['task_action']=='delete' && $_GET['task_id']) 
	{
		$qry=$db->prepare("DELETE FROM `tprojects_task` WHERE `id`=:id");
		$qry->execute(array('id' => $_GET['task_id']));
	}
	//add task
	if($_POST['task_add'] && $_GET['id'] && $_POST['add_task_number'] && $_POST['add_ticket_number'])
	{
		//secure string
		$_POST['add_task_number']=strip_tags($_POST['add_task_number']);
		$_POST['add_ticket_number']=strip_tags($_POST['add_ticket_number']);
			
		$qry=$db->prepare("INSERT INTO `tprojects_task` (`number`,`project_id`,`ticket_id`) VALUES (:number,:project_id,:ticket_id)");
		$qry->execute(array('number' => $_POST['add_task_number'],'project_id' => $_GET['id'],'ticket_id' => $_POST['add_ticket_number']));
	}
	//////////////////////////////////////////////////////////// add project
	if($_GET['action']=='add')
	{
		if($_POST['save'])
		{
			//secure string
			$_POST['name']=strip_tags($_POST['name']);
			
			$qry=$db->prepare("INSERT INTO `tprojects` (`name`) VALUES (:name)");
			$qry->execute(array('name' => $_POST['name']));
			$project_id=$db->lastInsertId();
			
			//display action message
			echo '
				<div class="alert alert-block alert-success">
					<i class="icon-ok green"></i>
					'.T_('Le projet a été crée').'.
				</div>
			';
			
			//redirect
			$www = "./index.php?page=project&id=$project_id&action=edit";
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
		///////////////////////////FORM add project
		echo '
		<div class="page-header position-relative">
			<h1>
				<i class="icon-tasks"></i> '.T_("Ajout d'un projet").'
			</h1>
		</div>
		<div class="space-3"></div>
		<fieldset>
			<div class="col-xs-12">
				<form method="POST" enctype="multipart/form-data" name="myform" id="myform" action="" >
					<label for="name">'.T_('Nom du projet').' :</label>
					<input name="name" maxlength="50" size="50px" type="text" value="'; echo $_POST['name']; echo '">
					<br />
					<br />
					<div class="form-actions align-right clearfix">
						<button name="return" value="return" id="return" type="submit" class="btn btn-danger">
							<i class="icon-undo bigger-110"></i>
							'.T_('Retour').'
						</button>
						&nbsp;&nbsp;&nbsp;
						<button name="save" value="save" id="save" type="submit" class="btn btn-success">
							<i class="icon-save bigger-110"></i>
							'.T_('Sauvegarder').'
						</button>
					</div>
				</form>
			</div>
		</fieldset>	
		
		';
		
	} elseif($_GET['action']=='edit') //////////////////////////////////////////////////////////// edit project
	{
		
		if($_POST['save'] && $_GET['id'])
		{
			//secure string
			$_POST['name']=strip_tags($_POST['name']);
			
			$qry=$db->prepare("UPDATE `tprojects` SET `name`=:name WHERE `id`=:id");
			$qry->execute(array('id' => $_GET['id'], 'name' => $_POST['name']));
			
			//display action message
			echo '
				<div class="alert alert-block alert-success">
					<i class="icon-ok green"></i>
					'.T_('Le projet a été mis à jour').'.
				</div>
			';
			
			//redirect
			$www = "./index.php?page=project&id=$_GET[id]&action=$_GET[action]";
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
		
		//get project data
		$qry=$db->prepare("SELECT `name` FROM `tprojects` WHERE id=:id");
		$qry->execute(array('id' => $_GET['id']));
		$project=$qry->fetch();
		$qry->closeCursor();
		
		echo '
		<div class="page-header position-relative">
			<h1>
				<i class="icon-tasks"></i> '.T_("Modification du projet").' : '.$project['name'].'
			</h1>
		</div>
		<div class="space-3"></div>
		<fieldset>
			<div class="col-xs-12">
				<form method="POST" enctype="multipart/form-data" name="myform" id="myform" action="" >
					<label for="name">'.T_('Nom du projet').' :</label>
					<input maxlength="50" name="name" size="50px" type="text" value="'; echo $project['name']; echo '">
					<div class="space-2"></div>
					<label for="tasks">'.T_('Liste des tâches').' :</label>
					<div class="space-2"></div>
					';
					//list tasks
					$qry=$db->prepare("SELECT `id`,`number`,`project_id`,`ticket_id` FROM `tprojects_task` WHERE `project_id`=:project_id ORDER BY `number`");
					$qry->execute(array('project_id' => $_GET['id']));
					while($task=$qry->fetch()) 
					{
						//get ticket data
						$qry2=$db->prepare("SELECT `title` FROM `tincidents` WHERE id=:id");
						$qry2->execute(array('id' => $task['ticket_id']));
						$ticket=$qry2->fetch();
						$qry2->closeCursor();
						
						echo '&nbsp;<i class="icon-circle green"></i> <b>'.T_('Tâche n°').' '.$task['number'].' :</b> '.T_('Ticket').' '.$task['ticket_id'].' ('.$ticket['title'].')';
						echo '&nbsp;<a href="./index.php?page=project&id='.$_GET['id'].'&action=edit&task_id='.$task['id'].'&task_action=delete" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cette tâche ?').'\');" ><i title="'.T_('Supprimer cette tâche').'" class="icon-trash red bigger-110"></i></a>';
						echo '<br />';
					}
					$qry->closeCursor();
					//add task
					echo '&nbsp;<i class="icon-circle green"></i> '.T_('Tâche n°').'&nbsp;<input type="text" size="2" name="add_task_number" value="">';
					echo '&nbsp;'.T_('ticket n°').' <input type="text" size="4" name="add_ticket_number" value="">';
					echo '&nbsp;&nbsp;<button class="btn btn-minier btn-success" title="Ajouter" id="task_add" name="task_add" value="task_add" type="submit" ><i class="icon-plus"></i> '.T_('Ajouter').'</button>';
					
					echo '
					<div class="form-actions align-right clearfix">
						<button name="return" value="return" id="return" type="submit" class="btn btn-danger">
							<i class="icon-undo bigger-110"></i>
							'.T_('Retour').'
						</button>
						&nbsp;&nbsp;&nbsp;
						<button name="save" value="save" id="save" type="submit" class="btn btn-success">
							<i class="icon-save bigger-110"></i>
							'.T_('Sauvegarder').'
						</button>
					</div>
				</form>
			</div>
		</fieldset>		
		';
		
	} else { //////////////////////////////////////////////////////////// list projects
		$qry=$db->prepare("SELECT COUNT(id) FROM `tprojects` WHERE `disable`=0");
		$qry->execute();
		$row=$qry->fetch();
		$qry->closeCursor();
		echo '
			<div class="page-header position-relative">
				<h1>
					<i class="icon-tasks"></i> '.T_('Liste des projets').'
					<small>
						<i class="icon-double-angle-right"></i>
						&nbsp;Nombre: '.$row[0].' &nbsp;&nbsp;
					</small>
				</h1>
			</div>
		';
		
		//for each project
		$qry=$db->prepare("SELECT `id`,`name` FROM `tprojects` WHERE `disable`=0 ORDER BY `id` DESC");
		$qry->execute();
		while($project=$qry->fetch()) 
		{
			//check finish project
			$finish=1;
			$qry2=$db->prepare("SELECT `tincidents`.state FROM `tprojects_task`,`tincidents` WHERE `tprojects_task`.`ticket_id`=`tincidents`.`id` AND `tincidents`.`disable`=0 AND `tprojects_task`.project_id=:project_id");
			$qry2->execute(array('project_id' => $project['id']));
			while($row2=$qry2->fetch()) 
			{
				if($row2['state']!=3) {$finish=0;}
			}
			$qry2->closeCursor();
			if($finish==1) {$flag='green';} else {$flag='orange';}
			
			echo '
			<h4 class="header smaller lighter grey">
				<i class="icon-flag '.$flag.'"></i> '.T_("Projet").' n°'.$project['id'].' : '.$project['name'].'
				<a href="./index.php?page=project&id='.$project['id'].'&action=delete" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer ce projet ?').'\');" >
					<i style="float:right; margin:5px;" title="'.T_('Supprimer ce projet').'" class="icon-trash red bigger-110"></i>
				</a>
				<a href="./index.php?page=project&id='.$project['id'].'&action=edit" >
					<i style="float:right; margin:5px;" title="'.T_('Modifier ce projet').'" class="icon-pencil orange bigger-110"></i>
				</a>
			</h4>
			<div id="fuelux-wizard" data-target="#step-container">
				<ul class="wizard-steps">
					';
					$qry2=$db->prepare("SELECT `number`,`ticket_id` FROM `tprojects_task` WHERE `project_id`=:project_id ORDER by `number`");
					$qry2->execute(array('project_id' => $project['id']));
					while($task=$qry2->fetch()) 
					{
						//get ticket data
						$qry3=$db->prepare("SELECT `id`,`title`,`state`,`date_hope`,`date_res` FROM `tincidents` WHERE id=:id");
						$qry3->execute(array('id' => $task['ticket_id']));
						$ticket=$qry3->fetch();
						$qry3->closeCursor();
						
						
						//check if event exist with this ticket
						$qry3=$db->prepare("SELECT `date_start`,`date_end` FROM `tevents` WHERE incident=:incident AND type=2");
						$qry3->execute(array('incident' => $task['ticket_id']));
						$event=$qry3->fetch();
						$qry3->closeCursor();
						
						if($event)
						{
							//convert datetime
							if($event['date_start'] && $event['date_start']!='0000-00-00 00:00:00')
							{
								$date_start=DateTime::createFromFormat('Y-m-d H:i:s',$event['date_start']);
								$date_start=$date_start->format('d/m/Y');
							}  else {$date_start='';}
							
							if($event['date_end'] && $event['date_end']!='0000-00-00 00:00:00')
							{
								$date_end=DateTime::createFromFormat('Y-m-d H:i:s',$event['date_end']);
								$date_end=$date_end->format('d/m/Y');
							} else {$date_end='';}
							
							if($ticket['state']=='3') {
								$class='class="active green"';
								$color='green';
								$date=T_('Résolu le').' : '.$date_end;
								$date_label=T_('Date de résolution du ticket');
							} else {
								$class='';
								$color='orange';
								$date=T_('Planifié le').' : '.$date_start;
								$date_label=T_('Date de résolution estimée du ticket');
							}
						} else {
							//convert datetime
							if($ticket['date_res'] && $ticket['date_res']!='0000-00-00 00:00:00')
							{
								$date_res=DateTime::createFromFormat('Y-m-d H:i:s',$ticket['date_res']);
								$date_res=$date_res->format('d/m/Y');
							}  else {$date_res='';}
							
							if($ticket['date_hope'] && $ticket['date_hope']!='0000-00-00')
							{
								$date_hope=DateTime::createFromFormat('Y-m-d',$ticket['date_hope']);
								$date_hope=$date_hope->format('d/m/Y');
							} else {$date_hope='';}
							
							if($ticket['state']=='3') {
								$class='class="active green"';
								$color='green';
								$date=T_('Résolu le').' : '.$date_res;
								$date_label=T_('Date de résolution du ticket');
							} else {
								$class='';
								$color='orange';
								$date=T_('Date estimée le').' : '.$date_hope;
								$date_label=T_('Date de résolution estimée du ticket');
							}
						}
						echo '
						<li data-target="" '.$class.' >
							<span title="'.T_('Numéro de la tâche').'" class="step">'.$task['number'].'</span>
							<span class="title">
								<a title="'.T_('Ouvrir le ticket associé à cette tâche').'" target="_blank " href="index.php?page=ticket&id='.$ticket['id'].'">
									<i class="icon-ticket '.$color.'"></i> '.$ticket['id'].' : '.$ticket['title'].'
								</a>
								<div space="2"></div>
								<i title="'.$date_label.'" class="icon-calendar '.$color.'"></i> '.$date.'
								
							</span>
						</li>
						';
					}
					$qry2->closeCursor();
					echo '
				</ul>
			</div>
			<div class="space-30"></div>
			';
		}
		$qry->closeCursor();
	}
} else {
 echo '<div class="alert alert-danger"><i class="icon-remove"></i> <strong>'.T_('Erreur').':</strong> '.T_("Vous n'avez pas les droits d'accès à la fonction projet").' </div>';
}
?>