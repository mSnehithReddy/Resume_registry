<?php
require_once "head.php";
require_once "pdo.php";
require_once "valid.php";
session_start();

 if ( ! isset($_SESSION['name']) ) {
  die('ACCESS DENIED');
}
// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

//checks if add is clicked or not
if(isset($_POST['save'])){


  $msg = validateProfile();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
    return;
  }

  $msg = validateEducation();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
    return;
  }

  $msg = validatePos();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
    return;
  }



                //inserting data
               $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :profile_id');
                        $stmt->execute(array(
                          ':fn' => $_POST['first_name'],
                          ':ln' => $_POST['last_name'],
                          ':em' => $_POST['email'],
                          ':he' => $_POST['headline'],
                          ':su' => $_POST['summary'],
                          ':profile_id' => $_POST['profile_id'])
                        );

                //Update Education Section By Deleting
                $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
                $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
                insertEducation($pdo,$_REQUEST['profile_id']);

                //Update Position Section By Deleting
                $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
                $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
                insertPosition($pdo,$_REQUEST['profile_id']);

                    $_SESSION['success'] = "Profile updated";
                    header("Location: index.php"); 
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



// Load the positions and edu positions
$positions = loadPos($pdo,$_REQUEST['profile_id']);
$schools = loadEduPos($pdo,$_REQUEST['profile_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>MANCHALA SNEHITH REDDY</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Edding Profile for <?= htmlentities($_SESSION["name"]) ?></h1>
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
<p>Summary:<br><textarea name="summary" rows="10" cols="80" ><?= htmlentities($row['summary']) ?></textarea></p>
<?php
$eduPos = 0;
echo ('<p>Education: <input type="submit" id="addEdu" value="+"/>'."\n");
echo ('<div id="edu_fields">'."\n");
foreach ($schools as $school) {
  $eduPos++;
  echo('<div id="edu_position'.$eduPos.'">'."\n");
  echo('<p>Year:<input type="text" name="edu_year'.$eduPos.'"');
  echo(' value="'.$school['year'].'"/>'."\n");
  echo('<input type="button" value="-"');
  echo('onclick="$(\'#edu_position'.$eduPos.'\').remove(); return false;">'."\n");
  echo("</p>\n");
  echo('<p>School:<input type="text" class="school" name="edu_school'.$eduPos.'" size="60" value="'.htmlentities($school['name']).'"/></p>'."\n");
  echo ("\n</div>\n");
}
echo "</div></p>\n";

$pos=0;
echo ('<p>Position: <input type="submit" id="addPos" value="+"/>'."\n");
echo ('<div id="position_fields">'."\n");
foreach ($positions as $position) {
  $pos++;
  echo('<div id="position'.$pos.'">'."\n");
  echo('<p>Year:<input type="text" name="year'.$pos.'"');
  echo(' value="'.$position['year'].'"/>'."\n");
  echo('<input type="button" value="-"');
  echo('onclick="$(\'#position'.$pos.'\').remove(); return false;">'."\n");
  echo("</p>\n");
  echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
  echo (htmlentities($position['description'])."\n");
  echo ("\n</textarea>\n</div>\n");
}
echo "</div></p>\n";
?>
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<p><input type="submit" name="save" value="Save">
<input type="submit" name="cancel" value="Cancel"></p>
</form>
</div>
<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript" src="jquery-ui-1.12.1/jquery-ui.min.js"></script>
<!-- jQuery Code to Generate Education field Automatically -->
<script type="text/javascript">
  var countEdu=<?= $eduPos ?>;

  $(document).ready( function() {
    console.log("document ready called edu");
    $('#addEdu').click( function(event){
      event.preventDefault();
      if(countEdu>=9) {
        alert("Maximum of nine Education entries exceeded");
        return;
      }
      countEdu++;
      console.log("Adding edu position "+countEdu);
      $('#edu_fields').append(
        '<div id="edu_position'+countEdu+'"> \
          <p>Year: <input type="text" name="edu_year'+countEdu+'" value=""/> \
          <input type="button" value="-"\
            onclick="$(\'#edu_position'+countEdu+'\').remove(); return false;"></p>\
            <p>School: <input type="text" class="school" name="edu_school'+countEdu+'" size="60" value=""/></p>\
            </div>'
        );
    
    $(".school").autocomplete({
      source: "school.php"
    });


    }); //end of click

    $(".school").autocomplete({
      source: "school.php"
    });


  }); //end of ready function

</script>
<!-- jQuery Code to Generate Position field Automatically -->
<script type="text/javascript">
  var countPos=<?= $pos ?>;

  $(document).ready( function() {
    console.log("document ready called");
    $('#addPos').click( function(event){
      event.preventDefault();
      if(countPos>=9) {
        alert("Maximum of nine position entries exceeded");
        return;
      }
      countPos++;
      console.log("Adding position "+countPos);
      $('#position_fields').append(
        '<div id="position'+countPos+'"> \
          <p>Year: <input type="text" name="year'+countPos+'" value=""/> \
          <input type="button" value="-"\
            onclick="$(\'#position'+countPos+'\').remove(); return false;"></p>\
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>'
        );
    })
  });

</script>
</body>
</html>