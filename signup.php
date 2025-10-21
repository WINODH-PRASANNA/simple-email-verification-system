<?php
require "functions.php";

$errors = array();

if($_SERVER['REQUEST_METHOD'] == "POST")
{
        $errors = signup($_POST);
        if(count($errors) == 0)
        {
                header("Location: login.php");
                die;
        }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Signup</title>
</head>
<body>
        <h1>Signup</h1>
        <?php include('header.php'); ?>

        <div>
                <div>
                        <?php if(count($errors) > 0) : ?>
                                <?php foreach ($errors as $error) : ?>
                                        <?= $error ?> <br>
                                <?php endforeach; ?>
                        <?php endif; ?>
                </div>
                <form method="POST" action="signup.php">
                        <input type="text" name="username" id="username" placeholder="username" require><br>
                        <input type="email" name="email" id="email" placeholder="email" require><br>
                        <input type="pass" name="pass" id="pass" placeholder="password" require><br>
                        <input type="cpass" name="cpass" id="cpass" placeholder="confirm password" require><br><br>
                        <input type="submit" value="Signup">
                </form>
        </div>
</body>
</html>