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

$username = $_SESSION['username'];
/* Return a Resource */
if (!empty($_GET['return_deploy'])) {
    $ID = mysqli_real_escape_string($connect, $_GET['return_deploy']);
    $query1 =
        "UPDATE Resource " .
        "SET status = 'available', availableDate = NULL " .
        "WHERE resourceID IN (SELECT resourceID_Req FROM Request WHERE requestID = '$ID' )";
    $query2 =
        "UPDATE Deploy " .
        "SET returnDate = CURDATE() " .
        "WHERE requestID_D = '$ID' ";
    $result1 = mysqli_query($connect, $query1);
    $result2 = mysqli_query($connect, $query2);
    if (!$result1) {
        print '<p class="error">Error: ' . mysqli_error($connect) . '</p>';
        exit();
    }
    if (!$result2) {
        print '<p class="error">Error: ' . mysqli_error($connect) . '</p>';
        exit();
    }
}
/* Cancel a request */
if (!empty($_GET['cancel_request'])) {
    $ID = mysqli_real_escape_string($connect, $_GET['cancel_request']);
    $query = "DELETE FROM Request " .
        "WHERE requestID =  '$ID' ";
    $result = mysqli_query($connect, $query);
    if (!$result) {
        print '<p class="error">Error: ' . mysqli_error($connect) . '</p>';
        exit();
    }
}
/* Deploy a request */
if (!empty($_GET['deploy_request'])) {
    $ID = mysqli_real_escape_string($connect, $_GET['deploy_request']);
    $returnBy = mysqli_real_escape_string($connect, $_GET['returnBy']);
    /*$query = "SELECT * FROM Request ".
        "WHERE requestID = '$ID'";
    if (!mysqli_query($connect, $query)) {
        print '<p class="error">Error: Failed to query the request. ' . mysqli_error($connect) . '</p>';
        exit();
    }
    $row = mysqli_fetch_row(mysqli_query($connect, $query));
    $returnBy = $row['returnBy'];*/

    $startDate = date('Y-m-d');
    $query = "INSERT INTO Deploy (requestID_D, startDate_D) ".
        "VALUES ('$ID', '$startDate') ";
    if (!mysqli_query($connect, $query)) {
        print '<p class="error">Error: Failed to deploy the request. ' . mysqli_error($connect) . '</p>';
        exit();
    }

    $availableDate = date('Y-m-d', strtotime($returnBy ." + 1 days"));
    $query = "UPDATE Resource ".
        "SET status =  'in use', availableDate = '$availableDate' ".
        "WHERE resourceID IN (SELECT resourceID_Req FROM Request WHERE requestID='$ID' )";
    if (!mysqli_query($connect, $query)) {
        print '<p class="error">Error: Failed to update the resource status. ' . mysqli_error($connect) . '</p>';
        exit();
    }
}
/* Cancel a repair */
if (!empty($_GET['cancel_repair_ID'])) {
    $ID = mysqli_real_escape_string($connect, $_GET['cancel_repair_ID']);
    $sd = mysqli_real_escape_string($connect, $_GET['cancel_repair_sd']);
    $ed = mysqli_real_escape_string($connect, $_GET['cancel_repair_ed']);
    $query = "DELETE FROM Repair " .
        "WHERE resourceID_Rep='$ID' AND startDate_R='$sd' AND endDate='$ed' ";
    $result = mysqli_query($connect, $query);
    if (!$result) {
        print '<p class="error">Error: ' . mysqli_error($connect) . '</p>';
        exit();
    }
}
?>

<html>
<head>
    <title>
        ERMS Resource Status

    </title>

    <link rel="stylesheet" type="text/css" href="style.css"/>
    <style>
        table, th, td {
            font-size: 12px;
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
                <div class="title_name"><?php echo "Resource Status" ?></div>

            <div class="features">

                <div class="profile_section">

                    <div class="subtitle">Resource in use</div>

                    <?php
                    mysqli_query($connect, "DROP VIEW IF EXISTS MyIncident; ");
                    $query_MyIncident = "CREATE VIEW MyIncident AS " .
                        "SELECT Incident.incidentID, Incident.description AS Incident " .
                        "FROM Incident INNER JOIN User ON Incident.username_Inc = User.username " .
                        "WHERE User.username = '$username'";
                    $result_MyIncident = mysqli_query($connect, $query_MyIncident);
                    /*                    if (!$result_MyIncident) {
                                            print '<p class="error">Error: result_MyIncident ' . mysqli_error($connect) . '</p>';
                                            exit();
                                        }*/
                    mysqli_query($connect, "DROP VIEW IF EXISTS MyRequest; ");
                    $query_MyRequest = "CREATE VIEW MyRequest AS " .
                        "SELECT Resource.resourceID AS ID, Resource.name AS ResourceName, Resource.username_R, MyIncident.Incident,
                         Request.requestID, Request.returnBy " .
                        "FROM Request INNER JOIN MyIncident ON Request.incidentID_Req =
                        MyIncident.incidentID INNER JOIN Resource ON Request.resourceID_Req = Resource.resourceID";
                    $result_MyRequest = mysqli_query($connect, $query_MyRequest);
                    if (!$result_MyRequest) {
                        print '<p class="error">Error: result_MyRequest ' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    mysqli_query($connect, "DROP VIEW IF EXISTS MyRequestInUse; ");
                    $query_MyRequestInUse = "CREATE VIEW MyRequestInUse AS " .
                        "SELECT MyRequest.ID, MyRequest.ResourceName, MyRequest.Incident, User.name, Deploy.startDate_D, MyRequest.returnBy, MyRequest.requestID " .
                        "FROM MyRequest INNER JOIN Deploy ON MyRequest.requestID = Deploy.requestID_D " .
                        "INNER JOIN User ON User.username = MyRequest.username_R " .
                        "WHERE Deploy.returnDate is null";
                    $result_MyRequestInUse = mysqli_query($connect, $query_MyRequestInUse);
                    if (!$result_MyRequestInUse) {
                        print '<p class="error">Error: result_MyRequestInUse' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    $query = "SELECT * " .
                        "FROM MyRequestInUse";
                    $result = mysqli_query($connect, $query);
                    if (!$result) {
                        print '<p class="error">Error: result' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    $row = mysqli_fetch_array($result);
                    if ($row) {
                        print '<table width="80%">';
                        print '<tr>';
                        print '<th>ID</td>';
                        print '<th>Resource Name</td>';
                        print '<th>Incident</td>';
                        print '<th>Owner</td>';
                        print '<th>Start Date</td>';
                        print '<th>Return By</td>';
                        print '<th>Action</td>';
                        print '</tr>';
                        while ($row) {
                            print '<tr>';
                            print '<td>' . $row['ID'] . '</td>';
                            print '<td>' . $row['ResourceName'] . '</td>';
                            print '<td>' . $row['Incident'] . '</td>';
                            print '<td>' . $row['name'] . '</td>';
                            print '<td>' . $row['startDate_D'] . '</td>';
                            print '<td>' . $row['returnBy'] . '</td>';
                            print '<td><a href="ResourceStatus.php?return_deploy=' . urlencode($row['requestID'] ) .'">Return</a></td>';
                            print '</tr>';
                            $row = mysqli_fetch_array($result);
                        }
                        print '</table>';
                    } else {
                        print "<br/>None!";
                    }
                    ?>


                </div>
                <div class="profile_section">
                    <div class="subtitle">Resources Requested by me</div>
                    <?php
                    mysqli_query($connect, "DROP VIEW IF EXISTS MyRequestNotDeploy; ");
                    $query_MyRequestNotDeploy = "CREATE VIEW MyRequestNotDeploy AS " .
                        "SELECT * " .
                        "FROM MyRequest " .
                        "WHERE returnBy >= CURDATE() AND requestID NOT IN " .
                        "(SELECT requestID_D AS requestID from Deploy)";
                    mysqli_query($connect, $query_MyRequestNotDeploy);
                    $query_ResourceRequestedByMe = "SELECT MyRequestNotDeploy.ID, MyRequestNotDeploy.ResourceName, MyRequestNotDeploy.Incident, 
                         User.name, MyRequestNotDeploy.returnBy, MyRequestNotDeploy.requestID " .
                        "FROM MyRequestNotDeploy INNER JOIN User ON MyRequestNotDeploy.username_R = User.username";
                    $result_ResourceRequestedByMe = mysqli_query($connect, $query_ResourceRequestedByMe);
                    if (!$result_ResourceRequestedByMe) {
                        print '<p class="error">Error: result_MyRequestNotDeploy ' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    $row = mysqli_fetch_array($result_ResourceRequestedByMe);
                    if ($row) {
                        print '<table width="80%">';
                        print '<tr>';
                        print '<th>ID</td>';
                        print '<th>Resource Name</td>';
                        print '<th>Incident</td>';
                        print '<th>Owner</td>';
                        print '<th>Return By</td>';
                        print '<th>Action</td>';
                        print '</tr>';
                        while ($row) {
                            print '<tr>';
                            print '<td>' . $row['ID'] . '</td>';
                            print '<td>' . $row['ResourceName'] . '</td>';
                            print '<td>' . $row['Incident'] . '</td>';
                            print '<td>' . $row['name'] . '</td>';
                            print '<td>' . $row['returnBy'] . '</td>';
                            print '<td><a href="ResourceStatus.php?cancel_request=' . urlencode($row['requestID']) . '">Cancel</a></td>';
                            print '</tr>';
                            $row = mysqli_fetch_array($result_ResourceRequestedByMe);
                        }
                        print '</table>';
                    } else {
                        print "<br/>None!";
                    }
                    ?>


                </div>
                <div class="profile_section">
                    <div class="subtitle">Resources Request Received by me</div>
                    <?php
                    mysqli_query($connect, "DROP VIEW IF EXISTS MyResource; ");
                    $query_MyResource = "CREATE VIEW MyResource AS " .
                        "SELECT Resource.resourceID, Resource.name AS ResourceName, Resource.status " .
                        "FROM Resource INNER JOIN User ON Resource.username_R = User.username " .
                        "WHERE User.username = '$username'";
                    $result_MyResource = mysqli_query($connect, $query_MyResource);
                    if (!$result_MyResource) {
                        print '<p class="error">Error: result_MyResource ' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    mysqli_query($connect, "DROP VIEW IF EXISTS ReceivedRequest; ");
                    $query_ReceivedRequest = "CREATE VIEW ReceivedRequest AS " .
                        "SELECT Request.requestID, MyResource.resourceID AS ID, MyResource.ResourceName, 
                        Incident.description AS Incident, User.name AS RequestedBy, Request.returnBy, MyResource.status " .
                        "FROM Request INNER JOIN MyResource ON Request.resourceID_Req = MyResource.resourceID INNER JOIN 
                        Incident ON Incident.incidentID = Request.incidentID_Req INNER JOIN User ON Incident.username_Inc = User.username " .
                        "WHERE Request.requestID NOT IN ( SELECT requestID_D AS requestID FROM Deploy) AND MyResource.status = 'available';";
                    $result_ReceivedRequest = mysqli_query($connect, $query_ReceivedRequest);
                    if (!$result_ReceivedRequest) {
                        print '<p class="error">Error: result_MyResource ' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    mysqli_query($connect, "DROP VIEW IF EXISTS ResourcesWithScheduledRepair; ");
                    $query_ResourcesWithScheduledRepair = "CREATE VIEW ResourcesWithScheduledRepair AS " .
                        "SELECT resourceID_Rep AS ID " .
                        "FROM Repair " .
                        "WHERE startDate_R > CURDATE()";
                    $result_ResourcesWithScheduledRepair = mysqli_query($connect, $query_ResourcesWithScheduledRepair);
                    if (!$result_ResourcesWithScheduledRepair) {
                        print '<p class="error">Error: result_ResourcesWithScheduledRepair) ' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    mysqli_query($connect, "DROP VIEW IF EXISTS ReceivedRequestInUseInRepairScheduleRepair; ");
                    $query_ReceivedRequestInUseInRepairScheduleRepair = "CREATE VIEW ReceivedRequestInUseInRepairScheduleRepair AS " .
                        "SELECT Request.requestID, MyResource.resourceID AS ID, MyResource.ResourceName, 
                        Incident.description AS Incident, User.name AS RequestedBy, Request.returnBy, MyResource.status " .
                        "FROM Request INNER JOIN MyResource ON Request.resourceID_Req = MyResource.resourceID INNER JOIN 
                        Incident ON Incident.incidentID = Request.incidentID_Req INNER JOIN User ON Incident.username_Inc = User.username " .
                        "WHERE Request.requestID NOT IN ( SELECT requestID_D AS requestID FROM Deploy) AND MyResource.status != 'available' " .
                        "UNION " .
                        "(SELECT * " .
                        "FROM ReceivedRequest " .
                        "WHERE ReceivedRequest.ID IN (SELECT * FROM ResourcesWithScheduledRepair)); ";
                    $result_ReceivedRequestInUseInRepairScheduleRepair = mysqli_query($connect, $query_ReceivedRequestInUseInRepairScheduleRepair);
                    if (!$result_ReceivedRequestInUseInRepairScheduleRepair) {
                        print '<p class="error">Error: ReceivedRequestInUseInRepairScheduleRepair ' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    //mysqli_query($connect, "DROP VIEW IF EXISTS RequestToDeployReject; ");
                    //$query = "CREATE VIEW RequestToDeployReject AS " .
                    $query =
                        "SELECT * " .
                        "FROM ReceivedRequest " .
                        "WHERE ID NOT IN (SELECT * FROM ResourcesWithScheduledRepair); ";
                    $result = mysqli_query($connect, $query);
                    if (!$result) {
                        print '<p class="error">Error: asd ' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    $query_Deployed = "SELECT * " .
                        "FROM ReceivedRequestInUseInRepairScheduleRepair ";
                    $result_Deployed = mysqli_query($connect, $query_Deployed);
                    $row = mysqli_fetch_array($result);
                    $row_Deployed = mysqli_fetch_array($result_Deployed);
                    if ($row or $row_Deployed) {
                        print '<table width="80%">';
                        print '<tr>';
                        print '<th>ID</td>';
                        print '<th>Resource Name</td>';
                        print '<th>Incident</td>';
                        print '<th>Requested By</td>';
                        print '<th>Return By</td>';
                        print '<th>Action</td>';
                        print '</tr>';
                        while ($row) {
                            print '<tr>';
                            print '<td>' . $row['ID'] . '</td>';
                            print '<td>' . $row['ResourceName'] . '</td>';
                            print '<td>' . $row['Incident'] . '</td>';
                            print '<td>' . $row['RequestedBy'] . '</td>';
                            print '<td>' . $row['returnBy'] . '</td>';
                            //print '<td><a href="requests.php">Deploy</a></td>';
                            print '<td><a href="ResourceStatus.php?deploy_request=' . urlencode($row['requestID']) . '&returnBy=' . urlencode($row['returnBy']) . ' ">Deploy</a>';
                            print ' <a href="ResourceStatus.php?cancel_request=' . urlencode($row['requestID']) . '"> Reject</a></td>';
                            print '</tr>';
                            $row = mysqli_fetch_array($result);
                        }
                        while ($row_Deployed) {
                            print '<tr>';
                            print '<td>' . $row_Deployed['ID'] . '</td>';
                            print '<td>' . $row_Deployed['ResourceName'] . '</td>';
                            print '<td>' . $row_Deployed['Incident'] . '</td>';
                            print '<td>' . $row_Deployed['RequestedBy'] . '</td>';
                            print '<td>' . $row_Deployed['returnBy'] . '</td>';
                            print '<td><a href="ResourceStatus.php?cancel_request=' . urlencode($row_Deployed['requestID']) . '">Reject</a></td>';
                            print '</tr>';
                            $row_Deployed = mysqli_fetch_array($result_Deployed);
                        }
                        print '</table>';
                    } else {
                        print "<br/>None!";
                    }
                    ?>


                </div>
                <div class="profile_section">

                    <div class="subtitle">Repairs Scheduled/In-progress</div>

                    <?php
                    // mysqli_query($connect, "DROP VIEW IF EXISTS MyRepair; ");
                    // $query_MyRepair = "CREATE VIEW MyRepair AS " .
                    $query_MyRepair =
                        "SELECT MyResource.resourceID, MyResource.ResourceName,  Repair.startDate_R, Repair.endDate " .
                        "FROM MyResource INNER JOIN Repair ON MyResource.resourceID = Repair.resourceID_Rep " .
                        "WHERE Repair.endDate >= CURDATE()";
                    $result_MyRepair = mysqli_query($connect, $query_MyRepair);
                    if (!$result_MyRepair) {
                        print '<p class="error">Error: result_MyRepair' . mysqli_error($connect) . '</p>';
                        exit();
                    }
                    $row = mysqli_fetch_array($result_MyRepair);
                    if ($row) {
                        print '<table width="80%">';
                        print '<tr>';
                        print '<th>ID</td>';
                        print '<th>Resource Name</td>';
                        print '<th>Start On</td>';
                        print '<th>Ready By</td>';
                        print '<th>Action</td>';
                        print '</tr>';
                        while ($row) {
                            //print '<tr>';
                            print '<td>' . $row['resourceID'] . '</td>';
                            print '<td>' . $row['ResourceName'] . '</td>';
                            print '<td>' . $row['startDate_R'] . '</td>';
                            print '<td>' . $row['endDate'] . '</td>';
                            if ($row['startDate_R'] > date('Y-m-d'))
                                //print '<td><a href="requests.php">Cancel</a></td>';
                            {
                                print '<td><a href="ResourceStatus.php?cancel_repair_ID=' . urlencode($row['resourceID']) . '&cancel_repair_sd=' . urlencode($row['startDate_R']) .'&cancel_repair_ed=' . urlencode($row['endDate']) .'">Cancel</a></td>';
                            }
                            else {
                                print '<td></td>';
                            }
                            print '</tr>';
                            $row = mysqli_fetch_array($result_MyRepair);
                        }
                        print '</table>';
                    } else {
                        print "<br/>None!";
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