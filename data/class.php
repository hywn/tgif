<?php

if (!isset($_GET['class']))
	die('null');

$class = $_GET['class'];

preg_match('/([A-Z]+)\s*(\d+)/', $class, $matches);

list(, $grp, $num) = $matches;

if (!$grp or !$num) die('null');

$db = new SQLite3('./data.db');

$s = $db->prepare('select * from offerings join evals using (sem, cln) where grp=? and num=?');
$s      ->bindValue(1, $grp);
$s      ->bindValue(2, $num);
$r = $s->execute();

$rows = [];
while($row = $r->fetcharray(SQLITE3_ASSOC))
	array_push($rows, $row);

echo json_encode($rows);

// I think this is just cleanup or something
$r->finalize();
?>