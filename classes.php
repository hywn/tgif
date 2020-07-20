<style>
td, th { padding: 0.2em; text-align: right; border: ridge }
th { text-align: right }
input { font-size: 2em; padding: 0.2em }
body { display: flex; flex-direction: column; align-items: center }
</style>

<h1>all evaluations</h1>
<input id="input" type="text" placeholder="class name, e.g. 'CS 2150'" autofocus />
<table>
	<tr><th>class</th><th>avg. rating</th><th>num. evaluations</th></tr>
	<tbody id="classes"></tbody>
</table>

<script>
const TABLE = document.querySelector('#classes')
const INPUT = document.querySelector('#input')
const DATA  = <?php

require 'queries.php';
$classes = $a_all_classes();


$stuff = array_map(function($class) {

	[$grp, $num, $responded, $rating] = $class;

	$rating = number_format($rating, 2);

	$link = "<a href=\"/viewclass.html?class=$grp+$num\">$grp $num</a>";
	$display = "<tr><td>$link</td><td>$rating</td><td>$responded</td></tr>";

	return [$grp, $num, $display];
}, $classes);

echo json_encode($stuff);
echo "\n\n";
?>

INPUT.addEventListener('input', e => {
	const input = INPUT.value
	const grp   = (input.match(/\w+/i) || [''])[0].toUpperCase()
	const num   = (input.match(/\d+/)  || [''])[0]

	TABLE.innerHTML = DATA
		.filter(([g, n, disp]) => g.startsWith(grp) && n.startsWith(num))
		.map(([, , disp]) => disp)
		.join('')
})

INPUT.dispatchEvent(new Event('input'))

</script>