<?php
require_once "pdo.php";
require_once "util.php";
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Resume Registry</title>
    <?php
    require_once "head.php";
    ?>
</head>

<body>
    <div class="container">
        <h1>Resume Registry</h1>
        <?php
        flashMessages();

        if (!isset($_SESSION['name'])) {

        ?>
            <p>
                <a href="login.php">Please log in</a>
            </p>


            <?php
            echo ('<table border="1">' . "\n");
            $stmt = $pdo->query("SELECT * FROM profile");
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "
                <tr>
                <th>Name</th>
                <th>Headline</th>
                </tr>
                ";
            }
            $stmt = $pdo->query("SELECT * FROM profile");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>";
                echo ("<a href='view.php?profile_id=" . $row['profile_id'] . "'>" . htmlentities($row['first_name'] . ' ' . $row['last_name']) . '</a>');
                echo ("</td><td>");
                echo (htmlentities($row['headline']));
                echo ("</td></tr>\n");
            }
        } else { ?>

            <a href="logout.php">Logout</a>
            <br><br>

            <?php

            echo ('<table border="1">' . "\n");
            $stmt = $pdo->query("SELECT * FROM profile");
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "
                <tr>
                <th>Name</th>
                <th>Headline</th>
                <th>Action</th>
                </tr>
                ";
            }
            $stmt = $pdo->query("SELECT * FROM profile");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>";
                echo ("<a href='view.php?profile_id=" . $row['profile_id'] . "'>" . htmlentities($row['first_name'] . ' ' . $row['last_name']) . '</a>');
                echo ("</td><td>");
                echo (htmlentities($row['headline']));
                echo ("</td><td>");
                echo ('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
                echo ('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
                echo ("</td></tr>\n");
            }
            ?>

            </table>
            <br>
            <a href="add.php">Add New Entry</a>



        <?php } ?>

    </div>
</body>