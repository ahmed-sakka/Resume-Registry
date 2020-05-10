<?php
require_once "pdo.php";
require_once "util.php";
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Profile View</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <?php
        if (!isset($_GET['profile_id'])) {
            $_SESSION['error'] = "Missing profile_id";
            header('Location: index.php');
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
        $stmt->execute(array(":xyz" => $_GET['profile_id']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            $_SESSION['error'] = 'Could not load profile';
            header('Location: index.php');
            return;
        }

        $fn = htmlentities($row['first_name']);
        $ln = htmlentities($row['last_name']);
        $em = htmlentities($row['email']);
        $hl = htmlentities($row['headline']);
        $su = htmlentities($row['summary']);
        $profile_id = $row['profile_id'];
        ?>

        <h1>Profile information</h1>
        <p><b>First Name:</b> <?= $fn ?></p>
        <p><b>Last Name:</b> <?= $ln ?></p>
        <p><b>Email:</b> <?= $em ?></p>
        <p><b>Headline:</b> <br><?= $hl ?></p>
        <p><b>Summary:</b> <br><?= $su ?></p>
        <p><b>Education:</b> <br>
            <ul>
                <?php
                $stmt = $pdo->prepare("SELECT rank,year,name FROM Education, institution WHERE profile_id = :prof AND education.institution_id = institution.institution_id ORDER BY rank");
                $stmt->execute(array(":prof" => $_REQUEST['profile_id']));
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                ?>

                    <li><?= $row['year'] ?>: <?= $row['name'] ?></li>

                <?php
                }
                ?>
            </ul>
        </p>
        <p><b>Position:</b> <br>
            <ul>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
                $stmt->execute(array(":xyz" => $_REQUEST['profile_id']));
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                ?>

                    <li><?= $row['year'] ?>: <?= $row['description'] ?></li>

                <?php
                }
                ?>
            </ul>
        </p>
        <a href="index.php">Done</a>

    </div>
</body>