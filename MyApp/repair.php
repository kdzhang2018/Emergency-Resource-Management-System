<?php

$connect = mysqli_connect("localhost", "team063", "team063");
if (!$connect) {
    die("Failed to connect to database");
}
mysqli_select_db($connect, "cs6400_team063") or die("Unable to select database");

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//if (isset($_POST['submit'])) {
    $resourceID = $_GET['ID'];
    $repairDays = $_POST['repairDays'];
    if (!is_numeric($repairDays) || $repairDays < 1 || $repairDays != round($repairDays)) {
        $errorMsg = "Error: Provide positive integer";
    }
    else {
        $repairDays1 = $repairDays - 1;

        $query = "SELECT status, availableDate " .
            "FROM Resource " .
            "WHERE resourceID = '$resourceID'";
        $result = mysqli_query($connect, $query);
        if (!$result) {
            print "<p class='error'>Error: " . mysqli_error($connect) . "</p>";
            exit();
        }
        $row = mysqli_fetch_array($result);
        if (!$row) {
            print "<p>Error: No data returned from database.</p>";
            exit();
        }
        $status = $row['status'];
        $availableDate = $row['availableDate'];

        if ($status == 'available') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime("+$repairDays1 days"));

            $query = "INSERT INTO Repair (resourceID_Rep, startDate_R, endDate) " .
                "VALUES('$resourceID', '$startDate', '$endDate')";
            if (!mysqli_query($connect, $query)) {
                print '<p class="error">Error: Failed to set the repair. ' . mysqli_error($connect) . '</p>';
                exit();
            }

            $availableDate = date('Y-m-d', strtotime("+$repairDays days"));
            $query = "UPDATE Resource " .
                "SET status =  'in repair', availableDate = '$availableDate' " .
                "WHERE resourceID = '$resourceID'";
            if (!mysqli_query($connect, $query)) {
                print '<p class="error">Error: Failed to update the resource status. ' . mysqli_error($connect) . '</p>';
                exit();
            }
            echo "<script>window.close();</script>";

        }
        if ($status == 'in use') {
            $startDate = $availableDate;
            $endDate = date('Y-m-d', strtotime($availableDate . " + $repairDays1 days"));
            $query = "INSERT INTO Repair (resourceID_Rep, startDate_R, endDate) " .
                "VALUES('$resourceID', '$startDate', '$endDate')";
            if (!mysqli_query($connect, $query)) {
                print '<p class="error">Error: Failed to set the repair. ' . mysqli_error($connect) . '</p>';
                exit();
            }
            echo "<script>window.close();</script>";

        }
    }
}
?>

<html>
<body>
<form action="" method="post">
    Repair days: <input type="text" name="repairDays"><br>
    <input type="submit" name="submit" value="Submit">
    <input type=button onClick="self.close();" value="Cancel">
    <?php
    if (!empty($errorMsg)) {
        print "<div style='color:red'>$errorMsg</div>";
    }
    ?>
</form>

</body>
</html>