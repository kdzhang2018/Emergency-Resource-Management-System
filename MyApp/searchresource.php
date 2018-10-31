
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
// if form was submitted, then execute query to search for resources
//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['submit'])) {
    $keyword = mysqli_real_escape_string($connect, $_POST['Keyword']);
    $ESF = $_POST['selectESF'];

    $location = $_POST['Location'];
    $incident = $_POST['selectIncident'];

    $query = "SELECT DISTINCT Resource.resourceID AS ID, Resource.name AS Name, Resource.status, User.username, User.name AS Owner, ".
        "(CONCAT('$', Resource.costAmount, '/', Resource.costUnit_R)) AS Cost, ".
        "Resource.status AS Status, Resource.availableDate AS NextAvailable ".
        "FROM Resource INNER JOIN User ON Resource.username_R = User.username ".
        "LEFT OUTER JOIN ResourceCapabilities ON Resource.resourceID = ResourceCapabilities.resourceID_Res ".
        "LEFT OUTER JOIN AdditionalESFs ON Resource.resourceID = AdditionalESFs.resourceID_A ".
        "WHERE 1=1 ";

    if (!empty($incident) && empty($location)) {
        $errorMsg = "Error: Provide positive number in Location";
    }

    if (empty($incident) && !empty($location)) {
        $errorMsg = "Error: Choose incident";
        if (!is_numeric($location) || $location <= 0) {
            $errorMsg = "Error: Choose incident and Provide positive number in Location";
        }
    }

    if (!empty($incident) && !empty($location)) {
        if (!is_numeric($location) || $location <= 0) {
            $errorMsg = "Error: Provide positive number in Location";
            $incident = "";
            $location = "";
        }
        else {
            $incidentQuery = "SELECT * " .
                "FROM Incident WHERE incidentID = '$incident'";
            $incidentResult = mysqli_query($connect, $incidentQuery);
            if (!$incidentResult) {
                print "<p class='error'>Error: " . mysqli_error($connect) . "</p>";
                exit();
            }
            $row = mysqli_fetch_array($incidentResult);
            if (!$row) {
                print "<p>Error: No data returned from database.</p>";
                exit();
            }
            $lat = $row['latitude'];
            $long = $row['longitude'];
            $incidentDescription = $row['description'];

            $query = "SELECT DISTINCT Resource.resourceID AS ID, Resource.name AS Name, Resource.status, User.username, User.username, User.name AS Owner, " .
                "(CONCAT('$', Resource.costAmount, '/', Resource.costUnit_R)) AS Cost, " .
                "Resource.status AS Status, Resource.availableDate AS NextAvailable, " .
                "(((acos(sin(($lat*pi()/180)) * sin((latitude*pi()/180))+cos(($lat*pi()/180)) * cos((latitude*pi()/180)) * cos((($long-longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344) AS Distance " .
                "FROM Resource INNER JOIN User ON Resource.username_R = User.username " .
                "LEFT OUTER JOIN ResourceCapabilities ON Resource.resourceID = ResourceCapabilities.resourceID_Res " .
                "LEFT OUTER JOIN AdditionalESFs ON Resource.resourceID = AdditionalESFs.resourceID_A " .
                "WHERE (((acos(sin(($lat*pi()/180)) * sin((latitude*pi()/180))+cos(($lat*pi()/180)) * cos((latitude*pi()/180)) * cos((($long-longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344) <= $location ";

        }
    }

    if (!empty($keyword)) {
        $query = $query."AND (Resource.name LIKE '%$keyword%' ".
                        "OR Resource.model LIKE '%$keyword%' ".
                        "OR ResourceCapabilities.capability LIKE '%$keyword%') ";
    }

    if (!empty($ESF)) {
        $query = $query. "AND (Resource.ESFNumber_R = $ESF " .
                        "OR AdditionalESFs.ESFNumber_A = $ESF) ";
    }
    
    if (!empty($incident) && !empty($location)) {
        $query = $query."ORDER BY Distance, Resource.name";
    }
    else {
        $query = $query."ORDER BY Resource.name";
    }
    
    $result = mysqli_query($connect, $query);

    if (!$result) {
        print "<p class='error'>Error: ".mysqli_error($connect)."</p>";
        exit();
    }
}

?>


<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">-->
<html>
<head>
    <title>Search Resource</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
<!--    <meta http-equiv="refresh" content="10" >-->
    <style>
        table.searchresults, th.resultheader, td.resultdata {
            font-size:12px;
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
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
                <div class="title_name"><?php print "Search Resource" ?></div>


                <div class="features">

                    <div class='profile_section'>

<!--                        <div class="subtitle">Search Resource</div>-->
                        <form name="searchform" action="searchresource.php" method="post">
                            <table width="80%">
                                <tr>
                                    <td class="item_label">Keyword</td>
                                    <td><input type="text" name="Keyword" /></td>
                                </tr>
                                <tr>
                                    <td class="item_label">ESF</td>
                                    <td>
                                        <?php
                                        $ESFQuery = "SELECT ESFNumber, CONCAT('(#', ESFNumber, ') ', ESFDescription) AS ESF ".
                                            "FROM ESF";
                                        $ESFResult = mysqli_query($connect, $ESFQuery);

                                        echo '<select name="selectESF">';
                                        echo '<option value=""></option>';
                                        // Loop through the query results, outputing the options one by one
                                        while ($row = mysqli_fetch_array($ESFResult)) {
                                            echo '<option value="'.$row['ESFNumber'].'">'.$row['ESF'].'</option>';
                                        }
                                        echo '</select>';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Location</td>
                                    <td>Within <input type="text" name="Location" /> Kilometers of incident</td>

                                </tr>
                                <tr>
                                    <td class="item_label">Incident</td>
                                    <td>
                                        <?php
                                        // only show incident owned by the current user
                                        $incidentQuery = "SELECT incidentID, CONCAT('(', incidentID, ') ', description) AS Incident ".
                                            "FROM Incident WHERE username_Inc = '{$_SESSION['username']}'";
                                        $incidentResult = mysqli_query($connect, $incidentQuery);

                                        echo '<select name="selectIncident">';
                                        echo '<option value=""></option>';
                                        // Loop through the query results, outputing the options one by one
                                        while ($row = mysqli_fetch_array($incidentResult)) {
                                            echo '<option value="'.$row['incidentID'].'">'.$row['Incident'].'</option>';
                                        }
                                        echo '</select>';
                                        ?>
                                    </td>
                                </tr>

                            </table>
                            <input type="submit" name="submit" value="Search" />
<!--                            <a href="javascript:searchform.submit();" class="fancy_button">search</a>-->
                            <?php
                            if (!empty($errorMsg)) {
                                print "<div style='color:red'>$errorMsg</div>";
                            }
                            ?>
                        </form>
                    </div>

                        <?php
                        if (isset($result)) {
                            print "<div class='profile_section'>";
                            print "<div class='subtitle'>Search Results</div>";
                            if (!empty($incident) && !empty($location)) {
                                print $incidentDescription;
                            }

                            print "<table class = 'searchresults' width='80%'>";
                            print "<tr>
                                    <th class='resultheader'>ID</th>
                                    <th class='resultheader'>Name</th>
                                    <th class='resultheader'>Owner</th>
                                    <th class='resultheader'>Cost</th>
                                    <th class='resultheader'>Status</th>
                                    <th class='resultheader'>Next Available</th>";

                            if (!empty($incident) && !empty($location)) {
                                print "<th class='resultheader'> Distance</th>
                                        <th class='resultheader'> Action</th>";
                            }

                            print "</tr>";

                            while ($row = mysqli_fetch_array($result)){
                                print "<tr>";
                                print "<td class='resultdata'>{$row['ID']}</td>";
                                print "<td class='resultdata'>{$row['Name']}</td>";
                                print "<td class='resultdata'>{$row['Owner']}</td>";
                                print "<td class='resultdata'>{$row['Cost']}</td>";
                                print "<td class='resultdata'>{$row['Status']}</td>";
                                if ($row['status'] == 'available') {
                                    print "<td class='resultdata'>NOW</td>";
                                }
                                else {
                                    print "<td class='resultdata'>{$row['NextAvailable']}</td>";
                                }

                                if (!empty($incident) && !empty($location)) {
                                    $distance = number_format((float)$row['Distance'], 2, '.', '');
                                    print "<td class='resultdata'>$distance</td>";

                                    $_SESSION['incidentID'] = $incident;

                                    if ($row['username'] == $_SESSION['username'] && $row['status'] != 'in repair') {
                                        // check if the resource has scheduled repair
                                        $repairQuery = "SELECT startDate_R ".
                                            "FROM Repair ".
                                            "WHERE resourceID_Rep = '{$row['ID']}'";
                                        $repairResult = mysqli_query($connect, $repairQuery);
                                        $scheduledRepair = false;
                                        if ($repairResult) {
                                            while ($repairRow = mysqli_fetch_array($repairResult)) {
                                                if ($repairRow['startDate_R'] > date('Y-m-d')) {
                                                    $scheduledRepair = true;
                                                }
                                            }
                                        }
                                        if (!$scheduledRepair) {
                                            // resource owned, in use and has no scheduled repair: show repair button
                                            if ($row['status'] == 'in use') {
                                                print '<td class=resultdata><input type=button value="Repair" onClick=
                                                        window.open("repair.php?ID=' . $row['ID'] . '","Ratting","width=550,height=170,left=150,top=200,toolbar=0,status=0,");></td>';

                                            }
                                            // resource owned, available, has no scheduled repair, and has not been requested by the incident:
                                            // show deploy and repair button
                                            else if ($row['status'] == 'available') {
                                                // check if the resource has been requested or deployed by the incident
                                                $requestQuery = "SELECT * " .
                                                    "FROM Request " .
                                                    "WHERE resourceID_Req = '{$row['ID']}' and incidentID_Req = $incident";
                                                $requestResult = mysqli_query($connect, $requestQuery);

                                                // if the resource has not been requested by the incident: show deploy and repair
                                                if (mysqli_num_rows($requestResult) == 0) {
                                                    print '<td class=resultdata><input type=button value="Deploy" onClick=
                                                         window.open("deployowned.php?ID=' . $row['ID'] . '","Ratting","width=550,height=170,left=150,top=200,toolbar=0,status=0,");>
                                                         <input type=button value="Repair" onClick=
                                                         window.open("repair.php?ID=' . $row['ID'] . '","Ratting","width=550,height=170,left=150,top=200,toolbar=0,status=0,");></td>';
                                                }
                                                else {
                                                    print '<td class=resultdata><input type=button value="Repair" onClick=
                                                        window.open("repair.php?ID=' . $row['ID'] . '","Ratting","width=550,height=170,left=150,top=200,toolbar=0,status=0,");></td>';

                                                }
                                            }
                                        }
                                        // resource owned, available or in use, but has scheduled repair
                                        else {
                                            print "<td class=resultdata></td>";
                                        }
                                    }
                                    // resource of others and not in repair
                                    else if ($row['username'] != $_SESSION['username'] && $row['status'] != 'in repair') {
                                        // check if the resource has been requested or deployed by the incident
                                        $requestQuery = "SELECT * " .
                                            "FROM Request " .
                                            "WHERE resourceID_Req = '{$row['ID']}' and incidentID_Req = $incident";
                                        $requestResult = mysqli_query($connect, $requestQuery);

                                        // if the resource has not been requested by the incident: show request button
                                        if (mysqli_num_rows($requestResult) == 0) {
                                            print '<td class=resultdata><input type=button value="Request" onClick=
                                                window.open("request.php?ID=' . $row['ID'] . '","Ratting","width=550,height=170,left=150,top=200,toolbar=0,status=0,");></td>';
                                        }
                                        else {
                                            print "<td class='resultdata'></td>";
                                        }
                                    }
                                    // resource in repair, owned or others
                                    else {
                                        print "<td class='resultdata'></td>";
                                    }
                                }

                                print "</tr>";
                            }

                            print "</table>";
                            print "</div>";
                        }

                        ?>


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
