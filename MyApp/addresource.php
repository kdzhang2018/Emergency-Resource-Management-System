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
}
$errorMsg = "";
//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['submit'])) {

    if (empty($_POST['name']) || empty($_POST['primaryESF']) || empty($_POST['latitude']) || empty($_POST['longitude'])
        || empty($_POST['costAmount']) || empty($_POST['selectCostUnit'])) {
        $errorMsg = "Please provide all information";
    }
    elseif (!is_numeric($_POST['latitude']) || $_POST['latitude'] < -90 || $_POST['latitude'] > 90) {
        $errorMsg = "Error: Provide valid latitude";
    }
    elseif (!is_numeric($_POST['longitude']) || $_POST['longitude'] < -180 || $_POST['longitude'] > 180) {
        $errorMsg = "Error: Provide valid longitude";
    }
    elseif (!is_numeric($_POST['costAmount']) || $_POST['costAmount'] <= 0) {
        $errorMsg = "Error: Provide valid cost";
    }

    else {
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $model = mysqli_real_escape_string($connect, $_POST['model']);
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $costAmount = $_POST['costAmount'];
        $primaryESF = $_POST['primaryESF'];
        $costUnit = $_POST['selectCostUnit'];
        if (empty($model))
            $query = "INSERT INTO Resource (name, status, longitude, latitude , esfNumber_R, costUnit_R, costAmount, username_R) " .
                "VALUES('$name', 'available', '$longitude', '$latitude', '$primaryESF', '$costUnit', '$costAmount', '{$_SESSION['username']}')";
        else
            $query = "INSERT INTO Resource (name, model, status, longitude, latitude , esfNumber_R, costUnit_R, costAmount, username_R) " .
                "VALUES('$name', '$model', 'available', '$longitude', '$latitude', '$primaryESF', '$costUnit', '$costAmount', '{$_SESSION['username']}')";
        if (!mysqli_query($connect, $query)) {
            print '<p class="error">Error: Failed to add new resource. ' . mysqli_error($connect) . '</p>';
            exit();
        }
        else {
            $errorMsg = "New resource added";
            $resourceID = mysqli_insert_id($connect);
        }

        if (!empty($_POST['selectESF'])) {
            foreach ($_POST['selectESF'] as $ESFNumber) {
                if ($ESFNumber == $primaryESF) {
                    $errorMsg = "New resource added, but additional ESF should not be the same as Primary ESF";
                }
                else {
                    $query = "INSERT INTO AdditionalESFs (ESFNumber_A, resourceID_A) VALUES ('$ESFNumber', '$resourceID')";
                    if (!mysqli_query($connect, $query)) {
                        print '<p class="error">Error: Failed to add additional ESF. ' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                }
            }
        }

        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $capability) {
                $capability = mysqli_real_escape_string($connect, $capability);
                $query = "INSERT INTO ResourceCapabilities VALUES ('$resourceID', '$capability')";
                if (!mysqli_query($connect, $query)) {
                    print '<p class="error">Error: Failed to add capability. ' . mysqli_error($connect) . '</p>';
                    exit();
                }
            }
        }
    }


}

?>

<html>

<head>
    <title>Add New Resource</title>
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
            <div class="title_name"><?php print "Add Resource" ?></div>
            <div class="features">

                <div class='profile_section'>

                    <form name="searchform" action="addresource.php" method="post">
                        <table width="80%">
                            <tr>
                                <?php
                                $items = array();
                                //if('POST' === $_SERVER['REQUEST_METHOD']) {
                                if (isset($_POST['add'])) {
                                    if( ! empty($_POST['item'])) {
                                        $items[] = $_POST['item'];
                                    }
                                    if(isset($_POST['items']) && is_array($_POST['items'])) {
                                        foreach($_POST['items'] as $item) {
                                            $items[] = $item;
                                        }
                                    }
                                }
                                ?>
                                <td class="item_label">Capabilities</td>
                                <td>
                                    <?php if ($items): ?>
                                        <ul>
                                            <?php foreach ($items as $item): ?>
                                                <li><?php echo $item; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <form method="post">
                                        <input type="text" name="item"/>
                                        <input type="submit" name = "add" value="Add Capability"/>
                                        <?php if ($items): ?>
                                            <?php foreach ($items as $item): ?>
                                                <input type="hidden" name="items[]" value="<?php echo $item; ?>"/>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Resource Name</td>
                                <td><input type="text" name="name"/></td>
                            </tr>
                            <tr>
                                <td class="item_label">Primary ESF</td>
                                <td>
                                    <?php
                                    $ESFQuery = "SELECT ESFNumber, CONCAT('(#', ESFNumber, ') ', ESFDescription) AS ESF " .
                                        "FROM ESF";
                                    $ESFResult = mysqli_query($connect, $ESFQuery);
                                    echo '<select name="primaryESF">';
                                    echo '<option value=""></option>';
                                    // Loop through the query results, outputing the options one by one
                                    while ($row = mysqli_fetch_array($ESFResult)) {
                                        echo '<option value="' . $row['ESFNumber'] . '">' . $row['ESF'] . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="item_label">Additional ESFs</td>
                                <td>
                                    <?php
                                    $ESFQuery = "SELECT ESFNumber, CONCAT('(#', ESFNumber, ') ', ESFDescription) AS ESF " .
                                        "FROM ESF";
                                    $ESFResult = mysqli_query($connect, $ESFQuery);
                                    echo '<select name="selectESF[]" multiple>';
                                    // Loop through the query results, outputing the options one by one
                                    while ($row = mysqli_fetch_array($ESFResult)) {
                                        echo '<option value="' . $row['ESFNumber'] . '">' . $row['ESF'] . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td class="item_label">Model</td>
                                <td><input type="text" name="model"/></td>
                            </tr>

                            <tr>
                                <td class="item_label">Home Location: </td>
                                <td>Lat <input type="text" name="latitude"/>
                                    Long <input type="text" name="longitude"/></td>
                            </tr>
                            <tr>
                                <td class="item_label">Cost</td>
                                <td>$ <input type="text" name="costAmount"/> per
                                    <?php
                                    $CostPerQuery = "SELECT costUnit AS per " .
                                        "FROM CostPer";
                                    $CostPerResult = mysqli_query($connect, $CostPerQuery);
                                    echo '<select name="selectCostUnit">';
                                    echo '<option value=""></option>';
                                    // Loop through the query results, outputing the options one by one
                                    while ($row = mysqli_fetch_array($CostPerResult)) {
                                        echo '<option value="' . $row['per'] . '">' . $row['per'] . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="submit" value="Save"/>


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