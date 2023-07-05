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
        header('refresh:2;url= home.php');
    }
    echo "Connected successfully";


    // Now we check if the data from the login form was submitted, isset() will check if the data exists.
    if ( !isset($_POST['username'], $_POST['password']) ) {
        // Could not get the data that should have been sent.
        exit('Please fill both the username and password fields!');
        header('refresh:2;url= home.php');
    }

    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.

    $stmt = pg_query_params($conn,
                         "SELECT id, password_hash, imie, nazwisko, email FROM pianisci WHERE username = $1",
                         array($_POST['username']));
    $access_level = 1;

    if (pg_num_rows($stmt) == 0){
        $stmt = pg_query_params($conn,
            "SELECT id, password_hash, imie, nazwisko, email FROM pracownicy WHERE username = $1",
            array($_POST['username']));
            $access_level = 2;
    }




    if (pg_num_rows($stmt) > 0) {
        $row = pg_fetch_array($stmt, 0);

        $id = $row['id'];
        $password_hash = $row['password_hash'];
        $name = $row['name'];
        $surname = $row['surname'];
        $email = $row['email'];


        if (password_verify($_POST['password'], $password_hash)) {
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['access_level'] = $access_level;
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['surname'] = $surname;
            $_SESSION['email'] = $email;

            echo 'Welcome ' . $_SESSION['username'] . '!';

            header('Location: home.php');
        } else {
            // Incorrect password
            echo ' Incorrect username and/or password!';
            header('refresh:2;url= index.php');
        }
    } else {
        // Incorrect username
        echo ' Incorrect username and/or password!';
        header('refresh:2;url= index.php');
    }
    pg_close($conn);
    //$conn->close();
?>
