<?php
################################################################################
# @Name : /plugin/availability/median.php
# @Desc : calc median
# @call : /plugin/availability/index.php
# @parameters : category
# @Author : Flox
# @Create : 23/05/2015
# @Update : 23/05/2015
# @Version : 3.0.11
################################################################################

//function median
function calculate_median($arr) {
    sort($arr);
    $count = count($arr); //total numbers in array
    $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
    if($count % 2) { // odd number, middle is the median
        $median = $arr[$middleval];
    } else { // even number, calculate avg of 2 medians
        $low = $arr[$middleval];
        $high = $arr[$middleval+1];
        $median = (($low+$high)/2);
    }
    return $median;
}


//target median
$median_target_array = array();
$q = mysql_query("SELECT target FROM `tavailability_target` WHERE year='$year' ");
while ($r=mysql_fetch_array($q))
{
    array_push($median_target_array, "$r[0]");
}
$median_target = calculate_median($median_target_array);


//global median
$median_global_array = array();
$querysubcat = mysql_query("SELECT * FROM `tavailability`");
while ($rowsubcat=mysql_fetch_array($querysubcat))
{
    include('core.php');
    array_push($median_global_array, "$tx");
}
$median_global = calculate_median($median_global_array);

//none planned median
$median_none_planned_array = array();
$querysubcat = mysql_query("SELECT * FROM `tavailability`");
while ($rowsubcat=mysql_fetch_array($querysubcat))
{
    include('core.php');
    array_push($median_none_planned_array, "$tx_none_planned");
}
$median_none_planned = calculate_median($median_none_planned_array);
 
 
?>