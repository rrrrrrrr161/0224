
<?php
// Include config file
require_once "dbconfig.php";

// Define variables and initialize with empty values
$username = $userpass = $usercnfm = "";
$username_err = $userpass_err = $usercnfm_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $conn = new PDO("mysql:host=$hostname;dbname=$database;charset=UTF8", $dbuser, $dbpass);
    // 設定錯誤處理模式 set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";
        if($stmt = $conn->prepare($sql)){

            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }

    // Validate password
    if(empty(trim($_POST["userpass"]))){
        $userpass_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["userpass"])) < 6){
        $userpass_err = "Password must have atleast 6 characters.";
    } else{
        $userpass = trim($_POST["userpass"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["usercnfm"]))){
        $usercnfm_err = "Please confirm password.";
    } else{
        $usercnfm = trim($_POST["usercnfm"]);
        if(empty($userpass_err) && ($userpass != $usercnfm)){
            $usercnfm_err = "Password did not match. $userpass $usercnfm";
        }
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($userpass_err) && empty($usercnfm_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO `users` (`username`, `userpass`) VALUES (:username, :userpass)";
        if($stmt = $conn->prepare($sql)){

            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":userpass", $param_userpass, PDO::PARAM_STR);

            // Set parameters
            $param_username = $username;
            $param_userpass = password_hash($userpass, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            unset($stmt);
        }
    }
    // Close connection
    unset($pdo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Register / Sign Up</title>
</head>
<body>
    <div class="container">

        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>

        <form action="" method="post">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>

            <div class="form-group">
                <label for="userpass">Password</label>
                <input type="password" name="userpass" id="userpass" class="form-control <?php echo (!empty($userpass_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $userpass; ?>">
                <span class="invalid-feedback"><?php echo $userpass_err; ?></span>
            </div>

            <div class="form-group">
                <label for="usercnfm">Confirm Password</label>
                <input type="password" name="usercnfm" id="usercnfm" class="form-control <?php echo (!empty($usercnfm_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $usercnfm; ?>">
                <span class="invalid-feedback"><?php echo $usercnfm_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>

            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
