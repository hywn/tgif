<?php
require 'queries.php';
$classes = $a_all_classes();

$stuff = array_map(function($class) {

	list($grp, $num, $responded, $rating) = $class;

	$rating = number_format($rating, 2);

	$link = "<a href=\"./viewclass.html?class=$grp+$num\">$grp $num</a>";
	$display = "<tr><td>$link</td><td>$rating</td><td>$responded</td></tr>";

	return [$grp, $num, $display];

}, $classes);

echo json_encode($stuff);
?>