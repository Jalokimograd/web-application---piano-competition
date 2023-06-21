<?php
    session_start();
    require_once "connect.php";


    // Create connection
    $connecting_string = "host=$host dbname=$db_name user=$db_user password=$db_password port=$db_port";
    // echo $connecting_string;
    $conn = pg_connect($connecting_string);
    // Check connection
    if (!$conn) {
        die("Connection failed");
    }
    echo "Connected successfully";


    // Now we check if the data from the login form was submitted, isset() will check if the data exists.
    if ( !isset($_POST['username'], $_POST['name'], $_POST['surname'], $_POST['email'], $_POST['password']) ) {
        // Could not get the data that should have been sent.
        exit('Please fill both the username and password fields!');
    }

    if (empty($_POST['username'])|| empty($_POST['name'])|| empty($_POST['surname']) || empty($_POST['password']) || empty($_POST['email'])) {
        // One or more values are empty.
        exit('Please complete the registration form');
    }

    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    $stmt = pg_query_params($conn,
    "SELECT id, password_hash FROM pianisci WHERE username = $1",
    array($_POST['username']));
    

    if (pg_num_rows($stmt) > 0) {
        // Username already exists
        echo 'Username exists, please choose another!';
    } else {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $stmt = pg_query_params($conn,
        "INSERT INTO pianisci (username, Imie, Nazwisko, password, password_hash, email) VALUES ($1, $2, $3, $4, $5, $6)",
        array($_POST['username'], $_POST['name'], $_POST['surname'], $_POST['password'], $password_hash, $_POST['email']));


        echo 'You have successfully registered! You can now login!';
        header('refresh:2;url= index.php');
    }
    pg_close($conn);
?>
