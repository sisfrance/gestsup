<?php
################################################################################
# @Name : profile.php
# @Desc :  right management
# @call : admin.php
# @parameters : 
# @Autor : Flox
# @Create : 06/07/2013
# @Update : 07/09/2013
# @Version : 3.0
################################################################################

// initialize variables 
if(!isset($_GET['value'])) $_GET['value'] = '';
if(!isset($_GET['profile'])) $_GET['profile'] = '';
if(!isset($_GET['object'])) $_GET['object'] = '';

if($_GET['value']!='')
{
	$query = "UPDATE trights SET `$_GET[object]`=$_GET[value] WHERE profile='$_GET[profile]'";
	$exec = mysql_query($query);
	//redirect
		$www = "./index.php?page=admin&subpage=profile#$_GET[object]";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
}

	//dynamic right table
	echo '
	<div class="page-header position-relative">
		<h1>
			<i class="icon-lock"></i>  Gestion des droits
		</h1>
	</div><!--/.page-header-->
	';
	echo '
		<div class="col-sm-12">
			<table id="sample-table-1" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>Nom</th>
						<th>Description</th>
						<th>Utilisateur</th>
						<th>Utilisateur avec pouvoir</th>
						<th>Superviseur</th>
						<th>Technicien</th>
						<th>Administrateur</th>
					</tr>
				</thead>
				<tbody>';					
				$query= mysql_query("show full columns from trights"); 
				while ($row=mysql_fetch_array($query)) 
				{	
					//exclude id and profile
					if ($row[0]!='id' && $row[0]!='profile')
					{
						echo '
						<tr id="'.$row['0'].'">
							<td>'.$row['0'].'</td>
							<td>'.$row['Comment'].'</td>
							<td>
								<center>';
									//find value
									$qv= mysql_query("SELECT * FROM `trights` where profile LIKE '2'"); 
									$rv = mysql_fetch_array($qv);
									if($rv[$row[0]]!=0)
									{
										echo'	
											<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=0&object='.$row[0].'&profile=2";\'  class="btn btn-xs btn-success">
												<i class="icon-ok bigger-120"></i>
											</button>
										';
									} else {
										echo'
										<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=2&object='.$row[0].'&profile=2";\' class="btn btn-xs btn-danger">
											<i class="icon-ban-circle bigger-120"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
							<td>
								<center>';
									//find value
									$qv= mysql_query("SELECT * FROM `trights` where profile LIKE '1'"); 
									$rv = mysql_fetch_array($qv);
									if($rv[$row[0]]!=0)
									{
										echo'
											<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=0&object='.$row[0].'&profile=1";\' class="btn btn-xs btn-success">
											<i class="icon-ok bigger-120"></i>
											</button>
										';
									} else {
										echo'
										<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=2&object='.$row[0].'&profile=1";\' class="btn btn-xs btn-danger">
											<i class="icon-ban-circle bigger-120"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
							<td>
								<center>';
									//find value
									$qv= mysql_query("SELECT * FROM `trights` where profile LIKE '3'"); 
									$rv = mysql_fetch_array($qv);
									if($rv[$row[0]]!=0)
									{
										echo'
											<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=0&object='.$row[0].'&profile=3";\' class="btn btn-xs btn-success">
											<i class="icon-ok bigger-120"></i>
											</button>
										';
									} else {
										echo'
										<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=2&object='.$row[0].'&profile=3";\' class="btn btn-xs btn-danger">
											<i class="icon-ban-circle bigger-120"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
							<td>
								<center>';
									//find value
									$qv= mysql_query("SELECT * FROM `trights` where profile LIKE '0'"); 
									$rv = mysql_fetch_array($qv);
									if($rv[$row[0]]!=0)
									{
										echo'
											<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=0&object='.$row[0].'&profile=0";\' class="btn btn-xs btn-success">
											<i class="icon-ok bigger-120"></i>
											</button>
									
										';
									} else {
										echo'
										<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=2&object='.$row[0].'&profile=0";\' class="btn btn-xs btn-danger">
											<i class="icon-ban-circle bigger-120"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
							<td>
								<center>';
									//find value
									$qv= mysql_query("SELECT * FROM `trights` where profile LIKE '4'"); 
									$rv = mysql_fetch_array($qv);
									if($rv[$row[0]]!=0)
									{
										echo'
											<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=0&object='.$row[0].'&profile=4";\' class="btn btn-xs btn-success">
											<i class="icon-ok bigger-120"></i>
											</button>
									
										';
									} else {
										echo'
										<button onclick=\'window.location.href="./index.php?page=admin&subpage=profile&value=2&object='.$row[0].'&profile=4";\' class="btn btn-xs btn-danger">
											<i class="icon-ban-circle bigger-120"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
						</tr>
						';
					}
				}
				echo'
				</tbody>
			</table>
		</div><!--/span-->';
	

?>