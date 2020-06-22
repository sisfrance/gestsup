<style>
@media print {
	.sidebar,
	.breadcrumbs,
	.navbar {
		display: none;
	}
	.main-content {
		margin-left: 0 !important;
	}
}
</style>
<?php
$totalincidents = 0;
$totaltime = 0;

if(isset($_POST['clientid'])) {
	$client_id = $_POST['clientid'];
} else {
	$client_id = null;
}

if(isset($_POST['category'])) {
	$category = $_POST['category'];
} else {
	$category = null;
}


if(isset($_POST['toyear'])) {
	if(isset($_POST['tomouth'])) {
		if(strlen($_POST['tomouth']) < 2) {
			$tomouth = '0'.$_POST['tomouth'];
		} else {
			$tomouth = $_POST['tomouth'];
		}
		$date_create_min = $_POST['toyear'].'-'.$tomouth.'-00 00:00:00';
	} else {
		$date_create_min = null;
	}
} else {
	$date_create_min = null;
}

if(isset($_POST['ayear'])) {
	if(isset($_POST['amouth'])) {
		if(strlen($_POST['amouth']) < 2) {
			$amouth = '0'.$_POST['amouth'];
		} else {
			$amouth = $_POST['amouth'];
		}
		$date_create_max = $_POST['ayear'].'-'.$amouth.'-31 23:59:59';
	} else {
		$date_create_max = null;
	}
} else {
	$date_create_max = null;
}

$client_list = mysql_query("
	SELECT id, firstname, lastname
	FROM tusers
	WHERE profile < 3
");

?>
<form action="" method="POST">
	<div class="row" style="height: 50px">
		<div class="col-xs-3">
			<label>Client:</label>
			<select name="clientid" style="width: 160px">
			<?php
			if($_POST['clientid']) {
				$client_one = mysql_query("
					SELECT id, firstname, lastname
					FROM tusers
					WHERE id = ".$_POST['clientid']."
				");
				$one = mysql_fetch_array($client_one);
				echo '<option value="'.$_POST['clientid'].'" selected>'.$one['lastname'].' '.$one['firstname'].'</option>';
			}
			while($client = mysql_fetch_array($client_list)) {
				echo '<option value="'.$client["id"].'">'.$client["lastname"].' '.$client["firstname"].'</option>';
			}
			?>
			</select>
		</div>
		<div class="col-xs-3">
			<label>Cat.:</label>
			<select name="category">
				<option value="%">Toutes</option>
				<?php
				if($_POST['category'] && $_POST['category'] == "%") {
					echo '<option value="%" selected>Toutes</option>';
				} elseif($_POST['category'] && $_POST['category'] != "%") {
					$cat_un = mysql_query("
						SELECT *
						FROM tcategory
						WHERE id = ".$_POST['category']."
					");
					$un = mysql_fetch_array($cat_un);
					echo '<option value="'.$_POST['category'].'" selected>'.$un['name'].'</option>';
				}
				$cat_list = mysql_query("
					SELECT *
					FROM tcategory
				");
				while($cat = mysql_fetch_array($cat_list)) {
					echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="col-xs-2">
			<label>De:</label>
			<select name="tomouth">
				<?php
				if($_POST['tomouth']) echo '<option selected>'.$_POST['tomouth'].'</option>';
				for($mouth = 1; $mouth <= 12; $mouth++) {
					echo '<option>'.$mouth.'</option>';
				}
				?>
			</select>
			<select name="toyear">
				<?php
				if($_POST['toyear']) echo '<option selected>'.$_POST['toyear'].'</option>';
				for($year = 2015; $year <= date('Y'); $year++) {
					echo '<option>'.$year.'</option>';
				}
				?>
			</select>
		</div>
		<div class="col-xs-2">
			<label>A:</label>
			<select name="amouth">
				<?php
				$dontactive = false;
				if($_POST['amouth']) {
					echo '<option selected>'.$_POST['amouth'].'</option>';
					$dontactive = true;
				}
				$active = null;
				for($mouth = 1; $mouth <= 12; $mouth++) {
					$mouthif = $mouth - 1;
					if($mouthif == date('n') && $dontactive === false) $active = 'selected';
					echo '<option '.$active.'>'.$mouth.'</option>';
					$active = null;
				}
				?>
			</select>
			<select name="ayear">
				<?php
				$dontactive = false;
				if($_POST['ayear']) {
					echo '<option selected>'.$_POST['ayear'].'</option>';
					$dontactive = true;
				}
				$active = null;
				for($year = 2015; $year <= date('Y'); $year++) {
					if($year == date('Y') && $dontactive === false) $active = 'selected';
					echo '<option '.$active.'>'.$year.'</option>';
					$active = null;
				}
				?>
			</select>
		</div>
		<div class="col-xs-2">
			<input name="submit" type="submit" class="btn btn-primary btn-sm" value="Rechercher">
			&nbsp;
			<a class="btn btn-success btn-sm" href="javascript:window.print()"><i class="icon-print"></i></a>
		</div>
	</div>
</form>
<?php if(isset($_POST['submit'])) { 

	if(!is_null($client_id)) {
		$client_id = "AND ti.user = $client_id";
	}
	
	if(!is_null($category)) {
		if($category == "%") {
			$category = null;
		} else {
			$category = "AND ti.category = $category";
		}
	}

	if(!is_null($date_create_min)
	&& !is_null($date_create_max)) {
		$date_create = "AND ti.date_create BETWEEN '$date_create_min' AND '$date_create_max'";
		
	} else {
		$date_create = null;
	}
	
	$requete = "
		SELECT ti.id AS tiid, title, ti.date_create, tsubcat.name AS tscname, tcategory.name AS tcname,
		tusers.lastname AS tulastname, ti.time AS titime
		FROM tincidents AS ti
		INNER JOIN tusers ON tusers.id = ti.user
		LEFT JOIN tcategory ON tcategory.id = ti.category
		LEFT JOIN tsubcat ON tsubcat.id = ti.subcat
		WHERE ti.disable = '0'
		$category
		$date_create
		$client_id
	";
	
	$execution = mysql_query($requete) or die('Erreur SQL !<br /><br />'.mysql_error());

	if(mysql_num_rows($execution) > 0) {
?>
<br>
<table class="table table-bordered table-hover">
<thead>
   <tr>
       <th>ID</th>
       <th>Nom du client</th>
       <th>Titre</th>
       <th>Catégorie</th>
       <th>Sous catégorie</th>
       <th>Temps d'intervention</th>
       <th>Date de création</th>
   </tr>
</thead>
<tbody>
<?php while($tableau = mysql_fetch_array($execution)) { ?>
   <tr>
       <td><?= $tableau['tiid'] ?></td>
       <td><?= $tableau['tulastname'] ?></td>
       <td><?= substr($tableau['title'], 0, 20) ?>...</td>
       <td><?= $tableau['tcname'] ?></td>
       <td><?= $tableau['tscname'] ?></td>
       <td><?= $tableau['titime'] ?> minutes</td>
       <td><?= $tableau['date_create'] ?></td>
   </tr>
<?php
$totalincidents++;
$totaltime = $totaltime + $tableau['titime'];
} ?>
</tbody>
</table>
<table class="table table-bordered table-hover">
<thead>
	<tr>
       <th>Global</th>
       <th>Valeur</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>Temps total</td>
		<td><?= sprintf('%02d heures %02d minutes', floor($totaltime/60), $totaltime%60); ?></td>
	</tr>
	<tr>
		<td>Nombre d'incidents</td>
		<td><?= $totalincidents ?> interventions</td>
	</tr>
</tbody>
</table>
<?php } else { echo 'Cet utilisateur n\'a aucunes données'; } } ?>