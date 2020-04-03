<?php

function delTree($dir) { // Recursively Delete Directory Function 
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

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

echo "[STATUS] " . date('Y-m-d H:i:s') . " Clear Cache : START<br>";
/* Start of Clear Cache */
// Clear Database
$query = "TRUNCATE TABLE `words`";
$conn->query($query);

// Delete Dictionary Folder
delTree('dictionary');
delTree('dictionaryZip');

/* End of Clear Cache */
echo "[STATUS] " . date('Y-m-d H:i:s') . " Clear Cache : DONE<br>";

echo "[STATUS] " . date('Y-m-d H:i:s') . " Program : START<br>";
$time_start = microtime(true);  // Get Starting Time
/* Initialize Program */

// Create Structure Array
$structure = array();

// Create Dictionary Folder
if (!mkdir("dictionary", 0777, true)) {
    die("Failed to create folders dictionary");
}

// Create ZIP Folder
if (!mkdir("dictionaryZip", 0777, true)) {
    die("Failed to create folders dictionaryZip");
}

// Import Raw Dictionary File
echo "[STATUS] " . date('Y-m-d H:i:s') . " Import File : START<br>";

$filename = "words.txt";
echo "File : <a href='{$filename}' target='_blank'>CLICK</a><br>";
$objFopen = fopen($filename, 'r');

echo "[STATUS] " . date('Y-m-d H:i:s') . " Create file and insert word into database : START<br>";

// Create SQL Query
$sql_insert_query = "INSERT INTO words (word) VALUES ";
if ($objFopen) {
    while (!feof($objFopen)) {
        $line = fgets($objFopen, 4096); // Get each line of word
        $word = strtolower(trim($line));
        if (strlen($word) >= 2 && ctype_alpha($word)) { // Check if word contains 2 or more character and word contains only alphabet
            $first_alphabet = substr($word, 0, 1); // first alphabet (folder name)
            $first_second_alphabet = substr($word, 0, 2); // first and second alphabet (subfolder name) 
            /* if (!is_dir("dictionary/{$first_alphabet}")) { // Check if folder exists
              if (!mkdir("dictionary/{$first_alphabet}", 0777, true)) { // If not, create folder
              die("Failed to create folders {$first_alphabet}");
              }
              }
              if (!is_dir("dictionary/{$first_alphabet}/{$first_second_alphabet}")) { // Check if subfolder exist
              if (!mkdir("dictionary/{$first_alphabet}/{$first_second_alphabet}", 0777, true)) { /// If not, create subfolder
              die("Failed to create folders {$first_second_alphabet}");
              } else // Create subfolder array in Structure array
              $structure[$first_alphabet][$first_second_alphabet] = array();
              } */
            $wordFile = fopen("dictionary/{$word}.txt", "w") or die("Unable to open file!"); // Create word file that contains 100 words

            file_put_contents("dictionary/{$word}.txt", str_repeat($word . "\n", 100));
            $sql_insert_query .= " ('{$word}'),"; // Create SQL Query
        }
    }
    
    // Create SQL Query
    $sql_insert_query .= "('')";
    $sql_insert_query = str_replace(",('')", "", $sql_insert_query);
    $conn->query($sql_insert_query);
}

// Creat folder, subfolder
$result = $conn->query("SELECT DISTINCT LEFT(word,1) AS first, LEFT(word,2) AS second FROM `words` ORDER BY word ASC");
while ($row = $result->fetch_assoc()) {
    if (!mkdir("dictionary/{$row["first"]}/{$row["second"]}", 0777, true)) {
        die("Failed to create folders {$row["second"]}");
    }
    $structure[$row["first"]][$row["second"]] = array();
}

/* Start Generate PDF */
echo "[STATUS] " . date('Y-m-d H:i:s') . " Generate PDF : START<br>";
require('fpdf/tfpdf.php');

$pdf = new tFPDF('P', 'mm', 'A4');
$pdf->SetLeftMargin("14");
$pdf->SetTopMargin("10");
$pdf->SetAutoPageBreak(TRUE);
$pdf->AddPage('P', 'A4');
$result = $conn->query("SELECT LEFT(word,1) AS first, LEFT(word,2) AS second, word FROM `words` ORDER BY word ASC");
while ($row = $result->fetch_assoc()) {
    $word = $row["word"];
    $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->Cell(80, 8, $word);
    $pdf->Ln();
    rename("dictionary/{$word}.txt", "dictionary/{$row["first"]}/{$row["second"]}/{$word}.txt");
}
$pdf->Output('dict.pdf', 'F');
echo "PDF File = <a href='dict.pdf' target='_blank'>CLICK</a><br>";
echo "[STATUS] " . date('Y-m-d H:i:s') . " Generate PDF : END<br>";
/* End Generate PDF */

echo "[STATUS] " . date('Y-m-d H:i:s') . " Create file and insert word into database : DONE<br>";
echo "Directory : <a href='dictionary' target='_blank'>CLICK</a><br>";
echo "[STATUS] " . date('Y-m-d H:i:s') . " Import File : DONE<br>";
echo "- มีคำกี่คำที่มีความยาว > 5 character = <a href='query1.php' target='_blank'>CLICK</a><br>";
echo "- มีคำกี่คำที่มีตัวอักษรซ้ำในคำมากกว่าหรือเท่ากับ 2 character = <a href='query2.php' target='_blank'>CLICK</a><br>";
echo "- มีคำกี่คำที่ขึ้นต้นและลงท้ายด้วยตัวอักษรเดียวกัน = <a href='query3.php' target='_blank'>CLICK</a><br>";
echo "- ให้สั่งอัพเดตคำที่มีทั้งหมดให้ตัวอักษรตัวแรกเป็นตัวพิมพ์ใหญ่ = <a href='query4.php' target='_blank'>CLICK</a><br>";
// Sort Structure
ksort($structure);

echo "[STATUS] " . date('Y-m-d H:i:s') . " File Report : START<br>";
echo "[STATUS] " . date('Y-m-d H:i:s') . " Compress File: START<br>";
// Print output for Uncompress file
echo "<h1>File Size Report</h1>";
foreach ($structure as $folder => $subfolder) {
    $io = popen('/usr/bin/du -sk ' . "dictionary/{$folder}", 'r');
    $size = fgets($io, 4096);
    $size = substr($size, 0, strpos($size, "\t"));
    $structure[$folder]["unzipSize"] = $size;
    pclose($io);
    $size = number_format($size, 0);

    // Compress File
    // Get real path for our folder
    $rootPath = realpath("dictionary/{$folder}");

    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open("dictionaryZip/{$folder}.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (they would be added automatically)
        if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }
    $zip->close();

    $io = popen('/usr/bin/du -sk ' . "dictionaryZip/{$folder}.zip", 'r');
    $zipsize = fgets($io, 4096);
    $zipsize = substr($zipsize, 0, strpos($zipsize, "\t"));
    $structure[$folder]["zipSize"] = $zipsize;
    pclose($io);
    $zipsize = number_format($zipsize, 0);

    // Compare Compress/Uncompress
    $change = ($structure[$folder]["unzipSize"] - $structure[$folder]["zipSize"]) / $structure[$folder]["unzipSize"] * 100;
    $change = number_format($change, 0);

    // Print Result
    echo "{$folder} = Uncompress {$size} KB, Compress {$zipsize} KB ({$change}%)<br>";
}
echo "[STATUS] " . date('Y-m-d H:i:s') . " Compress File: END<br>";
echo "ZIP Directory : <a href='dictionaryZip' target='_blank'>CLICK</a><br>";
echo "[STATUS] " . date('Y-m-d H:i:s') . " File Report : END<br>";


echo "[STATUS] " . date('Y-m-d H:i:s') . " Program : DONE<br>";
$time_end = microtime(true);
$execution_time = number_format(($time_end - $time_start), 3);
echo "Total execution time = {$execution_time} seconds";
?>