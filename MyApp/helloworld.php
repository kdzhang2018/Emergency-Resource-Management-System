<!DOCTYPE html>
<html>
<body>

<h1>My first PHP page</h1>

<?php
//date_default_timezone_set("America/New_York");
//echo "The time is " . date("Y-m-d h:i:sa");
?>

<?php
echo "current date in php is ";
echo date("Y-m-d h:i:sa");?>

<?php
$connect = mysqli_connect("localhost", "team063", "team063");
if (!$connect) {
    die("Failed to connect to database");
}
mysqli_select_db($connect, "cs6400_team063") or die("Unable to select database");

$result = mysqli_query($connect, "SELECT CURDATE(), NOW()");
while($row=mysqli_fetch_array($result))
{
    echo "SQL time is " ;
    echo $row['NOW()'] ;
}
?>

</body>
</html>
