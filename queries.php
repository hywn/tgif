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
		select grp, num, sum(responded), avg(m5), dsc
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