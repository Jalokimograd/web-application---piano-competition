<?php

// tutaj są określane opcje które są dostępne na aktualnym etapie konkursu
require_once "connect.php";


$connecting_string = "host=$host dbname=$db_name user=$db_user password=$db_password port=$db_port";
$conn = pg_connect($connecting_string);
if (!$conn) {
    die("Connection failed ");
    header('refresh:2;url= home.php');
}


$submission_adding_available = FALSE;
$submission_acceptation_available = FALSE;
$performing_available = FALSE;
$evaluating_performance_available = FALSE;
$display_pianists_score = FALSE;

$competition = pg_fetch_array(pg_query($conn, "SELECT * from konkurs LIMIT 1"),0);
$competitionStage = $competition['etap_id'];


switch ($competitionStage){
    case 1:
        $submission_adding_available = TRUE;
        $submission_acceptation_available = TRUE;
        break;
    case 2:
        $submission_acceptation_available = TRUE;
        break;
    case 3:
        $performing_available = TRUE;
        $evaluating_performance_available = TRUE;
        break;
    case 4:
        $display_pianists_score = TRUE;
        break;
}

?>
