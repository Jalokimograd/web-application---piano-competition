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
    }
    echo "Connected successfully ";


    // Now we check if the data from the login form was submitted, isset() will check if the data exists.
    if ( !isset($_POST['title'], $_POST['composer_id']) ) {
        // Could not get the data that should have been sent.
        exit('Please fill both the name and surname fields!');
        header('refresh:3;url= home.php');
    }

    if (empty($_POST['title']) || empty($_POST['composer_id'])) {
        // One or more values are empty.
        exit('Please complete the form');
        header('refresh:3;url= home.php');
    }

    $stmt = pg_query_params($conn,
    "SELECT id FROM utwory WHERE tytul = $1 AND kompozytor_id = $2",
    array($_POST['title'], $_POST['composer_id']));


    if (pg_num_rows($stmt) > 0) {
        // Username already exists
        echo 'Song exists, please choose another!';
        header('refresh:3;url= home.php');
    } else {
        $stmt = pg_query_params($conn,
        "INSERT INTO utwory (tytul, kompozytor_id) VALUES ($1, $2)",
            array($_POST['title'], $_POST['composer_id']));


        echo 'You have successfully add new Song';
        header('refresh:2;url= home.php');
    }
    pg_close($conn);
?>
