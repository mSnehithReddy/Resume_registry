<?php
require_once "pdo.php";
require_once "bootstrap.php"; 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>MANCHALA SNEHITH REDDY</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Snehith's Resume Registry</h1>
<?php

if ( isset($_SESSION["error"])) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($_SESSION["error"])."</p>\n");
    unset($_SESSION["error"]);
}

if ( isset($_SESSION['success']) ) {
  echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
  unset($_SESSION['success']);
}
?>
<?php
    if(! isset($_SESSION['name']) && ! isset($_SESSION['user_id'])) {
        echo "<p><a href='login.php'>Please log in</a></p>";
    } else {
        echo "<p><a href='logout.php'>Logout</a></p>";
    }

 ?>
<?php
    $stmt = $pdo->query("SELECT first_name, last_name, headline,profile_id FROM Profile");
if($stmt->rowCount()>0){
    echo('<table border="1">'."\n");
    echo "<tr>
            <th> Name </th>
            <th> Headline </th>"; if (isset($_SESSION['name']) && isset($_SESSION['user_id'])) {
                echo "<th> Action </th>";
            }
            echo "<tr>";
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo('<a href= "view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name'])." ".htmlentities($row['last_name']).'</a>');
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    if (isset($_SESSION['name']) && isset($_SESSION['user_id'])){
    echo("</td><td>"); 
    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    }
    echo("</td></tr>\n");
    }
    echo "</table>";
} else {
  echo "<p>No rows Found</p>";
}
if (isset($_SESSION['name']) && isset($_SESSION['user_id'])){
echo "<p><a href='add.php'>Add New Entry</a></p>";
}
?>

</div>
<script type="text/javascript" src="jquery.min.js"></script>
</script>
</body>
</html>