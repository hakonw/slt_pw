<html>
<head>
<style>
html{
background-color:#141414;
color: white;
font-family:"Reader Bold", Arial, 'Helvetica Neue', Helvetica, sans-serif;
}

a:link {
    color: #FF851B;
}
a:visited {
    color: #FF851B;
}
tr{
font-size:21px;
}

</style>
</head>
<body>


<center>


<?php

$sql_cfg = array(
    "host" => "localhost",
    "username" => "yukiyuki",
    "password" => "yuki69",
    "dbname" => "db_slt",
    "table" => "loggy"
); //sql settings

$cone = mysqli_connect($sql_cfg["host"], $sql_cfg["username"], $sql_cfg["password"], $sql_cfg["dbname"]); // Create connection

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$result = mysqli_query($cone, "select * from loggy ORDER BY date DESC,time DESC"); /* Order by date, then time. Will make the newest file in the top */

/* Make the table */
echo "<table border='1'>
<tr>
<th>ip</th>
<th>date</th>
<th>time</th>
<th>file</th>
<th>size</th>
</tr>";

/* Add information into the table */
foreach($revert as $row) {
    echo "<tr>";
    echo "<td>" . $row["ip"] . "</td>";
    echo "<td>" . $row["date"] . "</td>";
    echo "<td>" . $row["time"] . "</td>";
    echo "<td>" . "<a href=\"http://slt.pw/" . $row["file"] . "\">" . $row["file"] . "</a> </td>";
    if ($row["size"] > 1024) {
        echo "<td>" . round($row["size"] / 1024, 1) . " mib</td>"; /* Is done to make large amounts of kb to mb */
    }
    else {
        echo "<td>" . $row["size"] . " kib</td>";
    }

    echo "</tr>";
}

echo "</table>";

mysqli_close($con); /* close the sql connection */
?>


</center>

</body>
</html>