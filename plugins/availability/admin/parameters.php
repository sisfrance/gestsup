<?php
################################################################################
# @Name : ./plugins/availability/admin/parameters.php
# @Desc : admin parameters for availability plugin
# @call : /admin/parameters.php
# @parameters : X
# @Author : Flox
# @Create : 28/04/2015
# @Update : 28/04/2015
# @Version : 3.0.11
################################################################################

?>
<div class="profile-info-row">
	<div class="profile-info-name"> 
		<i class="icon-time"></i>
		Disponibilité:
	</div>
	<div class="profile-info-value">
			<input class="ace" type="checkbox" <?php if ($rparameters['availability']==1) echo "checked"; ?> name="availability" value="1">
			<span class="lbl">&nbsp;Activer la fonction Disponibilité</span>
			<i title="Active le suivi des catégories afin de produire des statistiques de disponibilité (ex: 98.00% de disponibilité d'une application)." class="icon-question-sign blue"></i>
			<?php
			    if ($rparameters['availability']==1)
			    {
		    	    echo '
		    	        <div class="space-4"></div>
		    	        &nbsp; &nbsp; &nbsp;<i class="icon-circle green"></i> 1 - Surveiller toutes les catégories:&nbsp;
		    	        <label for="availability_all_cat">
							<input type="radio" class="ace" value="1" name="availability_all_cat"'; if ($rparameters['availability_all_cat']==1) {echo "checked";} echo '> <span class="lbl"> Oui </span>
							<input type="radio" class="ace" value="0" name="availability_all_cat"'; if ($rparameters['availability_all_cat']==0) {echo "checked";} echo '  > <span class="lbl"> Non </span>
						</label>
		    	    ';
		    	    if ($rparameters['availability_all_cat']==0)
		    	    {
		    	        echo '
		    	            <div class="space-4"></div>
		    	            &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		    	            <i class="icon-caret-right blue"></i> Selection des catégories ou sous-catégories à surveiller:<br />
		    	            ';
		    	            //display availability list
					        $query = mysql_query("SELECT * FROM `tavailability`");
					    	while ($row=mysql_fetch_array($query))
							{
							   
								$cname= mysql_query("SELECT name FROM `tcategory` WHERE id='$row[category]'"); 
								$cname= mysql_fetch_array($cname);
								if ($row['subcat']!='')
								{
									$sname= mysql_query("SELECT name FROM `tsubcat` WHERE id='$row[subcat]'"); 
									$sname= mysql_fetch_array($sname);
								} else {$sname='';}
								echo "
									&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
									<i class=\"icon-caret-right green\"></i> ($cname[name] > $sname[0]) 
									<a title=\"Supprimer cette catégorie\" href=\"./index.php?page=admin&subpage=parameters&tab=function&deleteavailability=$row[id]\"><i class=\"icon-trash red bigger-120\"></i></a>
									<br />
								";
                             }
							//display add cat form
							echo'
							&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		    	            <i class="icon-caret-right blue"></i> 
							Ajouter la	Catégorie:
							<select name="category" onchange="submit()" style="width:100px; display:inline;" >
								<option value="%"></option>';
								$query = mysql_query("SELECT * FROM tcategory ORDER BY name");
								while ($row=mysql_fetch_array($query)) 
								{
								if ($_POST['category']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
								}
							echo'
							</select>
						    	&nbsp;et la Sous-Catégorie:
							<select name="subcat" onchange="submit()" style="width:90px display:inline;">
								<option value="%"></option>';
								if($_POST['category']!='%')
								{$query = mysql_query("SELECT * FROM tsubcat WHERE cat LIKE $_POST[category] ORDER BY name");}
								else
								{$query = mysql_query("SELECT * FROM tsubcat ORDER BY name");}
								while ($row=mysql_fetch_array($query))
								{
									
									if ($_POST['subcat']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
								} 
								echo '
							</select>
							<div class="space-4"></div>
							';
		    	    }
		    	    echo '
		    	    <div class="space-4"></div>
		    	    &nbsp; &nbsp; &nbsp;<i class="icon-circle green"></i>
		    	    2 - Condition de prise en compte d\'un ticket:&nbsp;
		    	    <select name="availability_condition_type" onchange="submit()" style="display:inline;">
		    	        <option value="" '; if($_POST['availability_condition_type']=='' && $rparameters['availability_condition_type']=='') {echo ' selected';} echo ' ></option>
		    	        <option value="types" '; if($_POST['availability_condition_type']=='types' ) {echo ' selected';} echo ' >Type</option>
		    	        <option value="criticality" '; if($_POST['availability_condition_type']=='criticality') {echo ' selected';} echo '>Criticité</option>
		    	    </select> 
		    	    ';
			    	if($_POST['availability_condition_type'] || $rparameters['availability_condition_value']!='' )
		    	    {
		    	    	if ($_POST['availability_condition_type']) $table="$_POST[availability_condition_type]"; else $table="$rparameters[availability_condition_type]";
		    	        $query = mysql_query("SELECT * FROM t$table ORDER BY name");
		    	        echo 'est ';
		    	        echo '<select name="availability_condition_value" >';
							while ($row=mysql_fetch_array($query))
							{
								echo '<option '; if($rparameters['availability_condition_value']==$row['id']) {echo 'selected';} echo ' value="'.$row['id'].'">'.$row['name'].'</option>';
							} 
						echo '</select>';
		    	    }
		    	    echo'
		    	    <div class="space-4"></div>
		    	    &nbsp; &nbsp; &nbsp;<i class="icon-circle green"></i>
		    	    3 - Dépendances:&nbsp;
		    	    <label for="availability_dep">
							<input type="radio" class="ace" value="1" name="availability_dep"'; if ($rparameters['availability_dep']==1) {{echo "checked";}} echo '> <span class="lbl"> Oui </span>
							<input type="radio" class="ace" value="0" name="availability_dep"'; if ($rparameters['availability_dep']==0) echo "checked"; echo '  > <span class="lbl"> Non </span>
					</label>
					<i title="Permet de définir des sous-catégories qui seront comptabilisées dans toutes les statistiques si elles possèdent la même condition. (ex: un ticket réseau critique, entraine une indisponibilité d\'une application)" class="icon-question-sign blue"></i>
					';
				    if ($rparameters['availability_dep']==1)
		    	    {
		    	        echo '
		    	            <div class="space-4"></div>
		    	            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		    	            <i class="icon-caret-right blue"></i> Liste des sous-catégories impactant toutes les catégories surveillées:<br />
		    	            ';
		    	            //display availability dependancy list
					        $query = mysql_query("SELECT * FROM `tavailability_dep`");
					    	while ($row=mysql_fetch_array($query))
							{
								$cname= mysql_query("SELECT name FROM `tcategory` WHERE id='$row[category]'"); 
								$cname= mysql_fetch_array($cname);
								if ($row['subcat']!='')
								{
									$sname= mysql_query("SELECT name FROM `tsubcat` WHERE id='$row[subcat]'"); 
									$sname= mysql_fetch_array($sname);
								} else {$sname='';}
								echo "
									&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
									<i class=\"icon-caret-right green\"></i> ($cname[name] > $sname[0]) 
									<a title=\"Supprimer cette catégorie\" href=\"./index.php?page=admin&subpage=parameters&tab=function&deleteavailabilitydep=$row[id]\"><i class=\"icon-trash red bigger-120\"></i></a>
									<br />
								";
                             }
		    	            //display add cat form
							echo'
							&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		    	            <i class="icon-caret-right blue"></i> 
							Ajouter la	Catégorie:
							<select name="depcategory" onchange="submit()" style="width:100px; display:inline;" >
								<option value="%"></option>';
								$query = mysql_query("SELECT * FROM tcategory ORDER BY name");
								while ($row=mysql_fetch_array($query)) 
								{
								if ($_POST['depcategory']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
								}
							echo'
							</select>
						    	&nbsp;et la Sous-Catégorie:
							<select name="depsubcat" onchange="submit()" style="width:90px display:inline;">
								<option value="%"></option>';
								if($_POST['depcategory']!='%')
								{$query = mysql_query("SELECT * FROM tsubcat WHERE cat LIKE $_POST[depcategory] ORDER BY name");}
								else
								{$query = mysql_query("SELECT * FROM tsubcat ORDER BY name");}
								while ($row=mysql_fetch_array($query))
								{
									
									if ($_POST['depsubcat']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
								} 
								echo '
							</select>
							<div class="space-4"></div>
							';
		    	    }
		    	    //target tx part
		    	   	echo'
		    	    <div class="space-4"></div>
		    	    &nbsp; &nbsp; &nbsp;<i class="icon-circle green"></i>
		    	    4 - Définition des taux de disponibilités cible par années, par sous-catégories:&nbsp;
					<i title="Permet de fixer des objectifs de disponibilité, pour chaque sous catégorie, qui peuvent fluctués chaque année." class="icon-question-sign blue"></i>
				   	<br />
				   ';
					//find tickets years and display subcat 
					$queryyears = mysql_query("SELECT DISTINCT YEAR(date_create) FROM tincidents ORDER BY YEAR(date_create) DESC");
					while ($rowyear=mysql_fetch_array($queryyears))
					{
						echo ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i class="icon-caret-right blue"></i> <b>'.$rowyear[0].'</b> <br />';
						$querysubcat = mysql_query("SELECT * FROM `tavailability`");
					    while ($rowsubcat=mysql_fetch_array($querysubcat))
					    {
					    	//get subcat name
					   		$sname= mysql_query("SELECT name FROM `tsubcat` WHERE id='$rowsubcat[subcat]'"); 
							$sname= mysql_fetch_array($sname);
							
							//get target tx data from tavailability_target table
							$targettx= mysql_query("SELECT * FROM `tavailability_target` WHERE subcat='$rowsubcat[subcat]' AND year='$rowyear[0]'"); 
							$targettx= mysql_fetch_array($targettx);
							
							echo ' &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; <i class="icon-caret-right green"></i> <u>'.$sname[0].'</u> 
							taux de disponibilité cible: 
							<input type="text" size="4" name="target_'.$rowyear[0].'_'.$rowsubcat['subcat'].'" value="'.$targettx['target'].'" />
							%
							<br />';
					    }
					} 
			    }
		?>
	</div>
</div>
