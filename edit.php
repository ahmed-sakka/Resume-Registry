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
$em = htmlentities($row['email']);
$hl = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];


if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['save'])) {

    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    }
    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    }

    $msg = validateEdu();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    }

    $stmt = $pdo->prepare('UPDATE Profile SET first_name=:fn, last_name=:ln, email=:em, headline=:hl, summary=:su WHERE profile_id = :id');

    $stmt->execute(
        array(
            ':id' => $_REQUEST['profile_id'],
            ':fn' => htmlentities($_POST['first_name']),
            ':ln' => htmlentities($_POST['last_name']),
            ':em' => htmlentities($_POST['email']),
            ':hl' => htmlentities($_POST['headline']),
            ':su' => htmlentities($_POST['summary'])
        )
    );

    // Clear out the old schools entries
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    // Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    // insert positions and schools
    insertPos($pdo, $_REQUEST['profile_id']);
    insertEdu($pdo, $_REQUEST['profile_id']);

    $_SESSION['success'] = "Profile edited";
    header("Location: index.php");
    return;
}

$positions = loadPos($pdo, $_REQUEST['profile_id']);
$countPos = 0;

$schools = loadEdu($pdo, $_REQUEST['profile_id']);
$countEdu = 0;

?>

<!DOCTYPE html>
<html>

<head>
    <title>Profile Edit'</title>
    <?php
    require_once "head.php";
    ?>
</head>

<body>
    <div class="container">
        <h1>Editing Profile for <?php echo isset($_SESSION['name']) ? htmlentities($_SESSION['name']) : ''; ?></h1>

        <?php
        flashMessages();
        ?>


        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?= $fn ?>" /></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?= $ln ?>" /></p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?= $em ?>" /></p>
            <p>Headline:<br />
                <input type="text" name="headline" size="80" value="<?= $hl ?>" /></p>
            <p>Summary:<br />
                <textarea name="summary" rows="8" cols="80"><?= $su ?></textarea>
                <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
                <p>
                    Education: <input type="submit" id="addEdu" value="+">
                    <div id="edu_fields">

                        <?php
                        foreach ($schools as $school) {
                            $countEdu++;
                        ?>
                            <div id="edu<?= $school['rank'] ?>">
                                <p>Year: <input type="text" name="edu_year<?= $school['rank'] ?>" value="<?= $school['year'] ?>" />
                                    <input type="button" value="-" onclick="$('#edu<?= $school['rank'] ?>').remove();return false;"></p>
                                <p>School: <input type="text" size="80" name="edu_school<?= $school['rank'] ?>" class="school" value="<?= $school['name'] ?>" />
                            </div>
                        <?php
                        }
                        ?>

                    </div>
                </p>
                <p>
                    Position: <input type="submit" id="addPos" value="+">
                    <div id="position_fields">

                        <?php
                        foreach ($positions as $position) {
                            $countPos++;
                        ?>
                            <div id="position<?= $position['rank'] ?>">
                                <p>Year: <input type="text" name="year<?= $position['rank'] ?>" value="<?= $position['year'] ?>">
                                    <input type="button" value="-" onclick="$('#position<?= $position['rank'] ?>').remove();return false;"></p>
                                <textarea name="desc<?= $position['rank'] ?>" rows="8" cols="80"><?= $position['description'] ?></textarea>
                            </div>
                        <?php
                        }
                        ?>

                    </div>
                </p>
                <p>
                    <input type="submit" value="Save" name="save">
                    <input type="submit" name="cancel" value="Cancel">
                </p>
        </form>

        <script>
            countPos = <?= $countPos ?>;
            countEdu = <?= $countEdu ?>;

            $(document).ready(function() {
                window.console && console.log('Document ready called');

                $('#addPos').click(function(event) {
                    event.preventDefault();
                    if (countPos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position " + countPos);

                    $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
                });

                $('#addEdu').click(function(event) {
                    event.preventDefault();
                    if (countEdu >= 9) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEdu++;
                    window.console && console.log("Adding education " + countEdu);

                    // Grab some HTML with hot spots and insert into the DOM
                    var source = $("#edu-template").html();
                    $('#edu_fields').append(source.replace(/@COUNT@/g, countEdu));

                    // Add the even handler to the new ones
                    $('.school').autocomplete({
                        source: "school.php"
                    });

                });

                $('.school').autocomplete({
                    source: "school.php"
                });

            });
        </script>

        <!-- HTML with Substitution hot spots -->
        <script id="edu-template" type="text">
            <div id="edu@COUNT@">
            <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
            <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
            <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
            </p>
            </div>
        </script>

    </div>
</body>

</html>