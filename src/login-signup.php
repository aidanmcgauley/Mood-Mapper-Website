<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Signup</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <!-- JavaScript Bundle with Popper -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="./js/" defer></script>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login.css">

</head>
<body>

    <!-- Navbar -->
    <?php include 'nav.php'; ?>
    
    <div class="loginContainer formBorder text-center my-5">
        
        <form class="form" id="login">

            <h2 class="form__title">Login</h2>

            <div class="form__message form__message--error"></div>

            <div class="form__input-group">
                <input type="text" class="form__input" name="loginEmailUsername" id="loginEmailUsername" autofocus placeholder="Username or email">
                <div class="form__input-error-message"></div>
            </div>

            <div class="form__input-group">
                <input type="password" class="form__input" name="loginPassword" id="loginPassword" autofocus placeholder="Password">
                <div class="form__input-error-message"></div>
            </div>

            <button class="button btn btn-primary btn-lg" type="submit">Log in</button>
            
            <p class="form__text">
                <a class="form__link" id="linkCreateAccount">Don't have an account? Create account</a>
            </p>

        </form>

        <form action="process-signup.php" method="post" class="form  form--hidden" id="createAccount">

            <h2 class="form__title">Create Account</h2>

            <div class="form__message form__message--error"></div>

            <div class="form__input-group">
                <input type="text" class="form__input" id="username" name="username" autofocus placeholder="Username">
                <div class="form__input-error-message"></div>
            </div>

            <div class="form__input-group">
                <input type="text" class="form__input" id="firstname" name="firstname" autofocus placeholder="First name">
                <div class="form__input-error-message"></div>
            </div>

            <div class="form__input-group">
                <input type="text" class="form__input" id="surname" name="surname" autofocus placeholder="Surname">
                <div class="form__input-error-message"></div>
            </div>

            <div class="form__input-group">
                <input type="text" class="form__input" id="email" name="email" autofocus placeholder="Email Address">
                <div class="form__input-error-message"></div>
            </div>

            <div class="form__input-group">
                <input type="password" class="form__input" id="password" name="password" autofocus placeholder="Password">
                <div class="form__input-error-message"></div>
            </div>

            <div class="form__input-group">
                <input type="password" class="form__input" id="confirmpassword" name="confirmpassword" autofocus placeholder="Confirm Password">
                <div class="form__input-error-message"></div>
            </div>

            <button class="button btn btn-primary btn-lg" type="submit">Continue</button>
            
            <p class="form__text">
                <a class="form__link" id="linkLogin">Already have an account? Sign in</a>
            </p>
            
        </form>
    </div>
    
    
    <script src="../js/login-signup.js"></script>

</body>
</html>