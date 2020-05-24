<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION['name']) ) {
  die('ACCESS DENIED');
}


// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

//checks if save is clicked or not
if(isset($_POST['save'])){
    //checks if fields are filled or not
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1 ) {
        //error message if fields not filled
        $_SESSION["error"] = "All fields are required";
    }else {
            //checks if year and milage field are numbers or not 
            if(strrchr($_POST['email'],"@")) {

                $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :profile_id');
                        $stmt->execute(array(
                          ':fn' => $_POST['first_name'],
                          ':ln' => $_POST['last_name'],
                          ':em' => $_POST['email'],
                          ':he' => $_POST['headline'],
                          ':su' => $_POST['summary'],
                          ':profile_id' => $_POST['profile_id'])
                        );
                    $_SESSION['success'] = "Profile updated";
                    header("Location: index.php"); 
                    return;

            }else {
                //error message if year and milage fields are not numbers
                $_SESSION["error"] = "Email address must contain @";
            }
    }

    header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
    return;
}
// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>MANCHALA SNEHITH REDDY</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Editing Profile for <?= htmlentities($_SESSION["name"]) ?></h1>
<?php

if ( isset($_SESSION["error"])) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($_SESSION["error"])."</p>\n");
    unset($_SESSION["error"]);
}
?>

<form method="post">
<p>First Name: <input type="text" name="first_name" value="<?= htmlentities($row['first_name']) ?>" size="60"></p>
<p>Last Name: <input type="text" name="last_name" value="<?= htmlentities($row['last_name']) ?>" size="60"></p>
<p>Email: <input type="text" name="email" value="<?= htmlentities($row['email']) ?>" size="30"></p>
<p>Headline: <input type="text" name="headline" value="<?= htmlentities($row['headline']) ?>" size="80"></p><br>
Summary:<textarea name="summary" rows="10" cols="80" ><?= htmlentities($row['summary']) ?></textarea><br><br>
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<p><input type="submit" name="save" value="Save">
<input type="submit" name="cancel" value="Cancel"></p>
</form>
</div>
<script type="text/javascript" src="jquery.min.js"></script>
</body>
</html>



<p>Position: <input type="submit" id="addPos" value="+"/>
<div id="position_fields">
<div id="position1">
<p>Year:<input type="text" name="year1" value="2012"/>
<input type="button" value="-"onclick="$('#position1').remove(); return false;">
</p>
<textarea name="desc1" rows="8" cols="80">
fsvasfbvsf





</textarea>
</div>
</div></p>