<?php

$query = "SELECT * FROM words WHERE word LIKE '%a%a%' OR word LIKE '%b%b%' OR word LIKE '%c%c%' OR word LIKE '%d%d%' OR word LIKE '%e%e%' OR word LIKE '%f%f%' OR word LIKE '%g%g%' OR word LIKE '%h%h%' OR word LIKE '%i%i%' OR word LIKE '%j%j%' OR word LIKE '%k%k%' OR word LIKE '%l%l%' OR word LIKE '%m%m%' OR word LIKE '%n%n%' OR word LIKE '%o%o%' OR word LIKE '%p%p%' OR word LIKE '%q%q%' OR word LIKE '%r%r%' OR word LIKE '%s%s%' OR word LIKE '%t%t%' OR word LIKE '%u%u%' OR word LIKE '%v%v%' OR word LIKE '%w%w%' OR word LIKE '%x%x%' OR word LIKE '%y%y%' OR word LIKE '%z%z%' ORDER BY word ASC";
echo "<h1>มีคำกี่คำที่มีตัวอักษรซ้ำในคำมากกว่าหรือเท่ากับ 2 character</h1>";
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