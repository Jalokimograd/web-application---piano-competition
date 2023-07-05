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


    // Now we check if the data from the login form was submitted, isset() will check if the data exists.
    if ( !isset($_POST['name'], $_POST['surname']) ) {
        // Could not get the data that should have been sent.
        exit('Please fill both the name and surname fields!');
        header('refresh:2;url= home.php');
    }

    if (empty($_POST['name']) || empty($_POST['surname'])) {
        // One or more values are empty.
        exit('Please complete the form');
        header('refresh:2;url= home.php');
    }

    
    $stmt = pg_query_params($conn,
    "INSERT INTO kompozytorzy (Imie, Nazwisko) VALUES ($1, $2)",
        array($_POST['name'], $_POST['surname']));


    echo 'You have successfully add new Composer';
    header("Location: home.php");
    
    pg_close($conn);
?>
