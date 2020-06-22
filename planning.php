<?php
################################################################################
# @Name : planning.php
# @Desc : display planning
# @call : /menu.php
# @paramters : 
# @Author : Flox
# @Create : 28/12/2012
# @Update : 18/02/2015
# @Version : 3.0.11
################################################################################

// initialize variables 
if(!isset($_GET['view'])) $_GET['view'] = '';
if(!isset($mon_color)) $mon_color = '';
if(!isset($tue_color)) $tue_color = '';
if(!isset($wed_color)) $wed_color = '';
if(!isset($thu_color)) $thu_color = '';
if(!isset($fri_color)) $fri_color = '';
if(!isset($sat_color)) $sat_color = '';
if(!isset($sun_color)) $sun_color = '';
if(!isset($cursor)) $cursor = '';
if(!isset($previous)) $previous = '';
if(!isset($next)) $next = '';
if(!isset($_POST['technician'])) $_POST['technician']= $_SESSION['user_id'];

if(!isset($_GET['next'])) $_GET['next'] = '';
if(!isset($_GET['previous'])) $_GET['previous'] = '';
if(!isset($_GET['cursor'])) $_GET['cursor'] = '';
if(!isset($_GET['delete'])) $_GET['delete'] = '';

//default settings
if ($_GET['view']=='') $_GET['view']="week";
if ($next=='') $next=0;
if ($previous=='') $previous=0;

//calc dates
$cursor=$_GET['cursor']+$_GET['next']-$_GET['previous'];
$current = date("Y-m-d H:i");
$week = date("W")-1 + $cursor;
$year = date("Y");

$monday=strtotime(''.$year.'-01-01 +'.($week-1).' WEEK MONDAY');
$tuesday=strtotime(''.$year.'-01-01 +'.($week-1).' WEEK TUESDAY');
$wednesday=strtotime(''.$year.'-01-01 +'.($week-1).' WEEK WEDNESDAY');
$thursday=strtotime(''.$year.'-01-01 +'.$week.' WEEK THURSDAY');
$friday=strtotime(''.$year.'-01-01 +'.$week.' WEEK FRIDAY');
$saturday=strtotime(''.$year.'-01-01 +'.$week.' WEEK SATURDAY');
$sunday=strtotime(''.$year.'-01-01 +'.$week.' WEEK SUNDAY');

$frday = array ('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
$frmonth = array ('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

//Delete events
if($_GET['delete']!='')
{
//disable ticket
$query = "DELETE FROM tevents WHERE incident=$_GET[delete]";
$exec = mysql_query($query) or die('Erreur SQL !<br /><br />'.mysql_error());
}


//Select technician selection
if($_POST['technician']!='%')
{
   //Select name of technician
   $querytech= mysql_query("SELECT * FROM tusers WHERE id = $_POST[technician]"); 
   $resulttech=mysql_fetch_array($querytech);
   $displaytech='de  '.$resulttech['firstname'].' '.$resulttech['lastname'];
}
else
{
   $_POST['technician']='%';
   $displaytech='de tous les techniciens';
}

//Display Head
echo '
<div class="page-header position-relative">
	<h1>
		<i class="icon-calendar"></i>  
';
if ($_GET['view']=='day') echo 'Planning '.$displaytech.' du '.$frday[date('w')].' '.date("d/m/Y"); 
if ($_GET['view']=='week') echo 'Planning '.$displaytech.' du '.date("d/m/Y", strtotime('First Monday January '.$year.' +'.($week-1).' Week')).' au '.date("d/m/Y", strtotime('First Monday January '.$year.' +'.$week.' Week -1 day')); 
echo '
	</h1>
</div>';
echo'
	<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
		<button title="Semaine précédente" onclick=\'window.location.href="./index.php?page=planning&view=week&cursor='.$cursor.'&previous=1";\' class="btn btn-info">
			<i class="icon-arrow-left"></i>
		</button>
		<button title="Semaine suivante" onclick=\'window.location.href="./index.php?page=planning&view=week&cursor='.$cursor.'&next=1";\' class="btn btn-info">
			<i class="icon-arrow-right"></i>
		</button>
		&nbsp;&nbsp;
		<th colspan="8">Semaine '.date("W", strtotime('First Monday January '.$year.' +'.($week-1).' Week')).' </th>
	</div>
	<br />
';
?>
<form method="post" action="" name="technician">
	Technicien:
  <select name="technician" onchange=submit()>
	 <?php
	 $query = mysql_query("SELECT * FROM tusers WHERE profile=0 OR profile=4 and disable=0");            
	 while ($row=mysql_fetch_array($query)) {
		if ($row['id'] == $_POST['technician']) $selected1="selected" ;
		if ($row['id'] == $_POST['technician']) $find="1" ;
		echo "<option value=\"$row[id]\" $selected1>$row[firstname] $row[lastname]</option>"; 
		$selected1="";
	 } 
	 echo "<option value=\"%\" >Tous</option>";
	 if ($find!="1") echo "<option value=\"%\" selected>Tous</option>";                                    
	 ?>
  </select> 
</form>
<br />
<?php
////////////////////////////////////////////////////////////WEEK VIEW//////////////////////////////////////////////////////////////////
if ($_GET['view']=='week') 
{
	$period='Semaine '.date("W"); 
	$date=date("Y-m-d");
	//find day for display green on currrent day
	if(date("D")=='Mon' && date("j")==date("d", $monday)) $mon_color='bgcolor="#CEF6CE"';
	if(date("D")=='Tue' && date("j")==date("d", $tuesday)) $tue_color='bgcolor="#CEF6CE"';
	if(date("D")=='Wed' && date("j")==date("d", $wednesday)) $wed_color='bgcolor="#CEF6CE"';
	if(date("D")=='Thu' && date("j")==date("d", $thursday)) $thu_color='bgcolor="#CEF6CE"';
	if(date("D")=='Fri' && date("j")==date("d", $friday)) $fri_color='bgcolor="#CEF6CE"';

	$sat_color='bgcolor="#F2F5A9"';
	$sun_color='bgcolor="#F2F5A9"';
	
	echo"<table class=\"table table-striped table-bordered table-hover\">";
	//Display first Line
	echo '<tr>
			<td></td>
			<td '.$mon_color.' align="center">
				<b>
				'.$frday[date("w", $monday)].'
				'.date("d", $monday).'
				'.$frmonth[date("m", $monday)-1].' 
				</b>
			</td>
			<td '.$tue_color.' align="center">
				<b>
				'.$frday[date("w", $tuesday)].'
				'.date("d", $tuesday).'
				'.$frmonth[date("m", $tuesday)-1].' 
				</b>
			</td>
			<td '.$wed_color.' align="center">
				<b>
				'.$frday[date("w", $wednesday)].'
				'.date("d", $wednesday).'
				'.$frmonth[date("m", $wednesday)-1].' 
				</b>
			</td>
			<td '.$thu_color.' align="center">
				<b>
				'.$frday[date("w", $thursday)].'
				'.date("d", $thursday).'
				'.$frmonth[date("m", $thursday)-1].' 
				</b>
			</td>
			<td '.$fri_color.' align="center">
				<b>
				'.$frday[date("w", $friday)].'
				'.date("d", $friday).'
				'.$frmonth[date("m", $friday)-1].' 
				</b>
			</td>
			<td '.$sat_color.' align="center">
				<b>
				'.$frday[date("w", $saturday)].'
				'.date("d", $saturday).'
				'.$frmonth[date("m", $saturday)-1].' 
				</b>
			</td>
			<td '.$sun_color.' align="center">
				<b>
				'.$frday[date("w", $sunday)].'
				'.date("d", $sunday).'
				'.$frmonth[date("m", $sunday)-1].' 
				</b>
			</td align="center">
	</tr>';
	//Display each time line
	for ($i = 7; $i <= 19; $i++) 
	{ 

		echo '
		<tr>
			<td><b>'.$i.'h</b></td>
			<td '.$mon_color.'>';
				// find Monday date
				$date=date("Y-m-d", $monday);
				$query= mysql_query("SELECT * FROM tevents WHERE technician LIKE '$_POST[technician]' AND (date_start='$date $i:00' OR date_end='$date $i:00' OR (date_start<'$date $i:00' AND date_end>'$date $i:00'))");
				while ($row=mysql_fetch_array($query))
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= mysql_query( "SELECT * FROM `tincidents` WHERE id=$row[incident] ");
					$rowi = mysql_fetch_array($queryi);
					//Select name of technician
					if($_POST['technician']=='%') $querytech= mysql_query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= mysql_query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=mysql_fetch_array($querytech);
					echo '<a title="Voir le ticket '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="Supprimer cet évenement" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
			echo '
			</td>
			<td '.$tue_color.' >';
				// find Tuesday date
				$date=date("Y-m-d", $tuesday);
				$query= mysql_query("SELECT * FROM tevents WHERE technician LIKE '$_POST[technician]' AND (date_start='$date $i:00' OR date_end='$date $i:00' OR (date_start<'$date $i:00' AND date_end>'$date $i:00'))");
				while ($row=mysql_fetch_array($query))
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= mysql_query( "SELECT * FROM `tincidents` WHERE id=$row[incident] ");
					$rowi = mysql_fetch_array($queryi);
					//Select name of technician
					if($_POST['technician']=='%') $querytech= mysql_query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= mysql_query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=mysql_fetch_array($querytech);
					echo '<a title="Voir le ticket '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="Supprimer cet évenement" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
			echo '
			</td>
			<td '.$wed_color.'>';
				// find Wednesday date
				$date=date("Y-m-d", $wednesday);
				$query= mysql_query("SELECT * FROM tevents WHERE technician LIKE '$_POST[technician]' AND (date_start='$date $i:00' OR date_end='$date $i:00' OR (date_start<'$date $i:00' AND date_end>'$date $i:00'))");
				//echo "SELECT * FROM tevents WHERE technician LIKE '$_POST[technician]' AND (date_start='$date $i:00' OR date_end='$date $i:00' OR (date_start<'$date $i:00' AND date_end>'$date $i:00'))";
				while ($row=mysql_fetch_array($query))
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= mysql_query( "SELECT * FROM `tincidents` WHERE id=$row[incident] ");
					$rowi = mysql_fetch_array($queryi);
					//Select name of technician
					if($_POST['technician']=='%') $querytech= mysql_query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= mysql_query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=mysql_fetch_array($querytech);
					echo '<a title="Voir le ticket '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="Supprimer cet évenement" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
			echo '
			</td>
			<td '.$thu_color.'>';
				// find Thursday date
				$date=date("Y-m-d", $thursday);
				$query= mysql_query("SELECT * FROM tevents WHERE technician LIKE '$_POST[technician]' AND (date_start='$date $i:00' OR date_end='$date $i:00' OR (date_start<'$date $i:00' AND date_end>'$date $i:00'))");
				while ($row=mysql_fetch_array($query))
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= mysql_query( "SELECT * FROM `tincidents` WHERE id=$row[incident] ");
					$rowi = mysql_fetch_array($queryi);
					//Select name of technician
					if($_POST['technician']=='%') $querytech= mysql_query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= mysql_query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=mysql_fetch_array($querytech);
					echo '<a title="Voir le ticket '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="Supprimer cet évenement" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
			echo '
			</td>
			<td '.$fri_color.'>';
				// find Friday date
				$date=date("Y-m-d", $friday);
				$query= mysql_query("SELECT * FROM tevents WHERE technician LIKE '$_POST[technician]' AND (date_start='$date $i:00' OR date_end='$date $i:00' OR (date_start<'$date $i:00' AND date_end>'$date $i:00'))");
				while ($row=mysql_fetch_array($query))
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= mysql_query( "SELECT * FROM `tincidents` WHERE id=$row[incident] ");
					$rowi = mysql_fetch_array($queryi);
					//Select name of technician
					if($_POST['technician']=='%') $querytech= mysql_query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= mysql_query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=mysql_fetch_array($querytech);
					echo '<a title="Voir le ticket '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="Supprimer cet évenement" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
			echo '
			</td>
			<td '.$sat_color.'>';
				// find Saturday date
				$date=date("Y-m-d", $saturday);
				$query= mysql_query("SELECT * FROM tevents WHERE technician LIKE '$_POST[technician]' AND (date_start='$date $i:00' OR date_end='$date $i:00' OR (date_start<'$date $i:00' AND date_end>'$date $i:00')) ");
				while ($row=mysql_fetch_array($query))
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= mysql_query( "SELECT * FROM `tincidents` WHERE id=$row[incident] ");
					$rowi = mysql_fetch_array($queryi);
					//Select name of technician
					if($_POST['technician']=='%') $querytech= mysql_query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= mysql_query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=mysql_fetch_array($querytech);
					echo '<a title="Voir le ticket '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="Supprimer cet évenement" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
			echo '
			</td>
			<td '.$sun_color.'>';
				// find Sunday date
				$date=date("Y-m-d", $sunday);
				$query= mysql_query("SELECT * FROM tevents WHERE technician LIKE '$_POST[technician]' AND (date_start='$date $i:00' OR date_end='$date $i:00' OR (date_start<'$date $i:00' AND date_end>'$date $i:00'))");
				while ($row=mysql_fetch_array($query))
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= mysql_query( "SELECT * FROM `tincidents` WHERE id=$row[incident] ");
					$rowi = mysql_fetch_array($queryi);
					//Select name of technician
					if($_POST['technician']=='%') $querytech= mysql_query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= mysql_query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=mysql_fetch_array($querytech);
					echo '<a title="Voir le ticket '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="Supprimer cet évenement" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
			echo '
			</td>
		</tr>';
	}
		
	echo "</table>";
} 
?>