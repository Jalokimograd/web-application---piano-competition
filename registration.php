<?php
    session_start();
    require_once "connect.php";


    // Create connection
    $conn = @new mysqli($host, $db_user, $db_pawword, $db_name);
    //pg_connect(string $connection_string, int $flags = 0): PgSql\Connection|false

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully";


    // Now we check if the data from the login form was submitted, isset() will check if the data exists.
    if ( !isset($_POST['username'], $_POST['email'], $_POST['password']) ) {
        // Could not get the data that should have been sent.
        exit('Please fill both the username and password fields!');
    }

    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
        // One or more values are empty.
        exit('Please complete the registration form');
    }

    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    if ($stmt = $conn->prepare('SELECT id, password_hash FROM pianisci WHERE username = ?')) {
        // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Username already exists
            echo 'Username exists, please choose another!';
        } else {
            if ($stmt = $conn->prepare('INSERT INTO pianisci (username, password, password_hash, email) VALUES (?, ?, ?, ?)')) {
                // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param("ssss", $_POST['username'], $_POST['password'], $password_hash, $_POST['email']);
                $stmt->execute();

                echo 'You have successfully registered! You can now login!';
                header('refresh:2;url= index.php');

            } else {
                // Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
                echo 'Could not prepare statement!';
            }
        }
        $stmt->close();
    } else {
        // Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
        echo 'Could not prepare statement!';
    }
    $conn->close();
?>
