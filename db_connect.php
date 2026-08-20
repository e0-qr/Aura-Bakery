
<?php
$database_file = 'bakery.db';
try {
    $db = new PDO("sqlite:$database_file");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {   
    die("Connection failed: " . $e->getMessage());
}
?>
