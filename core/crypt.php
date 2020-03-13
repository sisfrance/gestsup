<?php
################################################################################
# @Name : ./core/crypt.php
# @Description : function to crypt string
# @Call :
# @Parameters : 
# @Author : Flox
# @Create : 24/09/2018  
# @Update : 24/09/2018  
# @Version : 3.1.35
################################################################################

function gs_crypt($string, $action = 'e', $key) {
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
?>