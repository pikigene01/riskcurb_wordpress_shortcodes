<?php
include_once('includes/cors.php');
include_once('includes/host.php');
include_once('includes/functions.php');
$chatHistory = [];
$profileScore = "";
$appQuestion = "";
$combinedDataUser = "";
$appOptions = "";
$answered = "";

if (isset($_POST['apiData'])) {
    $belongs = $_POST['belongs'];
    $table_name = "wp_riskcurb_fields";

    $con = multiTenancy($belongs, $table_name);

    $chatHistoryData = $con->query("SELECT value FROM $table_name WHERE belongs = '$belongs' AND name = 'chatHistory'");
    $profileScoreData = $con->query("SELECT value FROM $table_name WHERE belongs = '$belongs' AND name = 'profileScore'");
    $appQuestionData = $con->query("SELECT value FROM $table_name WHERE belongs = '$belongs' AND name = 'appQuestion'");
    $combinedDataUserData = $con->query("SELECT value FROM $table_name WHERE belongs = '$belongs' AND name = 'combinedDataUser'");
    $appOptionsData = $con->query("SELECT value FROM $table_name WHERE belongs = '$belongs' AND name = 'appOptions'");
    $answeredData = $con->query("SELECT value FROM $table_name WHERE belongs = '$belongs' AND name = 'answered'");

    if (mysqli_num_rows($profileScoreData) > 0) {
        while ($row = mysqli_fetch_assoc($profileScoreData)) {
            $profileScore = $row['value'];
        }
    }
    if (mysqli_num_rows($chatHistoryData) > 0) {
        while ($row = mysqli_fetch_assoc($chatHistoryData)) {
            $chatHistory = $row['value'];
        }
    }
    if (mysqli_num_rows($appQuestionData) > 0) {
        while ($row = mysqli_fetch_assoc($appQuestionData)) {
            $appQuestion = $row['value'];
        }
    }
    if (mysqli_num_rows($combinedDataUserData) > 0) {
        while ($row = mysqli_fetch_assoc($combinedDataUserData)) {
            $combinedDataUser = $row['value'];
        }
    }
    if (mysqli_num_rows($appOptionsData) > 0) {
        while ($row = mysqli_fetch_assoc($appOptionsData)) {
            $appOptions = $row['value'];
        }
    }
    if (mysqli_num_rows($answeredData) > 0) {
        while ($row = mysqli_fetch_assoc($answeredData)) {
            $answered = $row['value'];
        }
    }




    exit(json_encode(
        array(
            "status" => 200,
            "message" => "data fetched successfully",
            "chatHistory" => $chatHistory,
            "profileScore" => $profileScore,
            "appQuestion" => $appQuestion,
            "combinedDataUser" => $combinedDataUser,
            "appOptions" => $appOptions,
            "answered" => $answered,
        )
    ));
}

if (isset($_POST['chatHistory'])) {
    custom_field($_POST['chatHistory'], 'wp_riskcurb_fields', $_POST['belongs'], 'chatHistory');
}
if (isset($_POST['profileScore'])) {
    custom_field($_POST['profileScore'], 'wp_riskcurb_fields', $_POST['belongs'], 'profileScore');
}
if (isset($_POST['appQuestion'])) {
    custom_field($_POST['appQuestion'], 'wp_riskcurb_fields', $_POST['belongs'], 'appQuestion');
}
if (isset($_POST['appOptions'])) {
    custom_field($_POST['appOptions'], 'wp_riskcurb_fields', $_POST['belongs'], 'appOptions');
}
if (isset($_POST['combinedDataUser'])) {
    custom_field($_POST['combinedDataUser'], 'wp_riskcurb_fields', $_POST['belongs'], 'combinedDataUser');
}
if (isset($_POST['answered'])) {
    custom_field($_POST['answered'], 'wp_riskcurb_fields', $_POST['belongs'], 'answered');
}
if (isset($_POST['field'])) {
    custom_field($_POST['field'], 'wp_riskcurb_fields', $_POST['belongs'], $_POST['field']);
}
