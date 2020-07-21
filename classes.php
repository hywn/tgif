<?php
$db = new SQLite3('./data.db');

$r = $db->query('
	select
		grp,
		num,
		sum(responded),
		(sum(m1) + sum(m2)) / (count(m1) + count(m2)) as class_avg,
		(sum(m3) + sum(m4) + sum(m5)) / (count(m3) + count(m4) + count(m5)) as prof_avg,
		dsc
	from offerings join evals using (sem, cln)
	group by grp, num
	order by sum(responded) desc
');

$classes = [];
while($row = $r -> fetchArray(SQLITE3_NUM))
	array_push($classes, $row);

echo json_encode($classes);
?>