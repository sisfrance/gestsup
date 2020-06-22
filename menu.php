<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
################################################################################
# @Name : /menu.php
# @Desc : display left pannel menu
# @Call : /index.php
# @Parameters : 
# @Autor : Flox
# @Create : 06/09/2013
# @Update : 10/03/2015
# @Version : 3.0.11
################################################################################

//initialize variables 
if(!isset($_GET['viewid'])) $_GET['viewid'] = '';
if(!isset($_GET['userid'])) $_GET['userid'] = ''; 
if(!isset($_GET['state'])) $_GET['state'] = ''; 
if(!isset($_GET['techread'])) $_GET['techread'] = ''; 
if(!isset($state)) $state = ''; 
?>
<div class="sidebar" id="sidebar">
	<script type="text/javascript">
		try{ace.settings.check('sidebar' ,'fixed')}catch(e){}
	</script>
	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<?php

        if ($rright['side_open_ticket']!=0)
		{
			echo'
			<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
				<p>
					<a href="./index.php?page=ticket&amp;action=new&amp;userid='.$_SESSION['user_id'].'&amp;state='.$_GET['state'].'">
						<button title="Nouveau ticket" onclick=\'window.location.href="./index.php?page=ticket&amp;action=new&amp;userid='.$_SESSION['user_id'].'&amp;state='.$_GET['state'].'"\' class="btn btn-sm btn-success">
							<i class="icon-plus bigger-120"></i> Nouveau Ticket
						</button>
					</a>
				</p>
			</div>
			<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
				<span class="btn btn-success"></span>
			</div>
			';
		}
		?>
	</div><!--#sidebar-shortcuts-->
	<ul class="nav nav-list">
		<?php
		if ($rright['side_your']!=0)
		{
			$cntall= mysql_query("SELECT count(*) FROM `tincidents` WHERE  $profile='$uid' and disable='0'");
			$cntall= mysql_fetch_array($cntall);
			echo "<li class=\"active\">
				<a href=\"./index.php?page=dashboard&amp;userid=$_SESSION[user_id]&amp;state=%\" class=\"dropdown-toggle\" >
					<i class=\"icon-ticket\"></i>
					<span class=\"menu-text\">
						Vos tickets" ;

							if ($cnt3[0]>0 && $rright['side_your_not_read']!=0) echo '<span class="badge badge-transparent tooltip-error" title="" data-original-title="'.$cnt3[0].' Non lus"><i title="Tickets non lus sont en attente" class="icon-warning-sign light-orange bigger-130"></i></span>';
						echo " 
					</span>
					<b class=\"arrow icon-angle-down\"></b>
				</a>
				<ul class=\"submenu\" >";
				    //display all states link
					if ($_GET['page']=='dashboard' && $_GET['userid']!='%' && $_GET['state']=='%') {echo '<li class="active">';} else {echo "<li>";} echo "
						<a href=\"./index.php?page=dashboard&amp;userid=$_SESSION[user_id]&amp;state=%\">
							<i class=\"icon-double-angle-right\"></i>
							Tous les états ($cntall[0])
						</a>
					</li>";
					 //display meta  states link
					if ($rparameters['meta_state']==1 && $rright['side_your_meta']!=0)
					{
					    $cntmeta= mysql_query("SELECT count(*) FROM `tincidents` WHERE $profile='$uid' and disable='0' and (state=1 OR state=2 OR state=6)"); 
			            $cntmeta= mysql_fetch_array($cntmeta); 
					
    					if ($_GET['page']=='dashboard' && $_GET['userid']!='%' && $_GET['state']=='meta') {echo '<li class="active">';} else {echo "<li>";} echo "
    						<a title=\"Meta-état regroupant les états: Attente de PEC, En cours, et Attente de retour.\" href=\"./index.php?page=dashboard&amp;userid=$_SESSION[user_id]&amp;state=meta\">
    							<i class=\"icon-double-angle-right\"></i>
    							A traiter ($cntmeta[0])
    						</a>
    					</li>";
					}
					//display unread ticket
					if ($cnt3[0]>0 && $rright['side_your_not_read']!=0)
					{
						if ($_GET['techread']!='' && $_GET['page']!='searchengine') echo '<li class="active">'; else echo '<li>'; echo '
							<a href="./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;techread=0">
								<i class="icon-double-angle-right"></i>
								Non lus ('.$cnt3[0].')&nbsp;&nbsp;&nbsp;<i title="Des tickets non lus sont en attente" class="icon-warning-sign light-orange bigger-130"></i>
							</a>
						</li>';
						
					}
					//foreach state display in sub-menu
					$reqstate = mysql_query("SELECT * FROM `tstates` WHERE id not like 5 ORDER BY number"); 
					while ($row=mysql_fetch_array($reqstate))
					{
						$cnt= mysql_query("SELECT count(*) FROM `tincidents` WHERE $profile='$uid' and state LIKE '$row[id]' and disable='0'"); 
						$cnt= mysql_fetch_array($cnt);
						echo '
						<li';  
						if ($_GET['page']=='dashboard' && $_GET['userid']!='%' && $_GET['state']==$row['id']) echo ' class="active"';
						echo '>
							<a title="'.$row['description'].'" href="./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state='.$row['id'].'">
								<i class="icon-double-angle-right"></i>
								'.$row['name'].' ('.$cnt[0].')
							</a>
						</li>';
					}
					echo "
				</ul>
			</li>
			";
		}
		if ($rright['side_all']!=0)
		{
		    /// Recuperation du nombre de ticket en fonction du groupe
	       if ($_SESSION['profile_id'] !=4 ){
               $cntall= mysql_query("SELECT count(*) FROM `tincidents`,`tusers`,`tgroups_assoc`,`tstates` WHERE 
			    tincidents.state=tstates.id 
			AND tincidents.u_group=tgroups_assoc.group 
			AND tgroups_assoc.user=tusers.id  
			AND tincidents.user=tgroups_assoc.user
			 AND tincidents.disable ='0'");
               $cntall= mysql_fetch_array($cntall);
           } else {
               $cntall= mysql_query("SELECT count(*) FROM `tincidents` WHERE disable='0'");
               $cntall= mysql_fetch_array($cntall);

           }
            /////////////

			if ($_GET['page']=='dashboard' && ($_GET['userid']=='%' || $_GET['userid']=='0') && $_GET['viewid']=='') echo '<li  class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=dashboard&amp;userid=%&amp;state=%" class="dropdown-toggle">
					<i class="icon-ticket"></i>
						<span class="menu-text"> 
							Tous';
								if ($cnt5[0]>0 && $rright['side_your_not_attribute']!=0) echo '<span class="badge badge-transparent tooltip-error" title="" data-original-title="'.$cnt5[0].'&nbsp;Nouveaux&nbsp;tickets"><i title="De nouveaux tickets sont à attribuer" class="icon-warning-sign red bigger-130"></i></span>';
							echo '
						</span>
						<b class="arrow icon-angle-down"></b>
				</a>
				<ul class="submenu" >';
					if ($_GET['page']=='dashboard' && $_GET['userid']=='%' && $_GET['state']=='%') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=dashboard&amp;userid=%&amp;state=%">
							<i class="icon-double-angle-right"></i>
							Tous les états ('.$cntall[0].')
						</a>
					</li>';
					//display new tickets if exist
					if ($cnt5[0]>0 && $rright['side_your_not_attribute']!=0)
					{
						if ($_GET['page']=='dashboard' && $_GET['userid']=='0' && $_GET['state']=='%') echo '<li class="active">'; else echo '<li>'; echo '
							<a href="./index.php?page=dashboard&amp;userid=0&amp;state=%">
								<i class="icon-double-angle-right"></i>
								Nouveaux ('.$cnt5[0].')&nbsp;&nbsp;&nbsp;<i title="Des nouveaux tickets sont à attribués" class="icon-warning-sign red bigger-130"></i>
							</a>
						</li>';
						
					}
					 //display meta  states link
					if ($rparameters['meta_state']==1  && $rright['side_all_meta']!=0)
					{
					    ///////////////////////TEST////////////////////////////
					    if($_SESSION['profile_id'] == 4) {

                            $cntmetaall= mysql_query("SELECT count(*) FROM `tincidents` WHERE disable='0' and (state=1 OR state=2 OR state=6)");
                            $cntmetaall= mysql_fetch_array($cntmetaall);
                        } else {
                            $cntmetaall= mysql_query(  "SELECT count(*) FROM `tincidents`,`tusers`,`tgroups_assoc`,`tstates` WHERE 
                                tincidents.state=tstates.id
                                AND tincidents.u_group=tgroups_assoc.group
                                AND tgroups_assoc.user=tusers.id
                                AND tincidents.user=tgroups_assoc.user
                                AND tincidents.disable ='0'
                                and (state=1 OR state=2 OR state=6)");
                            $cntmetaall= mysql_fetch_array($cntmetaall);
                        }
					    ////////////////////////////////////////////

					
    					if ($_GET['page']=='dashboard' && $_GET['userid']=='%' && $_GET['state']=='meta') {echo '<li class="active">';} else {echo "<li>";} echo "
    						<a title=\"Meta-état regroupant les états: Attente de PEC, En cours, et Attente de retour.\" href=\"./index.php?page=dashboard&amp;userid=%&amp;state=meta\">
    							<i class=\"icon-double-angle-right\"></i>
    							A traiter ($cntmetaall[0])
    						</a>
    					</li>";
					}
					//foreach state display in sub-menu
					$reqstate = mysql_query("SELECT * FROM `tstates` WHERE id not like 5 ORDER BY number"); 
					while ($row=mysql_fetch_array($reqstate))
					{
						$cnt= mysql_query("SELECT count(*) FROM `tincidents` WHERE state LIKE '$row[id]' and disable='0'"); 
						$cnt= mysql_fetch_array($cnt);
						echo '
						<li';  
						if ($_GET['page']=='dashboard' && $_GET['userid']=='%' && $_GET['state']==$row['id']) echo ' class="active"';
						echo '>
							<a title="'.$row['description'].'" href="./index.php?page=dashboard&amp;userid=%&amp;state='.$row['id'].'">
								<i class="icon-double-angle-right"></i>
								'.$row['name'].' ('.$cnt[0].')
							</a>
						</li>';
					}
					echo'
				</ul>
			</li>';
		}
		if ($rright['side_view']!=0)
		{
			//if exist view for connected user then diplay link view
			$query = mysql_query("SELECT * FROM `tviews` WHERE uid='$_SESSION[user_id]' ORDER BY 'name' ");
			$row= mysql_fetch_array($query);
			if ($row[0]!='')
			{
				if($_GET['viewid']!='' || $_GET['page']=='view') echo '<li class="active">'; else echo '<li>'; echo '
					<a href="./index.php?page=view" class="dropdown-toggle">
						<i class="icon-eye-open"></i>
						<span class="menu-text"> Vos vues </span>
						<b class="arrow icon-angle-down"></b>
					</a>
					<ul class="submenu">';
					//get view of connected user
					$query = mysql_query("SELECT * FROM `tviews` WHERE uid='$_SESSION[user_id]' ORDER BY 'name' ");
					while ($row=mysql_fetch_array($query))
					{
						//case for no sub categories
						if ($row['subcat']==0) $subcat='%'; else $subcat=$row['subcat']; 
						//count entries
						$q= mysql_query("SELECT COUNT(*) FROM `tincidents` WHERE category='$row[category]' AND subcat LIKE'$subcat' AND (state='1' OR state='2' OR state='6') AND disable='0'");
						$n= mysql_fetch_array($q);
						// echo '<li '; if ($_GET['viewid']==$row['id'])  echo'class="active"'; echo '><a href="./index.php?page=dashboard&amp;userid=%&amp;category='.$row['category'].'&amp;subcat='.$subcat.'&amp;viewid='.$row['id'].'">Vue '.$row['name'].' ('.$n[0].')</a></li>';
						 if ($_GET['viewid']==$row['id']) echo '<li class="active">'; else  echo'<li>'; echo '
							<a href="./index.php?page=dashboard&amp;userid=%&amp;category='.$row['category'].'&amp;subcat='.$subcat.'&amp;viewid='.$row['id'].'">
								<i class="icon-double-angle-right"></i>
								'.$row['name'].' ('.$n[0].')
							</a>
						</li>';
					}
					echo '
					</ul>
				</li>';
			}
		}
		if ($rright['procedure']!=0 && $rparameters['procedure']==1)
		{
			 if($_GET['page']=='procedure') echo '<li class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=procedure">
					<i class="icon-book"></i>
					<span class="menu-text">Procédures</span>
				</a>
			</li>
			';
		}
		if ($rright['planning']!=0 && $rparameters['planning']==1)
		{
			if($_GET['page']=='planning') echo '<li class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=planning">
					<i class="icon-calendar"></i>
					<span class="menu-text">Calendrier</span>
				</a>
			</li>';
		}
	    if ($rright['asset']!=0 && $rparameters['asset']==1)
		{
			if($_GET['page']=='asset') echo '<li class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=asset">
					<i class="icon-desktop"></i>
					<span class="menu-text">Matériels</span>
				</a>
			</li>';
		}
		if ($rright['availability']!=0 && $rparameters['availability']==1)
		{
			if($_GET['page']=='availability') echo '<li class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=plugins/availability/index">
					<i class="icon-time"></i>
					<span class="menu-text">Disponibilité</span>
				</a>
			</li>';
		}
		if ($rright['admin']!=0)
		{
			if($_GET['page']=='procedure') echo '<li class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=procedure">
					<i class="icon-book"></i>
					<span class="menu-text">Procédure</span>
				</a>
			</li>';
		}
		if ($rright['stat']!=0)
		{
			if($_GET['page']=='stat') echo '<li class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=stat">
					<i class="icon-bar-chart"></i>
					<span class="menu-text">Ancienne Stats</span>
				</a>
			</li>';
		}
		if ($rright['stat']!=0)
		{
			if($_GET['page']=='statistiques') echo '<li class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=statistiques">
					<i class="icon-bar-chart"></i>
					<span class="menu-text">Nouvelles Stats</span>
				</a>
			</li>';
		}
		if ($rright['admin']!=0)
		{
			 if($_GET['page']=='admin') echo '<li class="active">'; else echo '<li>'; echo '
				<a href="./index.php?page=admin&subpage=parameters">
					<i class="icon-cogs"></i>
					<span class="menu-text"> Administration </span>
					<b class="arrow icon-angle-down"></b>
				</a>
				<ul class="submenu">';
					if($_GET['page']=='admin' && $_GET['subpage']=='parameters') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=parameters">
							<i class="icon-cog"></i>
							Paramètres
						</a>
					</li>';
					if($_GET['page']=='admin' && $_GET['subpage']=='user') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=user">
							<i class="icon-user"></i>
							Utilisateurs
						</a>
					</li>';
					if($_GET['page']=='admin' && $_GET['subpage']=='group') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=group">
							<i class="icon-group"></i>
							Groupes
						</a>
					</li>';
					if($_GET['page']=='admin' && $_GET['subpage']=='dump') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=dump">
							<i class="icon-book"></i>
							Dumps
						</a>
					</li>';
					if($_GET['page']=='admin' && $_GET['subpage']=='profile') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=profile">
							<i class="icon-lock"></i>
							Droits
						</a>
					</li>';
					if($_GET['page']=='admin' && $_GET['subpage']=='list') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=list">
							<i class="icon-list"></i>
							Listes
						</a>
					</li>';
					if($_GET['page']=='admin' && $_GET['subpage']=='backup') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=backup">
							<i class="icon-save"></i>
							Sauvegardes
						</a>
					</li>';
					if($_GET['page']=='admin' && $_GET['subpage']=='system') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=system">
							<i class="icon-desktop"></i>
							Système
						</a>
					</li>';
					if($_GET['page']=='admin' && $_GET['subpage']=='infos') echo '<li class="active">'; else echo '<li>'; echo '
						<a href="./index.php?page=admin&subpage=infos">
							<i class="icon-info-sign"></i>
							Informations
						</a>
					</li>
				</ul>
			</li>';
		}
		?>
	</ul><!--/.nav-list-->
	<div class="sidebar-collapse" id="sidebar-collapse">
		<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
	</div>
	<script type="text/javascript">
		try{ace.settings.check(\'sidebar\' , \'collapsed\')}catch(e){}
	</script>
</div>