<?php
$host = 'localhost';
$password = '';
$user = 'root';
$db = "wp";

$con = new mysqli("$host","$user","$password","$db");

if(!$con){
    exit(json_encode(array("status"=>404,"message"=>"failed to connect to db")));
}

?>