<?php
    session_start();
    require_once "connect.php";


    // Create connection
    //$conn = @new mysqli($host, $db_user, $db_password, $db_name);

    $connecting_string = "host=$host dbname=$db_name user=$db_user password=$db_password port=$db_port";
    // echo $connecting_string;
    $conn = pg_connect($connecting_string);

    // echo $conn;

    // Check connection
    if (!$conn) {
        die("Connection failed");
    }
    echo "Connected successfully";


    // Now we check if the data from the login form was submitted, isset() will check if the data exists.
    if ( !isset($_POST['id'], $_POST['rate']) ) {
        // Could not get the data that should have been sent.
        exit('wrong data');
        header('refresh:2;url= home.php');
    }

    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.

    pg_query_params($conn,
                         "UPDATE wykonania SET ocena = $1 WHERE id = $2 AND wykonany = TRUE",
                         array($_POST['rate'], $_POST['id']));



    pg_close($conn);

    echo 'You have successfully add rating';
    header("Location: home.php");
?>
