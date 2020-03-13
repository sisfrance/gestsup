<?php
################################################################################
# @Name : asset_findip.php
# @Description : search free IPv4 in selected network
# @call : ./asset.php
# @parameters :  
# @Author : Flox
# @Create : 16/12/2015
# @Update : 28/12/2018
# @Version : 3.1.37
################################################################################

//initialize variables 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_GET['iface'])) $_GET['iface'] = ''; 
if(!isset($_POST['Ajouter'])) $_POST['Ajouter'] = ''; 
if(!isset($_POST['network'])) $_POST['network'] = ''; 
if(!isset($_POST['add'])) $_POST['add'] = ''; 
if(!isset($_POST['ip'])) $_POST['ip'] = ''; 

if ($_POST['add'] && $_POST['ip']!='')
{
	//redirect to close modal
	if($_GET['action']=='findip1')
	{$dest_iface='ip_lan_new';}
	elseif
	($_GET['action']=='findip2')
	{$dest_iface='ip_wifi_new';}
	else 
	{
		$dest_iface=explode('_',$_GET['action']);
		$dest_iface=$dest_iface[1];
	}
	$www = "./index.php?page=asset&id=$_GET[id]&iface=$dest_iface&findip=$_POST[ip]&$url_get_parameters";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}

if($_POST['network']!='')
{
	//get selected network informations
	$qry=$db->prepare("SELECT `netmask`,`network` FROM `tassets_network` WHERE id=:id");
	$qry->execute(array('id' => $_POST['network']));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	$netmask=$row['netmask'];
	$network=$row['network'];
	$network=explode('.',$network);
	
	//find free ip in this network
	for ($i = 1; $i < 254; $i++) {
		//generate test ip
		$test_ip=$network[0].'.'.$network[1].'.'.$network[2].'.'.$i;
		//check if this ip exist
		$exist_ip=0;
		$qry=$db->prepare("
		SELECT tassets_iface.ip FROM `tassets_iface` 
		INNER JOIN tassets ON tassets.id=tassets_iface.asset_id
		INNER JOIN tassets_state ON tassets_state.id=tassets.state
		WHERE 
		tassets_iface.ip=:ip AND
		tassets_state.block_ip_search=1 AND
		tassets_iface.disable='0' AND
		tassets.disable='0'
		");
		$qry->execute(array('ip' => $test_ip));
		$row=$qry->fetch();
		$qry->closeCursor();
		
		if($row[0]) {$exist_ip=1;} 
		if ($exist_ip!=1) {break;}
	}
	$findip=$test_ip;
} else {$findip=$_POST['ip'];}

$boxtitle="<i class='icon-exchange blue bigger-120'></i> ".T_('Recherche d\'adresse IP');
$boxtext= '
<form name="form" method="POST" action="" id="form">
	<input  name="add" type="hidden" value="1">
	<label for=\"network\" >'.T_('Réseau').' :</label> 
	<select id="network" name="network" style="width:133px" onchange="submit();">
		';
			$boxtext= $boxtext.'<option value="">'.T_('Aucun').'</option>';
			$qry=$db->prepare("SELECT id,name FROM `tassets_network` WHERE disable='0' ORDER BY name ASC");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				if ($_POST['network']==$row['id']) 
				{
					$boxtext= $boxtext.'<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
				} else {
					$boxtext= $boxtext.'<option value="'.$row['id'].'">'.$row['name'].'</option>';	
				}
			}
			$qry->closeCursor();
			
        	$boxtext= $boxtext.'		
	</select>
	<div class="space-4"></div>
	<label for="ip">IP :</label> 
	<input  name="ip" type="text" value="'.$findip.'" size="20">
</form>
';
$valid=T_('Ajouter');
$action1="$('form#form').submit(); ";
$cancel=T_('Fermer');
$action2="$( this ).dialog( \"close\" ); ";
include "./modalbox.php"; 
?>