<?php
################################################################################
# @Name : event_update.php
# @Description : update event in db
# @Call : /calendar.php
# @Parameters : 
# @Author : Flox
# @Create : 19/02/2018
# @Update : 12/06/2019
# @Version : 3.1.45 p2
################################################################################

//init var
if(!isset($_POST['allday'])) $_POST['allday'] = '';
$_POST['title']=htmlspecialchars($_POST['title']);

//db connection
require "./../connect.php";
$db->exec('SET sql_mode = ""');

if($_POST['action']=='update_title')
{
	//data
	$id=$_POST['id'];
	$title=$_POST['title'];
	//db update
	$query = "UPDATE tevents SET title=? WHERE id=?";
	$query = $db->prepare($query);
	$query->execute(array($title,$id));
}
if($_POST['action']=='move_event' || $_POST['action']=='resize_event') {
	//data
	$id=$_POST['id'];
	$title=$_POST['title'];
	$start=$_POST['start'];
	$end=$_POST['end'];
	$allday=$_POST['allday'];
	//db update
	$query = "UPDATE tevents SET title=?, date_start=?, date_end=?, allday=? WHERE id=?";
	$query = $db->prepare($query);
	$query->execute(array($title,$start,$end,$allday,$id));
} 
if($_POST['action']=='delete_event')
{
	//db delete
	{
		$query = $db->prepare("DELETE FROM tevents WHERE id=:id");
		$query->execute(array(':id'=>$_POST['id']));
	}
	
}
if($_POST['action']=='add_event')
{
	//data
	$title=$_POST['title'];
	$start=$_POST['start'];
	$end=$_POST['end'];
	$allday=$_POST['allday'];
	$technician=$_POST['technician'];
	//db insert
	$query = "INSERT INTO tevents (technician,title, date_start, date_end, allday) VALUES (:technician, :title, :start, :end, :allday)";
	$query = $db->prepare($query);
	$query->execute(array(':technician'=>$technician,':title'=>$title, ':start'=>$start, ':end'=>$end, ':allday'=>$allday));
	echo json_encode(array("event_id" => $db->lastInsertId()));
}
?>