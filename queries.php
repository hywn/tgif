<?php

// some helper SQL functions

$db = new SQLite3('./data.db');

$a_avgs = function ($grp, $num) use ($db) {
	$s = $db->prepare('
		select avg(m1), avg(m2), avg(m3), avg(m4), avg(m5)
		from offerings join evals using (sem, cln) where grp=? and num=?
	');
	$s      ->bindValue(1, $grp);
	$s      ->bindValue(2, $num);
	$r = $s->execute();

	$avgs = $r->fetcharray(SQLITE3_NUM);
	$r->finalize();

	return array_map(function($avg) { return round($avg, 2); }, $avgs);
};

$a_all_classes = function () use ($db) {
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
	$ar = [];
	while($row = $r -> fetchArray(SQLITE3_NUM))
		array_push($ar, $row);

	$r->finalize();
	return $ar;
};

?>