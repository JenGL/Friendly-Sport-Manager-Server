<?php
include 'private/db.php';
include 'private/common_to_all.php';

$db = new DatabaseConnection();

echo $db->query('SELECT match FROM Goals')->fetch_assoc()['match'];
