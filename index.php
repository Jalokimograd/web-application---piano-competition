<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if ((isset($_SESSION['loggedin'])) && ($_SESSION['loggedin']==true)) {
	header('Location: home.php');
	exit;
}
?>

<!doctype html>
<html lang="pl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona konkursu Pianistycznego</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h2 class="logo">Konkurs Pianistyczny</h2>
        <nav class="navigation">
            <a href="#">Home</a>
            <a href="./konkurs_pianistyczny.sql">SQL code</a>
            <a id="btnDBSchema-popup" href="#">DB Schema</a>
            <button class="btnLogin-popup">Login</button>
        </nav>

    </header>

    <div id="login-popup" class="wrapper">
        <span id="LoginPopup-close" class="icon-close"><ion-icon name="close"></ion-icon></span>
        <div class="from-box login">
            <h2>Login</h2>
            <form action="login.php" method="post">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox">
                        Remember Me</label>
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="login-register text_button">
                    <p>Don't have an account? <a href="#" class="register-link">Register</a></p>
                </div>
            </form>
        </div>

        <div class="from-box register">
            <h2>Registration</h2>
            <form action="registration.php" method="post">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="name" required>
                    <label>Name</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="surname" required>
                    <label>Surname</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox">
                        I agree to the terms & conditions</label>
                </div>
                <button type="submit" class="btn">Register</button>
                <div class="login-register text_button">
                    <p>Already have an account? <a href="#" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <div id="DBSchema-popup" class="wrapper">
        <span id="DBSchemaPopup-close" class="icon-close"><ion-icon name="close"></ion-icon></span>
        <img style="width: 100%; height: 100%;" src="Baza_danych_schemat.png" alt="Baza danych">

    </div>

    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
