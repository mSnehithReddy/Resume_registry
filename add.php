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
if(isset($_POST['add'])){

//validation of Profile Section
  $msg = validateProfile();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: add.php"); 
    return;
  }
// validation of Position Section
  $msg = validateEducation();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: add.php"); 
    return;
  }
// validation of Position Section
  $msg = validatePos();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: add.php"); 
    return;
  }

    //inserting data of Profile Section
    $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
                  ':uid' => $_SESSION['user_id'],
                  ':fn' => $_POST['first_name'],
                  ':ln' => $_POST['last_name'],
                  ':em' => $_POST['email'],
                  ':he' => $_POST['headline'],
                  ':su' => $_POST['summary'])
                 );
    $profile_id = $pdo->lastInsertId(); // to know the profile_id of last Inserted Profile
    
      //insert Position Data
        insertPosition($pdo,$profile_id);

        //insert Education Data
        insertEducation($pdo,$profile_id);

                    $_SESSION['success'] = "Profile added";
                    header("Location: index.php"); 
                    return;

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>MANCHALA SNEHITH REDDY</title>
<?php require_once "bootstrap.php"; ?>
  <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
</head>
<body>
<div class="container">
<h1>Adding Profile for <?= htmlentities($_SESSION["name"]) ?></h1>
<?php

if ( isset($_SESSION["error"])) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($_SESSION["error"])."</p>\n");
    unset($_SESSION["error"]);
}
?>

<form method="post">
<p>First Name: <input type="text" name="first_name" size="60"></p>
<p>Last Name: <input type="text" name="last_name" size="60"></p>
<p>Email: <input type="text" name="email" size="30"></p>
<p>Headline: <input type="text" name="headline" size="80"></p><br>
<p>Summary:<br>
<textarea name="summary" rows="10" cols="80"></textarea></p>
<p>
  Education:<input type="submit" id="addEdu" name="+" value="+">
  <div id="edu_fields">
  </div>
</p>
<p>
  Position:<input type="submit" id="addPos" name="+" value="+">
  <div id="position_fields">
  </div>
</p>
<p><input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel"></p>
</form>
</div>
<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript" src="jquery-ui-1.12.1/jquery-ui.min.js"></script>
<!-- jQuery Code to Generate Education field Automatically -->
<script type="text/javascript">
  var countEdu=0;

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
  var countPos=0;

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
    });
  });

</script>
</body>
</html>