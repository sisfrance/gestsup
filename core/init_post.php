<?php
################################################################################
# @Name : init_post.php
# @Description : init and secure all app var
# @Call : 
# @Parameters : 
# @Author : Flox
# @Create : 08/11/2019
# @Update : 08/11/2019
# @Version : 3.1.45
################################################################################

//POST var definition
$all_post_var=array(
	'date',
	'selectrow',
	'ticket',
	'technician',
	'title',
	'userid',
	'company',
	'user',
	'category',
	'subcat',
	'asset',
	'place',
	'service',
	'sender_service',
	'agency',
	'date_create',
	'date_hope',
	'date_res',
	'date_start',
	'date_end',
	'state',
	'priority',
	'criticality',
	'type',
	'u_group',
	't_group',
	'Modifier',
	'Ajouter',
	'cat',
	'model',
	'ip',
	'wifi',
	'manufacturer',
	'name',
	'type',
	'confirm',
	'allday',
);

//action on all post var
foreach($all_post_var as $post_var) {
	//init var
	if(!isset($_POST[$post_var])){$_POST[$post_var]='';}
	//secure var
    $_POST[$post_var]=htmlspecialchars($_POST[$post_var], ENT_QUOTES, 'UTF-8');
}
?>