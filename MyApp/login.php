<?php
$connect = mysqli_connect("localhost", "team063", "team063", "cs6400_team063");
if (!$connect) {
    die("Failed to connect to database");
}
mysqli_select_db($connect, "cs6400_team063") or die("Unable to select database");

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {



    if (empty($_POST['username']) or empty($_POST['password'])) {
        $errorMsg = "Please provide both username and password.";
    } else {

        $username = mysqli_real_escape_string($connect,$_POST["username"]);
        $password = mysqli_real_escape_string($connect, $_POST["password"]);

        $query = "SELECT* FROM User WHERE BINARY username ='$username' AND BINARY password = '$password'";
        $result = mysqli_query($connect,$query);

        if (mysqli_num_rows($result) == 0) {
            /* login failed */
            $errorMsg = "Login failed.  Please try again.";

        } else {
            /* login successful */
            session_start();
            $_SESSION['username'] = $username;

            /* redirect to the mainMenu page */
            header('Location: mainMenu.php');
            exit();
        }

    }

}


?>

<!DOCTYPE html>

<html >
	
	<head>
		<title>Emergency Resource Management System</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
  
	<body>

		<div id="main_container">


			<div id="header">
				<div class="logo"><img height="130" width="400" src="images/welcome.gif" border="0" alt="" title="" /></div>
			</div>
     
			<div class="center_content">

                <div class="text_box">
		 
					    <form action="login.php" method="post">
				  
						<div class="title">Emergency Resource Management System</div>
							<div class="login_form_row">
							<label class="login_label">Username:</label>
							<input type="text" name="username" class="login_input" />
						</div>
										
						<div class="login_form_row">
							<label class="login_label">Password:</label>
							<input type="password" name="password" class="login_input" />
						</div>

							<input type="image" src="images/login.gif" class="login" />

                        </form>
				  
					<?php
					if (!empty($errorMsg)) {
						print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
					}
					?>
				</div>

                <div class="clear"><br/></div>

            </div>



		</div>

	</body>
</html>