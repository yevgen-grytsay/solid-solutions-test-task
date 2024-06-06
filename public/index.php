<?php

$date = date('Y-m-d H:i:s');

$pdo = new PDO("mysql:host=mysql;dbname=app", "uapp", "uapp123");

$stmt = $pdo->query("SELECT VERSION() as `version`");

$mysql = "ERR";
if (false !== $stmt) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $mysql = sprintf("%s=%s", key($row), current($row));
}
?>

<p>Date: <?= $date ?></p>
<p>MySql: <?= $mysql ?></p>
