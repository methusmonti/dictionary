<?php

$query = "SELECT * FROM `words` WHERE LENGTH(word) > 5 ORDER BY word ASC";
echo "<h1>มีคำกี่คำที่มีความยาว > 5 character</h1>";
echo "<h2>{$query}</h2>";

// Database Config
$servername = "localhost";
$username = "root";
$password = "root";

// Database Connector
$conn = new mysqli($servername, $username, $password, "dictionary");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

$result = $conn->query($query);
echo "Total Words = ".number_format(mysqli_num_rows($result),0)."<br>";
while ($row = $result->fetch_assoc()) {
    $word = $row["word"];
    echo $word."<br>";
}
?>