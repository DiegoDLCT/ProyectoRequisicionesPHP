<?php
require 'backend/config/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query('SELECT * FROM tipos_pagos');
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($tipos);
?>
