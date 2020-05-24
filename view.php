<?php
require_once "head.php";
require_once "pdo.php";
require_once "bootstrap.php";

// Guardian: first_name sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT first_name,last_name,email,headline,summary,profile_id FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div class="container">
	<h1>Profile information</h1>
	<p>First Name: <?= htmlentities($row['first_name']) ?></p>
	<p>Last Name: <?= htmlentities($row['last_name']) ?></p>
	<p>Email: <?= htmlentities($row['email']) ?></p>
	<p>Headline: <?= htmlentities($row['headline']) ?></p>
	<p>Summary: <?= htmlentities($row['summary']) ?></p>
	<?php

	$stmt = $pdo->query('SELECT year,name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id IN('.$_GET['profile_id'].') ORDER BY rank');
	if ($stmt->rowCount()>0) {
	echo "<p>Education";
	echo ('<ul id="list">');	
			/*$stmt->execute(array(":xyz" => $_GET['profile_id']));*/
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<li>".$row['year'].": ".$row['name']."</li>";
			}
					echo "</ul>\n</p>";
		
}




	$stmt = $pdo->query("SELECT year, description FROM Position WHERE profile_id IN(".$_GET['profile_id'].")");
	if ($stmt->rowCount()>0) {
	echo "<p>Position";
	echo ('<ul id="list">');	
			/*$stmt->execute(array(":xyz" => $_GET['profile_id']));*/
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				echo "<li>".$row['year'].": ".$row['description']."</li>";
			}
					echo "</ul>\n</p>";
		
}
?>
	<a href="index.php">Done</a>
</div>

<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript">
</script>
</body>
</html>