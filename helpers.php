<?php
header('Access-Control-Allow-Origin: *');  

if(!function_exists('abort')){
    function abort($msg, $status_code = 400){
        header("HTTP/1.1 $status_code $msg");
        throw new Exception($msg);
    }
}