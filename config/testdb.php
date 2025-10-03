<?php
include "dbconfig.php";
if ($conn) {
    echo "✅ Connected successfully to database: " . $dbname;
} else {
    echo "❌ Connection failed: " . mysqli_connect_error();
}
?>
