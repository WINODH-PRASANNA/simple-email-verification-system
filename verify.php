<?php
require "mail.php";
require "functions.php";
check_login();

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == "GET" && !check_verifyed()) {
        // generate random 5-digit code
        $vars['code'] = rand(10000, 99999);

        // expire in 5 minutes
        $vars['expire'] = time() + (60 * 5);
        $vars['email'] = $_SESSION['USER']->email;

        // save code in database
        $query = "INSERT INTO verify (code, expire, email) VALUES (:code, :expire, :email)";
        database_run($query, $vars);

        // email message content
        $recipient = $vars['email'];
        $subject = "Email verification";
        $message = "Your verification code is " . $vars['code'];

        send_mail($recipient, $subject, $message);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (!check_verifyed()) {
                // use AND instead of &&
                $query = "SELECT * FROM verify WHERE code = :code AND email = :email";
                $vars = array();
                $vars['email'] = $_SESSION['USER']->email;
                $vars['code'] = $_POST['code'];

                $row = database_run($query, $vars);
                if (is_array($row) && count($row) > 0) {
                        $row = $row[0];
                        $time = time();

                        // FIXED: changed $row->expires to $row->expire
                        if ($row->expire > $time) {
                                $query = "UPDATE users SET email_verified = email WHERE email = :email LIMIT 1";
                                $vars = ['email' => $_SESSION['USER']->email];
                                database_run($query, $vars);

                                header("Location: profile.php");
                                die;
                        } else {
                                echo "Code expired";
                        }
                } else {
                        echo "Verification code is wrong!";
                }
        } else {
                echo "You're already verified";
        }
}
?>


<!DOCTYPE html>
<html lang="en">

        <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Verify</title>
        </head>

        <body>

                <h1>Verify</h1>
                <?php include('header.php'); ?>

                <div>
                        <br>We send your verification code to your email address<br>
                        <div>
                                <?php if (count($errors) > 0): ?>
                                        <?php foreach ($errors as $error): ?>
                                                <?= $error ?> <br>
                                        <?php endforeach; ?>
                                <?php endif; ?>
                        </div>

                        <form method="POST" action="">
                                <input type="text" name="code" id="code" placeholder="Enter your verification code" require><br><br>
                                <input type="submit" value="Verify">
                        </form>
                </div>

        </body>

</html>

<!-- 1.12.22 -->