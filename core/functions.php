<?php
################################################################################
# @Name : ./core/functions.php
# @Description : define all functions for require_once
# @Call :
# @Parameters : 
# @Author : Flox
# @Create : 06/02/2018  
# @Update : 07/04/2019  
# @Version : 3.1.41
################################################################################

//crypt 
function gs_crypt($string, $action = 'e', $key) 
{
    $secret_key = $key;
    $secret_iv = 'G€$|$ùP!';
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if($action=='e') {
        $output='gs_en_'.base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    }
    elseif($action=='d'){
        $output=openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

if (!function_exists("date_cnv")){
	//date conversion to fr format
	function date_cnv($date) 
	{
		return substr($date,8,2) . "/" . substr($date,5,2) . "/" . substr($date,0,4);
	}
}

?>