<?php
session_start();

/*
|--------------------------------------------------------------
| SIGNUP FUNCTION
|--------------------------------------------------------------
*/
function signup($data)
{
        $errors = array();

        // safely get form values
        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $pass = $data['pass'] ?? '';
        $cpass = $data['cpass'] ?? '';

        // validate inputs
        if (!preg_match('/^[A-Za-z]+(?: [A-Za-z]+)*$/', $username)) {
                $errors[] = "Please enter a valid username";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Please enter a valid email";
        }

        if (strlen(trim($pass)) < 5) {
                $errors[] = "Password must be at least 5 characters";
        }

        if ($cpass != $pass) {
                $errors[] = "Confirm password does not match";
        }

        $check = database_run("SELECT  FROM users WHERE email = :email LIMIT 1", ['email'=>$data['email']]);

        if(is_array($check)){
                $errors[] = "That email is already exists";
        }

        // save to database
        if (count($errors) == 0) {
                $arr = [
                        'username' => $username,
                        'email' => $email,
                        'pass' => $pass,
                        'date' => date("Y-m-d H:i:s")
                ];

                $query = "INSERT INTO users (username, email, pass, date) VALUES (:username, :email, :pass, :date)";
                database_run($query, $arr);
        }

        return $errors;
}

/*
|--------------------------------------------------------------
| LOGIN FUNCTION
|--------------------------------------------------------------
*/
function login($data)
{
        $errors = array();

        // safely get form values
        $email = $data['email'] ?? '';
        $pass = $data['pass'] ?? '';

        // validate inputs
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Please enter a valid email";
        }

        if (strlen(trim($pass)) < 5) {
                $errors[] = "Password must be at least 5 characters";
        }

        // check credentials
        if (count($errors) == 0) {
                $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
                $row = database_run($query, ['email' => $email]);

                if (is_array($row) && count($row) > 0) {
                        $user = $row[0];
                        if ($pass === $user->pass) {
                                $_SESSION['USER'] = $user;
                                $_SESSION['LOGGED_IN'] = true;
                                header("Location: profile.php");
                                exit;
                        } else {
                                $errors[] = "Wrong email or password";
                        }
                } else {
                        $errors[] = "Wrong email or password";
                }
        }

        return $errors;
}

/*
|--------------------------------------------------------------
| CHECK LOGGED OR NOT
|--------------------------------------------------------------
*/
function check_login($redirect = true)
{
        if (isset($_SESSION['USER']) && isset($_SESSION['LOGGED_IN'])) {
                return true;
        }
        if ($redirect) {
                header("Location: login.php");
                die;
        } else {
                return false;
        }
}

/*
|--------------------------------------------------------------
| DATABASE FUNCTION
|--------------------------------------------------------------
*/
function database_run($query, $vars = array())
{
        $string = "mysql:host=localhost;dbname=verify_db";

        try {
                $con = new PDO($string, 'root', '');
                $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stm = $con->prepare($query);
                $check = $stm->execute($vars);

                if ($check) {
                        $data = $stm->fetchAll(PDO::FETCH_OBJ);
                        if (count($data) > 0) {
                                return $data;
                        }
                }
        } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
        }

        return false;
}

/*
|--------------------------------------------------------------
| CHECK VERIFICATION
|--------------------------------------------------------------
*/
function check_verifyed() {
        $id = $_SESSION['USER']->id;
        $query = "SELECT * FROM users WHERE id ='$id' LIMIT 1";
        $row = database_run($query);

        if(is_array($row)){
                $row = $row[0];

                if ($row->email == $row->email_verified) {
                        return true;
                }
        }
        return false;
}

?>
