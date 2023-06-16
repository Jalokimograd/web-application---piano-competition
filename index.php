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
    <link rel="stylesheet" href="style.scss">
</head>

<body>
    <header>
        <h2 class="logo">Konkurs Pianistyczny</h2>
        <nav class="navigation">
            <a href="#">Home</a>
            <a href="#">SQL code</a>
            <a id="btnDBSchema-popup" href="#">DB Schema</a>
            <button class="btnLogin-popup">Login</button>
        </nav>

    </header>

    <div class="wrapper">
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
                <div class="login-register">
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
                <div class="login-register">
                    <p>Already have an account? <a href="#" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <div id="DBSchema-popup" class="wrapper db-schema">
        <span id="DBSchemaPopup-close" class="icon-close"><ion-icon name="close"></ion-icon></span>
        <h2>Login</h2>

    </div>

    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
