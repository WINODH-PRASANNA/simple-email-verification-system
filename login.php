<?php
require "functions.php";

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $errors = login($_POST);
        if (count($errors) == 0) {
                header("Location: profile.php");
                die;
        }
}
?>

<!DOCTYPE html>
<html lang="en">

        <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Login</title>
        </head>

        <body>
                <h1>Login</h1>
                <?php include('header.php'); ?>

                <div>
                        <div>
                                <?php if (count($errors) > 0): ?>
                                        <?php foreach ($errors as $error): ?>
                                                <?= $error ?> <br>
                                        <?php endforeach; ?>
                                <?php endif; ?>
                        </div>

                        <form method="POST" action="">
                                <input type="email" name="email" id="email" placeholder="Email" require><br>
                                <input type="password" name="pass" id="pass" placeholder="Password" require><br><br>
                                <input type="submit" value="Login">
                        </form>
                </div>
        </body>

</html>