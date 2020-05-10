<?php
require_once "pdo.php";
require_once "util.php";
session_start();
if (!isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Profile deleted';
    header('Location: index.php');
    return;
}

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false || $row['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$profile_id = $row['profile_id'];

?>
<!DOCTYPE html>
<html>

<head>
    <title>Profile Delete</title>
    <?php
    require_once "head.php";
    ?>
</head>

<body>
    <div class="container">

        <h1>Deleteing Profile</h1>
        <p><b>First Name:</b> <?= $fn ?></p>
        <p><b>Last Name:</b> <?= $ln ?></p>
        <form method="post">
            <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
            <input type="submit" value="Delete" name="delete">
            <input type="submit" value="Cancel" name="cancel">
        </form>

    </div>
</body>

</html>