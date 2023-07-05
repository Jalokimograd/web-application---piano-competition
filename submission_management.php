<?php
    session_start();
    require_once "connect.php";


    // Create connection
    $connecting_string = "host=$host dbname=$db_name user=$db_user password=$db_password port=$db_port";
    // echo $connecting_string;
    $conn = pg_connect($connecting_string);
    // Check connection
    if (!$conn) {
        die("Connection failed ");
        header('refresh:2;url= home.php');
    }

    if ( !isset($_POST['selected_ids']) ) {
        // Could not get the data that should have been sent.
        $selectedIds = ["NULL"];
    }
    else{
        $selectedIds = $_POST['selected_ids'];
    }

    $competitionStages = $_SESSION['competitionStages'];

    $placeholders = implode(',', array_fill(0, count($selectedIds), '%s'));
    $idListEscaped = array_map('pg_escape_string', $selectedIds);
    
    $sql = sprintf("UPDATE wykonania SET zaakceptowany = CASE WHEN id IN ($placeholders) THEN true ELSE false END", ...$idListEscaped);
    
    // Wykonanie zapytania
    $result = pg_query($conn, $sql);


    if ($result) {
        $rowsAffected = pg_affected_rows($result);
        if ($rowsAffected > 0) {
            echo "Succesful Update";
        } else {
            echo "No rows to update";
        }
    } else {
        echo "Error: " . pg_last_error($conn);
    }

    echo 'You have successfully change settings';
    header("Location: home.php");
    
    pg_close($conn);
?>
