<?php
################################################################################
# @Name : admin.php
# @Desc : admin parent page
# @call : /index.php
# @paramters : 
# @Autor : Flox
# @Create : 12/01/2011
# @Update : 12/08/2013
# @Version : 2.9
################################################################################

// initialize variables 
if(!isset($_GET['subpage'])) $_GET['subpage'] = '';
if(!isset($_GET['profileid'])) $_GET['profileid'] = '';

//default settings
if ($_GET['subpage']=='') $_GET['subpage']='user';
if ($_GET['subpage']=='user')
if ($_GET['profileid']=='') if ($_GET['subpage']=='user') $_GET['profileid'] = '%';
if ($_GET['subpage']=='profile' && $_GET['profileid']=='') $_GET['profileid']=0;

//check rights for admin page
if ($rright['admin']!=0)
{
	include ('./admin/'.$_GET['subpage'].'.php');
} else {
	echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>Erreur:</strong> Vous n\'avez pas acc&egrave;s &agrave; cette page, contacter votre administrateur.<br></div>';
}
?>