<?php
include_once('host.php');
$data = "";
function custom_field($value, $table_name, $client, $name)
{
    $con = multiTenancy($client, $table_name);

    $sql = $con->query("SELECT value FROM $table_name WHERE name = '$name' AND belongs = '$client' ");
    //    exit(json_encode(array("status"=>200,"message"=>mysqli_num_rows($sql))));
    if (mysqli_num_rows($sql) > 0) {
        $con->query("UPDATE $table_name SET value = '$value' WHERE name = '$name' AND belongs = '$client'");
        $dataGet = $con->query("SELECT value FROM $table_name WHERE name = '$name' AND belongs = '$client'");
        if (mysqli_num_rows($dataGet) > 0) {
            while ($row = mysqli_fetch_assoc($dataGet)) {
                $data = $row["value"];
            }
        }
        exit(json_encode(array('status' => 200, "message" => "data fetched", $name => $data)));
    } else {
        $con->query("INSERT INTO $table_name (belongs,name,value) VALUES ('$client','$name','$value')");
        $dataGet = $con->query("SELECT value FROM $table_name WHERE name = '$name' AND belongs = '$client'");

        if (mysqli_num_rows($dataGet) > 0) {
            while ($row = mysqli_fetch_assoc($dataGet)) {
                $data = $row["value"];
            }
        }

        exit(json_encode(array('status' => 200, "message" => "data fetched", $name => $data)));
    }
}
