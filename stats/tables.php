<?php
################################################################################
# @Name : tables.php
# @Desc : Display Statistics by catgories
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 13/03/2014
# @Version : 3.0.7
################################################################################


echo '
<div class="col-xs-12 col-sm-3 widget-container-span ui-sortable">
	<div class="widget-box" style="opacity: 1; z-index: 0;">
		<div class="widget-header header-color-blue">
			<h5 class="bigger lighter">
				Délais moyen de résolution par technicien
			</h5>
		</div>
		<div class="widget-body">
			<div class="widget-main no-padding">
				<table class="table table-striped table-bordered table-hover">
					<thead class="thin-border-bottom">
						<tr>
							<th>
								<i class="icon-user"></i>
								Techniciens
							</th>

							<th>
								<i class="icon-bullseye"></i>
								jours
							</th>
							
						</tr>
					</thead>
					<tbody>
					';
					$query = mysql_query("select tusers.firstname, AVG(TO_DAYS(date_res) - TO_DAYS(date_create)) as jour from tincidents INNER JOIN tusers ON (tincidents.technician=tusers.id )where tincidents.technician NOT LIKE '0' AND tincidents.date_res NOT LIKE '0000-00-00' AND tincidents.date_create NOT LIKE '0000-00-00' AND tusers.disable='0' group by tincidents.technician ORDER BY jour ASC");
					while ($row=mysql_fetch_array($query)) {echo "<tr><td>$row[0]</td><td>$row[1]j</td></tr>";} 
					echo '
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
';

echo '
<div class="col-xs-12 col-sm-3 widget-container-span ui-sortable">
	<div class="widget-box" style="opacity: 1; z-index: 0;">
		<div class="widget-header header-color-blue">
			<h5 class="bigger lighter">
				Demandes par criticité
			</h5>
		</div>
		<div class="widget-body">
			<div class="widget-main no-padding">
				<table class="table table-striped table-bordered table-hover">
					<thead class="thin-border-bottom">
						<tr>
							<th>
								<i class="icon-user"></i>
								Criticité
							</th>

							<th>
								<i class="icon-bullseye"></i>
								Nombre
							</th>
							
						</tr>
					</thead>
					<tbody>
					';
					$query = mysql_query("select tcriticality.name, count(*) as number FROM tincidents INNER JOIN tcriticality ON (tincidents.criticality=tcriticality.id ) WHERE tincidents.disable='0' group by tincidents.criticality  order by tcriticality.number ASC");
					while ($row=mysql_fetch_array($query)) {echo "<tr><td>$row[0]</td><td>$row[1]</td></tr>";} 
					echo '
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
';

echo '
<div class="col-xs-12 col-sm-3 widget-container-span ui-sortable">
	<div class="widget-box" style="opacity: 1; z-index: 0;">
		<div class="widget-header header-color-blue">
			<h5 class="bigger lighter">
				Demandes par priorité
			</h5>
		</div>
		<div class="widget-body">
			<div class="widget-main no-padding">
				<table class="table table-striped table-bordered table-hover">
					<thead class="thin-border-bottom">
						<tr>
							<th>
								<i class="icon-user"></i>
								Priorité
							</th>

							<th>
								<i class="icon-bullseye"></i>
								Nombre
							</th>
							
						</tr>
					</thead>
					<tbody>
					';
					$query = mysql_query("select tpriority.name, count(*) as number FROM tincidents INNER JOIN tpriority ON (tincidents.priority=tpriority.id ) WHERE tincidents.disable='0' group by tincidents.priority order by tpriority.number ASC");
					while ($row=mysql_fetch_array($query)) {echo "<tr><td>$row[0]</td><td>$row[1]</td></tr>";} 
					echo '
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
';

echo '
<div class="col-xs-12 col-sm-3 widget-container-span ui-sortable">
	<div class="widget-box" style="opacity: 1; z-index: 0;">
		<div class="widget-header header-color-blue">
			<h5 class="bigger lighter">
				Top 10 des demandeurs
			</h5>
		</div>
		<div class="widget-body">
			<div class="widget-main no-padding">
				<table class="table table-striped table-bordered table-hover">
					<thead class="thin-border-bottom">
						<tr>
							<th>
								<i class="icon-user"></i>
								Utilisateurs
							</th>

							<th>
								<i class="icon-bullseye"></i>
								Nombre
							</th>
							
						</tr>
					</thead>
					<tbody>
					';
					$query = mysql_query("select tusers.firstname as Util, tusers.lastname, count(*) as demandes FROM tincidents INNER JOIN tusers ON (tincidents.user=tusers.id ) WHERE tincidents.disable='0' group by tincidents.user order by demandes DESC LIMIT 10");
						while ($row=mysql_fetch_array($query)) {echo "<tr><td>$row[0] $row[1]</td><td>$row[2]</td></tr>";}
					echo '
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
';

echo '
<div class="col-xs-12 col-sm-3 widget-container-span ui-sortable">
	<div class="widget-box" style="opacity: 1; z-index: 0;">
		<div class="widget-header header-color-blue">
			<h5 class="bigger lighter">
				TOP 10 consommateurs de temps
			</h5>
		</div>
		<div class="widget-body">
			<div class="widget-main no-padding">
				<table class="table table-striped table-bordered table-hover">
					<thead class="thin-border-bottom">
						<tr>
							<th>
								<i class="icon-user"></i>
								Utilisateurs
							</th>

							<th>
								<i class="icon-bullseye"></i>
								Heures
							</th>
							
						</tr>
					</thead>
					<tbody>
					';
					$query = mysql_query("
					select tusers.firstname as Util, tusers.lastname, sum(time) as temps 
					FROM tincidents 
					INNER JOIN tusers ON (tincidents.user=tusers.id )  
					WHERE tincidents.time NOT LIKE '0' AND
					tincidents.time NOT LIKE '0' AND
					tincidents.disable='0'
					group by tincidents.user
					order by sum(time) DESC limit 10");
					while ($row=mysql_fetch_array($query)) 
					{
						$tps=$row[2]/60;
						$tps=round($tps);
						echo "<tr><td>$row[0] $row[1]</td><td width=\"20\">$tps h</td></tr>";
					} 
					echo '
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
';
?>