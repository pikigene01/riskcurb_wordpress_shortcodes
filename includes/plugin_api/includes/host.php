<?php

function multiTenancyTables($con,$table_name){

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        belongs text DEFAULT NULL,
        name text DEFAULT NULL,
        value text DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        if ($con->query($sql) === TRUE) { //full connection
    
        return $con;
        } else {
        
            echo "Error creating table: " . $con->error;
        }

        return $con;
}

function multiTenancy($belongs, $table_name)
{
    if(empty($belongs)) die;


    $host = 'localhost';
    $password = '';
    $user = 'root';
    $con = "";
    $prefixed_db = "riskcurb_$belongs";
    $db = $prefixed_db;

    $con = new mysqli("$host", "$user", "$password");

    if (!$con) {
        exit(json_encode(array("status" => 404, "message" => "failed to connect to db")));
    }

    if (mysqli_select_db($con, $db)) {

        $con = new mysqli("$host", "$user", "$password", "$db");

        $con = multiTenancyTables($con,$table_name);
        return $con;

    } else {
        $sql = "CREATE DATABASE IF NOT EXISTS $db";
        if ($con->query($sql) === TRUE) {
            $con = new mysqli("$host", "$user", "$password", "$db");

            $con = multiTenancyTables($con,$table_name);
            return $con;

        } else {
            echo "Error creating database: " . $con->error;
        }
    }
    return $con;
}
// $con->close();
