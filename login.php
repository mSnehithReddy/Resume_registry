<?php // Do not put any HTML above this line
//view
require_once "pdo.php";

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
session_start();
$salt = 'XyZzy12*_';


// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION["error"] = "User name and password are required";
    } else {
        if(strrchr($_POST['email'],"@")) //checks if email has @ or not
        {
            $check = hash('md5', $salt.$_POST['pass']);
            $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
            $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $row !== false ) {
            // Redirect the browser to autos.php
            /*unset($_SESSION["name"]);*/
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            error_log("Login success ".$_POST['email']." $check");
            header("Location: index.php");
            return;
            } else {
            $_SESSION["error"] = "Incorrect password";
            error_log("Login fail ".$_POST['email']." $check");
            }

        } else
        {
            $_SESSION["error"] = "Email must have an at-sign (@)";
        }
        
    }

    header("Location: login.php");
    return;
}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>MANCHALA SNEHITH REDDY</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION["error"]) ) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($_SESSION["error"])."</p>\n");
    unset($_SESSION["error"]);
}
?>
<form method="POST" action="login.php">
<label for="nam">User Name</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the four character sound a cat
makes (all lower case) followed by 123. -->
</p>
</div>
<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript">
    function doValidate() {
        console.log('Validating...');
        console.log('Somet');

        try {

        email = document.getElementById('nam').value;
        pw = document.getElementById('id_1723').value;
        
        console.log("Validating addr="+email+"pw="+pw);

        if (pw == null || pw == "" || email == null || email =="") {

        alert("Both fields must be filled out");

        return false;

            }else if(!email.includes("@")){

                alert("Invalid email Address");
                return false;
            }

        return true;

        } catch(e) {

        return false;

        }

        return false;

        }
</script>
</body>
</html>



