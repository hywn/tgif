<h1>all evaluations</h1>
<input id="input" type="text" />
<table>
	<tr><th>class</th><th>num. evaluations</th><th>avg. rating</th></tr>
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

	$link = "<a href=\"/viewclass.html?class=$grp+$num\">$grp $num</a>";
	$display = "<tr><td>$link</td><td>$responded</td><td>$responded</td></tr>";

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