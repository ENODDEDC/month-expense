<?php
$host = 'sql101.iceiy.com';
$db   = 'icei_39614345_month_expense';
$user = 'icei_39614345';
$pass = 'IdA0COKsif19';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Successfully connected to MySQL database.";
}
