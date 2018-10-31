<?php
$connect = mysqli_connect("localhost", "team063", "team063");
if (!$connect) {
    die("Failed to connect to database");
}
mysqli_select_db($connect, "cs6400_team063") or die("Unable to select database");

session_start();


if (!isset($_SESSION['username'])) {

    header('Location: login.php');
    exit();
} else {

    $username = $_SESSION['username'];

}

//find out current user's information

$query = "SELECT name "
        ."FROM User WHERE username = '{$_SESSION['username']}'";
$result = mysqli_query($connect, $query);
$row = mysqli_fetch_array($result);
if (!$row) {
    print "<p>Error: No data returned from database.</p>";
    exit();
}
$array = $row[0];

$query1 = "SELECT* FROM Individual WHERE username_Ind='{$_SESSION['username']}'";
$query2 = "SELECT* FROM Municipality WHERE username_M='{$_SESSION['username']}'";
$query3 = "SELECT* FROM Company WHERE username_C='{$_SESSION['username']}'";
$query4 = "SELECT* FROM GovermentAgency WHERE username_G='{$_SESSION['username']}'";


$result1 = mysqli_query($connect, $query1);
$result2 = mysqli_query($connect, $query2);
$result3 = mysqli_query($connect, $query3);
$result4 = mysqli_query($connect, $query4);

if (mysqli_num_rows($result1) > 0) {
    $row = mysqli_fetch_array($result1);
    $array_2 = " Position: " . $row[1] . ", working from    " . $row[2] . "";
} elseif (mysqli_num_rows($result2) > 0) {
    $row = mysqli_fetch_array($result2);
    $array_2 = " Population size:  " . $row[1];
} elseif (mysqli_num_rows($result3) > 0) {
    $row = mysqli_fetch_array($result3);
    $array_2 = " Headquater Location:  " . $row[1];
} else {
    $row = mysqli_fetch_array($result4);
    $array_2 = " Jurisdiction:  " . $row[1];
}


// if the info is not found out.

if (!$row) {
    print "<p>Error: No data returned from database. </p>";

}

?>


<!DOCTYPE html >
<html>
<head>
    <title>Main Menu</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>

<body>

<div id="main_container">

         <div id="header">
             <div class="logo"><img src="images/ERMS2.png" border="0" alt="" title="" height="100" width="100"/></div>
         </div>


        <div class="menu">
            <ul>

                <li><a href="mainMenu.php">Main</a></li>
                <li><a href="addresource.php">Add Resource</a></li>
                <li><a href="addincident.php">Add Incident</a></li>
                <li><a href="searchresource.php">Search Resource</a></li>
                <li><a href="resourcestatus.php">Resource Status</a></li>
                <li><a href="resourceReport.php">Resource Report</a></li>
                <li><a href="logout.php">Log out</a></li>

            </ul>
        </div>


        <div class="center_content">
            <div class="center_left">
                <div class="title_name">Welcome to ERMS</div>
                <div class="features">
                    <div class="profile_section">
                    <!--<center>
                        <h1> Welcome to ERMS! </h1>
                        <h3>   <?php /*echo $array. "<br>";
                        echo $array_2; */?> <h3>
                    </center>-->

                        <div class="subtitle"><?php echo $array; ?></div>
                        <h3><?php echo $array_2; ?><h3>

                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>

    <div id="footer">
        <div class="right_footer"><a </a></div>
    </div>
    </div>
</body>
</html>				
