<?php
################################################################################
# @Name : ticket_stat.php
# @Description : Display Tickets Statistics
# @Call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 25/01/2016
# @Update : 09/10/2019
# @Version : 3.1.45
################################################################################

if ($rparameters['debug']==1) {echo '<u><b>DEBUG MODE:</b></u><br /><b>VAR</b> where_service='.$where_service.' where_agency='.$where_agency.' POST_service='.$_POST['service'].' POST_agency='.$_POST['agency'].' POST_state='.$_POST['state'];}
?>

<form method="post" action="" name="filter" >
	<?php echo T_('Filtre'); ?> :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<select style="width:160px"name="tech" onchange="submit()">
		<?php
		if ($_POST['tech']=='%') {echo '<option value="%" selected >'.T_('Tous les techniciens').'</option>';} else {echo '<option value="%" >'.T_('Tous les techniciens').'</option>';}											
		//case limit user service
		if ($rparameters['user_limit_service']==1 && $rright['admin']==0 && $where_service)
		{
			//case technician with agency et service
			$where_service2=str_replace('AND tincidents.u_service','service_id', $where_service);
			$where_service2=str_replace('AND', '', $where_service2);
			if($cnt_service>1 && $cnt_agency!=0)
			{
				$where_service2=preg_replace('/OR/', '', $where_service2, 1); //case user have single service and agency
			} elseif($cnt_service==1) {
				$where_service2=str_replace('OR', '', $where_service2); //case user have single service and agency
			}
			//case user have service and agency
			if($cnt_service==1 && $cnt_agency!=0) {
				$where_service=str_replace('OR', 'AND', $where_service); 
			} elseif ($cnt_service>1 && $cnt_agency!=0)
			{
				$where_service=preg_replace('/OR/', 'AND', $where_service, 1); 
			}
			//case user with only-one agency
			if($cnt_agency!=0 && $cnt_service==0)
			{
				$where_service2=' 1=1';
			}
			//case error
			if(!$where_service2)
			{
				$where_service2=' 1=1';
			}
			
			$where_service2=str_replace('tincidents.u_service', 'service_id', $where_service2);
			$query="SELECT id,firstname,lastname FROM tusers WHERE id IN (SELECT user_id FROM tusers_services WHERE $where_service2 ) AND profile='0' AND disable='0' ORDER BY lastname";
			$query = $db->query($query);
		} else {
			//display admin in technician liste
			if($rright['ticket_tech_admin']!=0)
			{
				$query = $db->query("SELECT id,firstname,lastname FROM tusers WHERE (profile='0' OR profile='4') AND disable=0 ORDER BY lastname");
			} else {
				$query = $db->query("SELECT id,firstname,lastname FROM tusers WHERE profile='0' AND disable=0 ORDER BY lastname");
			}
		}				
		while ($row=$query->fetch()) {
			if ($row['id'] == $_POST['tech']) {$selected1='selected';} else {$selected1='';}
			echo '<option value="'.$row['id'].'" '.$selected1.'>'.$row['firstname'].' '.$row['lastname'].'</option>'; 
		} 
		$query->closeCursor();
		?>
	</select>
	<select style="width:160px"name="service" onchange="submit()">
		<?php
		if ($_POST['service']=='%') {echo '<option value="%" selected>'.T_('Tous les services').'</option>';} else {echo '<option value="%" >'.T_('Tous les services').'</option>';}											
		//case limit user service
		if ($rparameters['user_limit_service']==1 && $rright['admin']==0)
		{
			$qry=$db->prepare("SELECT id,name FROM tservices WHERE id IN (SELECT service_id FROM tusers_services WHERE user_id=:user_id) AND disable=0 ORDER BY name");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if ($row['id']==$_POST['service']) {$selected2='selected';} else {$selected2='';} 
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>';
			}
			$qry->closeCursor();
		} else {
			$qry=$db->prepare("SELECT id,name FROM tservices WHERE disable=0 ORDER BY name");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				if ($row['id']==$_POST['service']) {$selected2='selected';} else {$selected2='';} 
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>';
			}
			$qry->closeCursor();
		}
		?>
	</select>
	<?php
	if($rparameters['user_agency']==1)
	{
		echo ' 
		<select style="width:160px"name="agency" onchange="submit()">';
			if ($_POST['agency']=='%') {echo '<option value="%" selected>'.T_('Toutes les agences').'</option>';} else {echo '<option value="%" >'.T_('Toutes les agences').'</option>';}											
			$qry=$db->prepare("SELECT `id`,`name` FROM `tagencies` WHERE disable=0 AND id!=0 ORDER BY name");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				if ($row['id']==$_POST['agency']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>'; 
			}
			$qry->closeCursor();
			echo'	
		</select>';
	}
	if($rparameters['ticket_type']==1)
	{
		echo ' 
		<select style="width:160px"name="type" onchange="submit()">';
			if ($_POST['type']=='%') {echo '<option value="%" selected>'.T_('Tous les types').'</option>';} else {echo '<option value="%" >'.T_('Tous les type').'</option>';}											
			$qry=$db->prepare("SELECT `id`,`name` FROM `ttypes` ORDER BY name");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				if ($row['id']==$_POST['type']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>'; 
			}
			$qry->closeCursor();
			echo'	
		</select>';
	}
	?>
	<select style="width:160px"name="category" onchange="submit()">
	<?php
		if ($_POST['category']=='%') {echo '<option value="%" selected>'.T_('Toutes les catégories').'</option>';} else {echo '<option value="%" >'.T_('Toutes les catégories').'</option>';}	
		//case limit user service
		if ($rparameters['user_limit_service']==1 && $rright['admin']==0)
		{
			$qry=$db->prepare("SELECT id,name FROM tcategory WHERE service IN (SELECT service_id FROM tusers_services WHERE user_id=:user_id) ORDER BY tcategory.name");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if ($row['id'] == $_POST['category']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>'; 
			}
			$qry->closeCursor();
		} else {
			$qry=$db->prepare("SELECT id,name FROM tcategory ORDER BY tcategory.name");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if ($row['id'] == $_POST['category']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>'; 
			}
			$qry->closeCursor();
		}				
		?>
	</select> 
	<select style="width:160px"name="criticality" onchange="submit()">
		<?php
		if ($_POST['criticality']=='%') {echo '<option value="%" selected>'.T_('Toutes les criticités').'</option>';} else {echo '<option value="%" >'.T_('Toutes les criticités').'</option>';}																					
		if ($rparameters['user_limit_service']==1 && $rright['admin']==0) //case limit user service
		{
			$qry=$db->prepare("SELECT id,name FROM tcriticality WHERE service IN (SELECT service_id FROM tusers_services WHERE user_id=:user_id) ORDER BY number");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if ($row['id'] == $_POST['criticality']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>'; 
			}
			$qry->closeCursor();
		} else { //case no limit user service
			$qry=$db->prepare("SELECT id,name FROM tcriticality ORDER BY number");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if ($row['id'] == $_POST['criticality']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>'; 
			}
			$qry->closeCursor();
		}			
		?>
	</select> 
	<select style="width:160px" name="state" onchange="submit()">
		<?php
		if($_POST['state']=='%') {echo '<option value="%" selected>Tous les états</option>';} else {echo '<option value="%" >Tous les états</option>';}
		$qry=$db->prepare("SELECT `id`,`name` FROM `tstates` ORDER BY number");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			if($_POST['state']==$row['id']) 
			{
				echo '<option value="'.$row['id'].'" selected >'.$row['name'].'</option>';
			} else {
				echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}
		}
		$qry->closeCursor();
		if($rparameters['meta_state']==1) {
			if($_POST['state']=='meta') 
			{
				echo '<option value="meta" selected>A traiter</option>';
			} else {
				echo '<option value="meta">A traiter</option>';
			}
		}
		?>
	</select>
	<div class="space-2"></div>
	<?php echo T_('Période'); ?> :
	<select style="width:160px"name="month" onchange="submit()">
		<option value="%" <?php if ($_POST['month'] == '%')echo "selected" ?>><?php echo T_('Tous les mois'); ?></option>
		<option value="01"<?php if ($_POST['month'] == '1')echo "selected" ?>><?php echo T_('Janvier'); ?></option>
		<option value="02"<?php if ($_POST['month'] == '2')echo "selected" ?>><?php echo T_('Février'); ?></option>
		<option value="03"<?php if ($_POST['month'] == '3')echo "selected" ?>><?php echo T_('Mars'); ?></option>
		<option value="04"<?php if ($_POST['month'] == '4')echo "selected" ?>><?php echo T_('Avril'); ?></option>
		<option value="05"<?php if ($_POST['month'] == '5')echo "selected" ?>><?php echo T_('Mai'); ?></option>
		<option value="06"<?php if ($_POST['month'] == '6')echo "selected" ?>><?php echo T_('Juin'); ?></option>
		<option value="07"<?php if ($_POST['month'] == '7')echo "selected" ?>><?php echo T_('Juillet'); ?></option>
		<option value="08"<?php if ($_POST['month'] == '8')echo "selected" ?>><?php echo T_('Aout'); ?></option>
		<option value="09"<?php if ($_POST['month'] == '9')echo "selected" ?>><?php echo T_('Septembre'); ?></option>
		<option value="10"<?php if ($_POST['month'] == '10')echo "selected" ?>><?php echo T_('Octobre'); ?></option>
		<option value="11"<?php if ($_POST['month'] == '11')echo "selected" ?>><?php echo T_('Novembre'); ?></option>	
		<option value="12"<?php if ($_POST['month'] == '12')echo "selected" ?>><?php echo T_('Décembre'); ?></option>	
	</select>
	<select style="width:160px"name="year" onchange="submit()">
		<?php
		echo '<option value="%"'; if ($_POST['year'] == '%') {echo 'selected';} echo ' >'.T_('Toutes les années').'</option>';
		
		$qry=$db->prepare("SELECT DISTINCT YEAR(date_create) AS year FROM `tincidents` WHERE date_create not like '0000-00-00 00:00:00' ORDER BY YEAR(date_create)");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			$selected='';
			if ($_POST['year']==$row['year']) {$selected="selected";}
			echo '<option value="'.$row['year'].'" '.$selected.' >'.$row['year'].'</option>';
		}
		$qry->closeCursor();
		?>
	</select>
</form>
<div class="space-12""></div>
<?php
	//case filter by meta states
	if($rparameters['meta_state'] && $_POST['state']=='meta')
	{
		$where_state="(tincidents.state='1' OR tincidents.state='2' OR tincidents.state='6')";
	} else {
		$where_state="tincidents.state LIKE '$_POST[state]'";
	}
	//call all graphics files from ./stats directory
	require('./stats/line_tickets.php');
	echo "<br />";
	echo "<a name=\"chart1\"></a>";
	require('./stats/pie_tickets_tech.php');
	echo "<br />";
	echo "<a name=\"chart3\"></a>";
	echo "<hr />";
	require('./stats/pie_states.php');
	echo "<br ";
	echo "<a name=\"chart2\"></a>";
	echo "<hr />";
	require('./stats/pie_cat.php');
	echo "<br />";
	//display pie service if exist services
	$qry=$db->prepare("SELECT COUNT(id) FROM `tservices` WHERE id!=0");
	$qry->execute();
	$row=$qry->fetch();
	$qry->closeCursor();
	
	if($row[0]>0)
	{
		echo "<a name=\"chart7\"></a>";
		echo "<hr />";
		require('./stats/pie_services.php');
		echo "<br />";
	}
	if($company_filter==1 && $rparameters['user_advanced']==1)
	{
		echo "<a name=\"chart8\"></a>";
		echo "<hr />";
		require('./stats/pie_company.php');
		echo "<br />";
	}
	echo "<a name=\"chart6\"></a>";
	echo "<hr />";
	require('./stats/pie_load.php');
	echo "<br />";
	echo "<hr />";
	require('./stats/histo_load.php');
	echo "<hr />";
	require('./stats/tables.php');	
?>