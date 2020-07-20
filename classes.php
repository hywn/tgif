<?php
require 'queries.php';
$classes = $a_all_classes();

echo json_encode($classes);
?>