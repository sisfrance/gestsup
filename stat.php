<?php
################################################################################
# @Name : stat.php
# @Desc : Display Statistics
# @call : /menu.php
# @parameters : 
# @Author : Flox
# @Create : 12/01/2011
# @Update : 23/09/2014
# @Version : 3.0.10
################################################################################

//initialize variables 
if(!isset($select)) $select = '';
if(!isset($libgraph)) $libgraph = '';
if(!isset($selected)) $selected= '';
if(!isset($find)) $find= '';
if(!isset($subcat)) $subcat= '%';
if(!isset($category)) $category= '';
if(!isset($result)) $result= '';
if(!isset($monthm)) $monthm= '';
if(!isset($container)) $container= '';
if(!isset($_POST['tech'])) $_POST['tech']='';
if(!isset($_POST['type'])) $_POST['type']='';
if(!isset($_POST['criticality'])) $_POST['criticality']='';
if(!isset($_POST['category'])) $_POST['category']='';
if(!isset($_POST['subcat'])) $_POST['subcat']= '';
if(!isset($_POST['year'])) $_POST['year'] = '';
if(!isset($_POST['month'])) $_POST['month'] = '';
if(!isset($_POST['category'])) $_POST['category'] = '';

//default values 
if ($_POST['tech']=="") $_POST['tech']="%";
if ($_POST['criticality']=="") $_POST['criticality']="%";
if ($_POST['year']=="") $_POST['year']=date('Y');
if ($_POST['month']=="") $_POST['month']=date('m');
if ($_POST['type']=="") $_POST['type']='%';
if ($_POST['category']=="") $_POST['category']='%';

//month & day table 
$mois = array();
$mois = array("01" => "Janvier", "02"=> "Février", "03"=> "Mars", "04"=> "Avril", "05"=> "Mai", "06"=> "Juin", "07"=> "Juillet", "08"=> "Aout", "09"=> "Septembre", "10"=> "Octobre", "11"=> "Novembre", "12"=> "Décembre");
$jour= array();
$jour = array(1 => "1", 2=> "2", 3=> "3", 4=> "4", 5=> "5", 6=> "6", 7=> "7", 8=> "8", 9=> "9", 10=> "10", 11=> "11", 12=> "12", 13=> "13", 14=> "14", 15=> "15", 16=> "16", 17=> "17", 18=> "18", 19=> "19", 20=> "20", 21=> "21", 22=> "22", 23=> "23", 24=> "24", 25=> "25", 26=> "26", 27=> "27", 28=> "28", 29=> "29", 30=> "30", 31=> "31");
?>

<div class="page-header position-relative">
	<h1>
		<i class="icon-bar-chart"></i>  Statistiques 
		<div class="pull-right">
			<a  target="about_blank" href="./core/export.php">
		        <button  class="btn btn-xs btn-purple">
		            <i align="right" class="icon-list "></i>
		            Export Excel
		        </button>
			 </a>
		</div>
	</h1>
</div>

<div class="page-header position-relative">
	
		<form method="post" action="" name="filter" >
			<center>
			<small>Filtre global:</small>
			<select name="tech" onchange=submit()>
				<?php
				$query = mysql_query("SELECT * FROM tusers WHERE profile=0 and disable=0");				
				while ($row=mysql_fetch_array($query)) {
					if ($row['id'] == $_POST['tech']) $selected1="selected" ;
					if ($row['id'] == $_POST['tech']) $find="1" ;
					echo "<option value=\"$row[id]\" $selected1>$row[firstname] $row[lastname]</option>"; 
					$selected1="";
				} 
				echo "<option value=\"%\" >Tous les techniciens</option>";
				if ($find!="1") echo "<option value=\"%\" selected>Tous les techniciens</option>";												
				?>
			</select>
			<?php
		    if($rparameters['ticket_type']==1)
		    {
		        echo ' 
    			<select name="type" onchange=submit()>';
    				$query = mysql_query("SELECT * FROM ttypes ORDER BY id");				
    				while ($row=mysql_fetch_array($query)) {
    					if ($row['id'] == $_POST['type']) {$selected2="selected"; $find="1";}
    					echo "<option value=\"$row[id]\" $selected2>$row[name]</option>"; 
    					$selected2="";
    				} 
    				echo "<option "; if ($_POST['type']=='%') echo "selected"; echo" value=\"%\" >Tous les types</option>";										
    			echo'
    			</select>';
			}
        	?>
        	<select name="criticality" onchange=submit()>
			<?php
			$query = mysql_query("SELECT * FROM tcriticality ORDER BY number");				
			while ($row=mysql_fetch_array($query)) {
				if ($row['id'] == $_POST['criticality']) $selected2="selected" ;
				if ($row['id'] == $_POST['criticality']) $find="1";
				echo "<option value=\"$row[id]\" $selected2>$row[name]</option>"; 
				$selected2="";
			} 
			echo "<option "; if ($_POST['criticality']=='%') echo "selected"; echo" value=\"%\" >Toutes les criticités</option>";										
			?>
			</select> 
			<select name="category" onchange=submit()>
			<?php
			$query = mysql_query("SELECT * FROM tcategory ORDER BY name");				
			while ($row=mysql_fetch_array($query)) {
				if ($row['id'] == $_POST['category']) $selected2="selected" ;
				if ($row['id'] == $_POST['category']) $find="1";
				echo "<option value=\"$row[id]\" $selected2>$row[name]</option>"; 
				$selected2="";
			} 
			echo "<option "; if ($_POST['category']=='%') echo "selected"; echo" value=\"%\" >Toutes les categories</option>";										
			?>
			</select> 
			
			<select name="month" onchange=submit()>
				<option value="%" <?php if ($_POST['month'] == '%')echo "selected" ?>>Tous les mois</option>
				<option value="01"<?php if ($_POST['month'] == '1')echo "selected" ?>>Janvier</option>
				<option value="02"<?php if ($_POST['month'] == '2')echo "selected" ?>>Février</option>
				<option value="03"<?php if ($_POST['month'] == '3')echo "selected" ?>>Mars</option>
				<option value="04"<?php if ($_POST['month'] == '4')echo "selected" ?>>Avril</option>
				<option value="05"<?php if ($_POST['month'] == '5')echo "selected" ?>>Mai</option>
				<option value="06"<?php if ($_POST['month'] == '6')echo "selected" ?>>Juin</option>
				<option value="07"<?php if ($_POST['month'] == '7')echo "selected" ?>>Juillet</option>
				<option value="08"<?php if ($_POST['month'] == '8')echo "selected" ?>>Aout</option>
				<option value="09"<?php if ($_POST['month'] == '9')echo "selected" ?>>Septembre</option>
				<option value="10"<?php if ($_POST['month'] == '10')echo "selected" ?>>Octobre</option>
				<option value="11"<?php if ($_POST['month'] == '11')echo "selected" ?>>Novembre</option>	
				<option value="12"<?php if ($_POST['month'] == '12')echo "selected" ?>>Décembre</option>	
			</select>
	
			<select name="year" onchange=submit()>
				<?php
				$q1= mysql_query("SELECT distinct year(date_create) as year FROM `tincidents` WHERE date_create not like '0000-00-00'");
				while ($row=mysql_fetch_array($q1)) 
				{ 
					$selected=0;
					if ($_POST['year']==$row['year']) $selected="selected";  
					echo "test $_POST[year]==$row[year] $selected";
					echo "<option value=$row[year] $selected>$row[year]</option>";
				}
				?>
				<option value="%" <?php if ($_POST['year'] == '%')echo "selected" ?>>Toutes les années</option>
			</select>
			</center>
		</form>
</div>

	<br /><br />
	<?php
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
    $qservice = mysql_query("SELECT count(*) FROM tservices");
    $rservice=mysql_fetch_array($qservice);
    if ($rservice[0]>0)
    {
        echo "<a name=\"chart7\"></a>";
        echo "<hr />";
    	require('./stats/pie_services.php');
    	echo "<br />";
    }
    //display pie company if exist companies
    $qcompany = mysql_query("SELECT count(*) FROM tcompany");
    $rcompany=mysql_fetch_array($qcompany);
    if ($rcompany[0]>0)
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
	echo '<hr><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
	?>