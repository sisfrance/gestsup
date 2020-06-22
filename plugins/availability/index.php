<?php
################################################################################
# @Name : /plugin/availability/index.php
# @Desc : display availability
# @call : /menu.php
# @paramters : 
# @Author : Flox
# @Create : 18/04/2014
# @Update : 03/06/2015
# @Version : 3.0.11
################################################################################

//initialize variables 
if(!isset($_GET['previous'])) $_GET['previous']='';
if(!isset($_GET['next'])) $_GET['next']='';
if(!isset($dependancy_time)) $dependancy_time='';
if(!isset($hourdependancy)) $hourdependancy='';
if(!isset($mindependancy)) $mindependancy='';
if(!isset($hour_planned)) $hour_planned='';
if(!isset($min_planned )) $min_planned ='';

//default settings
if(!isset($_GET['year']))$year=date('Y'); else $year=$_GET['year'];

//get median calc
include('median.php');

//display head
echo '
<div class="page-header position-relative">
	<h1>
		<i class="icon-time"></i> Disponibilité du Système d\'information du '.$rparameters['company'].'
	</h1>
</div>
<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
    ';
    //display link to years page
    $queryyears = mysql_query("SELECT DISTINCT YEAR(date_create) FROM tincidents WHERE date_create NOT LIKE '%0000%' ORDER BY YEAR(date_create) DESC");
    while ($rowyear=mysql_fetch_array($queryyears))
    {
        echo '<button onclick=\'window.location.href="./index.php?page=plugins/availability/index&year='.$rowyear[0].'";\' title="Accès direct à l\'année '.$rowyear[0].'" 
            class="btn btn-info" >
            '.$rowyear[0].'
            </button>
            &nbsp;&nbsp;&nbsp;';
    }
	echo '
            
           <a href="./plugins/availability/print.php?year='.$year.'" target="about_blank" <i title="Imprimer" class="icon-print green bigger-130"></i></a>

</div>
<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large"></div>
<script type="text/javascript" src="./template/assets/js/jquery-2.0.3.min.js"></script>
<script src="./components/Highcharts-2.3.3/js/highcharts.js"></script>
<script src="./components/Highcharts-2.3.3/js/modules/exporting.js"></script>';
echo "
<table border=\"0\">
    <tr >
    	<td>
            <a id=\"$year\"></a>
            <br />
            <h3>Pour l'année $year</h3>
            <blockquote>
            	"; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo "
                <i class=\"icon-circle grey\"></i> Médiane des taux de disponibilité cible des applications: <font color=\"green\">$median_target%</font><br />
                <i class=\"icon-circle grey\"></i> Médiane du taux de disponibilité constaté: <font color=\"green\">$median_global%</font><br />
                <i class=\"icon-circle grey\"></i> Médiane du taux de disponibilité constaté (hors interventions planifiées): <font color=\"green\">$median_none_planned%</font><br />
                ";
            	$querysubcat = mysql_query("SELECT * FROM `tavailability`");
    		    while ($rowsubcat=mysql_fetch_array($querysubcat))
    		    { 
    		    	//var init
    		    	$total_hour_planned=0;
    		    	$total_min_planned =0;
    		    	
    		        //get subcat name
    		   		$sname= mysql_query("SELECT name FROM `tsubcat` WHERE id='$rowsubcat[subcat]'"); 
    				$sname= mysql_fetch_array($sname);

    		        //statistics calc
    		        include('core.php');
    		        //find color red or green for tx
    				if ($tx_target>$tx) $color_tx="red"; else $color_tx="green";
    				if ($tx_target>$tx_planned) $color_tx_planned="red"; else $color_tx_planned="green";
    		        
    		        //display table
    				echo "
    				<table  "; if ($_GET['page']=='plugins/availability/index') {echo 'width=\"800\"';} echo " border=\"0\">
    					<tr>
    				        <td >
							<h4>Pour $sname[0] sur $year</h4>
    					        <blockquote>
    					        	"; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo "
    					            <i class=\"icon-circle green\"></i> <b>Taux de disponibilité constaté pour l'année $year: <font color=\"$color_tx\" size=\"3\">$tx %</font> sur $tx_target % attendu</b><br />
    					            <i class=\"icon-circle green\"></i> Taux de disponibilité hors interventions planifiées pour l'année $year: <font color=\"$color_tx_planned\">$tx_planned %</font> sur $tx_target % attendu<br />
    			                    <br />
    			                    <i class=\"icon-circle blue\"></i> <b>Durée d'indisponibilité pour l'année $year: <font color=\"$color_tx\">$global_hour h $global_min min</font></b><br />
    			                    <br />
    			                    <i class=\"icon-circle purple\"></i> <b>Liste des arrêts planifiés pour $sname[0]: ($total_hour_planned h $total_min_planned min)</b><br />
    			                    <blockquote>
    			                    	"; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo "
    			                        <i class=\"icon-caret-right purple\"></i> <u>Maintenance de $sname[0]</u><br />
    			                        ";
    			                        //find and display planned ticket
    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality WHERE subcat=$rowsubcat[subcat] AND tincidents.criticality=tcriticality.id AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' AND tincidents.availability_planned=1 ORDER BY tincidents.start_availability";
                        				$queryticket = mysql_query("$queryticket");
                        				while ($rowticket=mysql_fetch_array($queryticket)) 
                        				{
                        					//calc time by ticket
                        					$t1 =strtotime($rowticket['start_availability']) ;
                                            $t2 =strtotime($rowticket['end_availability']) ;
                                           	$time=(($t2-$t1)/60)/60;
                                           	$time=number_format($time,2);
                                           	$time_hour=explode(".", $time);
                                           	$time_min=60*"0.$time_hour[1]";
                        					$time_min=round($time_min);
                                           	$time_hour=$time_hour[0];
                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                        					echo "&nbsp; - <a target=\"about_blank\" href=\"./index.php?page=ticket&id=$rowticket[id]\">Ticket n°$rowticket[id]</a>: $rowticket[title] le $dateticket ($time_hour h $time_min min)<br />";
                        				}
    			                        echo "
    			                        <i class=\"icon-caret-right purple\"></i> <u>Autres maintenances</u><br />
    			                        ";
    			                        //find depandancy of planned tickets
    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality, tavailability_dep WHERE tincidents.criticality=tcriticality.id AND tincidents.subcat=tavailability_dep.subcat AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' AND tincidents.availability_planned=1 ORDER BY tincidents.start_availability";
                        				$queryticket = mysql_query("$queryticket");
                        				while ($rowticket=mysql_fetch_array($queryticket)) 
                        				{
                        					//calc time by ticket
                        					$t1 =strtotime($rowticket['start_availability']) ;
                                            $t2 =strtotime($rowticket['end_availability']) ;
                                           	$time=(($t2-$t1)/60)/60;
                                           	$time=number_format($time,2);
                                           	$time_hour=explode(".", $time);
                    	                   	$time_min=60*"0.$time_hour[1]";
                    						$time_min=round($time_min);
                    	                   	$time_hour=$time_hour[0];
                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                        					echo "&nbsp;&nbsp;&nbsp; - <a target=\"about_blank\" href=\"./index.php?page=ticket&id=$rowticket[id]\">Ticket n°$rowticket[id]</a>: $rowticket[title] le $dateticket ($time_hour h $time_min m)<br />";
                        				}
    			                        echo "
    			                    </blockquote>
    			                    <i class=\"icon-circle orange\"></i> <b>Liste des arrêts non planifiés pour $sname[0]: ($total_hour_none_planned h $total_min_none_planned min)</b><br />
    			                    <blockquote>
    			                    	"; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo "
    			                        <i class=\"icon-caret-right orange\"></i> <u>Problèmes de $sname[0]</u><br />
    			                        ";
    			                       //find and display non planned ticket
    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality WHERE subcat=$rowsubcat[subcat] AND tincidents.criticality=tcriticality.id AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' AND tincidents.availability_planned=0 ORDER BY tincidents.start_availability";
                        				$queryticket = mysql_query("$queryticket");
                        				while ($rowticket=mysql_fetch_array($queryticket)) 
                        				{
                        					//calc time by ticket
                        					$t1 =strtotime($rowticket['start_availability']) ;
                                            $t2 =strtotime($rowticket['end_availability']) ;
                                           	$time=(($t2-$t1)/60)/60;
                                           	$time=number_format($time,2);
                                           	$time_hour=explode(".", $time);
                                           	$time_min=60*"0.$time_hour[1]";
                        					$time_min=round($time_min);
                                           	$time_hour=$time_hour[0];
                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                        					echo "&nbsp;&nbsp;&nbsp; - <a target=\"about_blank\" href=\"./index.php?page=ticket&id=$rowticket[id]\">Ticket n°$rowticket[id]</a>: $rowticket[title] le $dateticket ($time_hour h $time_min min)<br />";
                        				}
    			                        echo "
    			                        <i class=\"icon-caret-right orange\"></i> <u>Autres problème</u><br />
    			                        ";
    			                        //find depandancy of non planned tickets
    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality, tavailability_dep WHERE tincidents.criticality=tcriticality.id AND tincidents.subcat=tavailability_dep.subcat AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' AND tincidents.availability_planned=0 ORDER BY tincidents.start_availability";
                        				$queryticket = mysql_query("$queryticket");
                        				while ($rowticket=mysql_fetch_array($queryticket)) 
                        				{
                        					//calc time by ticket
                        					$t1 =strtotime($rowticket['start_availability']) ;
                                            $t2 =strtotime($rowticket['end_availability']) ;
                                           	$time=(($t2-$t1)/60)/60;
                                           	$time=number_format($time,2);
                                           	$time_hour=explode(".", $time);
                    	                   	$time_min=60*"0.$time_hour[1]";
                    						$time_min=round($time_min);
                    	                   	$time_hour=$time_hour[0];
                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                        					echo "&nbsp;&nbsp;&nbsp; - <a target=\"about_blank\" href=\"./index.php?page=ticket&id=$rowticket[id]\">Ticket n°$rowticket[id]</a>: $rowticket[title] le $dateticket ($time_hour h $time_min m)<br />";
                        				}
    			                        echo "
    			                    </blockquote>
    					        </blockquote>
    				        </td>
    				        <td>
    	   	    			";
    				   	    	//display graphic
    							$container="container".$sname[0].$year;
    							include('./stat_bar_stacked.php');
								if ($_GET['page']=='plugins/availability/index') {echo "<div id=\"$container\" style=\"min-width: 300px; height: 300px; margin: 0 auto\"></div>";}
    							echo "
    	   	    			</td>
       	    			</tr>
       	    		</table>
    		        	";
    		    }
				echo "<br /><hr /><h1>Détails par trimestres</h1><br />";
    		    //////////////////////////////////display all trimesters of this year
    		    for ($i = 1; $i <= 4; $i++)
    		    {
    		        //launch month number quarter
    		        if($i==1){$m1="01"; $m2="02"; $m3="03"; $label="Janvier à Mars"; $trim_hours=90;}
    		        if($i==2){$m1="04"; $m2="05"; $m3="06"; $label="Avril à Juin"; $trim_hours=91;}
    		        if($i==3){$m1="07"; $m2="08"; $m3="09"; $label="Juillet à Septembre"; $trim_hours=92;}
    		        if($i==4){$m1="10"; $m2="11"; $m3="12"; $label="Octobre à Décembre"; $trim_hours=92;}
    		    	echo "
    		    		<hr>
    		        	<h3>Pour le trimestre $i de l'année $year (Période de $label)</h3>
    		       		<blockquote>";
            		        $querysubcat = mysql_query("SELECT * FROM `tavailability`");
                		    while ($rowsubcat=mysql_fetch_array($querysubcat))
                		    { 
                		        //call statistics calc
    		                    include('core.php');
    		                    
    		                    //get subcat name
                		   		$sname= mysql_query("SELECT name FROM `tsubcat` WHERE id='$rowsubcat[subcat]'"); 
                				$sname= mysql_fetch_array($sname);
                				
    		                    //check 100% case
    		                    if($tx!="100.00")
    		                    {
                    				 //find color red or green for tx
                    				if ($tx_target>$tx) $color_tx="red"; else $color_tx="green";
                    				if ($tx_target>$tx_planned) $color_tx_planned="red"; else $color_tx_planned="green";
                    				
                    		        echo "
                    		        <table "; if ($_GET['page']=='plugins/availability/index') {echo 'width=\"800\"';} echo " border=0>
                		    			<tr>
                	        		        <h4>Pour $sname[0] sur le trimestre $i de l'année $year</h4>
                	        		        <td>
                		        		        <blockquote>
                		        		        	"; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo "
                		        		            <i class=\"icon-circle green\"></i> <b>Taux de disponibilité constaté pour le trimestre $i de l'année $year: <font color=\"$color_tx\" size=\"3\">$tx %</font> sur $tx_target % attendu.</b><br />
                		        		            <i class=\"icon-circle green\"></i> Taux de disponibilité hors interventions planifiées pour l'année $year: <font color=\"$color_tx_planned\" >$tx_planned %</font> sur $tx_target attendu.<br />
                		                            <br />
                		                            <i class=\"icon-circle blue\"></i> <b>Durée d'indisponibilité pour le trimestre $i de l'année $year: <font color=\"green\"> $global_hour h $global_min min</font></b><br />
                		                            <br />
                		                            <i class=\"icon-circle purple\"></i> <b>Liste des arrêts planifiés pour $sname[0]:</b> ($total_hour_planned h $total_min_planned min)<br />
                		                            <blockquote>
														"; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo "
                		                                <i class=\"icon-caret-right purple\"></i> <u>Maintenance de $sname[0]</u><br />
                		                                ";
                        			                        //find and display planned ticket
                			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality WHERE subcat=$rowsubcat[subcat] AND tincidents.criticality=tcriticality.id AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' $months AND tincidents.availability_planned=1 ORDER BY tincidents.start_availability";
                                        				$queryticket = mysql_query("$queryticket");
                                        				while ($rowticket=mysql_fetch_array($queryticket)) 
                                        				{
                                        					//calc time by ticket
                                        					$t1 =strtotime($rowticket['start_availability']) ;
                                                            $t2 =strtotime($rowticket['end_availability']) ;
                                                           	$time=(($t2-$t1)/60)/60;
                                                           	$time=number_format($time,2);
                                                           	$time_hour=explode(".", $time);
                                                           	$time_min=60*"0.$time_hour[1]";
                                        					$time_min=round($time_min);
                                                           	$time_hour=$time_hour[0];
                                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                                        					echo "&nbsp;&nbsp;&nbsp; - <a target=\"about_blank\" href=\"./index.php?page=ticket&id=$rowticket[id]\">Ticket n°$rowticket[id]</a>: $rowticket[title] le $dateticket ($time_hour h $time_min min)<br />";
                                        				}
                    			                        echo "
                		                                <i class=\"icon-caret-right purple\"></i> <u>Autres maintenances</u><br />
                		                                ";
                    			                        //find depandancy of planned tickets
                    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality, tavailability_dep WHERE tincidents.criticality=tcriticality.id AND tincidents.subcat=tavailability_dep.subcat AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' $months AND tincidents.availability_planned=1 ORDER BY tincidents.start_availability";
                                        				$queryticket = mysql_query("$queryticket");
                                        				while ($rowticket=mysql_fetch_array($queryticket)) 
                                        				{
                                        					//calc time by ticket
                                        					$t1 =strtotime($rowticket['start_availability']) ;
                                                            $t2 =strtotime($rowticket['end_availability']) ;
                                                           	$time=(($t2-$t1)/60)/60;
                                                           	$time=number_format($time,2);
                                                                       	$time_hour=explode(".", $time);
                                    	                   	$time_min=60*"0.$time_hour[1]";
                        						$time_min=round($time_min);
                                    	                   	$time_hour=$time_hour[0];
                                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                                        					echo "&nbsp;&nbsp;&nbsp; - <a target=\"about_blank\" href=\"./index.php?page=ticket&id=$rowticket[id]\">Ticket n°$rowticket[id]</a>: $rowticket[title] le $dateticket ($time_hour h $time_min m)<br />";
                                        				}
                    			                        echo "
                		                            </blockquote>
                		                            <i class=\"icon-circle orange\"></i> <b>Liste des arrêts non planifiés pour $sname[0]:</b> ($total_hour_none_planned h $total_min_none_planned min)<br />
                		                            <blockquote>
														"; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo "
                		                                <i class=\"icon-caret-right orange\"></i> <u>Problèmes de $sname[0]</u><br />
                		                                ";
                    			                         //find and display non planned ticket
                    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality WHERE subcat=$rowsubcat[subcat] AND tincidents.criticality=tcriticality.id AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' $months AND tincidents.availability_planned=0 ORDER BY tincidents.start_availability";
                                        				$queryticket = mysql_query("$queryticket");
                                        				while ($rowticket=mysql_fetch_array($queryticket)) 
                                        				{
                                        					//calc time by ticket
                                        					$t1 =strtotime($rowticket['start_availability']) ;
                                                            $t2 =strtotime($rowticket['end_availability']) ;
                                                           	$time=(($t2-$t1)/60)/60;
                                                           	$time=number_format($time,2);
                                                           	$time_hour=explode(".", $time);
                                                           	$time_min=60*"0.$time_hour[1]";
                                        					$time_min=round($time_min);
                                                           	$time_hour=$time_hour[0];
                                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                                        					echo "&nbsp;&nbsp;&nbsp; - <a target=\"about_blank\" href=\"./index.php?page=ticket&id=$rowticket[id]\">Ticket n°$rowticket[id]</a>: $rowticket[title] le $dateticket ($time_hour h $time_min min)<br />";
                                        				}
                    			                        echo "
                		                                <i class=\"icon-caret-right orange\"></i> <u>Autres problème</u><br />
                		                                ";
                    			                        //find depandancy of non planned tickets
                    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality, tavailability_dep WHERE tincidents.criticality=tcriticality.id AND tincidents.subcat=tavailability_dep.subcat AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' $months AND tincidents.availability_planned=0 ORDER BY tincidents.start_availability";
                                        				$queryticket = mysql_query("$queryticket");
                                        				while ($rowticket=mysql_fetch_array($queryticket)) 
                                        				{
                                        					//calc time by ticket
                                        					$t1 =strtotime($rowticket['start_availability']) ;
                                                            $t2 =strtotime($rowticket['end_availability']) ;
                                                           	$time=(($t2-$t1)/60)/60;
                                                           	$time=number_format($time,2);
                                                           	$time_hour=explode(".", $time);
                                    	                   	$time_min=60*"0.$time_hour[1]";
                                    						$time_min=round($time_min);
                                    	                   	$time_hour=$time_hour[0];
                                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                                        					echo "&nbsp;&nbsp;&nbsp; - <a target=\"about_blank\" href=\"./index.php?page=ticket&id=$rowticket[id]\">Ticket n°$rowticket[id]</a>: $rowticket[title] le $dateticket ($time_hour h $time_min m)<br />";
                                        				}
                    			                        echo "
                		                            </blockquote>
                		            		    </blockquote>
                	            		    </td>
                	            		    <td>";
                	            		    //display graphic
                							$container="container".$sname[0].$i.$year;
                							include('./stat_bar_stacked.php');
											if ($_GET['page']=='plugins/availability/index') {echo "<div id=\"$container\" style=\"min-width: 300px; height: 300px; margin: 0 auto\"></div>";}
                							echo "
                	            		    </td>
                        				</tr>
                    			    </table>
                    		        ";
    		                    } else {
    		                        echo"<h4> Pour $sname[0] sur le trimestre $i de l'année $year, le taux de disponibilité constaté est <font color=\"green\">$tx %</font> sur $tx_target % attendu.</h4><br />";        
    		                    }
                		    }
            		echo "</blockquote>
            		";
    		    }
    		    echo "
            </blockquote>
        </td>
    <tr>
</table>";
?>