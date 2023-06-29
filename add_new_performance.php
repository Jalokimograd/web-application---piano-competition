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
    echo "Connected successfully ";


    if ( !isset($_POST['song_id']) ) {
        // Could not get the data that should have been sent.
        exit('Please fill both the name and surname fields!');
        header('refresh:2;url= home.php');
    }

    if (empty($_POST['song_id'])) {
        // One or more values are empty.
        exit('Please complete the form');
        header('refresh:2;url= home.php');
    }

    
    $stmt = pg_query_params($conn,
    "INSERT INTO wykonania (Pianisci_Id, Utwory_Id) VALUES ($1, $2)",
        array($_SESSION['id'], $_POST['song_id']));


    echo 'You have successfully add new Performance';
    header('refresh:2;url= home.php');
    
    pg_close($conn);
?>
