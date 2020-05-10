<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

// Check to see if we have some POST data, if we do process it
if (isset($_POST['email']) && isset($_POST['pass'])) {
    unset($_SESSION["username"]);  // Logout current user
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = "User name and password are required";
        header("Location: login.php");
        return;
    } else {
        $check = hash('md5', $salt . $_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
        $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row !== false) {
            error_log("Login success " . htmlentities($_POST['email']));
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return;
        } else {
            error_log("Login fail " . htmlentities($_POST['email']) . " $check");
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            return;
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <?php
    require_once "head.php";
    ?>
    <title>Resume Registry</title>
</head>

<body>
    <div class="container">
        <h1>Please Log In</h1>
        <?php

        if (isset($_SESSION['error'])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
            unset($_SESSION['error']);
        }
        ?>
        <form method="POST">
            <label for="nam">Email</label>
            <input name="email" id="email"><br />
            <label for="id_1723">Password</label>
            <input name="pass" id="id_1723"><br />
            <input type="submit" value="Log In" onclick="return doValidate();">
            <input type="submit" name="cancel" value="Cancel">
        </form>

    </div>

</body>

<script>
    function doValidate() {
        console.log('Validating...');
        try {
            addr = document.getElementById('email').value;
            pw = document.getElementById('id_1723').value;
            console.log("Validating addr=" + addr + " pw=" + pw);
            if (addr == null || addr == "" || pw == null || pw == "") {
                alert("Both fields must be filled out");
                return false;
            }
            if (addr.indexOf('@') == -1) {
                alert("Invalid email address");
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
        return false;
    }
</script>