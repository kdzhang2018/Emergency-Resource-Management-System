<?php
$connect = mysqli_connect("localhost", "team063", "team063");
if (!$connect) {
    die("Failed to connect to database");
}
mysqli_select_db($connect, "cs6400_team063") or die( "Unable to select database");
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$errorMsg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['date']) or empty($_POST['description']) or empty($_POST['latitude']) or empty($_POST['longitude'])) {
        $errorMsg = "Please provide all information.";
    }
    elseif (!is_numeric($_POST['latitude']) || $_POST['latitude'] < -90 || $_POST['latitude'] > 90) {
        $errorMsg = "Error: Provide valid latitude";
    }
    elseif (!is_numeric($_POST['longitude']) || $_POST['longitude'] < -180 || $_POST['longitude'] > 180) {
        $errorMsg = "Error: Provide valid longitude";
    }
    elseif (!is_date($_POST['date'])) {
        $errorMsg = "Error: Provide valid date YYYY-MM-DD";
    }
    elseif (strtotime($_POST['date']) > strtotime('now') ){
        $errorMsg = "Error: Provide a date no later than today";
    }
    else {
        $username_inc = $_SESSION['username'];
        $date = $_POST['date'];
        $description = mysqli_real_escape_string($connect, $_POST['description']);
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $query = "INSERT INTO Incident (description, occurDate, latitude, longitude, username_inc) ".
            "VALUES('$description', '$date', '$latitude', '$longitude', '$username_inc')";
        if (!mysqli_query($connect, $query)) {
            print '<p class="error">Error: Failed to add new incident. ' . mysqli_error($connect) . '</p>';
            exit();
        }
        else {
            $errorMsg = "New accident added";
        }
    }
}
function is_date($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
?>
<html>

<head>
    <title>Add Incident</title>
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
            <div class="title_name"><?php print "Add Incident" ?></div>
            <div class="features">

                <div class='profile_section'>
                    <form name="searchform" action="addincident.php" method="post">
                        <table width="80%">

                            <tr>
                                <td class="item_label">Date </td>
                                <td><input type="text" name="date" value="<?php echo date('Y-m-d');?>"/></td>
                            </tr>
                            <tr>
                                <td class="item_label">Description</td>
                                <td><input type="text" name="description" /></td>
                            </tr>
                            <tr>
                                <td class="item_label">Location</td>
                                <td>Lat <input type="text" name="latitude" />
                                Long <input type="text" name="longitude" /></td>
                            </tr>
                        </table>
                        <input type="submit" name="submit" value="Save" />
                        <!--                        <input type=button onClick="self.close();" value="Cancel">-->

                    </form>
                    <?php
                    if (!empty($errorMsg)) {
                        print "<div style='color:red'>$errorMsg</div>";
                    }
                    ?>
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