<?php

if (!isset($_GET['class']))
	die("oop can't find that class !");

$class = $_GET['class'];

preg_match('/([A-Z]+)(\d+)/', $class, $matches);

[, $grp, $num] = $matches;

if (!$grp or !$num) die("oop can't find that class !");

require './queries.php';

$avgs = $a_avgs($grp, $num);
echo "<h1>$grp $num offerings (avg. overall $avgs[4])</h1>";

// using SQL, gets all comments from database
$s = $db->prepare('select * from offerings join evals using (sem, cln) where grp=? and num=?');
$s      ->bindValue(1, $grp);
$s      ->bindValue(2, $num);
$r = $s->execute();

echo '<table>';
echo '<tr><th>semester</th><th>no.</th><th>professor</th><th>overall rating</th></tr>';
while($row = $r->fetcharray()) {

	$sem = $row['sem'];
	$cln = $row['cln'];
	$prf = $row['prf'];
	$m5  = $row['m5'];

	echo '<tr>';
	foreach(['sem', 'cln', 'prf', 'm5'] as $colname)
		echo "<td>$row[$colname]</td>";
	echo '</tr>';

}
echo '</table>';

// I think this is just cleanup or something
$r->finalize();

?>