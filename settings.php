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


    $date = isset($_POST['date'])&&$_POST['date']!="" ? $_POST['date'] : null;

    
    $stmt = pg_query_params($conn,
    "UPDATE konkurs 
        SET
            data_zakonczenia = $1",
        array($date));


    echo 'You have successfully change settings';
    header("Location: home.php");
    
    pg_close($conn);
?>
